<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\RendererInterface;
use Paperdoc\Document\{Document, Image, PageBreak, Paragraph, Section, Table, TextRun};
use Paperdoc\Document\Style\{ParagraphStyle, TableStyle, TextStyle};
use Paperdoc\Document\Link\{TextLink};
use Paperdoc\Enum\Alignment;
use Paperdoc\Renderers\MarkdownRenderer;

class MarkdownRendererTest extends TestCase
{
    private string $tmpDir;

    /**
     * @group Markdown
     */

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

    private function outputPath(string $name = 'test.md'): string
    {
        return $this->tmpDir . '/' . $name;
    }

    public function test_implements_renderer_interface(): void
    {
        $this->assertInstanceOf(RendererInterface::class, new MarkdownRenderer());
    }

    public function test_format_is_markdown(): void
    {
        $this->assertSame('md', (new MarkdownRenderer())->getFormat());
    }

    public function test_render_returns_valid_markdown(): void
    {
        $doc = Document::make('markdown', 'Test Document');
        $section = Section::make('main');
        $section->addText('Hello World');
        $doc->addSection($section);

        $renderer = new MarkdownRenderer();
        $markdown = $renderer->render($doc);

        //$this->assertStringContainsString('Test Document', $markdown);
        $this->assertStringContainsString('Hello World', $markdown);
    }

    public function test_saves_valid_markdown_file(): void
    {
        $doc = Document::make('markdown', 'Test Document');
        $section = Section::make('main');
        $section->addText('Hello World');
        $doc->addSection($section);

        $renderer = new MarkdownRenderer();
        $renderer->save($doc, $this->outputPath());

        $this->assertFileExists($this->outputPath());
        $markdown = file_get_contents($this->outputPath());
        $this->assertStringContainsString('Hello World', $markdown);
    }

    public function test_renders_paragraph(): void
    {
        $doc = Document::make('markdown');
        $section = Section::make('s1');
        $section->addText('Paragraphe simple');
        $doc->addSection($section);

        $markdown = (new MarkdownRenderer())->render($doc);
        $this->assertStringContainsString('Paragraphe simple', $markdown);
    }

    public function test_renders_styled_text_run(): void
    {
        $doc = Document::make('markdown');
        $section = Section::make('s1');
        $style = TextStyle::make()->setBold()->setColor('#FF0000')->setFontSize(18.0);
        $section->addText('Bold Red', $style);
        $doc->addSection($section);

        $markdown = (new MarkdownRenderer())->render($doc);
        $this->assertStringContainsString('**Bold Red**', $markdown);
    }

    public function test_renders_italic(): void
    {
        $doc = Document::make('markdown');
        $section = Section::make('s1');
        $style = TextStyle::make()->setItalic();
        $section->addText('Styled text', $style);
        $doc->addSection($section);

        $markdown = (new MarkdownRenderer())->render($doc);
        $this->assertStringContainsString('*Styled text*', $markdown);
    }
    public function test_renders_hyperlink(): void
    {
        $doc = Document::make('markdown');
        $section = Section::make('s1');
        $link = TextLink::make()->setUrl('https://example.com');
        $section->addText('Example Link', null, $link);
        $doc->addSection($section);

        $markdown = (new MarkdownRenderer())->render($doc);

         $this->assertStringContainsString('[Example Link](https://example.com)', $markdown);

        $this->assertStringContainsString('[Example Link](https://example.com)', $markdown);
    }

    public function test_renders_paragraph_style(): void
    {
        $doc = Document::make('markdown');
        $section = Section::make('s1');
        $paraStyle = ParagraphStyle::make()->setAlignment(Alignment::CENTER)->setSpaceAfter(12.0);
        $p = Paragraph::make($paraStyle);
        $p->addRun(new TextRun('Centré'));
        $section->addElement($p);
        $doc->addSection($section);

        $markdown = (new MarkdownRenderer())->render($doc);
        $this->assertStringContainsString('Centré', $markdown);
    }

    public function test_renders_table_with_headers(): void
    {
        $doc = Document::make('markdown');
        $section = Section::make('data');
        $table = Table::make();
        $table->setHeaders(['Nom', 'Valeur']);
        $table->addRowFromArray(['CA', '120k']);
        $table->addRowFromArray(['Clients', '34']);
        $section->addElement($table);
        $doc->addSection($section);

        $markdown = (new MarkdownRenderer())->render($doc);
        $this->assertStringContainsString('| Nom | Valeur |', $markdown);
        $this->assertStringContainsString('| CA | 120k |', $markdown);
        $this->assertStringContainsString('| Clients | 34 |', $markdown);
    }

    public function test_renders_table_without_headers(): void
    {
        $doc = Document::make('markdown');
        $section = Section::make('data');
        $table = Table::make();
        $table->addRowFromArray(['A', 'B']);
        $table->addRowFromArray(['C', 'D']);
        $section->addElement($table);
        $doc->addSection($section);

        $markdown = (new MarkdownRenderer())->render($doc);
        $this->assertStringNotContainsString('<thead>', $markdown);
        $this->assertStringContainsString('| A | B |', $markdown);
    }

    public function test_renders_image(): void
    {
        $doc = Document::make('markdown');
        $section = Section::make('s1');
        $section->addElement(new Image('/path/logo.png', 200, 100, 'Logo'));
        $doc->addSection($section);

        $markdown = (new MarkdownRenderer())->render($doc);
        $this->assertStringContainsString('![Logo](/path/logo.png)', $markdown);
    }

    public function test_renders_page_break(): void
    {
        $doc = Document::make('markdown');
        $section = Section::make('s1');
        $section->addText('Before');
        $section->addPageBreak();
        $section->addText('After');
        $doc->addSection($section);

        $markdown = (new MarkdownRenderer())->render($doc);
        $this->assertStringContainsString('---', $markdown);
    }


    public function test_creates_directory_if_needed(): void
    {
        $doc = Document::make('markdown');
        $doc->addSection(Section::make('s1'));

        $path = $this->tmpDir . '/sub/dir/output.md';
        $renderer = new MarkdownRenderer();
        $renderer->save($doc, $path);

        $this->assertFileExists($path);

        unlink($path);
        rmdir($this->tmpDir . '/sub/dir');
        rmdir($this->tmpDir . '/sub');
    }

}
