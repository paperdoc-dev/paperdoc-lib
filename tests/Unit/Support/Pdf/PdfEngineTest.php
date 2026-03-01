<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Support\Pdf;

use PHPUnit\Framework\TestCase;
use Paperdoc\Support\Pdf\PdfEngine;

class PdfEngineTest extends TestCase
{
    public function test_default_dimensions(): void
    {
        $engine = new PdfEngine();

        $expectedWidth = 595.28 - 40 - 40;
        $this->assertEqualsWithDelta($expectedWidth, $engine->getContentWidth(), 0.01);
    }

    public function test_custom_dimensions(): void
    {
        $engine = new PdfEngine(
            pageWidth: 612,
            pageHeight: 792,
            marginLeft: 50,
            marginRight: 50,
        );

        $this->assertEqualsWithDelta(512, $engine->getContentWidth(), 0.01);
    }

    public function test_initial_cursor_position(): void
    {
        $engine = new PdfEngine(
            pageHeight: 800,
            marginTop: 30,
        );

        $this->assertEqualsWithDelta(770, $engine->getCursorY(), 0.01);
    }

    public function test_move_cursor(): void
    {
        $engine = new PdfEngine(pageHeight: 800, marginTop: 30);
        $initialY = $engine->getCursorY();

        $engine->moveCursorY(-20);

        $this->assertEqualsWithDelta($initialY - 20, $engine->getCursorY(), 0.01);
    }

    public function test_needs_new_page(): void
    {
        $engine = new PdfEngine(
            pageHeight: 100,
            marginTop: 10,
            marginBottom: 10,
        );

        $this->assertFalse($engine->needsNewPage(50));

        $engine->moveCursorY(-70);
        $this->assertTrue($engine->needsNewPage(20));
    }

    public function test_new_page_resets_cursor(): void
    {
        $engine = new PdfEngine(pageHeight: 800, marginTop: 30);

        $engine->moveCursorY(-500);
        $engine->newPage();

        $this->assertEqualsWithDelta(770, $engine->getCursorY(), 0.01);
    }

    public function test_output_generates_valid_pdf(): void
    {
        $engine = new PdfEngine();
        $engine->setTitle('Test');
        $engine->writeText('Hello World', 'Helvetica', 12);

        $output = $engine->output();

        $this->assertStringStartsWith('%PDF-1.4', $output);
        $this->assertStringContainsString('%%EOF', $output);
        $this->assertStringContainsString('/Type /Catalog', $output);
        $this->assertStringContainsString('/Type /Pages', $output);
    }

    public function test_output_contains_text(): void
    {
        $engine = new PdfEngine();
        $engine->writeText('My Content', 'Helvetica', 12);

        $output = $engine->output();

        $this->assertStringContainsString('My Content', $output);
    }

    public function test_output_contains_fonts(): void
    {
        $engine = new PdfEngine();
        $engine->writeText('Normal', 'Helvetica', 12);
        $engine->writeText('Bold', 'Helvetica-Bold', 14);

        $output = $engine->output();

        $this->assertStringContainsString('/BaseFont /Helvetica', $output);
        $this->assertStringContainsString('/BaseFont /Helvetica-Bold', $output);
    }

    public function test_save_writes_file(): void
    {
        $path = sys_get_temp_dir() . '/paperdoc_engine_' . uniqid() . '.pdf';

        $engine = new PdfEngine();
        $engine->writeText('Save test', 'Helvetica', 12);
        $engine->save($path);

        $this->assertFileExists($path);
        $this->assertStringStartsWith('%PDF-1.4', file_get_contents($path));

        unlink($path);
    }

    public function test_wrap_text_single_word(): void
    {
        $engine = new PdfEngine();
        $lines = $engine->wrapText('Hello', 'Helvetica', 12, 1000);

        $this->assertCount(1, $lines);
        $this->assertSame('Hello', $lines[0]);
    }

    public function test_wrap_text_wraps_long_text(): void
    {
        $engine = new PdfEngine();
        $longText = str_repeat('Word ', 100);

        $lines = $engine->wrapText(trim($longText), 'Helvetica', 12, 200);

        $this->assertGreaterThan(1, count($lines));
    }

    public function test_wrap_text_empty(): void
    {
        $engine = new PdfEngine();
        $lines = $engine->wrapText('', 'Helvetica', 12, 500);

        $this->assertCount(1, $lines);
        $this->assertSame('', $lines[0]);
    }

    public function test_measure_text_width(): void
    {
        $engine = new PdfEngine();

        $width = $engine->measureTextWidth('Hello', 'Helvetica', 12);
        $this->assertGreaterThan(0, $width);

        $widerWidth = $engine->measureTextWidth('Hello World', 'Helvetica', 12);
        $this->assertGreaterThan($width, $widerWidth);
    }

    public function test_measure_text_width_scales_with_font_size(): void
    {
        $engine = new PdfEngine();

        $small = $engine->measureTextWidth('Test', 'Helvetica', 10);
        $large = $engine->measureTextWidth('Test', 'Helvetica', 20);

        $this->assertEqualsWithDelta($small * 2, $large, 0.01);
    }

    public function test_multiple_pages(): void
    {
        $engine = new PdfEngine();
        $engine->writeText('Page 1', 'Helvetica', 12);
        $engine->newPage();
        $engine->writeText('Page 2', 'Helvetica', 12);
        $engine->newPage();
        $engine->writeText('Page 3', 'Helvetica', 12);

        $output = $engine->output();

        $pageCount = substr_count($output, '/Type /Page ');
        $this->assertSame(3, $pageCount);
    }

    public function test_draw_rect_in_output(): void
    {
        $engine = new PdfEngine();
        $engine->drawRect(10, 10, 100, 50, '#FFFFFF', '#000000', 1.0);

        $output = $engine->output();

        $this->assertStringContainsString('re', $output);
    }

    public function test_draw_line_in_output(): void
    {
        $engine = new PdfEngine();
        $engine->drawLine(0, 0, 100, 100, 1.0);

        $output = $engine->output();

        $this->assertStringContainsString(' m ', $output);
        $this->assertStringContainsString(' l S', $output);
    }

    public function test_set_creator(): void
    {
        $engine = new PdfEngine();
        $engine->setCreator('MyApp');

        $output = $engine->output();
        $this->assertStringContainsString('MyApp', $output);
    }

    public function test_xref_table(): void
    {
        $engine = new PdfEngine();
        $engine->writeText('xref test', 'Helvetica', 12);

        $output = $engine->output();

        $this->assertStringContainsString('xref', $output);
        $this->assertStringContainsString('trailer', $output);
        $this->assertStringContainsString('startxref', $output);
    }
}
