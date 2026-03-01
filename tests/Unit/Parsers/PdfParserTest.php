<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Parsers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\ParserInterface;
use Paperdoc\Document\Paragraph;
use Paperdoc\Parsers\PdfParser;
use Paperdoc\Support\DocumentManager;

class PdfParserTest extends TestCase
{
    private PdfParser $parser;

    private string $tmpDir;

    protected function setUp(): void
    {
        $this->parser = new PdfParser();
        $this->tmpDir = sys_get_temp_dir() . '/paperdoc_pdf_parser_tests_' . uniqid();
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

    public function test_supports_pdf(): void
    {
        $this->assertTrue($this->parser->supports('pdf'));
        $this->assertTrue($this->parser->supports('PDF'));
        $this->assertFalse($this->parser->supports('html'));
        $this->assertFalse($this->parser->supports('docx'));
    }

    public function test_nonexistent_file_throws(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->parser->parse('/nonexistent/file.pdf');
    }

    public function test_invalid_pdf_throws(): void
    {
        $path = $this->tmpDir . '/invalid.pdf';
        file_put_contents($path, 'not a pdf');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('PDF valide');
        $this->parser->parse($path);
    }

    public function test_parse_paperdoc_generated_pdf(): void
    {
        $doc = DocumentManager::create('pdf', 'Test PDF Parser');
        $section = \Paperdoc\Document\Section::make('content');
        $section->addHeading('Titre de Test', 1);
        $section->addText('Premier paragraphe de contenu.');
        $section->addText('Deuxième paragraphe.');
        $doc->addSection($section);

        $pdfPath = $this->tmpDir . '/generated.pdf';
        DocumentManager::save($doc, $pdfPath, 'pdf');

        $parsed = $this->parser->parse($pdfPath);

        $this->assertSame('pdf', $parsed->getFormat());
        $this->assertGreaterThanOrEqual(1, count($parsed->getSections()));

        $texts = $this->collectText($parsed);

        $this->assertStringContainsString('Titre de Test', $texts);
        $this->assertStringContainsString('Premier paragraphe', $texts);
    }

    public function test_parse_pdf_with_table(): void
    {
        $doc = DocumentManager::create('pdf', 'Table Test');
        $section = \Paperdoc\Document\Section::make('data');

        $table = \Paperdoc\Document\Table::make();
        $table->setHeaders(['Produit', 'Prix', 'Stock']);
        $table->addRowFromArray(['Widget A', '29.99', '150']);
        $table->addRowFromArray(['Widget B', '49.99', '75']);
        $section->addElement($table);

        $doc->addSection($section);

        $pdfPath = $this->tmpDir . '/table.pdf';
        DocumentManager::save($doc, $pdfPath);

        $parsed = $this->parser->parse($pdfPath);

        $texts = $this->collectText($parsed);

        $this->assertStringContainsString('Produit', $texts);
        $this->assertStringContainsString('Widget A', $texts);
        $this->assertStringContainsString('29.99', $texts);

        $elements = $parsed->getSections()[0]->getElements();
        $tables = array_values(array_filter($elements, fn ($e) => $e instanceof \Paperdoc\Document\Table));

        if (count($tables) > 0) {
            $parsedTable = $tables[0];
            $this->assertGreaterThanOrEqual(2, count($parsedTable->getRows()));
            $this->assertTrue($parsedTable->getRows()[0]->isHeader());
        }
    }

    public function test_parse_multi_page_pdf(): void
    {
        $doc = DocumentManager::create('pdf', 'Multi Page');

        $s1 = \Paperdoc\Document\Section::make('page1');
        $s1->addHeading('Page Un', 1);
        $s1->addText('Contenu de la première page.');
        $doc->addSection($s1);

        $s2 = \Paperdoc\Document\Section::make('page2');
        $s2->addHeading('Page Deux', 1);
        $s2->addText('Contenu de la deuxième page.');
        $doc->addSection($s2);

        $pdfPath = $this->tmpDir . '/multi.pdf';
        DocumentManager::save($doc, $pdfPath);

        $parsed = $this->parser->parse($pdfPath);

        $this->assertGreaterThanOrEqual(2, count($parsed->getSections()));

        $texts = $this->collectText($parsed);
        $this->assertStringContainsString('Page Un', $texts);
        $this->assertStringContainsString('Page Deux', $texts);
    }

    public function test_parse_real_pdf(): void
    {
        $realPath = dirname(__DIR__, 4) . '/public/docs/document.pdf';

        if (! file_exists($realPath)) {
            $this->markTestSkipped('Fichier document.pdf non disponible');
        }

        $doc = $this->parser->parse($realPath);

        $this->assertGreaterThanOrEqual(1, count($doc->getSections()));

        $texts = $this->collectText($doc);

        $this->assertStringContainsString('Devis', $texts);
        $this->assertStringContainsString('D2026-00193', $texts);
        $this->assertStringContainsString('27/02/2026', $texts);
        $this->assertStringContainsString('Zerarka', $texts);
    }

    public function test_pdf_roundtrip_text_preserved(): void
    {
        $doc = DocumentManager::create('pdf', 'Roundtrip');
        $section = \Paperdoc\Document\Section::make('main');
        $section->addText('Bonjour le monde');
        $section->addText('Test de roundtrip');
        $doc->addSection($section);

        $pdfPath = $this->tmpDir . '/roundtrip.pdf';
        DocumentManager::save($doc, $pdfPath);

        $parsed = $this->parser->parse($pdfPath);
        $texts = $this->collectText($parsed);

        $this->assertStringContainsString('Bonjour le monde', $texts);
        $this->assertStringContainsString('roundtrip', $texts);
    }

    public function test_format_is_pdf(): void
    {
        $doc = DocumentManager::create('pdf', 'Format');
        $section = \Paperdoc\Document\Section::make('main');
        $section->addText('Test');
        $doc->addSection($section);

        $pdfPath = $this->tmpDir . '/format.pdf';
        DocumentManager::save($doc, $pdfPath);

        $parsed = $this->parser->parse($pdfPath);
        $this->assertSame('pdf', $parsed->getFormat());
    }

    private function collectText(\Paperdoc\Contracts\DocumentInterface $doc): string
    {
        $text = '';

        foreach ($doc->getSections() as $section) {
            foreach ($section->getElements() as $el) {
                if ($el instanceof Paragraph) {
                    $text .= $el->getPlainText() . "\n";
                } elseif ($el instanceof \Paperdoc\Document\Table) {
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
