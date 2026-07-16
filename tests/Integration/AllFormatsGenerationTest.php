<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Paperdoc\Tests\Support\InflatesPdfStreams;
use Paperdoc\Contracts\DocumentInterface;
use Paperdoc\Document\{ListBlock, Paragraph, Section, Table, TextRun};
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Document\Style\{ParagraphStyle, TableStyle, TextStyle};
use Paperdoc\Enum\Alignment;
use Paperdoc\Factory\DocumentFactory;
use Paperdoc\Support\DocumentManager;

/**
 * Génération réelle de documents pour chaque format supporté,
 * incluant listes (PR #7) et contenu riche (tableaux, liens, styles).
 */
class AllFormatsGenerationTest extends TestCase
{
    use InflatesPdfStreams;
    private string $outputDir;

    protected function setUp(): void
    {
        $this->outputDir = sys_get_temp_dir() . '/paperdoc_all_formats_' . uniqid();
        mkdir($this->outputDir, 0755, true);
    }

    protected function tearDown(): void
    {
        $this->removeDir($this->outputDir);
    }

    /* =============================================================
     | Génération — tous les formats
     |============================================================= */

    /**
     * @return iterable<string, array{string}>
     */
    public static function supportedFormatsProvider(): iterable
    {
        foreach (DocumentFactory::getSupportedRendererFormats() as $format) {
            yield $format => [$format];
        }
    }

    #[DataProvider('supportedFormatsProvider')]
    public function test_generates_real_file_for_format(string $format): void
    {
        $doc = $this->buildRichDocument();
        $path = $this->outputDir . '/sample.' . $format;

        DocumentManager::save($doc, $path, $format);

        $this->assertFileExists($path);
        $this->assertGreaterThan(0, filesize($path));

        $content = file_get_contents($path) ?: '';

        match ($format) {
            'pdf'      => $this->assertPdfOutput($content),
            'html'     => $this->assertHtmlOutput($content),
            'csv'      => $this->assertCsvOutput($content),
            'md', 'markdown' => $this->assertMarkdownOutput($content),
            'docx'     => $this->assertDocxOutput($path, $content),
            'doc'      => $this->assertOle2Output($content, 'Rapport Paperdoc'),
            'xlsx'     => $this->assertXlsxOutput($path),
            'xls'      => $this->assertXlsOutput($path, $content),
            'pptx'     => $this->assertPptxOutput($path),
            'ppt'      => $this->assertOle2Output($content, 'Rapport Paperdoc'),
            default    => $this->fail("Format non couvert par les assertions : {$format}"),
        };
    }

    /* =============================================================
     | Roundtrips — listes (PR #7)
     |============================================================= */

    public function test_docx_roundtrip_preserves_bullet_and_numbered_lists(): void
    {
        $doc = $this->buildRichDocument();
        $path = $this->outputDir . '/lists.docx';

        DocumentManager::save($doc, $path, 'docx');
        $parsed = DocumentManager::open($path);

        $lists = $this->collectLists($parsed);
        $this->assertCount(2, $lists);

        $bullet = $lists[0];
        $this->assertTrue($bullet->isBullet());
        $this->assertSame('Premier élément', $bullet->getItems()[0]->getPlainText());
        $this->assertSame('Deuxième élément', $bullet->getItems()[1]->getPlainText());

        $ordered = $lists[1];
        $this->assertTrue($ordered->isOrdered());
        $this->assertSame('Étape 1', $ordered->getItems()[0]->getPlainText());
        $this->assertSame('Étape 2', $ordered->getItems()[1]->getPlainText());
    }

    public function test_markdown_roundtrip_preserves_list_text(): void
    {
        $doc = $this->buildRichDocument();
        $path = $this->outputDir . '/lists.md';

        DocumentManager::save($doc, $path, 'md');
        $markdown = file_get_contents($path) ?: '';

        $this->assertMarkdownOutput($markdown);

        $parsed = DocumentManager::open($path);
        $text = $this->collectPlainText($parsed);

        $this->assertStringContainsString('Premier élément', $text);
        $this->assertStringContainsString('Deuxième élément', $text);
        $this->assertStringContainsString('Étape 1', $text);
        $this->assertStringContainsString('Étape 2', $text);
    }

    public function test_pdf_generation_preserves_lists_and_table_content(): void
    {
        $doc = $this->buildRichDocument();
        $path = $this->outputDir . '/rich.pdf';

        DocumentManager::save($doc, $path, 'pdf');
        $content = $this->inflatePdf(file_get_contents($path) ?: '');

        $this->assertPdfOutput($content);
        $this->assertStringContainsString('paperdoc.dev', $content);
        $this->assertStringContainsString('(CA) Tj', $content);

        $parsed = DocumentManager::open($path);
        $text = $this->collectPlainText($parsed);

        $this->assertStringContainsString('Premier', $text);
        $this->assertStringContainsString('tape 1', $text);
        $this->assertStringContainsString('120 000', $text);
        $this->assertStringContainsString('Métrique', $text);
    }

    public function test_html_roundtrip_preserves_content_and_hyperlink(): void
    {
        $doc = $this->buildRichDocument();
        $path = $this->outputDir . '/rich.html';

        DocumentManager::save($doc, $path, 'html');
        $html = file_get_contents($path) ?: '';
        $this->assertHtmlOutput($html);

        $parsed = DocumentManager::open($path);
        $text = $this->collectPlainText($parsed);

        $this->assertStringContainsString('Premier élément', $text);
        $this->assertStringContainsString('Étape 1', $text);
        $this->assertStringContainsString('Métrique', $text);
        $this->assertStringContainsString('href="https://paperdoc.dev"', $html);
    }

    /* =============================================================
     | Conversions croisées
     |============================================================= */

    public function test_convert_rich_docx_to_pdf_html_and_markdown(): void
    {
        $source = $this->outputDir . '/source.docx';
        DocumentManager::save($this->buildRichDocument(), $source, 'docx');

        $pdfPath = $this->outputDir . '/from_docx.pdf';
        $htmlPath = $this->outputDir . '/from_docx.html';
        $mdPath = $this->outputDir . '/from_docx.md';

        DocumentManager::convert($source, $pdfPath, 'pdf');
        DocumentManager::convert($source, $htmlPath, 'html');
        DocumentManager::convert($source, $mdPath, 'md');

        $this->assertPdfOutput(file_get_contents($pdfPath) ?: '');
        $this->assertHtmlOutput(file_get_contents($htmlPath) ?: '');
        $this->assertMarkdownOutput(file_get_contents($mdPath) ?: '');
    }

    /* =============================================================
     | Assertions par format
     |============================================================= */

    private function assertPdfOutput(string $content): void
    {
        $content = $this->inflatePdf($content);
        $this->assertStringStartsWith('%PDF-1.4', $content);
        $this->assertStringContainsString('/Type /Catalog', $content);
        $this->assertStringContainsString('Rapport Paperdoc', $content);
    }

    private function assertHtmlOutput(string $content): void
    {
        $this->assertStringContainsString('<!DOCTYPE html>', $content);
        $this->assertStringContainsString('Rapport Paperdoc', $content);
        $this->assertStringContainsString('<ul>', $content);
        $this->assertStringContainsString('<ol>', $content);
        $this->assertStringContainsString('Premier élément', $content);
        $this->assertStringContainsString('Étape 1', $content);
        $this->assertStringContainsString('<table', $content);
        $this->assertStringContainsString('Métrique', $content);
        $this->assertStringContainsString('href="https://paperdoc.dev"', $content);
    }

    private function assertCsvOutput(string $content): void
    {
        $this->assertStringContainsString('Métrique', $content);
        $this->assertStringContainsString('Valeur', $content);
        $this->assertStringContainsString('120 000', $content);
        $this->assertStringContainsString('CA', $content);
    }

    private function assertMarkdownOutput(string $content): void
    {
        $this->assertStringContainsString('# Rapport Paperdoc', $content);
        $this->assertStringContainsString('- Premier élément', $content);
        $this->assertStringContainsString('- Deuxième élément', $content);
        $this->assertStringContainsString('1. Étape 1', $content);
        $this->assertStringContainsString('2. Étape 2', $content);
        $this->assertStringContainsString('| Métrique |', $content);
        $this->assertStringContainsString('[paperdoc.dev](https://paperdoc.dev)', $content);
    }

    private function assertDocxOutput(string $path, string $content): void
    {
        $this->assertStringStartsWith('PK', $content);

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($path));

        foreach (['[Content_Types].xml', 'word/document.xml', 'word/numbering.xml'] as $part) {
            $this->assertNotFalse($zip->locateName($part), "Partie manquante : {$part}");
        }

        $documentXml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();

        $this->assertStringContainsString('Rapport Paperdoc', $documentXml);
        $this->assertStringContainsString('Premier élément', $documentXml);
        $this->assertStringContainsString('Étape 1', $documentXml);
        $this->assertStringContainsString('<w:numPr>', $documentXml);
        $this->assertStringContainsString('Métrique', $documentXml);
        $this->assertStringContainsString('<w:hyperlink', $documentXml);
        $this->assertStringContainsString('paperdoc.dev', $documentXml);
    }

    private function assertXlsxOutput(string $path): void
    {
        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($path));

        foreach (['[Content_Types].xml', 'xl/workbook.xml', 'xl/worksheets/sheet1.xml', 'xl/sharedStrings.xml'] as $part) {
            $this->assertNotFalse($zip->locateName($part), "Partie manquante : {$part}");
        }

        $sharedStrings = $zip->getFromName('xl/sharedStrings.xml') ?: '';
        $zip->close();

        $this->assertStringContainsString('Métrique', $sharedStrings);
        $this->assertStringContainsString('120 000', $sharedStrings);
    }

    private function assertPptxOutput(string $path): void
    {
        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($path));

        $this->assertNotFalse($zip->locateName('[Content_Types].xml'));
        $this->assertNotFalse($zip->locateName('ppt/presentation.xml'));
        $this->assertNotFalse($zip->locateName('ppt/slides/slide1.xml'));

        $slideXml = $zip->getFromName('ppt/slides/slide1.xml') ?: '';
        $zip->close();

        $this->assertStringContainsString('Rapport Paperdoc', $slideXml);
        $this->assertStringContainsString('Métrique', $slideXml);
    }

    private function assertOle2Output(string $content, string $expectedText): void
    {
        $this->assertStringStartsWith("\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1", $content);
        $this->assertStringContainsString($expectedText, $content);
    }

    private function assertXlsOutput(string $path, string $content): void
    {
        $this->assertOle2Output($content, 'Rapport Paperdoc');

        // Les chaînes BIFF8 sont stockées en UTF-16LE dans le flux OLE2.
        $utf16Metric = mb_convert_encoding('Métrique', 'UTF-16LE', 'UTF-8');
        $utf16Value = mb_convert_encoding('120 000', 'UTF-16LE', 'UTF-8');

        $this->assertStringContainsString($utf16Metric, $content);
        $this->assertStringContainsString($utf16Value, $content);
    }

    /* =============================================================
     | Document riche partagé
     |============================================================= */

    private function buildRichDocument(): DocumentInterface
    {
        $doc = DocumentManager::create('pdf', 'Rapport Paperdoc');
        $doc->setMetadata('author', 'Paperdoc Test Suite');

        $section = Section::make('contenu');
        $section->addHeading('Rapport Paperdoc', 1);

        $boldStyle = TextStyle::make()->setBold()->setColor('#1A5276');
        $paraStyle = ParagraphStyle::make()->setAlignment(Alignment::JUSTIFY)->setSpaceAfter(12);
        $paragraph = Paragraph::make($paraStyle);
        $paragraph->addRun(TextRun::make('Ce document présente '));
        $paragraph->addRun(TextRun::make('les résultats clés', $boldStyle));
        $paragraph->addRun(TextRun::make(' — visitez '));
        $paragraph->addRun(TextRun::make('paperdoc.dev', null, TextLink::make('https://paperdoc.dev')));
        $paragraph->addRun(TextRun::make(' pour en savoir plus.'));
        $section->addElement($paragraph);

        $bulletList = $section->addBulletList();
        $bulletList->addText('Premier élément');
        $bulletList->addText('Deuxième élément');

        $orderedList = $section->addOrderedList();
        $orderedList->addText('Étape 1');
        $orderedList->addText('Étape 2');

        $table = Table::make(TableStyle::make()->setCellPadding(6)->setHeaderBg('#e5e7eb'));
        $table->setHeaders(['Métrique', 'Valeur', 'Tendance']);
        $table->addRowFromArray(['CA', '120 000 €', '+12%']);
        $table->addRowFromArray(['Clients', '34', '+5%']);
        $section->addElement($table);

        $section->addText('Document généré par la suite de tests Paperdoc.');

        $doc->addSection($section);

        return $doc;
    }

    /* =============================================================
     | Helpers
     |============================================================= */

    /** @return ListBlock[] */
    private function collectLists(DocumentInterface $document): array
    {
        $lists = [];

        foreach ($document->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof ListBlock) {
                    $lists[] = $element;
                }
            }
        }

        return $lists;
    }

    private function collectPlainText(DocumentInterface $document): string
    {
        $parts = [];

        foreach ($document->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof Paragraph) {
                    $parts[] = $element->getPlainText();
                } elseif ($element instanceof ListBlock) {
                    foreach ($element->getItems() as $item) {
                        $parts[] = $item->getPlainText();
                    }
                } elseif ($element instanceof Table) {
                    foreach ($element->getRows() as $row) {
                        foreach ($row->getCells() as $cell) {
                            $parts[] = $cell->getPlainText();
                        }
                    }
                }
            }
        }

        return implode("\n", $parts);
    }

    private function removeDir(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        foreach (glob($dir . '/*') ?: [] as $file) {
            @unlink($file);
        }

        @rmdir($dir);
    }
}
