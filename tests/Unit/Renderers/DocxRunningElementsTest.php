<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\Document;
use Paperdoc\Document\Style\{RunningElement, TextStyle};
use Paperdoc\Enum\Alignment;
use Paperdoc\Renderers\DocxRenderer;

class DocxRunningElementsTest extends TestCase
{
    /** @return array<string, string> part name => xml */
    private function unzip(string $docx): array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'pd_hf_') . '.docx';
        file_put_contents($tmp, $docx);
        $zip = new \ZipArchive();
        $zip->open($tmp);

        $parts = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = (string) $zip->getNameIndex($i);
            $parts[$name] = (string) $zip->getFromIndex($i);
        }
        $zip->close();
        @unlink($tmp);

        return $parts;
    }

    private function renderedParts(): array
    {
        $doc = Document::make('docx', 'Rapport 2026');
        $doc->setHeader(RunningElement::make('{title}')->setAlignment(Alignment::LEFT));
        $doc->setFooter(
            RunningElement::make('Page {page} / {pages}')
                ->setAlignment(Alignment::CENTER)
                ->setStyle(TextStyle::make()->setFontSize(9)->setItalic()),
        );
        $doc->openSection()->addParagraph('Corps.');

        return $this->unzip((new DocxRenderer())->render($doc));
    }

    public function test_header_and_footer_parts_are_created(): void
    {
        $parts = $this->renderedParts();

        $this->assertArrayHasKey('word/header1.xml', $parts);
        $this->assertArrayHasKey('word/footer1.xml', $parts);
        $this->assertStringContainsString('<w:hdr', $parts['word/header1.xml']);
        $this->assertStringContainsString('<w:ftr', $parts['word/footer1.xml']);
    }

    public function test_title_placeholder_is_resolved_in_header(): void
    {
        $parts = $this->renderedParts();

        $this->assertStringContainsString('Rapport 2026', $parts['word/header1.xml']);
        $this->assertStringContainsString('<w:jc w:val="left"/>', $parts['word/header1.xml']);
    }

    public function test_page_placeholders_become_native_fields(): void
    {
        $footer = $this->renderedParts()['word/footer1.xml'];

        $this->assertStringContainsString('> PAGE <', $footer);
        $this->assertStringContainsString('> NUMPAGES <', $footer);
        $this->assertStringContainsString('fldCharType="begin"', $footer);
        $this->assertStringContainsString('<w:jc w:val="center"/>', $footer);
        $this->assertStringContainsString('<w:i/>', $footer);
    }

    public function test_sectpr_references_and_rels_are_wired(): void
    {
        $parts = $this->renderedParts();

        $this->assertMatchesRegularExpression(
            '/<w:headerReference w:type="default" r:id="rId\d+"\/>/',
            $parts['word/document.xml'],
        );
        $this->assertMatchesRegularExpression(
            '/<w:footerReference w:type="default" r:id="rId\d+"\/>/',
            $parts['word/document.xml'],
        );
        $this->assertStringContainsString('relationships/header" Target="header1.xml"', $parts['word/_rels/document.xml.rels']);
        $this->assertStringContainsString('relationships/footer" Target="footer1.xml"', $parts['word/_rels/document.xml.rels']);
        $this->assertStringContainsString('/word/header1.xml', $parts['[Content_Types].xml']);
        $this->assertStringContainsString('/word/footer1.xml', $parts['[Content_Types].xml']);
    }

    public function test_no_parts_without_running_elements(): void
    {
        $doc = Document::make('docx');
        $doc->openSection()->addParagraph('Plain.');

        $parts = $this->unzip((new DocxRenderer())->render($doc));

        $this->assertArrayNotHasKey('word/header1.xml', $parts);
        $this->assertArrayNotHasKey('word/footer1.xml', $parts);
        $this->assertStringNotContainsString('headerReference', $parts['word/document.xml']);
    }
}
