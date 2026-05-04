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

    public function test_draw_image_gif_is_embedded_as_dct(): void
    {
        if (! function_exists('imagecreate') || ! function_exists('imagegif')
            || ! function_exists('imagecolorallocate')) {
            $this->markTestSkipped('GD with GIF is required to build the fixture');
        }

        $tmp = tempnam(sys_get_temp_dir(), 'pdftgif_') . '.gif';
        $im  = imagecreate(2, 2);
        $this->assertNotFalse($im);
        imagecolorallocate($im, 0, 0, 0);
        imagecolorallocate($im, 255, 255, 255);
        imagegif($im, $tmp);
        unset($im);

        try {
            $engine = new PdfEngine();
            $y = $engine->getCursorY() - 50.0;
            $engine->drawImage($tmp, 40, $y, 50, 50);
            $output = $engine->output();
            $this->assertStringContainsString('/Subtype /Image', $output);
            $this->assertStringContainsString('DCTDecode', $output);
        } finally {
            @unlink($tmp);
        }
    }

    /* =============================================================
     | A2 — Per-glyph AFM metrics (regression for v0.7.3)
     |============================================================= */

    /**
     * Before v0.7.3, all characters in a font shared a single average
     * width per font, so 'WWWW' and 'iiii' measured identically. The
     * Core 14 width tables make 'W' come out roughly 4× wider than
     * 'i' in Helvetica-Bold.
     */
    public function test_measure_text_width_distinguishes_narrow_and_wide_glyphs(): void
    {
        $engine = new PdfEngine();
        $narrow = $engine->measureTextWidth('iiii', 'Helvetica-Bold', 12.0);
        $wide   = $engine->measureTextWidth('WWWW', 'Helvetica-Bold', 12.0);

        $this->assertGreaterThan($narrow * 2.5, $wide,
            'Wide glyphs (W) should measure noticeably wider than narrow ones (i)');
    }

    public function test_measure_text_width_handles_french_typography(): void
    {
        $engine = new PdfEngine();
        $w = $engine->measureTextWidth('éàèçôîï«»œ', 'Times-Roman', 12.0);

        $this->assertGreaterThan(40.0, $w);
        $this->assertLessThan(80.0, $w);
    }

    public function test_get_font_metrics_returns_real_values(): void
    {
        $engine = new PdfEngine();
        $m = $engine->getFontMetrics('Helvetica-Bold');

        $this->assertGreaterThan(700, $m['ascender']);
        $this->assertLessThan(0, $m['descender']);
        $this->assertGreaterThan(600, $m['capHeight']);
    }

    public function test_get_font_metrics_falls_back_for_unknown_font(): void
    {
        $engine = new PdfEngine();
        $m = $engine->getFontMetrics('SomeCustomFontNotInCore14');

        $this->assertSame(718, $m['ascender']);
        $this->assertSame(-207, $m['descender']);
    }

    /* =============================================================
     | B1 — Justification with combined Tw + Tc + threshold
     |============================================================= */

    public function test_justified_short_line_falls_back_to_flush_left(): void
    {
        $engine = new PdfEngine();
        // A 3-word fragment in a wide column would need extreme word
        // spacing to fill the line — much larger than the 3pt threshold.
        // The engine should detect this and fall back to flush-left.
        $engine->writeWrappedTextAt(
            text:        'Hi there.',
            fontName:    'Helvetica',
            fontSize:    12.0,
            x:           40.0,
            yTopLeft:    100.0,
            maxWidth:    400.0,
            lineSpacing: 1.15,
            align:       'justify',
        );

        $output = $engine->output();

        // A fall-back should not have emitted any non-zero Tw operator.
        $this->assertDoesNotMatchRegularExpression('/[1-9]\d*\.\d+ Tw/', $output);
    }

    public function test_justified_normal_line_emits_word_spacing(): void
    {
        $engine = new PdfEngine();
        $engine->writeWrappedTextAt(
            text:        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do.',
            fontName:    'Helvetica',
            fontSize:    11.0,
            x:           40.0,
            yTopLeft:    100.0,
            maxWidth:    300.0,
            lineSpacing: 1.3,
            align:       'justify',
        );

        $output = $engine->output();

        $this->assertMatchesRegularExpression('/\d+\.\d+ Tw/', $output);
        // Reset operator must always be present so subsequent text
        // isn't accidentally justified.
        $this->assertStringContainsString('0 Tw', $output);
    }

    /* =============================================================
     | C3 — escapePdfString fallback for non-WinAnsi
     |============================================================= */

    public function test_french_typography_glyphs_round_trip(): void
    {
        $engine = new PdfEngine();
        $engine->writeText("« Café — où ? » … 'œuvre'", 'Times-Roman', 12.0);

        $output = $engine->output();
        // Each unique cp1252 byte should appear in the literal string;
        // we just spot-check that the encoded content is non-empty and
        // doesn't carry raw UTF-8 sequences (which would mean encoding
        // silently failed).
        $this->assertStringNotContainsString("\xC3\xA9", $output, // UTF-8 'é'
            'UTF-8 sequences should have been transcoded to cp1252');
    }

    public function test_non_winansi_chars_are_replaced_with_question_mark(): void
    {
        $engine = new PdfEngine();
        $engine->writeText("Pi: π — square root: √2", 'Times-Roman', 12.0);

        $output = $engine->output();

        // π and √ aren't in cp1252 → must be substituted with '?',
        // never silently dropped (which would leave wrong widths
        // downstream).
        $this->assertStringContainsString('?', $output);
    }
}
