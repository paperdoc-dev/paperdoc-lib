<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use Paperdoc\Support\Ole2\Ole2Reader;

class Ole2ReaderTest extends TestCase
{
    /**
     * A FAT entry pointing back into its own chain used to make readStream()
     * concatenate for ever — the directory and mini-FAT are read with
     * $maxSize = PHP_INT_MAX, so PHP died on a fatal memory error that no
     * try/catch can intercept.
     */
    public function test_cyclic_fat_chain_does_not_exhaust_memory(): void
    {
        $file = $this->buildFileWithCyclicFat();

        $this->assertLessThan(2048, strlen($file), 'le fichier piège doit rester minuscule');

        $before = memory_get_usage();
        $reader = new Ole2Reader($file);
        $consumed = memory_get_usage() - $before;

        $this->assertSame([], $reader->getStreamNames());
        $this->assertLessThan(1_000_000, $consumed, 'la chaîne cyclique a fait gonfler la mémoire');
    }

    /**
     * Header conforme, mais fat[1] = 1 : le secteur du directory pointe
     * sur lui-même.
     */
    private function buildFileWithCyclicFat(): string
    {
        $h  = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1" . str_repeat("\x00", 16);
        $h .= pack('v', 0x3E) . pack('v', 3) . pack('v', 0xFFFE);
        $h .= pack('v', 9) . pack('v', 6) . str_repeat("\x00", 6);
        $h .= pack('V', 0);           // number of directory sectors
        $h .= pack('V', 1);           // number of FAT sectors
        $h .= pack('V', 1);           // first directory sector
        $h .= pack('V', 0);           // transaction signature
        $h .= pack('V', 4096);        // mini stream cutoff
        $h .= pack('V', 0xFFFFFFFE);  // first mini-FAT sector
        $h .= pack('V', 0);           // number of mini-FAT sectors
        $h .= pack('V', 0xFFFFFFFE);  // first DIFAT sector
        $h .= pack('V', 0);           // number of DIFAT sectors
        $h .= pack('V', 0);           // DIFAT[0] — sector 0 holds the FAT

        for ($i = 1; $i < 109; $i++) {
            $h .= pack('V', 0xFFFFFFFF);
        }

        $fatSector = pack('V', 0xFFFFFFFD)              // sector 0 = FAT itself
            . pack('V', 1)                              // fat[1] = 1 → cycle
            . str_repeat(pack('V', 0xFFFFFFFF), 126);

        return $h . $fatSector . str_repeat("\x00", 512);
    }
}
