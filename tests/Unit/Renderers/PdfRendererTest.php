<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\RendererInterface;
use Paperdoc\Document\{Document, Paragraph, Section, Table, TextRun};
use Paperdoc\Document\Style\{ParagraphStyle, TextStyle};
use Paperdoc\Enum\Alignment;
use Paperdoc\Renderers\PdfRenderer;

class PdfRendererTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/paperdoc_pdf_' . uniqid();
        mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->tmpDir . '/*'));
        @rmdir($this->tmpDir);
    }

    private function outputPath(string $name = 'test.pdf'): string
    {
        return $this->tmpDir . '/' . $name;
    }

    public function test_implements_renderer_interface(): void
    {
        $this->assertInstanceOf(RendererInterface::class, new PdfRenderer());
    }

    public function test_format_is_pdf(): void
    {
        $this->assertSame('pdf', (new PdfRenderer())->getFormat());
    }

    public function test_render_returns_pdf_string(): void
    {
        $doc = Document::make('pdf', 'Test PDF');
        $section = Section::make('s1');
        $section->addText('Hello PDF World');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);

        $this->assertStringStartsWith('%PDF-1.4', $content);
        $this->assertStringContainsString('%%EOF', $content);
    }

    public function test_generates_valid_pdf(): void
    {
        $doc = Document::make('pdf', 'Test PDF');
        $section = Section::make('s1');
        $section->addText('Hello PDF World');
        $doc->addSection($section);

        $renderer = new PdfRenderer();
        $renderer->save($doc, $this->outputPath());

        $this->assertFileExists($this->outputPath());

        $content = file_get_contents($this->outputPath());
        $this->assertStringStartsWith('%PDF-1.4', $content);
        $this->assertStringContainsString('%%EOF', $content);
    }

    public function test_pdf_contains_title(): void
    {
        $doc = Document::make('pdf', 'Mon Rapport');
        $section = Section::make('s1');
        $section->addText('Content');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);
        $this->assertStringContainsString('Mon Rapport', $content);
    }

    public function test_pdf_contains_text(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $section->addText('Le texte du document');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);
        $this->assertStringContainsString('Le texte du document', $content);
    }

    public function test_pdf_has_catalog_and_pages(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $section->addText('Test');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);
        $this->assertStringContainsString('/Type /Catalog', $content);
        $this->assertStringContainsString('/Type /Pages', $content);
        $this->assertStringContainsString('/Type /Page', $content);
    }

    public function test_pdf_has_font_resources(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $section->addText('Font test');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);
        $this->assertStringContainsString('/Type /Font', $content);
        $this->assertStringContainsString('/BaseFont /Helvetica', $content);
    }

    public function test_pdf_with_bold_text(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $section->addText('Bold text', TextStyle::make()->setBold());
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);
        $this->assertStringContainsString('/BaseFont /Helvetica-Bold', $content);
    }

    public function test_pdf_with_table(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $table = Table::make();
        $table->setHeaders(['Col1', 'Col2']);
        $table->addRowFromArray(['A', 'B']);
        $section->addElement($table);
        $doc->addSection($section);

        $renderer = new PdfRenderer();
        $renderer->save($doc, $this->outputPath());

        $this->assertFileExists($this->outputPath());
        $this->assertGreaterThan(100, filesize($this->outputPath()));
    }

    public function test_pdf_multiple_sections_create_pages(): void
    {
        $doc = Document::make('pdf');
        $doc->addSection(Section::make('page1'));
        $doc->addSection(Section::make('page2'));
        $doc->addSection(Section::make('page3'));

        foreach ($doc->getSections() as $section) {
            $section->addText('Content on this page');
        }

        $content = (new PdfRenderer())->render($doc);
        $this->assertSame(3, substr_count($content, '/Type /Page '));
    }

    public function test_pdf_has_xref_and_trailer(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $section->addText('Test');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);
        $this->assertStringContainsString('xref', $content);
        $this->assertStringContainsString('trailer', $content);
        $this->assertStringContainsString('startxref', $content);
        $this->assertStringContainsString('/Root', $content);
    }

    public function test_pdf_with_paragraph_style(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $paraStyle = ParagraphStyle::make()
            ->setAlignment(Alignment::JUSTIFY)
            ->setSpaceBefore(10.0)
            ->setSpaceAfter(20.0);
        $p = Paragraph::make($paraStyle);
        $p->addRun(new TextRun('Styled paragraph'));
        $section->addElement($p);
        $doc->addSection($section);

        $renderer = new PdfRenderer();
        $renderer->save($doc, $this->outputPath());

        $this->assertFileExists($this->outputPath());
        $this->assertGreaterThan(100, filesize($this->outputPath()));
    }

    public function test_pdf_with_multiple_text_runs(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $p = new Paragraph();
        $p->addRun(new TextRun('Normal '));
        $p->addRun(new TextRun('Bold ', TextStyle::make()->setBold()));
        $p->addRun(new TextRun('Italic', TextStyle::make()->setItalic()));
        $section->addElement($p);
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);
        $this->assertStringContainsString('Normal', $content);
        $this->assertStringContainsString('Bold', $content);
        $this->assertStringContainsString('Italic', $content);
    }

    public function test_creates_directory_if_needed(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $section->addText('Deep directory');
        $doc->addSection($section);

        $path = $this->tmpDir . '/deep/nested/dir/output.pdf';
        $renderer = new PdfRenderer();
        $renderer->save($doc, $path);

        $this->assertFileExists($path);

        unlink($path);
        rmdir($this->tmpDir . '/deep/nested/dir');
        rmdir($this->tmpDir . '/deep/nested');
        rmdir($this->tmpDir . '/deep');
    }
}
