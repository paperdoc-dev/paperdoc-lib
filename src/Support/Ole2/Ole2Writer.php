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

    private const SECTOR_SIZE        = 512;
    private const ENTRIES_PER_SECTOR = 128; // 512 / 4
    private const DIRS_PER_SECTOR    = 4;   // 512 / 128

    private const MINI_SECTOR_SIZE    = 64;
    private const MINI_STREAM_CUTOFF  = 4096;

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

        // Sectors are emitted in index order — sector N must sit at
        // 512 + N * 512 — so this order mirrors planLayout()'s allocation.
        $output = $this->buildHeader($layout);

        foreach ($this->streams as $i => $stream) {
            if ($layout['isMini'][$i] || $stream['data'] === '') {
                continue;
            }

            $output .= $this->padToSector($stream['data']);
        }

        if ($layout['miniStream'] !== '') {
            $output .= $this->padToSector($layout['miniStream']);
        }

        if ($layout['miniFatBin'] !== '') {
            $output .= $this->padToSector($layout['miniFatBin'], "\xFF");
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

    private function padToSector(string $data, string $pad = "\x00"): string
    {
        $len = (int) ceil(strlen($data) / self::SECTOR_SIZE) * self::SECTOR_SIZE;

        return str_pad($data, $len, $pad);
    }

    /* =============================================================
     | Layout Planning
     |============================================================= */

    /**
     * Streams shorter than 4096 bytes must live in the mini-stream, chained
     * through the mini-FAT — [MS-CFB] 2.6.1. A conformant reader routes any
     * such stream through the mini-stream, so storing it in a normal sector
     * makes it read back as empty.
     *
     * @return array{streamStarts: int[], isMini: bool[], miniStream: string,
     *               miniFatBin: string, miniStreamStart: int, miniFatStart: int,
     *               numMiniFatSectors: int, dirStart: int, fatStart: int,
     *               numDirSectors: int, numFatSectors: int, fat: int[]}
     */
    private function planLayout(): array
    {
        $streamStarts = [];
        $isMini       = [];
        $miniStream   = '';
        $miniFat      = [];

        // 1. Small streams: pack into the mini-stream, chain in the mini-FAT.
        foreach ($this->streams as $i => $stream) {
            $len = strlen($stream['data']);
            $isMini[$i] = $len > 0 && $len < self::MINI_STREAM_CUTOFF;

            if (! $isMini[$i]) {
                continue;
            }

            $numMini = (int) ceil($len / self::MINI_SECTOR_SIZE);
            $first   = intdiv(strlen($miniStream), self::MINI_SECTOR_SIZE);

            $streamStarts[$i] = $first;

            for ($s = 0; $s < $numMini; $s++) {
                $miniFat[] = ($s < $numMini - 1) ? $first + $s + 1 : self::ENDOFCHAIN;
            }

            $miniStream .= str_pad($stream['data'], $numMini * self::MINI_SECTOR_SIZE, "\x00");
        }

        // 2. Normal sectors, in the order build() emits them.
        $sectorIdx = 0;

        foreach ($this->streams as $i => $stream) {
            if ($isMini[$i]) {
                continue;
            }

            if ($stream['data'] === '') {
                $streamStarts[$i] = self::ENDOFCHAIN;

                continue;
            }

            $streamStarts[$i] = $sectorIdx;
            $sectorIdx += (int) ceil(strlen($stream['data']) / self::SECTOR_SIZE);
        }

        $miniStreamStart      = self::ENDOFCHAIN;
        $numMiniStreamSectors = 0;

        if ($miniStream !== '') {
            $numMiniStreamSectors = (int) ceil(strlen($miniStream) / self::SECTOR_SIZE);
            $miniStreamStart      = $sectorIdx;
            $sectorIdx += $numMiniStreamSectors;
        }

        $miniFatBin        = '';
        $miniFatStart      = self::ENDOFCHAIN;
        $numMiniFatSectors = 0;

        if ($miniFat !== []) {
            foreach ($miniFat as $entry) {
                $miniFatBin .= pack('V', $entry);
            }

            $numMiniFatSectors = (int) ceil(strlen($miniFatBin) / self::SECTOR_SIZE);
            $miniFatStart      = $sectorIdx;
            $sectorIdx += $numMiniFatSectors;
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

        // 3. FAT chains for everything held in normal sectors.
        $fat = array_fill(0, $totalSectors, self::FREESECT);

        $chain = static function (int $start, int $count) use (&$fat): void {
            for ($s = 0; $s < $count; $s++) {
                $fat[$start + $s] = ($s < $count - 1) ? $start + $s + 1 : self::ENDOFCHAIN;
            }
        };

        foreach ($this->streams as $i => $stream) {
            if ($isMini[$i] || $stream['data'] === '') {
                continue;
            }

            $chain($streamStarts[$i], (int) ceil(strlen($stream['data']) / self::SECTOR_SIZE));
        }

        if ($numMiniStreamSectors > 0) {
            $chain($miniStreamStart, $numMiniStreamSectors);
        }

        if ($numMiniFatSectors > 0) {
            $chain($miniFatStart, $numMiniFatSectors);
        }

        $chain($dirStart, $numDirSectors);

        for ($s = 0; $s < $numFatSectors; $s++) {
            $fat[$fatStart + $s] = self::FATSECT;
        }

        return [
            'streamStarts'      => $streamStarts,
            'isMini'            => $isMini,
            'miniStream'        => $miniStream,
            'miniFatBin'        => $miniFatBin,
            'miniStreamStart'   => $miniStreamStart,
            'miniFatStart'      => $miniFatStart,
            'numMiniFatSectors' => $numMiniFatSectors,
            'dirStart'          => $dirStart,
            'fatStart'          => $fatStart,
            'numDirSectors'     => $numDirSectors,
            'numFatSectors'     => $numFatSectors,
            'fat'               => $fat,
        ];
    }

    /* =============================================================
     | Header (512 bytes)
     |============================================================= */

    /**
     * @param array{streamStarts: int[], isMini: bool[], miniStream: string,
     *               miniFatBin: string, miniStreamStart: int, miniFatStart: int,
     *               numMiniFatSectors: int, dirStart: int, fatStart: int,
     *               numDirSectors: int, numFatSectors: int, fat: int[]} $layout
     */
    private function buildHeader(array $layout): string
    {
        $h  = self::MAGIC;
        $h .= str_repeat("\x00", 16);               // CLSID — [MS-CFB] 2.2, offset 8
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
        $h .= pack('V', self::MINI_STREAM_CUTOFF);  // mini-stream cutoff (4096)
        $h .= pack('V', $layout['miniFatStart']);    // first mini-FAT sector
        $h .= pack('V', $layout['numMiniFatSectors']);
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

    /**
     * @param array{streamStarts: int[], isMini: bool[], miniStream: string,
     *               miniFatBin: string, miniStreamStart: int, miniFatStart: int,
     *               numMiniFatSectors: int, dirStart: int, fatStart: int,
     *               numDirSectors: int, numFatSectors: int, fat: int[]} $layout
     */
    private function buildDirectoryData(array $layout): string
    {
        $data = '';

        // Root Entry — owns the mini-stream: its sector chain and byte length
        // are how a reader locates the storage backing every small stream.
        $data .= $this->packDirEntry(
            'Root Entry',
            5,
            $layout['miniStreamStart'],
            strlen($layout['miniStream']),
            count($this->streams) > 0 ? 1 : self::FREESECT,
        );

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
