<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\RendererInterface;
use Paperdoc\Document\{Document, HorizontalRule, Image, PageBreak, Paragraph, Section, Table, TextRun};
use Paperdoc\Document\Style\{ParagraphStyle, RunningElement, TableStyle, TextStyle};
use Paperdoc\Document\Link\{TextLink};
use Paperdoc\Enum\{Alignment, VerticalAlignment};
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
    public function test_renders_hyperlink(): void
    {
        $doc = Document::make('html');
        $section = Section::make('s1');
        $link = TextLink::make()->setUrl('https://example.com');
        $section->addText('Example Link', null, $link);
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);
        $this->assertStringContainsString('href="https://example.com"', $html);
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

    /* =============================================================
     | v0.8.0 — A3 / A4 / B3 / B4 / B5 regressions
     |============================================================= */

    public function test_section_hide_footer_suppresses_document_footer(): void
    {
        $doc = Document::make('html');
        $doc->setFooter(RunningElement::make('FOOTER-{page}'));

        $cover = Section::make('cover')->hideFooter();
        $cover->addText('Cover');
        $doc->addSection($cover);

        $body = Section::make('body');
        $body->addText('Body');
        $doc->addSection($body);

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringNotContainsString('FOOTER-1', $html);
        $this->assertStringContainsString('FOOTER-2', $html);
    }

    public function test_section_header_override_replaces_document_header(): void
    {
        $doc = Document::make('html');
        $doc->setHeader(RunningElement::make('DOC'));

        $section = Section::make('s')->setHeader(RunningElement::make('SECTION'));
        $section->addText('hi');
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringContainsString('SECTION', $html);
        $this->assertStringNotContainsString('DOC<', $html); // crude but works
    }

    public function test_vertical_alignment_center_emits_flex_centering(): void
    {
        $doc = Document::make('html');
        $section = Section::make('s')->setVerticalAlignment(VerticalAlignment::CENTER);
        $section->addText('centred');
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringContainsString('display:flex', $html);
        $this->assertStringContainsString('justify-content:center', $html);
        $this->assertStringContainsString('paperdoc-section-body', $html);
    }

    public function test_vertical_alignment_bottom_uses_flex_end(): void
    {
        $doc = Document::make('html');
        $section = Section::make('s')->setVerticalAlignment(VerticalAlignment::BOTTOM);
        $section->addText('bottom');
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringContainsString('justify-content:flex-end', $html);
    }

    public function test_first_line_indent_emits_text_indent_css(): void
    {
        $doc = Document::make('html');
        $section = Section::make('s');
        $section->addElement(
            (new Paragraph())
                ->setStyle(ParagraphStyle::make()->setFirstLineIndent(24.0))
                ->addRun(new TextRun('Indented body text.'))
        );
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringContainsString('text-indent:24.00pt', $html);
    }

    public function test_letter_spacing_emits_letter_spacing_css(): void
    {
        $doc = Document::make('html');
        $section = Section::make('s');
        $section->addElement(
            (new Paragraph())->addRun(new TextRun(
                'WIDE',
                TextStyle::make()->setLetterSpacing(2.5)
            ))
        );
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringContainsString('letter-spacing:2.50pt', $html);
    }

    public function test_horizontal_rule_renders_hr_with_inline_style(): void
    {
        $doc = Document::make('html');
        $section = Section::make('s');
        $section->addRule()->setWidth('60%')->setColor('#ff0000')->setThickness(2.0);
        $doc->addSection($section);

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringContainsString('<hr', $html);
        $this->assertStringContainsString('width:60%', $html);
        $this->assertStringContainsString('#ff0000', $html);
        $this->assertStringContainsString('border-top:2.00pt solid', $html);
    }
}
