<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\{Document, Section};
use Paperdoc\Document\Style\{PageSetup, RunningElement, TextStyle};
use Paperdoc\Enum\{Alignment, PageSize, VerticalAlignment};
use Paperdoc\Renderers\PdfRenderer;

/**
 * Continuation pages — regression suite for the v0.8.3 "page chrome
 * (background, header, footer) was only painted on the first page of
 * each section" bug.
 *
 * Up to v0.8.2, `PdfEngine::writeWrappedText()` could call its own
 * `newPage()` mid-paragraph when a long line wouldn't fit, and the
 * `PdfRenderer` had no way to know — so the new page never received
 * its `PageSetup::backgroundColor` fill nor the document/section
 * footer. The exact same gap existed for the auto-pagination paths
 * inside `writeTable()`, `writeHorizontalRule()` and `writeImage()`.
 *
 * v0.8.3 fixed all four sites by adding a single `setOnNewPage` hook
 * on the engine, which the renderer uses to re-run its chrome painter
 * on every new physical page. These tests freeze that invariant.
 */
class PdfRendererContinuationPagesTest extends TestCase
{
    /**
     * Builds a section with a paragraph long enough to be guaranteed
     * to overflow at least one A5 page (≈30 lines fit on an A5 with
     * 40pt gutters at 12pt body, so 80 short lines = ~3 pages worth).
     */
    private function makeOverflowParagraph(int $lines = 80): string
    {
        $line = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, '
              . 'sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        return implode("\n", array_fill(0, $lines, $line));
    }

    /**
     * Counts how many `/Type /Page ` declarations appear in the
     * generated content. Each declared page is exactly one physical
     * page in the resulting PDF.
     */
    private function countPdfPages(string $pdfContent): int
    {
        return substr_count($pdfContent, '/Type /Page ');
    }

    /**
     * §1 — A long paragraph that forces several `writeWrappedText`
     * auto-page-breaks in a section configured with a background
     * color must paint that background on EVERY physical page, not
     * just the first.
     */
    public function test_page_background_is_repainted_on_every_continuation_page(): void
    {
        $doc = Document::make('pdf', 'overflow-bg');

        // #FFEEDD → 1.00 / 0.93 / 0.87 in PDF rg space.
        $section = Section::make('long')->setPageSetup(
            PageSetup::fromSize(PageSize::A5)->setBackgroundColor('#FFEEDD')
        );
        $section->addParagraph($this->makeOverflowParagraph());
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);

        $pageCount = $this->countPdfPages($content);
        $this->assertGreaterThanOrEqual(2, $pageCount,
            'test scenario requires at least 2 physical pages');

        $bgFillCount = substr_count($content, '1.00 0.93 0.87 rg');
        $this->assertSame($pageCount, $bgFillCount,
            'every physical page must receive the section background fill, '
            . "got {$bgFillCount} fills for {$pageCount} pages");
    }

    /**
     * §1 — Same scenario with a document-level footer: every
     * continuation page must render the footer text and the `{page}`
     * placeholder must reflect the actual page number.
     */
    public function test_document_footer_is_repainted_on_every_continuation_page(): void
    {
        $doc = Document::make('pdf', 'overflow-footer');
        $doc->setFooter(
            RunningElement::make('p.{page}')
                ->setStyle(TextStyle::make()->setFontSize(10))
                ->setAlignment(Alignment::CENTER)
        );

        $section = Section::make('long')->setPageSetup(
            PageSetup::fromSize(PageSize::A5)
        );
        $section->addParagraph($this->makeOverflowParagraph());
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);

        $pageCount = $this->countPdfPages($content);
        $this->assertGreaterThanOrEqual(2, $pageCount);

        // The {page} placeholder must be substituted with the right
        // index on each page — page 1 prints "p.1", page 2 prints
        // "p.2", etc. We assert at least the first two are present.
        $this->assertStringContainsString('(p.1)', $content,
            'first page must carry the resolved footer');
        $this->assertStringContainsString('(p.2)', $content,
            'continuation page must also carry the resolved footer');
    }

    /**
     * §1 — Combined scenario: backgroundColor + footer + multi-page
     * paragraph. This is the exact production symptom reported on
     * Plumio: page 1 looked correct, page 2 came out blank-chromed.
     */
    public function test_background_and_footer_both_repainted_on_continuation_pages(): void
    {
        $doc = Document::make('pdf', 'overflow-combo');
        $doc->setFooter(
            RunningElement::make('p.{page}')
                ->setStyle(TextStyle::make()->setFontSize(10))
                ->setAlignment(Alignment::CENTER)
        );

        $section = Section::make('long')->setPageSetup(
            PageSetup::fromSize(PageSize::A5)->setBackgroundColor('#FFEEDD')
        );
        $section->addParagraph($this->makeOverflowParagraph());
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);

        $pageCount = $this->countPdfPages($content);
        $this->assertGreaterThanOrEqual(2, $pageCount);

        $this->assertSame($pageCount, substr_count($content, '1.00 0.93 0.87 rg'),
            'background fill missing on at least one continuation page');
        $this->assertStringContainsString('(p.1)', $content);
        $this->assertStringContainsString('(p.2)', $content);
    }

    /**
     * §5 — Conversely, `hideFooter()` must still suppress the footer
     * on EVERY page of the section, including continuation pages.
     * Otherwise the fix could swing the bug the other way and start
     * leaking footers onto cover-style sections.
     */
    public function test_hide_footer_suppresses_footer_on_continuation_pages_too(): void
    {
        $doc = Document::make('pdf', 'hide-footer-overflow');
        $doc->setFooter(RunningElement::make('LEAKED-{page}'));

        $section = Section::make('cover')
            ->setPageSetup(PageSetup::fromSize(PageSize::A5))
            ->hideFooter();
        $section->addParagraph($this->makeOverflowParagraph());
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);

        $this->assertGreaterThanOrEqual(2, $this->countPdfPages($content));
        $this->assertStringNotContainsString('LEAKED-1', $content);
        $this->assertStringNotContainsString('LEAKED-2', $content);
    }

    /**
     * §6 — Sections with `verticalAlignment = CENTER` that overflow
     * onto a second page bail out of the CTM translation silently
     * (the engine can't translate content already flushed to the
     * previous page). The bail-out is fine; what we MUST guarantee is
     * that the bail-out doesn't drag chrome painting down with it —
     * continuation pages still need their background and footer.
     */
    public function test_vertical_centered_section_paints_chrome_on_overflow_pages(): void
    {
        $doc = Document::make('pdf', 'vcenter-overflow');
        $doc->setFooter(
            RunningElement::make('p.{page}')->setAlignment(Alignment::CENTER)
        );

        $section = Section::make('long')
            ->setPageSetup(PageSetup::fromSize(PageSize::A5)->setBackgroundColor('#FFEEDD'))
            ->setVerticalAlignment(VerticalAlignment::CENTER);
        $section->addParagraph($this->makeOverflowParagraph());
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);

        $pageCount = $this->countPdfPages($content);
        $this->assertGreaterThanOrEqual(2, $pageCount,
            'scenario requires the section to overflow');

        $this->assertSame($pageCount, substr_count($content, '1.00 0.93 0.87 rg'),
            'vertical-align overflow must still repaint the background on every page');
        $this->assertStringContainsString('(p.1)', $content);
        $this->assertStringContainsString('(p.2)', $content);
    }

    /**
     * Z-order invariant — the page background fill MUST be emitted
     * before the body text on EACH page (not just the first), so the
     * text remains visible over the colored backdrop.
     */
    public function test_background_precedes_body_text_on_continuation_pages(): void
    {
        $doc = Document::make('pdf', 'z-order');
        $section = Section::make('long')->setPageSetup(
            PageSetup::fromSize(PageSize::A5)->setBackgroundColor('#FFEEDD')
        );
        $section->addParagraph($this->makeOverflowParagraph());
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);

        // Split the PDF stream by page content boundaries. Each
        // content stream is wrapped in `<< /Length ... >>\nstream\n`
        // and ends with `\nendstream`. Per-page stream content lives
        // between those markers and is where we expect the bg fill
        // to precede the first `Tj` (show-text) operator.
        if (! preg_match_all('/stream\n(.*?)\nendstream/s', $content, $matches)) {
            $this->fail('no content streams found in generated PDF');
        }

        $streamsWithBody = 0;
        foreach ($matches[1] as $stream) {
            if (! str_contains($stream, 'Tj')) {
                continue; // page has only chrome (rare); skip
            }
            $streamsWithBody++;

            $bgPos   = strpos($stream, '1.00 0.93 0.87 rg');
            $bodyPos = strpos($stream, 'Tj');

            $this->assertNotFalse($bgPos, 'continuation page must contain background fill');
            $this->assertLessThan($bodyPos, $bgPos,
                'background fill must precede body text on every page');
        }

        $this->assertGreaterThanOrEqual(2, $streamsWithBody,
            'test relies on at least two pages carrying body text');
    }
}
