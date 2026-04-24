<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Paperdoc\Exceptions\InvalidDocumentException;
use Paperdoc\Exceptions\PaperdocException;
use Paperdoc\Exceptions\ParserException;
use Paperdoc\Exceptions\RendererException;
use Paperdoc\Exceptions\UnsupportedFormatException;

class ExceptionsTest extends TestCase
{
    public function test_all_extend_paperdoc_exception(): void
    {
        $this->assertInstanceOf(PaperdocException::class, new ParserException());
        $this->assertInstanceOf(PaperdocException::class, new RendererException());
        $this->assertInstanceOf(PaperdocException::class, new UnsupportedFormatException());
        $this->assertInstanceOf(PaperdocException::class, new InvalidDocumentException());
    }

    public function test_paperdoc_exception_extends_runtime_exception(): void
    {
        $this->assertInstanceOf(\RuntimeException::class, new PaperdocException());
    }

    public function test_parser_for_file_builds_message(): void
    {
        $e = ParserException::forFile('/tmp/doc.docx', 'missing word/document.xml');

        $this->assertStringContainsString('/tmp/doc.docx', $e->getMessage());
        $this->assertStringContainsString('missing word/document.xml', $e->getMessage());
    }

    public function test_parser_for_file_chains_previous(): void
    {
        $prev = new \RuntimeException('boom');
        $e    = ParserException::forFile('a.pdf', 'corrupt', $prev);

        $this->assertSame($prev, $e->getPrevious());
    }

    public function test_renderer_for_format_builds_message(): void
    {
        $e = RendererException::forFormat('pdf', 'write failed');

        $this->assertStringContainsString('pdf', $e->getMessage());
        $this->assertStringContainsString('write failed', $e->getMessage());
    }

    public function test_unsupported_format_factory(): void
    {
        $e = UnsupportedFormatException::forFormat('xyz');

        $this->assertStringContainsString('xyz', $e->getMessage());
    }

    public function test_unsupported_format_from_extension(): void
    {
        $e = UnsupportedFormatException::forExtension('/tmp/a.unknown');

        $this->assertStringContainsString('unknown', $e->getMessage());
        $this->assertStringContainsString('/tmp/a.unknown', $e->getMessage());
    }
}
