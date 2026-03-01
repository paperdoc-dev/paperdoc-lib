<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Parsers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\ParserInterface;
use Paperdoc\Document\{Image, Paragraph, Table};
use Paperdoc\Parsers\HtmlParser;

class HtmlParserTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/paperdoc_html_parse_' . uniqid();
        mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->tmpDir . '/*'));
        @rmdir($this->tmpDir);
    }

    private function writeHtml(string $html, string $name = 'test.html'): string
    {
        $path = $this->tmpDir . '/' . $name;
        file_put_contents($path, $html);

        return $path;
    }

    public function test_implements_parser_interface(): void
    {
        $this->assertInstanceOf(ParserInterface::class, new HtmlParser());
    }

    public function test_supports_html(): void
    {
        $parser = new HtmlParser();

        $this->assertTrue($parser->supports('html'));
        $this->assertTrue($parser->supports('htm'));
        $this->assertTrue($parser->supports('HTML'));
        $this->assertFalse($parser->supports('pdf'));
        $this->assertFalse($parser->supports('csv'));
    }

    public function test_parse_title(): void
    {
        $path = $this->writeHtml('<html><head><title>Mon Titre</title></head><body></body></html>');
        $doc = (new HtmlParser())->parse($path);

        $this->assertSame('Mon Titre', $doc->getTitle());
    }

    public function test_parse_simple_paragraphs(): void
    {
        $path = $this->writeHtml('
            <html><body>
                <p>Premier paragraphe</p>
                <p>Deuxième paragraphe</p>
            </body></html>
        ');

        $doc = (new HtmlParser())->parse($path);

        $this->assertCount(1, $doc->getSections());
        $elements = $doc->getSections()[0]->getElements();

        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $this->assertGreaterThanOrEqual(2, count($paragraphs));
    }

    public function test_parse_headings(): void
    {
        $path = $this->writeHtml('
            <html><body>
                <h1>Heading 1</h1>
                <h2>Heading 2</h2>
                <h3>Heading 3</h3>
            </body></html>
        ');

        $doc = (new HtmlParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $this->assertGreaterThanOrEqual(3, count($paragraphs));

        $h1 = $paragraphs[0];
        $this->assertSame('Heading 1', $h1->getPlainText());
        $this->assertSame(24.0, $h1->getRuns()[0]->getStyle()->getFontSize());
        $this->assertTrue($h1->getRuns()[0]->getStyle()->isBold());
    }

    public function test_parse_bold_text(): void
    {
        $path = $this->writeHtml('<html><body><p><strong>Gras</strong></p></body></html>');

        $doc = (new HtmlParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $this->assertNotEmpty($paragraphs);

        $run = $paragraphs[0]->getRuns()[0];
        $this->assertSame('Gras', $run->getText());
        $this->assertTrue($run->getStyle()->isBold());
    }

    public function test_parse_italic_text(): void
    {
        $path = $this->writeHtml('<html><body><p><em>Italique</em></p></body></html>');

        $doc = (new HtmlParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();
        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));

        $run = $paragraphs[0]->getRuns()[0];
        $this->assertSame('Italique', $run->getText());
        $this->assertTrue($run->getStyle()->isItalic());
    }

    public function test_parse_table(): void
    {
        $path = $this->writeHtml('
            <html><body>
                <table>
                    <thead><tr><th>Nom</th><th>Age</th></tr></thead>
                    <tbody>
                        <tr><td>Alice</td><td>30</td></tr>
                        <tr><td>Bob</td><td>25</td></tr>
                    </tbody>
                </table>
            </body></html>
        ');

        $doc = (new HtmlParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $tables = array_values(array_filter($elements, fn ($e) => $e instanceof Table));
        $this->assertCount(1, $tables);

        $table = $tables[0];
        $rows = $table->getRows();
        $this->assertCount(3, $rows);
        $this->assertTrue($rows[0]->isHeader());
        $this->assertFalse($rows[1]->isHeader());
    }

    public function test_parse_table_cell_content(): void
    {
        $path = $this->writeHtml('
            <html><body>
                <table><tr><td>Cell Value</td></tr></table>
            </body></html>
        ');

        $doc = (new HtmlParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();
        $tables = array_values(array_filter($elements, fn ($e) => $e instanceof Table));

        $cell = $tables[0]->getRows()[0]->getCells()[0];
        $this->assertSame('Cell Value', $cell->getPlainText());
    }

    public function test_parse_image(): void
    {
        $path = $this->writeHtml('
            <html><body>
                <img src="/logo.png" alt="Logo" width="200" height="100">
            </body></html>
        ');

        $doc = (new HtmlParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $images = array_values(array_filter($elements, fn ($e) => $e instanceof Image));
        $this->assertCount(1, $images);
        $this->assertSame('/logo.png', $images[0]->getSrc());
        $this->assertSame('Logo', $images[0]->getAlt());
        $this->assertSame(200, $images[0]->getWidth());
        $this->assertSame(100, $images[0]->getHeight());
    }

    public function test_parse_sections(): void
    {
        $path = $this->writeHtml('
            <html><body>
                <section id="intro"><p>Introduction</p></section>
                <section id="body"><p>Body text</p></section>
            </body></html>
        ');

        $doc = (new HtmlParser())->parse($path);

        $this->assertCount(2, $doc->getSections());
        $this->assertSame('intro', $doc->getSections()[0]->getName());
        $this->assertSame('body', $doc->getSections()[1]->getName());
    }

    public function test_parse_styled_span(): void
    {
        $path = $this->writeHtml('
            <html><body>
                <p><span style="font-weight:bold;color:#FF0000;font-size:18pt">Styled</span></p>
            </body></html>
        ');

        $doc = (new HtmlParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();
        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $run = $paragraphs[0]->getRuns()[0];

        $this->assertTrue($run->getStyle()->isBold());
        $this->assertSame('#FF0000', $run->getStyle()->getColor());
        $this->assertSame(18.0, $run->getStyle()->getFontSize());
    }

    public function test_parse_nonexistent_file_throws(): void
    {
        $this->expectException(\RuntimeException::class);

        (new HtmlParser())->parse('/nonexistent/file.html');
    }

    public function test_format_is_html(): void
    {
        $path = $this->writeHtml('<html><body></body></html>');
        $doc = (new HtmlParser())->parse($path);

        $this->assertSame('html', $doc->getFormat());
    }

    public function test_parse_table_with_colspan(): void
    {
        $path = $this->writeHtml('
            <html><body>
                <table><tr><td colspan="3">Wide cell</td></tr></table>
            </body></html>
        ');

        $doc = (new HtmlParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();
        $tables = array_values(array_filter($elements, fn ($e) => $e instanceof Table));

        $cell = $tables[0]->getRows()[0]->getCells()[0];
        $this->assertSame(3, $cell->getColspan());
    }

    public function test_parse_figure_with_img(): void
    {
        $path = $this->writeHtml('
            <html><body>
                <figure><img src="/photo.jpg" alt="Photo" width="400"></figure>
            </body></html>
        ');

        $doc = (new HtmlParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();
        $images = array_values(array_filter($elements, fn ($e) => $e instanceof Image));

        $this->assertCount(1, $images);
        $this->assertSame('/photo.jpg', $images[0]->getSrc());
    }
}
