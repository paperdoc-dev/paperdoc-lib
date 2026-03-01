<?php

declare(strict_types=1);

namespace Paperdoc\Support\Ole2;

/**
 * Lecteur natif du format OLE2 Compound Binary File (Microsoft Structured Storage).
 *
 * Implémente la lecture des fichiers composites binaires (.doc, .xls, .ppt)
 * conformément à la spécification [MS-CFB].
 *
 * Structure :
 *  - Header (512 octets) : magic, taille secteurs, FAT, directory
 *  - FAT : chaîne de secteurs pour chaque flux
 *  - Mini-FAT : chaîne pour les petits flux (< 4096 octets)
 *  - Directory : entrées nommées (Root, WordDocument, 1Table, etc.)
 */
class Ole2Reader
{
    private const MAGIC = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";

    private const ENDOFCHAIN = 0xFFFFFFFE;
    private const FREESECT   = 0xFFFFFFFF;

    private string $data;

    private int $sectorSize;
    private int $miniSectorSize;
    private int $miniStreamCutoff;

    /** @var int[] */
    private array $fat = [];

    /** @var int[] */
    private array $miniFat = [];

    /** @var Ole2DirEntry[] */
    private array $directory = [];

    private string $miniStream = '';

    /**
     * @throws \RuntimeException
     */
    public function __construct(string $fileContent)
    {
        $this->data = $fileContent;

        if (strlen($this->data) < 512) {
            throw new \RuntimeException('Fichier OLE2 trop court');
        }

        $magic = substr($this->data, 0, 8);

        if ($magic !== self::MAGIC) {
            throw new \RuntimeException('Signature OLE2 invalide');
        }

        $this->parseHeader();
        $this->buildFat();
        $this->readDirectory();
        $this->buildMiniFat();
        $this->loadMiniStream();
    }

    /**
     * @throws \RuntimeException
     */
    public static function fromFile(string $filename): self
    {
        if (! file_exists($filename) || ! is_readable($filename)) {
            throw new \RuntimeException("Fichier introuvable ou illisible : {$filename}");
        }

        return new self(file_get_contents($filename));
    }

    /**
     * Lit un flux (stream) par son nom.
     *
     * @throws \RuntimeException si le flux n'existe pas
     */
    public function getStream(string $name): string
    {
        $entry = $this->findEntry($name);

        if ($entry === null) {
            throw new \RuntimeException("Flux OLE2 introuvable : {$name}");
        }

        if ($entry->size < $this->miniStreamCutoff && $entry->size > 0) {
            return $this->readMiniStream($entry->startSector, $entry->size);
        }

        return $this->readStream($entry->startSector, $entry->size);
    }

    /**
     * Vérifie si un flux existe.
     */
    public function hasStream(string $name): bool
    {
        return $this->findEntry($name) !== null;
    }

    /**
     * @return string[] Noms de tous les flux disponibles
     */
    public function getStreamNames(): array
    {
        $names = [];

        foreach ($this->directory as $entry) {
            if ($entry->type === Ole2DirEntry::TYPE_STREAM && $entry->name !== '') {
                $names[] = $entry->name;
            }
        }

        return $names;
    }

    /* =============================================================
     | Header Parsing
     |============================================================= */

    private function parseHeader(): void
    {
        $sectorPow = $this->readUint16(30);
        $miniPow   = $this->readUint16(32);

        $this->sectorSize      = 1 << $sectorPow;
        $this->miniSectorSize  = 1 << $miniPow;
        $this->miniStreamCutoff = $this->readUint32(56);
    }

    /* =============================================================
     | FAT (File Allocation Table)
     |============================================================= */

    private function buildFat(): void
    {
        $this->fat = [];

        $numFatSectors    = $this->readUint32(44);
        $difatStart       = $this->readInt32(68);
        $numDifatSectors  = $this->readUint32(72);

        $difatEntries = [];
        for ($i = 0; $i < 109; $i++) {
            $sect = $this->readInt32(76 + $i * 4);
            if ($sect >= 0 && $sect < 0xFFFFFFFE) {
                $difatEntries[] = $sect;
            }
        }

        $current = $difatStart;
        while ($current >= 0 && $current < 0xFFFFFFFE && $numDifatSectors > 0) {
            $offset = $this->sectorOffset($current);
            $entriesPerSector = $this->sectorSize / 4 - 1;

            for ($i = 0; $i < $entriesPerSector; $i++) {
                $sect = $this->readInt32($offset + $i * 4);
                if ($sect >= 0 && $sect < 0xFFFFFFFE) {
                    $difatEntries[] = $sect;
                }
            }

            $current = $this->readInt32($offset + (int) $entriesPerSector * 4);
            $numDifatSectors--;
        }

        foreach ($difatEntries as $fatSector) {
            $offset = $this->sectorOffset($fatSector);
            $entries = $this->sectorSize / 4;

            for ($i = 0; $i < $entries; $i++) {
                $pos = $offset + $i * 4;
                if ($pos + 4 <= strlen($this->data)) {
                    $this->fat[] = $this->readUint32($pos);
                }
            }
        }
    }

    /* =============================================================
     | Directory
     |============================================================= */

    private function readDirectory(): void
    {
        $this->directory = [];
        $dirStart = $this->readInt32(48);

        $dirData = $this->readStream($dirStart);

        $entrySize = 128;
        $count = (int) floor(strlen($dirData) / $entrySize);

        for ($i = 0; $i < $count; $i++) {
            $raw = substr($dirData, $i * $entrySize, $entrySize);

            if (strlen($raw) < $entrySize) {
                break;
            }

            $entry = new Ole2DirEntry();

            $nameLen = unpack('v', substr($raw, 64, 2))[1];
            $nameLen = min($nameLen, 64);

            if ($nameLen > 2) {
                $nameRaw = substr($raw, 0, $nameLen - 2);
                $entry->name = mb_convert_encoding($nameRaw, 'UTF-8', 'UTF-16LE');
            }

            $entry->type        = ord($raw[66]);
            $entry->startSector = unpack('V', substr($raw, 116, 4))[1];
            $entry->size        = unpack('V', substr($raw, 120, 4))[1];

            $this->directory[] = $entry;
        }
    }

    /* =============================================================
     | Mini-FAT & Mini-Stream
     |============================================================= */

    private function buildMiniFat(): void
    {
        $this->miniFat = [];
        $miniFatStart = $this->readInt32(60);

        if ($miniFatStart < 0 || $miniFatStart >= 0xFFFFFFFE) {
            return;
        }

        $miniFatData = $this->readStream($miniFatStart);
        $entries = (int) floor(strlen($miniFatData) / 4);

        for ($i = 0; $i < $entries; $i++) {
            $this->miniFat[] = unpack('V', substr($miniFatData, $i * 4, 4))[1];
        }
    }

    private function loadMiniStream(): void
    {
        $this->miniStream = '';

        if (empty($this->directory)) {
            return;
        }

        $root = $this->directory[0];

        if ($root->startSector < 0xFFFFFFFE && $root->size > 0) {
            $this->miniStream = $this->readStream($root->startSector, $root->size);
        }
    }

    /* =============================================================
     | Stream Reading
     |============================================================= */

    private function readStream(int $startSector, int $maxSize = PHP_INT_MAX): string
    {
        $result = '';
        $current = $startSector;
        $remaining = $maxSize;

        while ($current >= 0 && $current < 0xFFFFFFFE && $remaining > 0) {
            $offset = $this->sectorOffset($current);
            $chunk = min($this->sectorSize, $remaining);

            if ($offset + $chunk > strlen($this->data)) {
                break;
            }

            $result .= substr($this->data, $offset, $chunk);
            $remaining -= $chunk;

            if (! isset($this->fat[$current])) {
                break;
            }

            $current = $this->fat[$current];
        }

        if (strlen($result) > $maxSize) {
            $result = substr($result, 0, $maxSize);
        }

        return $result;
    }

    private function readMiniStream(int $startMiniSector, int $size): string
    {
        $result = '';
        $current = $startMiniSector;
        $remaining = $size;

        while ($current >= 0 && $current < 0xFFFFFFFE && $remaining > 0) {
            $offset = $current * $this->miniSectorSize;
            $chunk = min($this->miniSectorSize, $remaining);

            if ($offset + $chunk > strlen($this->miniStream)) {
                break;
            }

            $result .= substr($this->miniStream, $offset, $chunk);
            $remaining -= $chunk;

            if (! isset($this->miniFat[$current])) {
                break;
            }

            $current = $this->miniFat[$current];
        }

        return $result;
    }

    /* =============================================================
     | Helpers
     |============================================================= */

    private function findEntry(string $name): ?Ole2DirEntry
    {
        $nameLower = strtolower($name);

        foreach ($this->directory as $entry) {
            if (strtolower($entry->name) === $nameLower) {
                return $entry;
            }
        }

        return null;
    }

    private function sectorOffset(int $sector): int
    {
        return 512 + $sector * $this->sectorSize;
    }

    private function readUint16(int $offset): int
    {
        return unpack('v', substr($this->data, $offset, 2))[1];
    }

    private function readUint32(int $offset): int
    {
        return unpack('V', substr($this->data, $offset, 4))[1];
    }

    private function readInt32(int $offset): int
    {
        $val = unpack('V', substr($this->data, $offset, 4))[1];

        if ($val >= 0x80000000) {
            return (int) ($val - 0x100000000);
        }

        return (int) $val;
    }
}
