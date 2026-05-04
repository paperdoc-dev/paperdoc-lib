<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\{Document, HorizontalRule, Paragraph, Section, Table, TableCell, TableRow, TextRun};
use Paperdoc\Document\Style\{ParagraphStyle, TextStyle};
use Paperdoc\Enum\Alignment;
use Paperdoc\Parsers\DocxParser;
use Paperdoc\Renderers\DocxRenderer;

class DocxRendererTest extends TestCase
{
    private string $tmp;

    protected function setUp(): void
    {
        $this->tmp = tempnam(sys_get_temp_dir(), 'paperdoc_docx_test_') . '.docx';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tmp)) {
            @unlink($this->tmp);
        }
    }

    public function test_save_produces_valid_zip_package(): void
    {
        $doc = new Document('docx', 'My Word Document');
        $doc->openSection()->addParagraph('Testing the creation of a Word Document');

        (new DocxRenderer())->save($doc, $this->tmp);

        $this->assertFileExists($this->tmp);
        $this->assertGreaterThan(0, filesize($this->tmp));

        $zip = new \ZipArchive();
        $this->assertSame(true, $zip->open($this->tmp));

        foreach (['[Content_Types].xml', '_rels/.rels', 'word/document.xml', 'word/styles.xml', 'docProps/core.xml'] as $part) {
            $this->assertNotFalse($zip->locateName($part), "Missing package part: {$part}");
        }

        $documentXml = $zip->getFromName('word/document.xml');
        $this->assertIsString($documentXml);
        $this->assertStringContainsString('Testing the creation of a Word Document', $documentXml);

        $zip->close();
    }

    public function test_document_xml_is_well_formed_and_uses_wordprocessingml_namespace(): void
    {
        $doc = new Document('docx');
        $section = Section::make('main');
        $section->addParagraph('Hello');
        $doc->addSection($section);

        (new DocxRenderer())->save($doc, $this->tmp);

        $zip = new \ZipArchive();
        $zip->open($this->tmp);
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        $dom = new \DOMDocument();
        $this->assertTrue($dom->loadXML($xml));

        $this->assertSame('document', $dom->documentElement->localName);
        $this->assertSame(
            'http://schemas.openxmlformats.org/wordprocessingml/2006/main',
            $dom->documentElement->namespaceURI
        );
    }

    public function test_renders_heading_runs_bold_italic_and_color(): void
    {
        $doc = new Document('docx');
        $section = Section::make();
        $section->addHeading('Title', 1);

        $paragraph = new Paragraph();
        $paragraph->addRun(new TextRun('Bold', TextStyle::make()->setBold()));
        $paragraph->addRun(new TextRun(' italic', TextStyle::make()->setItalic()));
        $paragraph->addRun(new TextRun(' red', TextStyle::make()->setColor('#FF0000')));
        $section->addElement($paragraph);

        $doc->addSection($section);

        (new DocxRenderer())->save($doc, $this->tmp);

        $zip = new \ZipArchive();
        $zip->open($this->tmp);
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        $this->assertStringContainsString('<w:pStyle w:val="Heading1"/>', $xml);
        $this->assertStringContainsString('<w:b/>', $xml);
        $this->assertStringContainsString('<w:i/>', $xml);
        $this->assertStringContainsString('<w:color w:val="FF0000"/>', $xml);
    }

    public function test_renders_table_with_header_row(): void
    {
        $doc = new Document('docx');
        $section = Section::make();

        $table = new Table();
        $table->setHeaders(['A', 'B']);
        $table->addRowFromArray(['1', '2']);
        $section->addElement($table);

        $doc->addSection($section);

        (new DocxRenderer())->save($doc, $this->tmp);

        $zip = new \ZipArchive();
        $zip->open($this->tmp);
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        $this->assertStringContainsString('<w:tbl>', $xml);
        $this->assertStringContainsString('<w:tblHeader/>', $xml);
        $this->assertStringContainsString('>A<', $xml);
        $this->assertStringContainsString('>1<', $xml);
    }

    public function test_alignment_center_is_mapped(): void
    {
        $doc = new Document('docx');
        $section = Section::make();
        $paragraph = new Paragraph(ParagraphStyle::make()->setAlignment(Alignment::CENTER));
        $paragraph->addRun(new TextRun('Centered'));
        $section->addElement($paragraph);
        $doc->addSection($section);

        (new DocxRenderer())->save($doc, $this->tmp);

        $zip = new \ZipArchive();
        $zip->open($this->tmp);
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        $this->assertStringContainsString('<w:jc w:val="center"/>', $xml);
    }

    public function test_xml_special_characters_are_escaped(): void
    {
        $doc = new Document('docx');
        $doc->openSection()->addParagraph('5 < 10 & 10 > 5 "test"');

        (new DocxRenderer())->save($doc, $this->tmp);

        $zip = new \ZipArchive();
        $zip->open($this->tmp);
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        $this->assertStringContainsString('5 &lt; 10 &amp; 10 &gt; 5', $xml);
        $dom = new \DOMDocument();
        $this->assertTrue($dom->loadXML($xml));
    }

    public function test_round_trip_through_parser_preserves_text(): void
    {
        $doc = new Document('docx', 'Roundtrip');
        $section = Section::make('main');
        $section->addHeading('Hello heading', 1);
        $section->addParagraph('Line one.');
        $section->addParagraph('Line two.');
        $doc->addSection($section);

        (new DocxRenderer())->save($doc, $this->tmp);

        $parsed = (new DocxParser())->parse($this->tmp);

        $this->assertCount(1, $parsed->getSections());

        $text = '';
        foreach ($parsed->getSections()[0]->getElements() as $el) {
            if ($el instanceof Paragraph) {
                $text .= $el->getPlainText() . "\n";
            }
        }

        $this->assertStringContainsString('Hello heading', $text);
        $this->assertStringContainsString('Line one.', $text);
        $this->assertStringContainsString('Line two.', $text);
    }

    public function test_empty_document_produces_valid_package(): void
    {
        $doc = new Document('docx');

        (new DocxRenderer())->save($doc, $this->tmp);

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($this->tmp) === true);
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        $dom = new \DOMDocument();
        $this->assertTrue($dom->loadXML($xml));
    }

    /**
     * v0.8.0 / B5 — HorizontalRule renders as the canonical Word
     * "horizontal line": an empty paragraph with a bottom border. We
     * inspect the document.xml directly so the test is unaffected by
     * future model-core refactors.
     */
    public function test_horizontal_rule_renders_as_bordered_paragraph(): void
    {
        $doc = Document::make('docx');
        $section = Section::make('s');
        $section->addText('Above');
        $section->addRule()->setColor('#999999')->setThickness(0.75);
        $section->addText('Below');
        $doc->addSection($section);

        (new DocxRenderer())->save($doc, $this->tmp);

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($this->tmp) === true);
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        // The rule emits a <w:pBdr><w:bottom .../></w:pBdr> on an
        // empty paragraph — the canonical Word "horizontal line".
        $this->assertStringContainsString('<w:pBdr>', $xml);
        $this->assertStringContainsString('<w:bottom', $xml);
        $this->assertStringContainsString('w:color="999999"', $xml);
    }
}
