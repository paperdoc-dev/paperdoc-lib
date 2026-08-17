<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use Paperdoc\Support\Ole2\Ole2Reader;
use Paperdoc\Support\Ole2\Ole2Writer;

class Ole2WriterTest extends TestCase
{
    public function test_header_is_512_bytes_with_clsid(): void
    {
        $bin = (new Ole2Writer())->addStream('WordDocument', str_repeat('A', 100))->build();

        // [MS-CFB] 2.2: signature (8) + CLSID (16) then the version fields.
        $this->assertSame("\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1", substr($bin, 0, 8));
        $this->assertSame(str_repeat("\x00", 16), substr($bin, 8, 16));

        // Sector shift lives at offset 30 and must read back as 2^9 = 512.
        $this->assertSame(9, unpack('v', substr($bin, 30, 2))[1]);

        // First data sector starts right after the 512-byte header.
        $this->assertSame(0, strlen($bin) % 512);
    }

    public function test_small_streams_round_trip_through_mini_stream(): void
    {
        // Under the 4096-byte cutoff: must be stored in the mini-stream,
        // which is where any conformant reader looks for them.
        $short = str_repeat('S', 100);
        $other = str_repeat('T', 3000);

        $bin = (new Ole2Writer())
            ->addStream('WordDocument', $short)
            ->addStream('0Table', $other)
            ->build();

        $reader = new Ole2Reader($bin);

        $this->assertSame(['WordDocument', '0Table'], $reader->getStreamNames());
        $this->assertSame($short, $reader->getStream('WordDocument'));
        $this->assertSame($other, $reader->getStream('0Table'));
    }

    public function test_large_streams_round_trip_through_normal_sectors(): void
    {
        $big = str_repeat('B', 10_000);

        $reader = new Ole2Reader(
            (new Ole2Writer())->addStream('Workbook', $big)->build()
        );

        $this->assertSame($big, $reader->getStream('Workbook'));
    }

    public function test_mixed_sizes_round_trip(): void
    {
        $streams = [
            'Tiny'   => 'x',
            'Small'  => str_repeat('s', 4095),
            'Exact'  => str_repeat('e', 4096),
            'Large'  => str_repeat('l', 9000),
        ];

        $writer = new Ole2Writer();
        foreach ($streams as $name => $data) {
            $writer->addStream($name, $data);
        }

        $reader = new Ole2Reader($writer->build());

        foreach ($streams as $name => $data) {
            $this->assertSame($data, $reader->getStream($name), "flux {$name}");
        }
    }
}
