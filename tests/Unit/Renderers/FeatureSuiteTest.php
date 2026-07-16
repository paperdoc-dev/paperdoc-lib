<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Tests\Support\InflatesPdfStreams;
use Paperdoc\Document\{Document, Heading, TableOfContents};
use Paperdoc\Document\Style\{TextStyle, Watermark};
use Paperdoc\Enum\Format;
use Paperdoc\Renderers\{DocxRenderer, HtmlRenderer, MarkdownRenderer, PdfRenderer};
use Paperdoc\Support\DocumentManager;

class FeatureSuiteTest extends TestCase
{
    use InflatesPdfStreams;
    private function docxDocumentXml(string $docx): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'pd_test_') . '.docx';
        file_put_contents($tmp, $docx);
        $zip = new \ZipArchive();
        $zip->open($tmp);
        $xml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();
        @unlink($tmp);

        return $xml;
    }

    /* ------------------- Rich text styles ------------------- */

    public function test_strikethrough_and_highlight_in_html(): void
    {
        $doc = Document::make('html');
        $doc->openSection()->addText(
            'obsolete',
            TextStyle::make()->setStrikethrough()->setHighlight('#FFEE00'),
        );

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringContainsString('text-decoration:line-through', $html);
        $this->assertStringContainsString('background-color:#FFEE00', $html);
    }

    public function test_underline_and_strikethrough_combined_in_html(): void
    {
        $doc = Document::make('html');
        $doc->openSection()->addText('both', TextStyle::make()->setUnderline()->setStrikethrough());

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringContainsString('text-decoration:underline line-through', $html);
    }

    public function test_strikethrough_and_highlight_in_docx(): void
    {
        $doc = Document::make('docx');
        $doc->openSection()->addText(
            'marked',
            TextStyle::make()->setStrikethrough()->setHighlight('#FFFF00'),
        );

        $xml = $this->docxDocumentXml((new DocxRenderer())->render($doc));

        $this->assertStringContainsString('<w:strike/>', $xml);
        $this->assertStringContainsString('w:fill="FFFF00"', $xml);
    }

    public function test_strikethrough_in_markdown(): void
    {
        $doc = Document::make('md');
        $doc->openSection()->addText('gone', TextStyle::make()->setStrikethrough());

        $this->assertStringContainsString('~~gone~~', (new MarkdownRenderer())->render($doc));
    }

    public function test_underline_and_strike_are_drawn_in_pdf(): void
    {
        $doc = Document::make('pdf');
        $doc->openSection()->addText('decorated', TextStyle::make()->setUnderline()->setStrikethrough());

        $pdf = $this->inflatePdf((new PdfRenderer())->render($doc));

        // Two stroked lines (m ... l S) beyond any table/rule usage.
        $this->assertGreaterThanOrEqual(2, preg_match_all('/ m [\d.]+ [\d.]+ l S/', $pdf));
    }

    public function test_highlight_paints_rect_in_pdf(): void
    {
        $doc = Document::make('pdf');
        $doc->openSection()->addText('lit', TextStyle::make()->setHighlight('#FFFF00'));

        $pdf = $this->inflatePdf((new PdfRenderer())->render($doc));

        $this->assertStringContainsString('1.00 1.00 0.00 rg', $pdf);
        $this->assertMatchesRegularExpression('/re\nf\n/', $pdf);
    }

    /* ------------------- Table of contents ------------------- */

    private function outlinedDocument(string $format): Document
    {
        $doc = Document::make($format, 'Doc');
        $section = $doc->openSection();
        $section->addTableOfContents(3, 'Sommaire');
        $section->addElement(Heading::make('Intro', 1, 'intro'));
        $section->addElement(Heading::make('Detail Two', 2));
        $section->addParagraph('Body text.');

        return $doc;
    }

    public function test_toc_in_html_links_headings(): void
    {
        $html = (new HtmlRenderer())->render($this->outlinedDocument('html'));

        $this->assertStringContainsString('paperdoc-toc', $html);
        $this->assertStringContainsString('Sommaire', $html);
        $this->assertStringContainsString('<a href="#intro">Intro</a>', $html);
        $this->assertStringContainsString('<a href="#paperdoc-h2">Detail Two</a>', $html);
        $this->assertStringContainsString('<h2 id="paperdoc-h2">', $html);
    }

    public function test_toc_in_markdown_uses_slugs(): void
    {
        $md = (new MarkdownRenderer())->render($this->outlinedDocument('md'));

        $this->assertStringContainsString('## Sommaire', $md);
        $this->assertStringContainsString('- [Intro](#intro)', $md);
        $this->assertStringContainsString('  - [Detail Two](#detail-two)', $md);
    }

    public function test_toc_in_pdf_emits_goto_links(): void
    {
        $pdf = $this->inflatePdf((new PdfRenderer())->render($this->outlinedDocument('pdf')));

        $this->assertStringContainsString('Sommaire', $pdf);
        $this->assertGreaterThanOrEqual(2, substr_count($pdf, '/Dest ['));
    }

    public function test_toc_in_docx_is_a_native_field(): void
    {
        $xml = $this->docxDocumentXml((new DocxRenderer())->render($this->outlinedDocument('docx')));

        $this->assertStringContainsString('TOC \o &quot;1-3&quot;', $xml);
        $this->assertStringContainsString('fldCharType="begin"', $xml);
    }

    public function test_empty_toc_renders_nothing(): void
    {
        $doc = Document::make('html');
        $doc->openSection()->addTableOfContents();
        $doc->getSections()[0]->addParagraph('No headings.');

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringNotContainsString('paperdoc-toc', $html);
    }

    /* ------------------- Watermark ------------------- */

    public function test_watermark_in_pdf_uses_extgstate_and_rotation(): void
    {
        $doc = Document::make('pdf', 'Draft');
        $doc->setWatermark(Watermark::make('CONFIDENTIEL')->setOpacity(0.25));
        $section = $doc->openSection();
        $section->addParagraph('Page one.');
        $section->addPageBreak();
        $section->addParagraph('Page two.');

        $pdf = $this->inflatePdf((new PdfRenderer())->render($doc));

        $this->assertStringContainsString('/Type /ExtGState /ca 0.25 /CA 0.25', $pdf);
        $this->assertStringContainsString('/ExtGState <<', $pdf);
        $this->assertSame(2, substr_count($pdf, '(CONFIDENTIEL) Tj'));
        $this->assertMatchesRegularExpression('/0\.7071 -?0\.7071 0\.7071 0\.7071/', $pdf);
    }

    public function test_watermark_in_html_is_an_overlay(): void
    {
        $doc = Document::make('html');
        $doc->setWatermark(Watermark::make('DRAFT')->setColor('#FF0000'));
        $doc->openSection()->addParagraph('Body.');

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringContainsString('paperdoc-watermark', $html);
        $this->assertStringContainsString('rotate(-45.0deg)', $html);
        $this->assertStringContainsString('DRAFT', $html);
        $this->assertStringContainsString('color:#FF0000', $html);
    }

    /* ------------------- String API + Format enum ------------------- */

    public function test_open_string_parses_markdown(): void
    {
        $doc = DocumentManager::openString("# Hello\n\nWorld paragraph.", Format::MD, ['ocr' => false]);

        $md = DocumentManager::renderAs($doc, Format::MD);

        $this->assertStringContainsString('Hello', $md);
        $this->assertStringContainsString('World paragraph.', $md);
    }

    public function test_convert_string_md_to_html(): void
    {
        $html = DocumentManager::convertString("# Big Title\n\nSome text.", 'md', 'html', ['ocr' => false]);

        $this->assertStringContainsString('Big Title', $html);
        $this->assertStringContainsString('<!DOCTYPE html>', $html);
    }

    public function test_create_accepts_format_enum(): void
    {
        $doc = DocumentManager::create(Format::PDF, 'Enum made');

        $this->assertSame('pdf', $doc->getFormat());
        $this->assertStringStartsWith('%PDF-1.4', DocumentManager::renderAs($doc, Format::PDF));
    }
}
