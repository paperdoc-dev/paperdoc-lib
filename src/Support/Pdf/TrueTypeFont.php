<?php

declare(strict_types=1);

namespace Paperdoc\Support\Pdf;

/**
 * Lecteur de fichier TrueType (.ttf) — juste ce qu'il faut pour embarquer
 * une police dans un PDF en CIDFontType2 / Identity-H.
 *
 * Les 14 polices standard n'existent qu'en WinAnsiEncoding : tout ce qui
 * sort du latin-1 y devient « ? ». Embarquer une police est la seule façon
 * d'écrire du cyrillique, du grec, de l'arabe, du CJK ou du thaï dans un
 * PDF.
 *
 * Tables lues : head, hhea, maxp, hmtx, cmap (formats 4 et 12), OS/2, post.
 *
 * La bibliothèque ne livre aucune donnée de police : le fichier est fourni
 * par l'appelant.
 */
final class TrueTypeFont
{
    private string $data;

    /** @var array<string, array{offset: int, length: int}> */
    private array $tables = [];

    /** @var array<int, int> point de code Unicode → identifiant de glyphe */
    private array $cmap = [];

    /** @var array<int, int> identifiant de glyphe → chasse en unités de police */
    private array $advances = [];

    /** @var array<int, int> identifiant de glyphe → approche gauche */
    private array $leftSideBearings = [];

    private int $indexToLocFormat = 0;

    private int $numberOfHMetrics = 0;

    private int $unitsPerEm = 1000;
    private int $numGlyphs = 0;
    private int $ascender = 0;
    private int $descender = 0;
    private int $capHeight = 0;
    private int $italicAngle = 0;
    private int $flags = 32;

    /** @var array{int, int, int, int} */
    private array $bbox = [0, 0, 1000, 1000];

    private string $postScriptName = 'EmbeddedFont';

    /**
     * @throws \RuntimeException si le fichier n'est pas un TrueType exploitable
     */
    public function __construct(string $fileContent, string $sourceLabel = 'police', int $fontIndex = 0)
    {
        $this->data = $fileContent;

        if (strlen($this->data) < 12) {
            throw new \RuntimeException("Fichier TrueType tronqué : {$sourceLabel}");
        }

        $this->readTableDirectory($sourceLabel, $fontIndex);
        $this->readHead();
        $this->readMaxp();
        $this->readHhea();
        $this->readHmtx();
        $this->readCmap($sourceLabel);
        $this->readOs2();
        $this->readPost();
        $this->readName();
    }

    /**
     * @throws \RuntimeException
     */
    public static function fromFile(string $filename, int $fontIndex = 0): self
    {
        if (! is_file($filename) || ! is_readable($filename)) {
            throw new \RuntimeException("Police introuvable ou illisible : {$filename}");
        }

        $content = file_get_contents($filename);

        if ($content === false) {
            throw new \RuntimeException("Impossible de lire la police : {$filename}");
        }

        return new self($content, $filename, $fontIndex);
    }

    /**
     * Les contours sont-ils décrits en CFF (Compact Font Format, typique
     * des .otf) plutôt qu'en glyf ? Cela change la façon dont le PDF doit
     * embarquer la police, et interdit le sous-ensemble.
     */
    public function isCff(): bool
    {
        return isset($this->tables['CFF ']) && ! isset($this->tables['glyf']);
    }

    public function isSubsettable(): bool
    {
        return isset($this->tables['glyf']) && isset($this->tables['loca']);
    }

    public function getData(): string { return $this->data; }

    public function getUnitsPerEm(): int { return $this->unitsPerEm; }

    public function getIndexToLocFormat(): int { return $this->indexToLocFormat; }

    public function getNumberOfHMetrics(): int { return $this->numberOfHMetrics; }

    /**
     * Contenu brut d'une table, null si absente.
     */
    public function getTable(string $tag): ?string
    {
        if (! isset($this->tables[$tag])) {
            return null;
        }

        $entry = $this->tables[$tag];

        return substr($this->data, $entry['offset'], $entry['length']);
    }

    /**
     * Chasse en unités de police (et non en millièmes d'em), telle qu'elle
     * doit être réécrite dans une table hmtx.
     */
    public function rawAdvance(int $glyphId): int
    {
        return $this->advances[$glyphId] ?? 0;
    }

    public function rawLeftSideBearing(int $glyphId): int
    {
        return $this->leftSideBearings[$glyphId] ?? 0;
    }

    public function getPostScriptName(): string { return $this->postScriptName; }

    public function getNumGlyphs(): int { return $this->numGlyphs; }

    public function getItalicAngle(): int { return $this->italicAngle; }

    public function getFlags(): int { return $this->flags; }

    /** @return array{int, int, int, int} en unités de 1000em */
    public function getBoundingBox(): array
    {
        return array_map(fn (int $v): int => $this->toThousandths($v), $this->bbox);
    }

    public function getAscender(): int { return $this->toThousandths($this->ascender); }

    public function getDescender(): int { return $this->toThousandths($this->descender); }

    public function getCapHeight(): int
    {
        return $this->capHeight !== 0 ? $this->toThousandths($this->capHeight) : $this->getAscender();
    }

    /**
     * Identifiant de glyphe d'un point de code, 0 (.notdef) si absent.
     */
    public function glyphForCodepoint(int $codepoint): int
    {
        return $this->cmap[$codepoint] ?? 0;
    }

    /**
     * Chasse d'un glyphe en unités de 1000em, l'unité des largeurs PDF.
     */
    public function glyphWidth(int $glyphId): int
    {
        return $this->toThousandths($this->advances[$glyphId] ?? 0);
    }

    /**
     * Suite d'identifiants de glyphes correspondant au texte UTF-8.
     *
     * @return int[]
     */
    public function glyphsForText(string $text): array
    {
        $glyphs = [];

        foreach ($this->codepoints($text) as $codepoint) {
            $glyphs[] = $this->glyphForCodepoint($codepoint);
        }

        return $glyphs;
    }

    /**
     * Points de code du texte, dans l'ordre.
     *
     * @return list<int>
     */
    public function codepoints(string $text): array
    {
        $codepoints = [];
        $converted = mb_convert_encoding($text, 'UTF-32BE', 'UTF-8');
        $len = strlen($converted);

        for ($i = 0; $i + 4 <= $len; $i += 4) {
            $unpacked = unpack('N', substr($converted, $i, 4));

            if ($unpacked !== false && is_int($unpacked[1])) {
                $codepoints[] = $unpacked[1];
            }
        }

        return $codepoints;
    }

    /* =============================================================
     | Lecture des tables
     |============================================================= */

    private function readTableDirectory(string $sourceLabel, int $fontIndex): void
    {
        $base = 0;

        // 'ttcf' : collection de plusieurs polices dans un seul fichier.
        // L'en-tête donne le décalage du répertoire de tables de chacune.
        if ($this->uint32(0) === 0x74746366) {
            $numFonts = $this->uint32(8);

            if ($numFonts === 0) {
                throw new \RuntimeException("Collection TrueType vide : {$sourceLabel}");
            }

            if ($fontIndex < 0 || $fontIndex >= $numFonts) {
                throw new \RuntimeException(sprintf(
                    'Police %d absente de la collection (%d disponible%s) : %s',
                    $fontIndex,
                    $numFonts,
                    $numFonts > 1 ? 's' : '',
                    $sourceLabel,
                ));
            }

            $base = $this->uint32(12 + $fontIndex * 4);
        }

        $version = $this->uint32($base);

        // 0x00010000 = contours TrueType, 'OTTO' = contours CFF.
        // Les deux exposent les mêmes tables de métriques et de cmap.
        if ($version !== 0x00010000 && $version !== 0x4F54544F && $version !== 0x74727565) {
            throw new \RuntimeException(
                "Signature de police inconnue : {$sourceLabel}"
            );
        }

        $numTables = $this->uint16($base + 4);

        for ($i = 0; $i < $numTables; $i++) {
            $entry = $base + 12 + $i * 16;

            if ($entry + 16 > strlen($this->data)) {
                break;
            }

            $tag = substr($this->data, $entry, 4);

            $this->tables[$tag] = [
                'offset' => $this->uint32($entry + 8),
                'length' => $this->uint32($entry + 12),
            ];
        }

        foreach (['head', 'hhea', 'maxp', 'hmtx', 'cmap'] as $required) {
            if (! isset($this->tables[$required])) {
                throw new \RuntimeException("Table TrueType « {$required} » absente : {$sourceLabel}");
            }
        }

        if (! isset($this->tables['glyf']) && ! isset($this->tables['CFF '])) {
            // CFF2 est le format des polices variables : l'embarquer
            // demanderait d'en figer une instance, ce que PDF ne fait pas.
            if (isset($this->tables['CFF2'])) {
                throw new \RuntimeException(
                    "Police variable (CFF2) non prise en charge, fournir une instance statique : {$sourceLabel}"
                );
            }

            throw new \RuntimeException(
                "Ni contours TrueType (glyf) ni CFF : {$sourceLabel}"
            );
        }
    }

    private function readHead(): void
    {
        $head = $this->tables['head']['offset'];

        $this->unitsPerEm = max(1, $this->uint16($head + 18));
        $this->indexToLocFormat = $this->int16($head + 50);
        $this->bbox = [
            $this->int16($head + 36),
            $this->int16($head + 38),
            $this->int16($head + 40),
            $this->int16($head + 42),
        ];
    }

    private function readMaxp(): void
    {
        $this->numGlyphs = $this->uint16($this->tables['maxp']['offset'] + 4);
    }

    private function readHhea(): void
    {
        $hhea = $this->tables['hhea']['offset'];

        $this->ascender  = $this->int16($hhea + 4);
        $this->descender = $this->int16($hhea + 6);
    }

    private function readHmtx(): void
    {
        $numberOfHMetrics = max(1, $this->uint16($this->tables['hhea']['offset'] + 34));
        $this->numberOfHMetrics = $numberOfHMetrics;
        $hmtx = $this->tables['hmtx']['offset'];

        $last = 0;

        for ($gid = 0; $gid < $this->numGlyphs; $gid++) {
            if ($gid < $numberOfHMetrics) {
                $last = $this->uint16($hmtx + $gid * 4);
                $this->leftSideBearings[$gid] = $this->int16($hmtx + $gid * 4 + 2);
            } else {
                // Au-delà de numberOfHMetrics les glyphes reprennent la
                // dernière chasse : c'est ainsi que les polices monochasse
                // compressent leur table. Seules les approches suivent.
                $this->leftSideBearings[$gid] = $this->int16(
                    $hmtx + $numberOfHMetrics * 4 + ($gid - $numberOfHMetrics) * 2
                );
            }

            $this->advances[$gid] = $last;
        }
    }

    private function readCmap(string $sourceLabel): void
    {
        $cmap = $this->tables['cmap']['offset'];
        $numSubtables = $this->uint16($cmap + 2);

        $candidates = [];

        for ($i = 0; $i < $numSubtables; $i++) {
            $rec = $cmap + 4 + $i * 8;
            $platform = $this->uint16($rec);
            $encoding = $this->uint16($rec + 2);
            $offset = $cmap + $this->uint32($rec + 4);

            // Priorité : Unicode complet (3,10) > BMP (3,1) > Unicode (0,x)
            $priority = match (true) {
                $platform === 3 && $encoding === 10 => 3,
                $platform === 3 && $encoding === 1  => 2,
                $platform === 0                     => 1,
                default                             => 0,
            };

            if ($priority > 0) {
                $candidates[] = ['priority' => $priority, 'offset' => $offset];
            }
        }

        if ($candidates === []) {
            throw new \RuntimeException("Aucune table cmap Unicode exploitable : {$sourceLabel}");
        }

        // Fusion, priorité croissante : une sous-table format 12 n'est pas
        // toujours un sur-ensemble du format 4 — certaines polices n'y
        // mettent que le supplémentaire et laissent l'ASCII au format 4.
        // N'en garder qu'une perdait tout un pan du répertoire.
        usort($candidates, static fn (array $a, array $b): int => $a['priority'] <=> $b['priority']);

        $read = 0;

        foreach ($candidates as $candidate) {
            $format = $this->uint16($candidate['offset']);

            if ($format === 4) {
                $this->readCmapFormat4($candidate['offset']);
                $read++;
            } elseif ($format === 12) {
                $this->readCmapFormat12($candidate['offset']);
                $read++;
            }
        }

        if ($read === 0 || $this->cmap === []) {
            throw new \RuntimeException(
                "Aucune sous-table cmap au format 4 ou 12 : {$sourceLabel}"
            );
        }
    }

    private function readCmapFormat4(int $offset): void
    {
        $segCountX2 = $this->uint16($offset + 6);
        $segCount = intdiv($segCountX2, 2);

        $endCodes    = $offset + 14;
        $startCodes  = $endCodes + $segCountX2 + 2;
        $idDeltas    = $startCodes + $segCountX2;
        $idRangeOffs = $idDeltas + $segCountX2;

        for ($seg = 0; $seg < $segCount; $seg++) {
            $end   = $this->uint16($endCodes + $seg * 2);
            $start = $this->uint16($startCodes + $seg * 2);
            $delta = $this->int16($idDeltas + $seg * 2);
            $rangeOffsetPos = $idRangeOffs + $seg * 2;
            $rangeOffset = $this->uint16($rangeOffsetPos);

            if ($start > $end) {
                continue;
            }

            for ($code = $start; $code <= $end && $code !== 0xFFFF; $code++) {
                if ($rangeOffset === 0) {
                    $gid = ($code + $delta) & 0xFFFF;
                } else {
                    $glyphPos = $rangeOffsetPos + $rangeOffset + ($code - $start) * 2;

                    if ($glyphPos + 2 > strlen($this->data)) {
                        continue;
                    }

                    $gid = $this->uint16($glyphPos);

                    if ($gid !== 0) {
                        $gid = ($gid + $delta) & 0xFFFF;
                    }
                }

                if ($gid !== 0) {
                    $this->cmap[$code] = $gid;
                }
            }
        }
    }

    private function readCmapFormat12(int $offset): void
    {
        $nGroups = $this->uint32($offset + 12);

        for ($g = 0; $g < $nGroups; $g++) {
            $rec = $offset + 16 + $g * 12;

            if ($rec + 12 > strlen($this->data)) {
                break;
            }

            $start = $this->uint32($rec);
            $end   = $this->uint32($rec + 4);
            $gid   = $this->uint32($rec + 8);

            // Un groupe couvrant tout le plan Unicode ferait exploser la
            // table : les polices réelles restent bien en deçà.
            if ($end - $start > 0x10FFFF) {
                continue;
            }

            for ($code = $start; $code <= $end; $code++) {
                $this->cmap[$code] = $gid + ($code - $start);
            }
        }
    }

    private function readOs2(): void
    {
        if (! isset($this->tables['OS/2'])) {
            return;
        }

        $os2 = $this->tables['OS/2']['offset'];
        $version = $this->uint16($os2);

        if ($version >= 2 && $os2 + 90 <= strlen($this->data)) {
            $this->capHeight = $this->int16($os2 + 88);
        }

        // fsSelection bit 0 = italique, bit 5 = gras
        $fsSelection = $this->uint16($os2 + 62);

        if (($fsSelection & 0x01) !== 0) {
            $this->flags |= 64; // Italic
        }
    }

    private function readPost(): void
    {
        if (! isset($this->tables['post'])) {
            return;
        }

        $post = $this->tables['post']['offset'];

        // italicAngle est un Fixed 16.16 ; seule la partie entière importe
        // pour le descripteur de police PDF.
        $this->italicAngle = $this->int16($post + 4);

        $isFixedPitch = $this->uint32($post + 12);

        if ($isFixedPitch !== 0) {
            $this->flags |= 1; // FixedPitch
        }
    }

    private function readName(): void
    {
        if (! isset($this->tables['name'])) {
            return;
        }

        $name = $this->tables['name']['offset'];
        $count = $this->uint16($name + 2);
        $stringOffset = $name + $this->uint16($name + 4);

        for ($i = 0; $i < $count; $i++) {
            $rec = $name + 6 + $i * 12;

            if ($rec + 12 > strlen($this->data)) {
                break;
            }

            // nameID 6 = nom PostScript
            if ($this->uint16($rec + 6) !== 6) {
                continue;
            }

            $platform = $this->uint16($rec);
            $length = $this->uint16($rec + 8);
            $offset = $stringOffset + $this->uint16($rec + 10);

            if ($length === 0 || $offset + $length > strlen($this->data)) {
                continue;
            }

            $raw = substr($this->data, $offset, $length);
            $value = $platform === 3
                ? (mb_convert_encoding($raw, 'UTF-8', 'UTF-16BE') ?: '')
                : $raw;

            $value = preg_replace('/[^A-Za-z0-9\-]/', '', $value) ?? '';

            if ($value !== '') {
                $this->postScriptName = $value;

                return;
            }
        }
    }

    /* =============================================================
     | Primitives binaires
     |============================================================= */

    private function toThousandths(int $value): int
    {
        return (int) round($value * 1000 / $this->unitsPerEm);
    }

    private function uint16(int $offset): int
    {
        if ($offset + 2 > strlen($this->data)) {
            return 0;
        }

        $unpacked = unpack('n', substr($this->data, $offset, 2));

        return $unpacked !== false && is_int($unpacked[1]) ? $unpacked[1] : 0;
    }

    private function int16(int $offset): int
    {
        $value = $this->uint16($offset);

        return $value >= 0x8000 ? $value - 0x10000 : $value;
    }

    private function uint32(int $offset): int
    {
        if ($offset + 4 > strlen($this->data)) {
            return 0;
        }

        $unpacked = unpack('N', substr($this->data, $offset, 4));

        return $unpacked !== false && is_int($unpacked[1]) ? $unpacked[1] : 0;
    }
}
