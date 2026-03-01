<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Parsers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\ParserInterface;
use Paperdoc\Document\{Image, Paragraph, Table};
use Paperdoc\Parsers\DocxParser;

class DocxParserTest extends TestCase
{
    private DocxParser $parser;

    private string $tmpDir;

    protected function setUp(): void
    {
        $this->parser = new DocxParser();
        $this->tmpDir = sys_get_temp_dir() . '/paperdoc_docx_tests_' . uniqid();
        mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        $files = glob($this->tmpDir . '/*');
        if ($files) {
            array_map('unlink', $files);
        }
        @rmdir($this->tmpDir);
    }

    public function test_implements_parser_interface(): void
    {
        $this->assertInstanceOf(ParserInterface::class, $this->parser);
    }

    public function test_supports_docx(): void
    {
        $this->assertTrue($this->parser->supports('docx'));
        $this->assertTrue($this->parser->supports('DOCX'));
        $this->assertFalse($this->parser->supports('doc'));
        $this->assertFalse($this->parser->supports('pdf'));
        $this->assertFalse($this->parser->supports('html'));
    }

    public function test_parse_simple_docx(): void
    {
        $path = $this->createMinimalDocx('Hello World');

        $doc = $this->parser->parse($path);

        $this->assertSame('docx', $doc->getFormat());
        $this->assertGreaterThanOrEqual(1, count($doc->getSections()));

        $elements = $doc->getSections()[0]->getElements();
        $this->assertNotEmpty($elements);

        $texts = $this->collectText($doc);
        $this->assertStringContainsString('Hello World', $texts);
    }

    public function test_parse_docx_with_title(): void
    {
        $path = $this->createDocxWithMetadata('Mon Document', 'Akram');

        $doc = $this->parser->parse($path);

        $this->assertSame('Mon Document', $doc->getTitle());
        $this->assertSame('Akram', $doc->getMetadata()['author'] ?? null);
    }

    public function test_parse_docx_with_bold_italic_underline(): void
    {
        $path = $this->createStyledDocx();

        $doc = $this->parser->parse($path);

        $elements = $doc->getSections()[0]->getElements();
        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));

        $this->assertNotEmpty($paragraphs);

        $hasStyled = false;
        foreach ($paragraphs as $p) {
            foreach ($p->getRuns() as $run) {
                if ($run->getStyle() !== null) {
                    $hasStyled = true;
                }
            }
        }

        $this->assertTrue($hasStyled, 'Au moins un run stylé devrait exister');
    }

    public function test_parse_docx_with_table(): void
    {
        $path = $this->createDocxWithTable();

        $doc = $this->parser->parse($path);

        $elements = $doc->getSections()[0]->getElements();
        $tables = array_values(array_filter($elements, fn ($e) => $e instanceof Table));

        $this->assertNotEmpty($tables, 'Un tableau devrait être extrait');

        $table = $tables[0];
        $this->assertGreaterThanOrEqual(2, count($table->getRows()));

        $firstRow = $table->getRows()[0];
        $this->assertTrue($firstRow->isHeader());

        $headers = array_map(fn ($c) => $c->getPlainText(), $firstRow->getCells());
        $this->assertContains('Nom', $headers);
        $this->assertContains('Valeur', $headers);
    }

    public function test_parse_docx_with_headings(): void
    {
        $path = $this->createDocxWithHeadings();

        $doc = $this->parser->parse($path);

        $texts = $this->collectText($doc);
        $this->assertStringContainsString('Titre Principal', $texts);
        $this->assertStringContainsString('Sous-titre', $texts);
    }

    public function test_nonexistent_file_throws(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->parser->parse('/nonexistent/file.docx');
    }

    public function test_invalid_zip_throws(): void
    {
        $path = $this->tmpDir . '/invalid.docx';
        file_put_contents($path, 'not a zip file');

        $this->expectException(\RuntimeException::class);
        $this->parser->parse($path);
    }

    public function test_parse_docx_with_embedded_image(): void
    {
        $path = $this->createDocxWithImage();

        $doc = $this->parser->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $images = array_values(array_filter($elements, fn ($e) => $e instanceof Image));
        $this->assertCount(1, $images);

        $image = $images[0];
        $this->assertTrue($image->hasData());
        $this->assertSame('image/png', $image->getMimeType());
        $this->assertGreaterThan(0, $image->getDataSize());
        $this->assertSame(100, $image->getWidth());
        $this->assertSame(50, $image->getHeight());
        $this->assertSame('Test Image', $image->getAlt());
        $this->assertNotNull($image->getDataUri());
        $this->assertStringStartsWith('data:image/png;base64,', $image->getDataUri());
    }

    public function test_parse_real_docx(): void
    {
        $realPath = dirname(__DIR__, 4) . '/public/docs/Astérisque.docx';

        if (! file_exists($realPath)) {
            $this->markTestSkipped('Fichier Astérisque.docx non disponible');
        }

        $doc = $this->parser->parse($realPath);

        $this->assertStringContainsString('RAPPORT D', $doc->getTitle());
        $this->assertStringContainsString('AUDIT TECHNIQUE', $doc->getTitle());
        $this->assertGreaterThanOrEqual(1, count($doc->getSections()));

        $texts = $this->collectText($doc);
        $this->assertGreaterThan(1000, strlen($texts));
        $this->assertStringContainsString('audit', strtolower($texts));
    }

    /* =============================================================
     | Helpers — DOCX creation
     |============================================================= */

    private function createMinimalDocx(string $text): string
    {
        $path = $this->tmpDir . '/test.docx';

        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?>
            <Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
                <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
                <Default Extension="xml" ContentType="application/xml"/>
                <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
            </Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?>
            <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
                <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
            </Relationships>');

        $zip->addFromString('word/document.xml', '<?xml version="1.0" encoding="UTF-8"?>
            <w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
                <w:body>
                    <w:p><w:r><w:t>' . htmlspecialchars($text) . '</w:t></w:r></w:p>
                </w:body>
            </w:document>');

        $zip->close();

        return $path;
    }

    private function createDocxWithMetadata(string $title, string $author): string
    {
        $path = $this->tmpDir . '/meta.docx';

        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?>
            <Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
                <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
                <Default Extension="xml" ContentType="application/xml"/>
                <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
                <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
            </Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?>
            <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
                <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
                <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
            </Relationships>');

        $zip->addFromString('docProps/core.xml', '<?xml version="1.0" encoding="UTF-8"?>
            <cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties"
                               xmlns:dc="http://purl.org/dc/elements/1.1/"
                               xmlns:dcterms="http://purl.org/dc/terms/">
                <dc:title>' . htmlspecialchars($title) . '</dc:title>
                <dc:creator>' . htmlspecialchars($author) . '</dc:creator>
            </cp:coreProperties>');

        $zip->addFromString('word/document.xml', '<?xml version="1.0" encoding="UTF-8"?>
            <w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
                <w:body>
                    <w:p><w:r><w:t>Contenu du document</w:t></w:r></w:p>
                </w:body>
            </w:document>');

        $zip->close();

        return $path;
    }

    private function createStyledDocx(): string
    {
        $path = $this->tmpDir . '/styled.docx';

        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?>
            <Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
                <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
                <Default Extension="xml" ContentType="application/xml"/>
                <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
            </Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?>
            <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
                <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
            </Relationships>');

        $zip->addFromString('word/document.xml', '<?xml version="1.0" encoding="UTF-8"?>
            <w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
                <w:body>
                    <w:p>
                        <w:r>
                            <w:rPr><w:b/></w:rPr>
                            <w:t>Texte gras</w:t>
                        </w:r>
                        <w:r>
                            <w:t xml:space="preserve"> puis </w:t>
                        </w:r>
                        <w:r>
                            <w:rPr><w:i/></w:rPr>
                            <w:t>italique</w:t>
                        </w:r>
                        <w:r>
                            <w:t xml:space="preserve"> puis </w:t>
                        </w:r>
                        <w:r>
                            <w:rPr><w:u w:val="single"/><w:color w:val="FF0000"/><w:sz w:val="28"/></w:rPr>
                            <w:t>souligné rouge 14pt</w:t>
                        </w:r>
                    </w:p>
                </w:body>
            </w:document>');

        $zip->close();

        return $path;
    }

    private function createDocxWithTable(): string
    {
        $path = $this->tmpDir . '/table.docx';

        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?>
            <Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
                <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
                <Default Extension="xml" ContentType="application/xml"/>
                <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
            </Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?>
            <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
                <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
            </Relationships>');

        $zip->addFromString('word/document.xml', '<?xml version="1.0" encoding="UTF-8"?>
            <w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
                <w:body>
                    <w:tbl>
                        <w:tr>
                            <w:trPr><w:tblHeader/></w:trPr>
                            <w:tc><w:p><w:r><w:t>Nom</w:t></w:r></w:p></w:tc>
                            <w:tc><w:p><w:r><w:t>Valeur</w:t></w:r></w:p></w:tc>
                        </w:tr>
                        <w:tr>
                            <w:tc><w:p><w:r><w:t>Alpha</w:t></w:r></w:p></w:tc>
                            <w:tc><w:p><w:r><w:t>100</w:t></w:r></w:p></w:tc>
                        </w:tr>
                        <w:tr>
                            <w:tc><w:p><w:r><w:t>Beta</w:t></w:r></w:p></w:tc>
                            <w:tc><w:p><w:r><w:t>200</w:t></w:r></w:p></w:tc>
                        </w:tr>
                    </w:tbl>
                </w:body>
            </w:document>');

        $zip->close();

        return $path;
    }

    private function createDocxWithHeadings(): string
    {
        $path = $this->tmpDir . '/headings.docx';

        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?>
            <Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
                <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
                <Default Extension="xml" ContentType="application/xml"/>
                <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
            </Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?>
            <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
                <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
            </Relationships>');

        $zip->addFromString('word/document.xml', '<?xml version="1.0" encoding="UTF-8"?>
            <w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
                <w:body>
                    <w:p>
                        <w:pPr><w:pStyle w:val="Heading1"/></w:pPr>
                        <w:r><w:t>Titre Principal</w:t></w:r>
                    </w:p>
                    <w:p>
                        <w:r><w:t>Un paragraphe de texte.</w:t></w:r>
                    </w:p>
                    <w:p>
                        <w:pPr><w:pStyle w:val="Heading2"/></w:pPr>
                        <w:r><w:t>Sous-titre</w:t></w:r>
                    </w:p>
                    <w:p>
                        <w:r><w:t>Encore du texte.</w:t></w:r>
                    </w:p>
                </w:body>
            </w:document>');

        $zip->close();

        return $path;
    }

    private function createDocxWithImage(): string
    {
        $path = $this->tmpDir . '/image.docx';

        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?>
            <Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
                <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
                <Default Extension="xml" ContentType="application/xml"/>
                <Default Extension="png" ContentType="image/png"/>
                <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
            </Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?>
            <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
                <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
            </Relationships>');

        $zip->addFromString('word/_rels/document.xml.rels', '<?xml version="1.0" encoding="UTF-8"?>
            <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
                <Relationship Id="rId10" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/image1.png"/>
            </Relationships>');

        $zip->addFromString('word/document.xml', '<?xml version="1.0" encoding="UTF-8"?>
            <w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
                        xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"
                        xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"
                        xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"
                        xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">
                <w:body>
                    <w:p><w:r><w:t>Avant image</w:t></w:r></w:p>
                    <w:p>
                        <w:r>
                            <w:drawing>
                                <wp:inline>
                                    <wp:extent cx="952500" cy="476250"/>
                                    <wp:docPr id="1" name="Image 1" descr="Test Image"/>
                                    <a:graphic>
                                        <a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">
                                            <pic:pic>
                                                <pic:blipFill>
                                                    <a:blip r:embed="rId10"/>
                                                </pic:blipFill>
                                            </pic:pic>
                                        </a:graphicData>
                                    </a:graphic>
                                </wp:inline>
                            </w:drawing>
                        </w:r>
                    </w:p>
                    <w:p><w:r><w:t>Après image</w:t></w:r></w:p>
                </w:body>
            </w:document>');

        $zip->addFromString('word/media/image1.png', $this->createMinimalPng());

        $zip->close();

        return $path;
    }

    private function createMinimalPng(): string
    {
        $img = imagecreatetruecolor(4, 4);
        $red = imagecolorallocate($img, 255, 0, 0);
        imagefill($img, 0, 0, $red);
        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        unset($img);

        return $data;
    }

    private function collectText(\Paperdoc\Contracts\DocumentInterface $doc): string
    {
        $text = '';

        foreach ($doc->getSections() as $section) {
            foreach ($section->getElements() as $el) {
                if ($el instanceof Paragraph) {
                    $text .= $el->getPlainText() . "\n";
                } elseif ($el instanceof Table) {
                    foreach ($el->getRows() as $row) {
                        foreach ($row->getCells() as $cell) {
                            $text .= $cell->getPlainText() . ' ';
                        }
                        $text .= "\n";
                    }
                }
            }
        }

        return $text;
    }
}
