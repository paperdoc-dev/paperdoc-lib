<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Factory;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\ParserInterface;
use Paperdoc\Factory\ParserFactory;
use Paperdoc\Parsers\{CsvParser, DocParser, DocxParser, HtmlParser, MarkdownParser, PdfParser};

class ParserFactoryTest extends TestCase
{
    public function test_get_parser_for_html(): void
    {
        $parser = ParserFactory::getParser('document.html');

        $this->assertInstanceOf(HtmlParser::class, $parser);
    }

    public function test_get_parser_for_htm(): void
    {
        $parser = ParserFactory::getParser('page.htm');

        $this->assertInstanceOf(HtmlParser::class, $parser);
    }

    public function test_get_parser_for_csv(): void
    {
        $parser = ParserFactory::getParser('data.csv');

        $this->assertInstanceOf(CsvParser::class, $parser);
    }

    public function test_get_parser_for_tsv(): void
    {
        $parser = ParserFactory::getParser('data.tsv');

        $this->assertInstanceOf(CsvParser::class, $parser);
    }

    public function test_get_parser_for_docx(): void
    {
        $parser = ParserFactory::getParser('file.docx');

        $this->assertInstanceOf(DocxParser::class, $parser);
    }

    public function test_get_parser_for_doc(): void
    {
        $parser = ParserFactory::getParser('file.doc');

        $this->assertInstanceOf(DocParser::class, $parser);
    }

    public function test_get_parser_for_pdf(): void
    {
        $parser = ParserFactory::getParser('file.pdf');

        $this->assertInstanceOf(PdfParser::class, $parser);
    }

    public function test_get_parser_for_md(): void
    {
        $parser = ParserFactory::getParser('readme.md');

        $this->assertInstanceOf(MarkdownParser::class, $parser);
    }

    public function test_get_parser_for_markdown(): void
    {
        $parser = ParserFactory::getParser('readme.markdown');

        $this->assertInstanceOf(MarkdownParser::class, $parser);
    }

    public function test_get_parser_unsupported_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Aucun parser disponible');

        ParserFactory::getParser('file.xyz');
    }

    public function test_register_custom_parser(): void
    {
        $customParser = new class extends \Paperdoc\Parsers\AbstractParser implements ParserInterface {
            public function supports(string $extension): bool
            {
                return $extension === 'custom';
            }

            public function parse(string $filename): \Paperdoc\Contracts\DocumentInterface
            {
                return new \Paperdoc\Document\Document('custom');
            }
        };

        ParserFactory::registerParser($customParser);
        $parser = ParserFactory::getParser('file.custom');

        $this->assertSame($customParser, $parser);
    }
}
