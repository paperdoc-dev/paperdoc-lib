<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\{
    Document,
    Heading,
    Image,
    ListBlock,
    Metadata,
    Paragraph,
    TextRun,
};
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Document\Style\TextStyle;
use Paperdoc\Renderers\DocxRenderer;
use ZipArchive;

/**
 * v0.5.0 — DocxRenderer support for the v0.4.0 model core. Each test
 * renders a Document, unzips the DOCX in memory and asserts on XML
 * markers in word/document.xml and word/numbering.xml.
 */
class DocxRendererModelCoreTest extends TestCase
{
    private DocxRenderer $renderer;
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->renderer = new DocxRenderer();
        $this->tmpDir = sys_get_temp_dir() . '/paperdoc_docx_test_' . uniqid();
        mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->tmpDir . '/*') ?: []);
        @rmdir($this->tmpDir);
    }

    /* =============================================================
     | Helpers
     |============================================================= */

    private function save(Document $document): string
    {
        $path = $this->tmpDir . '/out.docx';
        $this->renderer->save($document, $path);

        $this->assertFileExists($path);

        return $path;
    }

    /** @return array<string, string> */
    private function extract(string $path): array
    {
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($path) === true, 'cannot open DOCX');

        $files = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            $files[$name] = (string) $zip->getFromIndex($i);
        }
        $zip->close();

        return $files;
    }

    /* =============================================================
     | Heading
     |============================================================= */

    public function test_heading_uses_pstyle_and_wraps_id_as_bookmark(): void
    {
        $doc = Document::make('docx');
        $doc->openSection()->addElement(Heading::make('Intro', 2, 'intro-id'));

        $xml = $this->extract($this->save($doc))['word/document.xml'] ?? '';

        $this->assertStringContainsString('<w:pStyle w:val="Heading2"/>', $xml);
        $this->assertStringContainsString('<w:bookmarkStart', $xml);
        $this->assertStringContainsString('w:name="intro_id"', $xml);
        $this->assertStringContainsString('<w:t>Intro</w:t>', $xml);
    }

    public function test_heading_levels_5_and_6_are_supported(): void
    {
        $doc = Document::make('docx');
        $section = $doc->openSection();
        $section->addElement(Heading::make('Five', 5));
        $section->addElement(Heading::make('Six', 6));

        $xml = $this->extract($this->save($doc))['word/document.xml'] ?? '';

        $this->assertStringContainsString('<w:pStyle w:val="Heading5"/>', $xml);
        $this->assertStringContainsString('<w:pStyle w:val="Heading6"/>', $xml);
    }

    /* =============================================================
     | Lists (numbering)
     |============================================================= */

    public function test_bullet_list_produces_numpr_and_bullet_abstract(): void
    {
        $doc = Document::make('docx');
        $section = $doc->openSection();
        $list = $section->addBulletList();
        $list->addText('One');
        $list->addText('Two');

        $files = $this->extract($this->save($doc));
        $document = $files['word/document.xml'] ?? '';
        $numbering = $files['word/numbering.xml'] ?? '';

        $this->assertSame(2, substr_count($document, '<w:numPr>'));
        $this->assertStringContainsString('<w:pStyle w:val="ListParagraph"/>', $document);
        $this->assertStringContainsString('<w:numFmt w:val="bullet"/>', $numbering);
    }

    public function test_ordered_list_start_override_is_emitted(): void
    {
        $doc = Document::make('docx');
        $list = $doc->openSection()->addOrderedList(5);
        $list->addText('Five');

        $files = $this->extract($this->save($doc));

        $this->assertStringContainsString('<w:numFmt w:val="decimal"/>', $files['word/numbering.xml']);
        $this->assertStringContainsString('<w:startOverride w:val="5"/>', $files['word/numbering.xml']);
    }

    public function test_nested_list_keeps_correct_ilvl(): void
    {
        $doc = Document::make('docx');
        $parent = $doc->openSection()->addBulletList();
        $itemA = $parent->addText('A');

        $sub = ListBlock::bullet();
        $sub->addText('A.1');
        $itemA->addBlock($sub);

        $document = $this->extract($this->save($doc))['word/document.xml'] ?? '';

        $this->assertStringContainsString('<w:ilvl w:val="0"/>', $document);
        $this->assertStringContainsString('<w:ilvl w:val="1"/>', $document);
    }

    /* =============================================================
     | Blockquote, CodeBlock, Bookmark
     |============================================================= */

    public function test_blockquote_uses_quote_style(): void
    {
        $doc = Document::make('docx');
        $doc->openSection()->addBlockquote()->addText('Words');

        $document = $this->extract($this->save($doc))['word/document.xml'] ?? '';

        $this->assertStringContainsString('<w:pStyle w:val="Quote"/>', $document);
    }

    public function test_code_block_preserves_lines_with_break_and_monospace(): void
    {
        $doc = Document::make('docx');
        $doc->openSection()->addCodeBlock("line1\nline2", 'php');

        $document = $this->extract($this->save($doc))['word/document.xml'] ?? '';

        $this->assertStringContainsString('<w:pStyle w:val="Code"/>', $document);
        $this->assertStringContainsString('w:ascii="Consolas"', $document);
        $this->assertSame(1, substr_count($document, '<w:br/>'));
    }

    public function test_bookmark_block_emits_start_and_end(): void
    {
        $doc = Document::make('docx');
        $doc->openSection()->addBookmark('anchor-one');

        $document = $this->extract($this->save($doc))['word/document.xml'] ?? '';

        $this->assertStringContainsString('<w:bookmarkStart', $document);
        $this->assertStringContainsString('w:name="anchor_one"', $document);
        $this->assertStringContainsString('<w:bookmarkEnd', $document);
    }

    /* =============================================================
     | Hyperlinks
     |============================================================= */

    public function test_external_link_registers_relationship_and_emits_hyperlink(): void
    {
        $doc = Document::make('docx');
        $paragraph = new Paragraph();
        $paragraph->addRun(new TextRun('Site', null, TextLink::make('https://paperdoc.dev')));
        $doc->openSection()->addElement($paragraph);

        $files = $this->extract($this->save($doc));
        $document = $files['word/document.xml'] ?? '';
        $rels = $files['word/_rels/document.xml.rels'] ?? '';

        $this->assertStringContainsString('<w:hyperlink', $document);
        $this->assertStringContainsString('r:id="rId', $document);
        $this->assertStringContainsString('Target="https://paperdoc.dev"', $rels);
        $this->assertStringContainsString('TargetMode="External"', $rels);
    }

    public function test_internal_anchor_link_uses_anchor_attribute(): void
    {
        $doc = Document::make('docx');
        $paragraph = new Paragraph();
        $paragraph->addRun(new TextRun('Jump', null, TextLink::make('', 'ref')));
        $doc->openSection()->addElement($paragraph);

        $document = $this->extract($this->save($doc))['word/document.xml'] ?? '';

        $this->assertStringContainsString('w:anchor="ref"', $document);
        $this->assertStringNotContainsString('r:id=', $document);
    }

    public function test_hyperlink_tooltip_is_set(): void
    {
        $doc = Document::make('docx');
        $paragraph = new Paragraph();
        $paragraph->addRun(new TextRun('Link', null, TextLink::make('https://paperdoc.dev', '', 'Homepage')));
        $doc->openSection()->addElement($paragraph);

        $document = $this->extract($this->save($doc))['word/document.xml'] ?? '';

        $this->assertStringContainsString('w:tooltip="Homepage"', $document);
    }

    /* =============================================================
     | Images
     |============================================================= */

    public function test_embedded_image_is_added_as_media_and_referenced(): void
    {
        $doc = Document::make('docx');
        $pngBytes = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAFElEQVQoU2NkYGD4z0AEYBxVSF+kAgCEmgJBgqs3vwAAAABJRU5ErkJggg==',
            true,
        ) ?: '';
        $doc->openSection()->addElement(Image::fromData($pngBytes, 'image/png', 8, 8, 'demo'));

        $files = $this->extract($this->save($doc));
        $document = $files['word/document.xml'] ?? '';
        $rels = $files['word/_rels/document.xml.rels'] ?? '';

        $this->assertArrayHasKey('word/media/image1.png', $files);
        $this->assertStringContainsString('<w:drawing>', $document);
        $this->assertStringContainsString('r:embed="rId', $document);
        $this->assertStringContainsString('Target="media/image1.png"', $rels);
    }

    /* =============================================================
     | Metadata → core.xml
     |============================================================= */

    public function test_document_properties_flow_into_core_xml(): void
    {
        $doc = Document::make('docx', 'Titre')->setProperties(
            Metadata::make()
                ->setAuthor('Test Author')
                ->setSubject('Subject line')
                ->setKeywords('k1, k2')
                ->setLanguage('fr-FR')
        );
        $doc->openSection()->addParagraph('Body');

        $core = $this->extract($this->save($doc))['docProps/core.xml'] ?? '';

        $this->assertStringContainsString('<dc:creator>Test Author</dc:creator>', $core);
        $this->assertStringContainsString('<dc:subject>Subject line</dc:subject>', $core);
        $this->assertStringContainsString('<cp:keywords>k1, k2</cp:keywords>', $core);
        $this->assertStringContainsString('<dc:language>fr-FR</dc:language>', $core);
    }
}
