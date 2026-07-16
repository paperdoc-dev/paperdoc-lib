<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\{Document, Heading, Metadata, PageBreak, Section};
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Renderers\PdfRenderer;

/**
 * v1.0.0 — clickable link annotations (/Annots), named internal
 * destinations, document outline (/Outlines) and the full /Info
 * metadata dictionary in the native PDF renderer.
 */
class PdfRendererLinksOutlineTest extends TestCase
{
    private PdfRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new PdfRenderer();
    }

    /* ---------------------------------------------------------------
     | External link annotations
     |--------------------------------------------------------------- */

    public function test_external_link_produces_uri_annotation(): void
    {
        $doc = Document::make('pdf', 'Links');
        $doc->openSection()->addText(
            'Visit the site',
            null,
            TextLink::make('https://example.com/docs'),
        );

        $pdf = $this->renderer->render($doc);

        $this->assertStringContainsString('/Subtype /Link', $pdf);
        $this->assertStringContainsString('/S /URI', $pdf);
        $this->assertStringContainsString('(https://example.com/docs)', $pdf);
        $this->assertStringContainsString('/Annots [', $pdf);
    }

    public function test_uri_with_parentheses_is_escaped(): void
    {
        $doc = Document::make('pdf');
        $doc->openSection()->addText(
            'wiki',
            null,
            TextLink::make('https://en.wikipedia.org/wiki/PDF_(format)'),
        );

        $pdf = $this->renderer->render($doc);

        $this->assertStringContainsString('PDF_\\(format\\)', $pdf);
    }

    public function test_annotation_rect_has_positive_area(): void
    {
        $doc = Document::make('pdf');
        $doc->openSection()->addText('click me', null, TextLink::make('https://x.dev'));

        $pdf = $this->renderer->render($doc);

        $this->assertSame(
            1,
            preg_match('/\/Rect \[([\d.]+) ([\d.]+) ([\d.]+) ([\d.]+)\]/', $pdf, $m),
            'A /Rect entry is expected',
        );
        $this->assertGreaterThan((float) $m[1], (float) $m[3], 'x2 > x1');
        $this->assertGreaterThan((float) $m[2], (float) $m[4], 'y2 > y1');
    }

    public function test_no_annotations_without_links(): void
    {
        $doc = Document::make('pdf');
        $doc->openSection()->addParagraph('Plain text only.');

        $pdf = $this->renderer->render($doc);

        $this->assertStringNotContainsString('/Annots', $pdf);
        $this->assertStringNotContainsString('/Subtype /Link', $pdf);
    }

    /* ---------------------------------------------------------------
     | Internal anchors (Bookmark / Heading id → GoTo destination)
     |--------------------------------------------------------------- */

    public function test_anchor_link_to_bookmark_produces_dest(): void
    {
        $doc = Document::make('pdf');
        $section = $doc->openSection();
        $section->addText('Jump to conclusion', null, TextLink::make('', 'conclusion'));
        $section->addElement(new PageBreak());
        $section->addBookmark('conclusion');
        $section->addParagraph('The end.');

        $pdf = $this->renderer->render($doc);

        $this->assertStringContainsString('/Subtype /Link', $pdf);
        $this->assertSame(
            1,
            preg_match('/\/Dest \[(\d+) 0 R \/XYZ null [\d.]+ null\]/', $pdf, $m),
            'A GoTo /Dest entry is expected',
        );

        // The destination must reference the SECOND page object (the
        // bookmark sits after an explicit PageBreak).
        preg_match('/\/Kids \[([^\]]+)\]/', $pdf, $kids);
        $pageRefs = array_map('intval', array_filter(preg_split('/\s+0 R\s*/', trim($kids[1]))));
        $this->assertCount(2, $pageRefs);
        $this->assertSame($pageRefs[1], (int) $m[1], 'Dest should point at page 2');
    }

    public function test_link_to_unknown_anchor_is_dropped_not_fatal(): void
    {
        $doc = Document::make('pdf');
        $doc->openSection()->addText('ghost', null, TextLink::make('', 'never-declared'));

        $pdf = $this->renderer->render($doc);

        $this->assertStringStartsWith('%PDF-1.4', $pdf);
        $this->assertStringNotContainsString('/Dest', $pdf);
    }

    public function test_heading_id_is_a_valid_anchor_target(): void
    {
        $doc = Document::make('pdf');
        $section = $doc->openSection();
        $section->addText('See chapter 2', null, TextLink::make('', 'chap-2'));
        $section->addElement(new PageBreak());
        $section->addElement(Heading::make('Chapter 2', 1, 'chap-2'));

        $pdf = $this->renderer->render($doc);

        $this->assertMatchesRegularExpression('/\/Dest \[\d+ 0 R \/XYZ null [\d.]+ null\]/', $pdf);
    }

    /* ---------------------------------------------------------------
     | Document outline (/Outlines)
     |--------------------------------------------------------------- */

    public function test_headings_produce_outline_tree(): void
    {
        $doc = Document::make('pdf', 'Outlined');
        $section = $doc->openSection();
        $section->addElement(Heading::make('Introduction', 1));
        $section->addElement(Heading::make('Details', 2));
        $section->addElement(Heading::make('Conclusion', 1));

        $pdf = $this->renderer->render($doc);

        $this->assertStringContainsString('/Type /Outlines', $pdf);
        $this->assertStringContainsString('/Outlines', $pdf);
        $this->assertStringContainsString('/PageMode /UseOutlines', $pdf);
        $this->assertStringContainsString('/Title (Introduction)', $pdf);
        $this->assertStringContainsString('/Title (Details)', $pdf);
        $this->assertStringContainsString('/Title (Conclusion)', $pdf);

        // Root outline: two top-level entries (Details is nested under
        // Introduction).
        $this->assertMatchesRegularExpression(
            '/\/Type \/Outlines \/First \d+ 0 R \/Last \d+ 0 R \/Count 2/',
            $pdf,
        );
    }

    public function test_outline_nesting_clamps_skipped_levels(): void
    {
        $doc = Document::make('pdf');
        $section = $doc->openSection();
        $section->addElement(Heading::make('Top', 1));
        // H4 right after H1: becomes a direct child, tree stays valid.
        $section->addElement(Heading::make('Deep', 4));

        $pdf = $this->renderer->render($doc);

        $this->assertStringContainsString('/Title (Top)', $pdf);
        $this->assertStringContainsString('/Title (Deep)', $pdf);
        $this->assertMatchesRegularExpression('/\/Count 1 >>/', $pdf);
    }

    public function test_no_outline_without_headings(): void
    {
        $doc = Document::make('pdf');
        $doc->openSection()->addParagraph('No headings here.');

        $pdf = $this->renderer->render($doc);

        $this->assertStringNotContainsString('/Outlines', $pdf);
        $this->assertStringNotContainsString('/PageMode', $pdf);
    }

    /* ---------------------------------------------------------------
     | /Info metadata dictionary
     |--------------------------------------------------------------- */

    public function test_info_dictionary_maps_typed_metadata(): void
    {
        $doc = Document::make('pdf', 'Annual Report')
            ->setProperties(
                Metadata::make()
                    ->setAuthor('Alice Martin')
                    ->setSubject('Yearly results')
                    ->setKeywords('finance, report, 2026')
                    ->setCreatedAt(new \DateTimeImmutable('2026-01-15 10:30:00'))
                    ->setModifiedAt(new \DateTimeImmutable('2026-02-01 08:00:00')),
            );
        $doc->openSection()->addParagraph('Body');

        $pdf = $this->renderer->render($doc);

        $this->assertStringContainsString('/Title (Annual Report)', $pdf);
        $this->assertStringContainsString('/Author (Alice Martin)', $pdf);
        $this->assertStringContainsString('/Subject (Yearly results)', $pdf);
        $this->assertStringContainsString('/Keywords (finance, report, 2026)', $pdf);
        $this->assertStringContainsString('/CreationDate (D:20260115103000)', $pdf);
        $this->assertStringContainsString('/ModDate (D:20260201080000)', $pdf);
        $this->assertStringContainsString('/Producer (Paperdoc PHP Library)', $pdf);
    }

    public function test_info_dictionary_omits_empty_fields(): void
    {
        $doc = Document::make('pdf');
        $doc->openSection()->addParagraph('Body');

        $pdf = $this->renderer->render($doc);

        $this->assertStringNotContainsString('/Author', $pdf);
        $this->assertStringNotContainsString('/Subject', $pdf);
        $this->assertStringNotContainsString('/Keywords', $pdf);
        $this->assertStringNotContainsString('/ModDate', $pdf);
        $this->assertStringContainsString('/Producer (Paperdoc PHP Library)', $pdf);
    }
}
