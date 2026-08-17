<?php

declare(strict_types=1);

namespace Paperdoc\Support\Pdf;

/**
 * Reconstruit un fichier TrueType ne contenant que les glyphes employés.
 *
 * Embarquer la police entière coûte cher : 423 Ko pour un document latin,
 * plus de 4 Mo avec une police CJK, quelle que soit la quantité de texte.
 * Le sous-ensemble ne garde que les tables indispensables au rendu —
 * glyf, loca, head, hhea, hmtx, maxp, plus les tables d'optimisation
 * (« hinting ») si elles existent.
 *
 * Les identifiants de glyphes sont renumérotés : c'est l'appelant qui
 * fournit la correspondance ancien → nouveau, celle-là même qu'il a
 * utilisée pour écrire le flux de contenu.
 */
final class TrueTypeSubset
{
    private const COMPOSITE_ARGS_ARE_WORDS = 0x0001;
    private const COMPOSITE_HAS_SCALE      = 0x0008;
    private const COMPOSITE_MORE           = 0x0020;
    private const COMPOSITE_HAS_XY_SCALE   = 0x0040;
    private const COMPOSITE_HAS_2X2        = 0x0080;

    /** Tables recopiées telles quelles quand la police les fournit. */
    private const COPIED_TABLES = ['cvt ', 'fpgm', 'prep', 'gasp'];

    /** @var int[] décalages de la table loca d'origine */
    private array $loca = [];

    private string $glyf;

    public function __construct(private readonly TrueTypeFont $font)
    {
        $this->glyf = $this->font->getTable('glyf') ?? '';
        $this->loca = $this->readLoca();
    }

    /**
     * La police peut-elle être réduite ? Une police à glyphes CFF n'a ni
     * glyf ni loca : il n'y a rien à découper ici.
     */
    public function isSupported(): bool
    {
        return $this->glyf !== '' && $this->loca !== [];
    }

    /**
     * Complète la correspondance avec les glyphes composants — un « é »
     * renvoie au « e » et à l'accent, qui doivent suivre dans le
     * sous-ensemble sous peine de dessiner du vide.
     *
     * @param array<int, int> $glyphMap ancien identifiant → nouveau
     * @return array<int, int> la correspondance complétée
     */
    public function closeOverComponents(array $glyphMap): array
    {
        $queue = array_keys($glyphMap);

        while ($queue !== []) {
            $oldGid = array_shift($queue);

            foreach ($this->componentsOf($oldGid) as $component) {
                if (isset($glyphMap[$component])) {
                    continue;
                }

                $glyphMap[$component] = count($glyphMap);
                $queue[] = $component;
            }
        }

        return $glyphMap;
    }

    /**
     * @param array<int, int> $glyphMap ancien identifiant → nouveau, déjà complété
     */
    public function build(array $glyphMap): string
    {
        // Ordonner par nouvel identifiant : loca et hmtx sont indexées par lui.
        $byNewGid = array_flip($glyphMap);
        ksort($byNewGid);

        $glyf = '';
        $loca = [0];
        $advances = [];
        $bearings = [];

        foreach ($byNewGid as $oldGid) {
            $data = $this->glyphData($oldGid);

            if ($data !== '' && $this->isComposite($data)) {
                $data = $this->renumberComponents($data, $glyphMap);
            }

            // Chaque glyphe démarre sur une frontière de 4 octets.
            $padding = (4 - (strlen($data) % 4)) % 4;
            $glyf .= $data . str_repeat("\x00", $padding);
            $loca[] = strlen($glyf);

            $advances[] = $this->font->rawAdvance($oldGid);
            $bearings[] = $this->font->rawLeftSideBearing($oldGid);
        }

        $numGlyphs = count($byNewGid);

        $tables = [
            'head' => $this->buildHead(),
            'hhea' => $this->buildHhea($numGlyphs),
            'maxp' => $this->buildMaxp($numGlyphs),
            'hmtx' => $this->buildHmtx($advances, $bearings),
            'loca' => $this->buildLoca($loca),
            'glyf' => $glyf,
        ];

        foreach (self::COPIED_TABLES as $tag) {
            $copied = $this->font->getTable($tag);

            if ($copied !== null && $copied !== '') {
                $tables[$tag] = $copied;
            }
        }

        return $this->assemble($tables);
    }

    /* =============================================================
     | Glyphes
     |============================================================= */

    /**
     * @return int[]
     */
    private function readLoca(): array
    {
        $raw = $this->font->getTable('loca');

        if ($raw === null || $raw === '') {
            return [];
        }

        $offsets = [];
        $long = $this->font->getIndexToLocFormat() === 1;
        $step = $long ? 4 : 2;
        $count = intdiv(strlen($raw), $step);

        for ($i = 0; $i < $count; $i++) {
            $unpacked = unpack($long ? 'N' : 'n', substr($raw, $i * $step, $step));

            if ($unpacked === false || ! is_int($unpacked[1])) {
                return [];
            }

            // Le format court stocke les décalages divisés par deux.
            $offsets[] = $long ? $unpacked[1] : $unpacked[1] * 2;
        }

        return $offsets;
    }

    private function glyphData(int $glyphId): string
    {
        $start = $this->loca[$glyphId] ?? null;
        $end   = $this->loca[$glyphId + 1] ?? null;

        // Un glyphe vide (espace) a un intervalle nul : c'est légitime.
        if ($start === null || $end === null || $end <= $start || $end > strlen($this->glyf)) {
            return '';
        }

        return substr($this->glyf, $start, $end - $start);
    }

    private function isComposite(string $glyphData): bool
    {
        if (strlen($glyphData) < 2) {
            return false;
        }

        $unpacked = unpack('n', substr($glyphData, 0, 2));

        if ($unpacked === false || ! is_int($unpacked[1])) {
            return false;
        }

        return $unpacked[1] >= 0x8000; // numberOfContours négatif
    }

    /**
     * Identifiants des glyphes composants, vide pour un glyphe simple.
     *
     * @return int[]
     */
    private function componentsOf(int $glyphId): array
    {
        $data = $this->glyphData($glyphId);

        if (! $this->isComposite($data)) {
            return [];
        }

        $components = [];

        foreach ($this->walkComponents($data) as [$offset, $componentGid]) {
            $components[] = $componentGid;
        }

        return $components;
    }

    /**
     * Réécrit les identifiants des composants avec la nouvelle numérotation.
     *
     * @param array<int, int> $glyphMap
     */
    private function renumberComponents(string $data, array $glyphMap): string
    {
        foreach ($this->walkComponents($data) as [$offset, $componentGid]) {
            $new = $glyphMap[$componentGid] ?? 0;
            $data = substr_replace($data, pack('n', $new), $offset, 2);
        }

        return $data;
    }

    /**
     * Parcourt les composants d'un glyphe composite.
     *
     * @return list<array{int, int}> [décalage du champ glyphIndex, identifiant]
     */
    private function walkComponents(string $data): array
    {
        $result = [];
        $position = 10; // après numberOfContours et la boîte englobante
        $length = strlen($data);

        while ($position + 4 <= $length) {
            $header = unpack('nflags/nglyphIndex', substr($data, $position, 4));

            if ($header === false || ! is_int($header['flags']) || ! is_int($header['glyphIndex'])) {
                break;
            }

            $flags = $header['flags'];
            $result[] = [$position + 2, $header['glyphIndex']];

            $position += 4;
            $position += ($flags & self::COMPOSITE_ARGS_ARE_WORDS) !== 0 ? 4 : 2;

            if (($flags & self::COMPOSITE_HAS_SCALE) !== 0) {
                $position += 2;
            } elseif (($flags & self::COMPOSITE_HAS_XY_SCALE) !== 0) {
                $position += 4;
            } elseif (($flags & self::COMPOSITE_HAS_2X2) !== 0) {
                $position += 8;
            }

            if (($flags & self::COMPOSITE_MORE) === 0) {
                break;
            }
        }

        return $result;
    }

    /* =============================================================
     | Tables reconstruites
     |============================================================= */

    private function buildHead(): string
    {
        $head = $this->font->getTable('head') ?? str_repeat("\x00", 54);
        $head = str_pad(substr($head, 0, 54), 54, "\x00");

        // checkSumAdjustment à zéro : il se recalcule sur le fichier final.
        $head = substr_replace($head, pack('N', 0), 8, 4);

        // On écrit toujours une loca au format long, plus simple et sans
        // limite de taille.
        return substr_replace($head, pack('n', 1), 50, 2);
    }

    private function buildHhea(int $numGlyphs): string
    {
        $hhea = $this->font->getTable('hhea') ?? str_repeat("\x00", 36);
        $hhea = str_pad(substr($hhea, 0, 36), 36, "\x00");

        // numberOfHMetrics : une entrée complète par glyphe conservé.
        return substr_replace($hhea, pack('n', $numGlyphs), 34, 2);
    }

    private function buildMaxp(int $numGlyphs): string
    {
        $maxp = $this->font->getTable('maxp') ?? str_repeat("\x00", 32);
        $maxp = str_pad(substr($maxp, 0, 32), 32, "\x00");

        return substr_replace($maxp, pack('n', $numGlyphs), 4, 2);
    }

    /**
     * @param int[] $advances
     * @param int[] $bearings
     */
    private function buildHmtx(array $advances, array $bearings): string
    {
        $hmtx = '';

        foreach ($advances as $i => $advance) {
            $bearing = $bearings[$i] ?? 0;
            $hmtx .= pack('n', max(0, $advance)) . pack('n', $bearing & 0xFFFF);
        }

        return $hmtx;
    }

    /**
     * @param int[] $offsets
     */
    private function buildLoca(array $offsets): string
    {
        $loca = '';

        foreach ($offsets as $offset) {
            $loca .= pack('N', $offset);
        }

        return $loca;
    }

    /**
     * @param array<string, string> $tables
     */
    private function assemble(array $tables): string
    {
        ksort($tables);

        $count = count($tables);
        $searchRange = 1;
        $entrySelector = 0;

        while ($searchRange * 2 <= $count) {
            $searchRange *= 2;
            $entrySelector++;
        }

        $searchRange *= 16;

        $header = pack('N', 0x00010000)
            . pack('n', $count)
            . pack('n', $searchRange)
            . pack('n', $entrySelector)
            . pack('n', $count * 16 - $searchRange);

        $directory = '';
        $body = '';
        $offset = 12 + $count * 16;

        foreach ($tables as $tag => $data) {
            $padded = $data . str_repeat("\x00", (4 - (strlen($data) % 4)) % 4);

            $directory .= substr(str_pad($tag, 4), 0, 4)
                . pack('N', $this->checksum($padded))
                . pack('N', $offset)
                . pack('N', strlen($data));

            $body .= $padded;
            $offset += strlen($padded);
        }

        return $header . $directory . $body;
    }

    private function checksum(string $data): int
    {
        $sum = 0;
        $length = strlen($data);

        for ($i = 0; $i + 4 <= $length; $i += 4) {
            $unpacked = unpack('N', substr($data, $i, 4));

            if ($unpacked !== false && is_int($unpacked[1])) {
                $sum = ($sum + $unpacked[1]) & 0xFFFFFFFF;
            }
        }

        return $sum;
    }
}
