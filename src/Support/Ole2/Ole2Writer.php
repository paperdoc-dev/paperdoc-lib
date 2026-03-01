<?php

declare(strict_types=1);

namespace Paperdoc\Support\Ole2;

/**
 * OLE2 Compound Binary File writer (Microsoft Structured Storage).
 *
 * Produces valid [MS-CFB] files with 512-byte sectors.
 * Used by DocRenderer, XlsRenderer, and PptRenderer
 * to wrap format-specific binary streams.
 */
class Ole2Writer
{
    private const MAGIC = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";

    private const SECTOR_SIZE       = 512;
    private const ENTRIES_PER_SECTOR = 128; // 512 / 4
    private const DIR_ENTRY_SIZE     = 128;
    private const DIRS_PER_SECTOR    = 4;   // 512 / 128

    private const ENDOFCHAIN = 0xFFFFFFFE;
    private const FREESECT   = 0xFFFFFFFF;
    private const FATSECT    = 0xFFFFFFFD;

    /** @var array<int, array{name: string, data: string}> */
    private array $streams = [];

    public function addStream(string $name, string $data): self
    {
        $this->streams[] = ['name' => $name, 'data' => $data];

        return $this;
    }

    /**
     * Build the complete OLE2 binary file and return it as a string.
     */
    public function build(): string
    {
        $layout = $this->planLayout();

        $output = $this->buildHeader($layout);

        foreach ($this->streams as $stream) {
            $data = $stream['data'];
            $padded = (int) ceil(max(1, strlen($data)) / self::SECTOR_SIZE) * self::SECTOR_SIZE;
            $output .= str_pad($data, $padded, "\x00");
        }

        $output .= str_pad(
            $this->buildDirectoryData($layout),
            $layout['numDirSectors'] * self::SECTOR_SIZE,
            "\x00",
        );

        $fatBin = '';
        foreach ($layout['fat'] as $entry) {
            $fatBin .= pack('V', $entry);
        }
        $output .= str_pad($fatBin, $layout['numFatSectors'] * self::SECTOR_SIZE, "\xFF");

        return $output;
    }

    /* =============================================================
     | Layout Planning
     |============================================================= */

    /**
     * @return array{streamStarts: int[], dirStart: int, fatStart: int,
     *               numDirSectors: int, numFatSectors: int, fat: int[]}
     */
    private function planLayout(): array
    {
        $sectorIdx = 0;
        $streamStarts = [];

        foreach ($this->streams as $stream) {
            $len = strlen($stream['data']);
            $numSectors = max(1, (int) ceil($len / self::SECTOR_SIZE));
            $streamStarts[] = $sectorIdx;
            $sectorIdx += $numSectors;
        }

        $numDirEntries = 1 + count($this->streams); // root + streams
        $numDirSectors = max(1, (int) ceil($numDirEntries / self::DIRS_PER_SECTOR));
        $dirStart = $sectorIdx;
        $sectorIdx += $numDirSectors;

        $numFatSectors = 1;
        while (($sectorIdx + $numFatSectors) > $numFatSectors * self::ENTRIES_PER_SECTOR) {
            $numFatSectors++;
        }
        $fatStart = $sectorIdx;
        $totalSectors = $sectorIdx + $numFatSectors;

        $fat = array_fill(0, $totalSectors, self::FREESECT);

        foreach ($this->streams as $i => $stream) {
            $len = strlen($stream['data']);
            $numSectors = max(1, (int) ceil($len / self::SECTOR_SIZE));

            for ($s = 0; $s < $numSectors; $s++) {
                $sector = $streamStarts[$i] + $s;
                $fat[$sector] = ($s < $numSectors - 1) ? $sector + 1 : self::ENDOFCHAIN;
            }
        }

        for ($s = 0; $s < $numDirSectors; $s++) {
            $fat[$dirStart + $s] = ($s < $numDirSectors - 1) ? $dirStart + $s + 1 : self::ENDOFCHAIN;
        }

        for ($s = 0; $s < $numFatSectors; $s++) {
            $fat[$fatStart + $s] = self::FATSECT;
        }

        return [
            'streamStarts'  => $streamStarts,
            'dirStart'      => $dirStart,
            'fatStart'      => $fatStart,
            'numDirSectors' => $numDirSectors,
            'numFatSectors' => $numFatSectors,
            'fat'           => $fat,
        ];
    }

    /* =============================================================
     | Header (512 bytes)
     |============================================================= */

    private function buildHeader(array $layout): string
    {
        $h  = self::MAGIC;
        $h .= pack('v', 0x003E);                   // minor version
        $h .= pack('v', 0x0003);                   // major version 3
        $h .= pack('v', 0xFFFE);                   // byte order (little-endian)
        $h .= pack('v', 9);                         // sector size power (2^9 = 512)
        $h .= pack('v', 6);                         // mini-sector size power (2^6 = 64)
        $h .= str_repeat("\x00", 6);                // reserved
        $h .= pack('V', 0);                         // total dir sectors (must be 0 for v3)
        $h .= pack('V', $layout['numFatSectors']);  // total FAT sectors
        $h .= pack('V', $layout['dirStart']);        // first directory sector SID
        $h .= pack('V', 0);                         // transaction signature
        $h .= pack('V', 0x00001000);                // mini-stream cutoff (4096)
        $h .= pack('V', self::ENDOFCHAIN);           // first mini-FAT sector (none)
        $h .= pack('V', 0);                         // num mini-FAT sectors
        $h .= pack('V', self::ENDOFCHAIN);           // first DIFAT sector (none)
        $h .= pack('V', 0);                         // num DIFAT sectors

        for ($i = 0; $i < 109; $i++) {
            $h .= pack('V', ($i < $layout['numFatSectors'])
                ? $layout['fatStart'] + $i
                : self::FREESECT);
        }

        return $h;
    }

    /* =============================================================
     | Directory
     |============================================================= */

    private function buildDirectoryData(array $layout): string
    {
        $data = '';

        // Root Entry
        $data .= $this->packDirEntry('Root Entry', 5, self::ENDOFCHAIN, 0, count($this->streams) > 0 ? 1 : self::FREESECT);

        foreach ($this->streams as $i => $stream) {
            $nextSibling = ($i + 1 < count($this->streams))
                ? $i + 2   // directory indices are 1-based for children
                : self::FREESECT;

            $data .= $this->packDirEntry(
                $stream['name'],
                2, // TYPE_STREAM
                $layout['streamStarts'][$i],
                strlen($stream['data']),
                self::FREESECT,
                self::FREESECT,
                $nextSibling,
            );
        }

        return $data;
    }

    /**
     * Pack a single 128-byte directory entry.
     */
    private function packDirEntry(
        string $name,
        int $type,
        int $startSector,
        int $size,
        int $child = self::FREESECT,
        int $leftSibling = self::FREESECT,
        int $rightSibling = self::FREESECT,
    ): string {
        $nameUtf16 = mb_convert_encoding($name, 'UTF-16LE', 'UTF-8') . "\x00\x00";
        $nameSize  = min(strlen($nameUtf16), 64);

        $d  = str_pad(substr($nameUtf16, 0, 64), 64, "\x00");
        $d .= pack('v', $nameSize);
        $d .= pack('C', $type);
        $d .= pack('C', 1);                    // color: black
        $d .= pack('V', $leftSibling);
        $d .= pack('V', $rightSibling);
        $d .= pack('V', $child);
        $d .= str_repeat("\x00", 16);          // CLSID
        $d .= pack('V', 0);                    // state bits
        $d .= str_repeat("\x00", 16);          // timestamps
        $d .= pack('V', $startSector & 0xFFFFFFFF);
        $d .= pack('V', $size);
        $d .= pack('V', 0);                    // padding

        return $d;
    }
}
