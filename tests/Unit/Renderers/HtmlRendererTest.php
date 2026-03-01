<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\RendererInterface;
use Paperdoc\Document\{Document, Image, PageBreak, Paragraph, Section, Table, TextRun};
use Paperdoc\Document\Style\{ParagraphStyle, TableStyle, TextStyle};
use Paperdoc\Enum\Alignment;
use Paperdoc\Renderers\HtmlRenderer;

class HtmlRendererTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/paperdoc_test_' . uniqid();
        mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->tmpDir . '/*'));
        @rmdir($this->tmpDir);
    }

    private function outputPath(string $name = 'test.html'): string
    {
        return $this->tmpDir . '/' . $name;
    }

    public function test_implements_renderer_interface(): void
    {
        $this->assertInstanceOf(RendererInterface::class, new HtmlRenderer());
    }

    public function test_format_is_html(): void
    {
        $this->assertSame('html', (new HtmlRenderer())->getFormat());
    }

    public function test_render_returns_valid_html(): void
    {
        $doc = Document::make('html', 'Test Document');
        $section = Section::make('main');
        $section->addText('Hello World');
        $doc->addSection($section);

        $renderer = new HtmlRenderer();
        $html = $renderer->render($doc);

        $this->assertStringContainsString('<!DOCTYPE html>', $html);
        $this->assertStringContainsString('<title>Test Document</title>', $html);
        $this->assertStringContainsString('Hello World', $html);
    }

    public function test_saves_valid_html_file(): void
    {
        $doc = Document::make('html', 'Test Document');
        $section = Section::make('main');
        $section->addText('Hello World');
        $doc->addSection($section);

        $renderer = new HtmlRenderer();
        $renderer->save($doc, $this->outputPath());

        $this->assertFileExists($this->outputPath());
        $html = file_get_contents($this->outputPath());
        $this->assertStringContainsString('<!DOCTYPE html>', $html);
    }

    public function test_renders_paragraph(): void
    {
        $doc = Document::make('html');
        $section = Section::make('s1');
        $section->addText('Paragraphe simple');
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);
        $this->assertStringContainsString('<p>Paragraphe simple</p>', $html);
    }

    public function test_renders_styled_text_run(): void
    {
        $doc = Document::make('html');
        $section = Section::make('s1');
        $style = TextStyle::make()->setBold()->setColor('#FF0000')->setFontSize(18.0);
        $section->addText('Bold Red', $style);
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);
        $this->assertStringContainsString('font-weight:bold', $html);
        $this->assertStringContainsString('color:#FF0000', $html);
        $this->assertStringContainsString('font-size:18pt', $html);
    }

    public function test_renders_italic_and_underline(): void
    {
        $doc = Document::make('html');
        $section = Section::make('s1');
        $style = TextStyle::make()->setItalic()->setUnderline();
        $section->addText('Styled text', $style);
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);
        $this->assertStringContainsString('font-style:italic', $html);
        $this->assertStringContainsString('text-decoration:underline', $html);
    }

    public function test_renders_paragraph_style(): void
    {
        $doc = Document::make('html');
        $section = Section::make('s1');
        $paraStyle = ParagraphStyle::make()->setAlignment(Alignment::CENTER)->setSpaceAfter(12.0);
        $p = Paragraph::make($paraStyle);
        $p->addRun(new TextRun('Centré'));
        $section->addElement($p);
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);
        $this->assertStringContainsString('text-align:center', $html);
        $this->assertStringContainsString('margin-bottom:12pt', $html);
    }

    public function test_renders_table_with_headers(): void
    {
        $doc = Document::make('html');
        $section = Section::make('data');
        $table = Table::make();
        $table->setHeaders(['Nom', 'Valeur']);
        $table->addRowFromArray(['CA', '120k']);
        $table->addRowFromArray(['Clients', '34']);
        $section->addElement($table);
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);
        $this->assertStringContainsString('<thead>', $html);
        $this->assertStringContainsString('<th>Nom</th>', $html);
        $this->assertStringContainsString('<th>Valeur</th>', $html);
        $this->assertStringContainsString('<td>CA</td>', $html);
        $this->assertStringContainsString('<td>120k</td>', $html);
        $this->assertStringContainsString('</tbody>', $html);
    }

    public function test_renders_table_without_headers(): void
    {
        $doc = Document::make('html');
        $section = Section::make('data');
        $table = Table::make();
        $table->addRowFromArray(['A', 'B']);
        $table->addRowFromArray(['C', 'D']);
        $section->addElement($table);
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);
        $this->assertStringNotContainsString('<thead>', $html);
        $this->assertStringContainsString('<td>A</td>', $html);
    }

    public function test_renders_image(): void
    {
        $doc = Document::make('html');
        $section = Section::make('s1');
        $section->addElement(new Image('/path/logo.png', 200, 100, 'Logo'));
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);
        $this->assertStringContainsString('src="/path/logo.png"', $html);
        $this->assertStringContainsString('alt="Logo"', $html);
        $this->assertStringContainsString('width="200"', $html);
        $this->assertStringContainsString('height="100"', $html);
    }

    public function test_renders_page_break(): void
    {
        $doc = Document::make('html');
        $section = Section::make('s1');
        $section->addText('Before');
        $section->addPageBreak();
        $section->addText('After');
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);
        $this->assertStringContainsString('page-break', $html);
    }

    public function test_renders_multiple_sections(): void
    {
        $doc = Document::make('html');
        $doc->addSection(Section::make('intro'));
        $doc->addSection(Section::make('body'));
        $doc->addSection(Section::make('conclusion'));

        $html = (new HtmlRenderer())->render($doc);
        $this->assertStringContainsString('id="intro"', $html);
        $this->assertStringContainsString('id="body"', $html);
        $this->assertStringContainsString('id="conclusion"', $html);
    }

    public function test_escapes_html_entities(): void
    {
        $doc = Document::make('html', 'Title <script>');
        $section = Section::make('s1');
        $section->addText('Text with <b>HTML</b> & "quotes"');
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);
        $this->assertStringContainsString('Title &lt;script&gt;', $html);
        $this->assertStringContainsString('&lt;b&gt;HTML&lt;/b&gt;', $html);
        $this->assertStringContainsString('&amp; &quot;quotes&quot;', $html);
    }

    public function test_default_style_applied_to_body(): void
    {
        $doc = Document::make('html');
        $doc->setDefaultTextStyle(TextStyle::make()->setFontFamily('Times')->setFontSize(14.0));
        $doc->addSection(Section::make('s1'));

        $html = (new HtmlRenderer())->render($doc);
        $this->assertStringContainsString('font-family:Times', $html);
        $this->assertStringContainsString('font-size:14pt', $html);
    }

    public function test_creates_directory_if_needed(): void
    {
        $doc = Document::make('html');
        $doc->addSection(Section::make('s1'));

        $path = $this->tmpDir . '/sub/dir/output.html';
        $renderer = new HtmlRenderer();
        $renderer->save($doc, $path);

        $this->assertFileExists($path);

        unlink($path);
        rmdir($this->tmpDir . '/sub/dir');
        rmdir($this->tmpDir . '/sub');
    }

    public function test_table_with_colspan(): void
    {
        $doc = Document::make('html');
        $section = Section::make('s1');
        $table = Table::make();

        $row = new \Paperdoc\Document\TableRow();
        $cell = new \Paperdoc\Document\TableCell();
        $cell->setColspan(2);
        $cell->addElement((new Paragraph())->addRun(new TextRun('Merged')));
        $row->addCell($cell);
        $table->addRow($row);

        $section->addElement($table);
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);
        $this->assertStringContainsString('colspan="2"', $html);
    }
}
