<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Parsers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\ParserInterface;
use Paperdoc\Document\{Image, ListBlock, Paragraph, Table};
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

    public function test_table_outside_section_is_kept(): void
    {
        $path = $this->writeHtml('
            <html><body>
                <table><tr><th>A</th></tr><tr><td>1</td></tr></table>
                <section id="s1"><p>Inside</p></section>
            </body></html>
        ');

        $doc = (new HtmlParser())->parse($path);
        $sections = $doc->getSections();

        $this->assertCount(2, $sections);
        $this->assertSame('main', $sections[0]->getName());
        $this->assertSame('s1', $sections[1]->getName());

        $tables = array_values(array_filter(
            $sections[0]->getElements(),
            fn ($e) => $e instanceof Table
        ));

        $this->assertCount(1, $tables);
        $this->assertCount(2, $tables[0]->getRows());
    }

    public function test_table_inside_unhandled_wrapper_is_kept(): void
    {
        foreach (['aside', 'blockquote', 'form', 'details'] as $wrapper) {
            $path = $this->writeHtml(
                "<html><body><{$wrapper}>"
                . '<table><tr><th>A</th></tr><tr><td>1</td></tr></table>'
                . "</{$wrapper}></body></html>"
            );

            $doc = (new HtmlParser())->parse($path);
            $tables = array_values(array_filter(
                $doc->getSections()[0]->getElements(),
                fn ($e) => $e instanceof Table
            ));

            $this->assertCount(1, $tables, "<{$wrapper}> swallowed its table");
            $this->assertCount(2, $tables[0]->getRows());
        }
    }

    /**
     * @return ListBlock[]
     */
    private function parseLists(string $body): array
    {
        $path = $this->writeHtml('<html><body>' . $body . '</body></html>');
        $doc = (new HtmlParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        return array_values(array_filter($elements, fn ($e) => $e instanceof ListBlock));
    }

    public function test_parse_bullet_list(): void
    {
        $lists = $this->parseLists('<ul><li>Premier</li><li>Deuxième</li></ul>');

        $this->assertCount(1, $lists);
        $this->assertTrue($lists[0]->isBullet());

        $items = $lists[0]->getItems();
        $this->assertCount(2, $items);
        $this->assertSame('Premier', $items[0]->getPlainText());
        $this->assertSame('Deuxième', $items[1]->getPlainText());
    }

    public function test_parse_ordered_list(): void
    {
        $lists = $this->parseLists('<ol><li>Étape 1</li><li>Étape 2</li></ol>');

        $this->assertCount(1, $lists);
        $this->assertTrue($lists[0]->isOrdered());
        $this->assertSame(1, $lists[0]->getStart());
        $this->assertCount(2, $lists[0]->getItems());
    }

    public function test_parse_ordered_list_honours_start_attribute(): void
    {
        $lists = $this->parseLists('<ol start="3"><li>Troisième</li></ol>');

        $this->assertSame(3, $lists[0]->getStart());
    }

    public function test_parse_ordered_list_ignores_invalid_start_attribute(): void
    {
        $lists = $this->parseLists('<ol start="abc"><li>Premier</li></ol>');

        $this->assertSame(1, $lists[0]->getStart());
    }

    public function test_parse_list_item_keeps_inline_styles(): void
    {
        $lists = $this->parseLists('<ul><li>Texte <strong>gras</strong></li></ul>');

        $runs = $lists[0]->getItems()[0]->getRuns();

        $this->assertCount(2, $runs);
        $this->assertSame('gras', $runs[1]->getText());
        $this->assertTrue($runs[1]->getStyle()->isBold());
    }

    public function test_parse_list_item_wrapped_in_paragraph(): void
    {
        // Shape emitted by rich-text editors such as TipTap / ProseMirror.
        $lists = $this->parseLists('<ul><li><p>Premier</p></li></ul>');

        $this->assertSame('Premier', $lists[0]->getItems()[0]->getPlainText());
    }

    public function test_parse_nested_list(): void
    {
        $lists = $this->parseLists('
            <ul>
                <li>Premier<ol><li>Imbriqué</li></ol></li>
                <li>Deuxième</li>
            </ul>
        ');

        $this->assertCount(1, $lists);

        $items = $lists[0]->getItems();
        $this->assertCount(2, $items);
        $this->assertTrue($items[0]->hasChildren());
        $this->assertFalse($items[1]->hasChildren());

        $nested = $items[0]->getBlocks()[0];
        $this->assertInstanceOf(ListBlock::class, $nested);
        $this->assertTrue($nested->isOrdered());
        $this->assertSame('Imbriqué', $nested->getItems()[0]->getPlainText());
    }

    public function test_parse_nested_list_declared_as_sibling(): void
    {
        // Word HTML exports nest by putting the child list after the <li>.
        $lists = $this->parseLists('<ul><li>Premier</li><ul><li>Imbriqué</li></ul></ul>');

        $items = $lists[0]->getItems();
        $this->assertCount(1, $items);
        $this->assertSame('Premier', $items[0]->getPlainText());

        $nested = $items[0]->getBlocks()[0];
        $this->assertInstanceOf(ListBlock::class, $nested);
        $this->assertSame('Imbriqué', $nested->getItems()[0]->getPlainText());
    }

    public function test_parse_list_inside_container(): void
    {
        $lists = $this->parseLists('<div><ul><li>Premier</li></ul></div>');

        $this->assertCount(1, $lists);
        $this->assertSame('Premier', $lists[0]->getItems()[0]->getPlainText());
    }

    public function test_parse_empty_list_is_skipped(): void
    {
        $this->assertSame([], $this->parseLists('<ul></ul>'));
    }

    /**
     * @return Paragraph[]
     */
    private function parseParagraphs(string $body): array
    {
        $path = $this->writeHtml('<html><body>' . $body . '</body></html>');
        $doc = (new HtmlParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        return array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
    }

    public function test_parse_link(): void
    {
        $paragraphs = $this->parseParagraphs('<p><a href="https://exemple.fr">Voir</a></p>');

        $run = $paragraphs[0]->getRuns()[0];

        $this->assertSame('Voir', $run->getText());
        $this->assertNotNull($run->getLink());
        $this->assertSame('https://exemple.fr', $run->getLink()->getUrl());
        $this->assertSame('https://exemple.fr', $run->getLink()->getHref());
        $this->assertTrue($run->getLink()->isExternal());
    }

    public function test_parse_link_with_title(): void
    {
        $paragraphs = $this->parseParagraphs('<p><a href="https://exemple.fr" title="Accueil">Voir</a></p>');

        $this->assertSame('Accueil', $paragraphs[0]->getRuns()[0]->getLink()->getTitle());
    }

    public function test_parse_anchor_link(): void
    {
        $paragraphs = $this->parseParagraphs('<p><a href="#intro">Introduction</a></p>');

        $link = $paragraphs[0]->getRuns()[0]->getLink();

        $this->assertSame('', $link->getUrl());
        $this->assertSame('intro', $link->getAnchor());
        $this->assertSame('#intro', $link->getHref());
        $this->assertFalse($link->isExternal());
    }

    public function test_parse_link_without_href_is_not_a_link(): void
    {
        $paragraphs = $this->parseParagraphs('<p><a name="ancre">Texte</a></p>');

        $this->assertNull($paragraphs[0]->getRuns()[0]->getLink());
    }

    public function test_parse_link_keeps_nested_inline_styles(): void
    {
        $paragraphs = $this->parseParagraphs('<p><a href="https://exemple.fr"><strong>Gras</strong></a></p>');

        $run = $paragraphs[0]->getRuns()[0];

        $this->assertSame('Gras', $run->getText());
        $this->assertTrue($run->getStyle()->isBold());
        $this->assertSame('https://exemple.fr', $run->getLink()->getUrl());
    }

    public function test_parse_link_inside_list_item(): void
    {
        $lists = $this->parseLists('<ul><li><a href="https://exemple.fr">Voir</a></li></ul>');

        $run = $lists[0]->getItems()[0]->getRuns()[0];

        $this->assertSame('Voir', $run->getText());
        $this->assertSame('https://exemple.fr', $run->getLink()->getUrl());
    }

    public function test_parse_link_inside_table_cell(): void
    {
        $path = $this->writeHtml('
            <html><body>
                <table><tr><td><a href="https://exemple.fr">Voir</a></td></tr></table>
            </body></html>
        ');

        $doc = (new HtmlParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();
        $tables = array_values(array_filter($elements, fn ($e) => $e instanceof Table));

        $cell = $tables[0]->getRows()[0]->getCells()[0];
        $run = $cell->getElements()[0]->getRuns()[0];

        $this->assertSame('https://exemple.fr', $run->getLink()->getUrl());
    }

    public function test_parse_text_without_link_has_no_link(): void
    {
        $paragraphs = $this->parseParagraphs('<p>Texte simple</p>');

        $this->assertNull($paragraphs[0]->getRuns()[0]->getLink());
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
