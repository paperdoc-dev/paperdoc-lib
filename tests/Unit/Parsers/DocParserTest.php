<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Parsers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\ParserInterface;
use Paperdoc\Document\Paragraph;
use Paperdoc\Parsers\DocParser;

class DocParserTest extends TestCase
{
    private DocParser $parser;

    private string $tmpDir;

    protected function setUp(): void
    {
        $this->parser = new DocParser();
        $this->tmpDir = sys_get_temp_dir() . '/paperdoc_doc_tests_' . uniqid();
        mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        $files = glob($this->tmpDir . '/*');
        if ($files) {
            array_map('unlink', $files);
        }
        @rmdir($this->tmpDir);
    }

    public function test_implements_parser_interface(): void
    {
        $this->assertInstanceOf(ParserInterface::class, $this->parser);
    }

    public function test_supports_doc(): void
    {
        $this->assertTrue($this->parser->supports('doc'));
        $this->assertTrue($this->parser->supports('DOC'));
        $this->assertFalse($this->parser->supports('docx'));
        $this->assertFalse($this->parser->supports('pdf'));
        $this->assertFalse($this->parser->supports('html'));
    }

    public function test_nonexistent_file_throws(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->parser->parse('/nonexistent/file.doc');
    }

    public function test_invalid_file_throws(): void
    {
        $path = $this->tmpDir . '/invalid.doc';
        file_put_contents($path, 'not an OLE2 file');

        $this->expectException(\RuntimeException::class);
        $this->parser->parse($path);
    }

    public function test_parse_minimal_ole2_doc(): void
    {
        $path = $this->createMinimalDoc('Hello World from DOC format');

        $doc = $this->parser->parse($path);

        $this->assertSame('doc', $doc->getFormat());
        $this->assertGreaterThanOrEqual(1, count($doc->getSections()));
    }

    public function test_format_is_doc(): void
    {
        $path = $this->createMinimalDoc('Test');

        $doc = $this->parser->parse($path);

        $this->assertSame('doc', $doc->getFormat());
    }

    public function test_title_defaults_to_filename(): void
    {
        $path = $this->createMinimalDoc('Test');

        $doc = $this->parser->parse($path);

        $this->assertSame('test', $doc->getTitle());
    }

    public function test_parser_factory_returns_doc_parser(): void
    {
        $parser = \Paperdoc\Factory\ParserFactory::getParser('file.doc');

        $this->assertInstanceOf(DocParser::class, $parser);
    }

    /* =============================================================
     | Helper — creates a minimal OLE2 .doc file
     |
     | This creates a bare-bones but valid OLE2 compound document
     | with a WordDocument stream containing text in the fallback-
     | readable area.
     |============================================================= */

    private function createMinimalDoc(string $text): string
    {
        $path = $this->tmpDir . '/test.doc';

        $sectorSize = 512;

        $header = str_repeat("\x00", 512);

        $header[0] = "\xD0"; $header[1] = "\xCF"; $header[2] = "\x11"; $header[3] = "\xE0";
        $header[4] = "\xA1"; $header[5] = "\xB1"; $header[6] = "\x1A"; $header[7] = "\xE1";

        $header = substr_replace($header, pack('v', 0x003E), 24, 2);
        $header = substr_replace($header, pack('v', 0x0003), 26, 2);
        $header = substr_replace($header, pack('v', 0xFFFE), 28, 2);

        $header = substr_replace($header, pack('v', 9), 30, 2);
        $header = substr_replace($header, pack('v', 6), 32, 2);

        $header = substr_replace($header, pack('V', 0), 44, 4);
        $header = substr_replace($header, pack('V', 0x00001000), 56, 4);

        $header = substr_replace($header, pack('V', 0), 48, 4);

        $header = substr_replace($header, pack('V', 0xFFFFFFFE), 60, 4);
        $header = substr_replace($header, pack('V', 0), 64, 4);

        $header = substr_replace($header, pack('V', 0xFFFFFFFE), 68, 4);
        $header = substr_replace($header, pack('V', 0), 72, 4);

        $header = substr_replace($header, pack('V', 0), 76, 4);

        for ($i = 1; $i < 109; $i++) {
            $header = substr_replace($header, pack('V', 0xFFFFFFFF), 76 + $i * 4, 4);
        }

        $fat = str_repeat("\x00", $sectorSize);
        $fat = substr_replace($fat, pack('V', 0xFFFFFFFD), 0, 4);
        $fat = substr_replace($fat, pack('V', 0xFFFFFFFE), 4, 4);
        $fat = substr_replace($fat, pack('V', 0xFFFFFFFE), 8, 4);
        for ($i = 3; $i < 128; $i++) {
            $fat = substr_replace($fat, pack('V', 0xFFFFFFFF), $i * 4, 4);
        }

        $dir = str_repeat("\x00", $sectorSize);

        $rootEntry = str_repeat("\x00", 128);
        $rootName = mb_convert_encoding("Root Entry", 'UTF-16LE', 'UTF-8');
        $rootEntry = substr_replace($rootEntry, $rootName, 0, strlen($rootName));
        $rootEntry = substr_replace($rootEntry, pack('v', strlen($rootName) + 2), 64, 2);
        $rootEntry[66] = "\x05";
        $rootEntry = substr_replace($rootEntry, pack('V', 1), 68, 4);
        $rootEntry = substr_replace($rootEntry, pack('V', 0xFFFFFFFF), 72, 4);
        $rootEntry = substr_replace($rootEntry, pack('V', 0xFFFFFFFF), 76, 4);
        $rootEntry = substr_replace($rootEntry, pack('V', 0xFFFFFFFE), 116, 4);
        $rootEntry = substr_replace($rootEntry, pack('V', 0), 120, 4);

        $wordDocEntry = str_repeat("\x00", 128);
        $wordName = mb_convert_encoding("WordDocument", 'UTF-16LE', 'UTF-8');
        $wordDocEntry = substr_replace($wordDocEntry, $wordName, 0, strlen($wordName));
        $wordDocEntry = substr_replace($wordDocEntry, pack('v', strlen($wordName) + 2), 64, 2);
        $wordDocEntry[66] = "\x02";
        $wordDocEntry = substr_replace($wordDocEntry, pack('V', 0xFFFFFFFF), 68, 4);
        $wordDocEntry = substr_replace($wordDocEntry, pack('V', 0xFFFFFFFF), 72, 4);
        $wordDocEntry = substr_replace($wordDocEntry, pack('V', 0xFFFFFFFF), 76, 4);
        $wordDocEntry = substr_replace($wordDocEntry, pack('V', 2), 116, 4);
        $wordDocEntry = substr_replace($wordDocEntry, pack('V', $sectorSize), 120, 4);

        $dir = substr_replace($dir, $rootEntry, 0, 128);
        $dir = substr_replace($dir, $wordDocEntry, 128, 128);

        $wordDocStream = str_repeat("\x00", $sectorSize);

        $wordDocStream = substr_replace($wordDocStream, pack('v', 0xA5EC), 0, 2);
        $wordDocStream = substr_replace($wordDocStream, pack('v', 0x00C1), 2, 2);

        $textBytes = $text . "\r";
        $textOffset = 256;
        $wordDocStream = substr_replace($wordDocStream, $textBytes, $textOffset, strlen($textBytes));

        $fileContent = $header . $fat . $dir . $wordDocStream;

        file_put_contents($path, $fileContent);

        return $path;
    }
}
