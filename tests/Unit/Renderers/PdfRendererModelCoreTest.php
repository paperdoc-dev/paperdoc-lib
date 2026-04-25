<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\{Document, Heading, Paragraph, TextRun};
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Renderers\PdfRenderer;

/**
 * v0.5.0 — PdfRenderer support for the v0.4.0 model core. The PDF
 * format is a binary stream of drawing commands, so assertions focus on
 * the presence of key text fragments (after decoding each compressed
 * content stream) and basic structural sanity checks.
 */
class PdfRendererModelCoreTest extends TestCase
{
    private PdfRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new PdfRenderer();
    }

    /** @return string the concatenation of all decompressed content streams */
    private function decompressStreams(string $pdf): string
    {
        $chunks = [];

        if (preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $pdf, $matches)) {
            foreach ($matches[1] as $raw) {
                $decoded = @gzuncompress($raw);
                if ($decoded !== false) {
                    $chunks[] = $decoded;
                    continue;
                }
                $chunks[] = $raw;
            }
        }

        return implode("\n", $chunks);
    }

    public function test_header_is_pdf_14(): void
    {
        $doc = Document::make('pdf', 'ok');
        $doc->openSection()->addParagraph('Hello');

        $pdf = $this->renderer->render($doc);

        $this->assertStringStartsWith('%PDF-1.4', $pdf);
    }

    public function test_heading_is_written_with_larger_font(): void
    {
        $doc = Document::make('pdf');
        $doc->openSection()->addElement(Heading::make('Big Title', 1));

        $content = $this->decompressStreams($this->renderer->render($doc));

        $this->assertStringContainsString('(Big Title) Tj', $content);
        $this->assertMatchesRegularExpression('/Tf.*Big Title/s', $content);
    }

    public function test_bullet_list_emits_bullet_marker_and_items(): void
    {
        $doc = Document::make('pdf');
        $list = $doc->openSection()->addBulletList();
        $list->addText('One');
        $list->addText('Two');

        $content = $this->decompressStreams($this->renderer->render($doc));

        // Windows-1252 encodes U+2022 (bullet) as 0x95
        $this->assertStringContainsString("\x95 One", $content);
        $this->assertStringContainsString("\x95 Two", $content);
    }

    public function test_ordered_list_emits_numeric_markers(): void
    {
        $doc = Document::make('pdf');
        $list = $doc->openSection()->addOrderedList(2);
        $list->addText('Alpha');
        $list->addText('Beta');

        $content = $this->decompressStreams($this->renderer->render($doc));

        $this->assertStringContainsString('2. Alpha', $content);
        $this->assertStringContainsString('3. Beta', $content);
    }

    public function test_code_block_is_written_in_courier(): void
    {
        $doc = Document::make('pdf');
        $doc->openSection()->addCodeBlock('$x = 1;', 'php');

        $content = $this->decompressStreams($this->renderer->render($doc));

        $this->assertStringContainsString('$x = 1;', $content);
    }

    public function test_blockquote_text_is_rendered(): void
    {
        $doc = Document::make('pdf');
        $doc->openSection()->addBlockquote()->addText('Quoted line');

        $content = $this->decompressStreams($this->renderer->render($doc));

        $this->assertStringContainsString('Quoted line', $content);
    }

    public function test_external_link_label_is_visible_and_styled(): void
    {
        $doc = Document::make('pdf');
        $paragraph = new Paragraph();
        $paragraph->addRun(new TextRun('Docs', null, TextLink::make('https://paperdoc.dev')));
        $doc->openSection()->addElement($paragraph);

        $content = $this->decompressStreams($this->renderer->render($doc));

        $this->assertStringContainsString('(Docs) Tj', $content);
        // styled link color 0563C1 -> rgb(0.02, 0.39, 0.76)
        $this->assertMatchesRegularExpression('/0\.02\s+0\.39\s+0\.76\s+rg/', $content);
    }

    public function test_bookmark_is_silent(): void
    {
        $doc = Document::make('pdf');
        $section = $doc->openSection();
        $section->addBookmark('ref');
        $section->addParagraph('Body');

        $content = $this->decompressStreams($this->renderer->render($doc));

        $this->assertStringContainsString('(Body) Tj', $content);
        $this->assertStringNotContainsString('ref', $content);
    }
}
