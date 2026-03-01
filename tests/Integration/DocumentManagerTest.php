<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\{Image, Paragraph, Section, Table, TextRun};
use Paperdoc\Document\Style\{ParagraphStyle, TableStyle, TextStyle};
use Paperdoc\Enum\Alignment;
use Paperdoc\Support\DocumentManager;

class DocumentManagerTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/paperdoc_integration_' . uniqid();
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

    /* -------------------------------------------------------------
     | Create
     |------------------------------------------------------------- */

    public function test_create_document(): void
    {
        $doc = DocumentManager::create('pdf', 'Mon Rapport');

        $this->assertSame('pdf', $doc->getFormat());
        $this->assertSame('Mon Rapport', $doc->getTitle());
    }

    /* -------------------------------------------------------------
     | Save PDF
     |------------------------------------------------------------- */

    public function test_save_pdf(): void
    {
        $doc = DocumentManager::create('pdf', 'PDF Test');
        $section = Section::make('s1');
        $section->addHeading('Titre', 1);
        $section->addText('Contenu du paragraphe.');
        $doc->addSection($section);

        $path = $this->tmpDir . '/test.pdf';
        DocumentManager::save($doc, $path);

        $this->assertFileExists($path);
        $this->assertStringStartsWith('%PDF-1.4', file_get_contents($path));
    }

    /* -------------------------------------------------------------
     | Save HTML
     |------------------------------------------------------------- */

    public function test_save_html(): void
    {
        $doc = DocumentManager::create('html', 'HTML Test');
        $section = Section::make('s1');
        $section->addText('Hello HTML');
        $doc->addSection($section);

        $path = $this->tmpDir . '/test.html';
        DocumentManager::save($doc, $path);

        $this->assertFileExists($path);
        $this->assertStringContainsString('<!DOCTYPE html>', file_get_contents($path));
    }

    /* -------------------------------------------------------------
     | Save CSV
     |------------------------------------------------------------- */

    public function test_save_csv(): void
    {
        $doc = DocumentManager::create('csv', 'CSV Test');
        $section = Section::make('data');
        $table = Table::make();
        $table->setHeaders(['A', 'B']);
        $table->addRowFromArray(['1', '2']);
        $section->addElement($table);
        $doc->addSection($section);

        $path = $this->tmpDir . '/test.csv';
        DocumentManager::save($doc, $path);

        $this->assertFileExists($path);
    }

    /* -------------------------------------------------------------
     | Save with format override
     |------------------------------------------------------------- */

    public function test_save_with_format_override(): void
    {
        $doc = DocumentManager::create('pdf', 'Override');
        $section = Section::make('s1');
        $section->addText('Content');
        $doc->addSection($section);

        $path = $this->tmpDir . '/output.html';
        DocumentManager::save($doc, $path, 'html');

        $content = file_get_contents($path);
        $this->assertStringContainsString('<!DOCTYPE html>', $content);
        $this->assertStringContainsString('Content', $content);
    }

    /* -------------------------------------------------------------
     | Open + Parse HTML
     |------------------------------------------------------------- */

    public function test_open_html(): void
    {
        $htmlPath = $this->tmpDir . '/source.html';
        file_put_contents($htmlPath, '<html><head><title>Parsed</title></head><body><p>Hello</p></body></html>');

        $doc = DocumentManager::open($htmlPath);

        $this->assertSame('html', $doc->getFormat());
        $this->assertSame('Parsed', $doc->getTitle());
    }

    /* -------------------------------------------------------------
     | Open + Parse CSV
     |------------------------------------------------------------- */

    public function test_open_csv(): void
    {
        $csvPath = $this->tmpDir . '/data.csv';
        file_put_contents($csvPath, "Name,Value\nAlice,100\n");

        $doc = DocumentManager::open($csvPath);

        $this->assertSame('csv', $doc->getFormat());
        $this->assertCount(1, $doc->getSections());
    }

    /* -------------------------------------------------------------
     | Convert HTML → PDF
     |------------------------------------------------------------- */

    public function test_convert_html_to_pdf(): void
    {
        $htmlPath = $this->tmpDir . '/source.html';
        file_put_contents($htmlPath, '
            <html><head><title>Conversion</title></head>
            <body><p>Paragraph to convert</p></body></html>
        ');

        $pdfPath = $this->tmpDir . '/converted.pdf';
        DocumentManager::convert($htmlPath, $pdfPath, 'pdf');

        $this->assertFileExists($pdfPath);
        $this->assertStringStartsWith('%PDF-1.4', file_get_contents($pdfPath));
    }

    /* -------------------------------------------------------------
     | Convert CSV → HTML
     |------------------------------------------------------------- */

    public function test_convert_csv_to_html(): void
    {
        $csvPath = $this->tmpDir . '/data.csv';
        file_put_contents($csvPath, "Col1,Col2\nA,B\nC,D\n");

        $htmlPath = $this->tmpDir . '/from_csv.html';
        DocumentManager::convert($csvPath, $htmlPath, 'html');

        $this->assertFileExists($htmlPath);
        $html = file_get_contents($htmlPath);
        $this->assertStringContainsString('<!DOCTYPE html>', $html);
        $this->assertStringContainsString('Col1', $html);
        $this->assertStringContainsString('A', $html);
    }

    /* -------------------------------------------------------------
     | Convert CSV → PDF
     |------------------------------------------------------------- */

    public function test_convert_csv_to_pdf(): void
    {
        $csvPath = $this->tmpDir . '/data.csv';
        file_put_contents($csvPath, "Metric,Value\nCA,120000\nClients,34\n");

        $pdfPath = $this->tmpDir . '/from_csv.pdf';
        DocumentManager::convert($csvPath, $pdfPath, 'pdf');

        $this->assertFileExists($pdfPath);
        $this->assertStringStartsWith('%PDF-1.4', file_get_contents($pdfPath));
    }

    /* -------------------------------------------------------------
     | HTML Roundtrip (Save → Parse → Save)
     |------------------------------------------------------------- */

    public function test_html_roundtrip(): void
    {
        $doc = DocumentManager::create('html', 'Roundtrip');
        $section = Section::make('intro');
        $section->addHeading('Titre du document', 1);
        $section->addText('Paragraphe important.');

        $table = Table::make();
        $table->setHeaders(['Nom', 'Valeur']);
        $table->addRowFromArray(['CA', '120k']);
        $section->addElement($table);

        $doc->addSection($section);

        $htmlPath = $this->tmpDir . '/roundtrip.html';
        DocumentManager::save($doc, $htmlPath);

        $parsed = DocumentManager::open($htmlPath);

        $this->assertSame('Roundtrip', $parsed->getTitle());
        $this->assertGreaterThanOrEqual(1, count($parsed->getSections()));

        $elements = $parsed->getSections()[0]->getElements();
        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $tables = array_values(array_filter($elements, fn ($e) => $e instanceof Table));

        $this->assertNotEmpty($paragraphs);
        $this->assertNotEmpty($tables);
    }

    /* -------------------------------------------------------------
     | CSV Roundtrip (Save → Parse → Verify)
     |------------------------------------------------------------- */

    public function test_csv_roundtrip(): void
    {
        $doc = DocumentManager::create('csv', 'Ventes');
        $section = Section::make('data');
        $table = Table::make();
        $table->setHeaders(['Mois', 'Revenus']);
        $table->addRowFromArray(['Janvier', '50000']);
        $table->addRowFromArray(['Février', '60000']);
        $section->addElement($table);
        $doc->addSection($section);

        $csvPath = $this->tmpDir . '/roundtrip.csv';
        DocumentManager::save($doc, $csvPath);

        $parsed = DocumentManager::open($csvPath);
        $elements = $parsed->getSections()[0]->getElements();
        $tables = array_values(array_filter($elements, fn ($e) => $e instanceof Table));

        $this->assertCount(1, $tables);
        $rows = $tables[0]->getRows();
        $this->assertCount(3, $rows);
        $this->assertTrue($rows[0]->isHeader());
        $this->assertSame('Mois', $rows[0]->getCells()[0]->getPlainText());
        $this->assertSame('Janvier', $rows[1]->getCells()[0]->getPlainText());
        $this->assertSame('60000', $rows[2]->getCells()[1]->getPlainText());
    }

    /* -------------------------------------------------------------
     | Complex Document
     |------------------------------------------------------------- */

    public function test_complex_document_to_all_formats(): void
    {
        $doc = DocumentManager::create('pdf', 'Rapport Complet');
        $doc->setMetadata('author', 'Akram');

        $intro = Section::make('intro');
        $intro->addHeading('Introduction', 1);

        $boldStyle = TextStyle::make()->setBold()->setColor('#1A5276');
        $paraStyle = ParagraphStyle::make()->setAlignment(Alignment::JUSTIFY);
        $p = Paragraph::make($paraStyle);
        $p->addRun(TextRun::make('Ce document présente '));
        $p->addRun(TextRun::make('les résultats clés', $boldStyle));
        $p->addRun(TextRun::make(' du mois.'));
        $intro->addElement($p);

        $table = Table::make();
        $table->setHeaders(['Métrique', 'Valeur', 'Tendance']);
        $table->addRowFromArray(['CA', '120 000 €', '+12%']);
        $table->addRowFromArray(['Clients', '34', '+5%']);
        $table->addRowFromArray(['Satisfaction', '94%', '→']);
        $intro->addElement($table);

        $doc->addSection($intro);

        $conclusion = Section::make('conclusion');
        $conclusion->addHeading('Conclusion', 2);
        $conclusion->addText('Objectifs atteints.');
        $doc->addSection($conclusion);

        $pdfPath = $this->tmpDir . '/rapport.pdf';
        DocumentManager::save($doc, $pdfPath);
        $this->assertFileExists($pdfPath);
        $this->assertStringStartsWith('%PDF-1.4', file_get_contents($pdfPath));

        $htmlPath = $this->tmpDir . '/rapport.html';
        DocumentManager::save($doc, $htmlPath, 'html');
        $htmlContent = file_get_contents($htmlPath);
        $this->assertStringContainsString('Rapport Complet', $htmlContent);
        $this->assertStringContainsString('les résultats clés', $htmlContent);
        $this->assertStringContainsString('120 000', $htmlContent);

        $csvPath = $this->tmpDir . '/rapport.csv';
        DocumentManager::save($doc, $csvPath, 'csv');
        $this->assertFileExists($csvPath);
    }

    /* -------------------------------------------------------------
     | Register Renderer
     |------------------------------------------------------------- */

    public function test_register_custom_renderer(): void
    {
        DocumentManager::registerRenderer('txt', \Paperdoc\Renderers\CsvRenderer::class);

        $doc = DocumentManager::create('txt');
        $section = Section::make('data');
        $table = Table::make();
        $table->addRowFromArray(['Test']);
        $section->addElement($table);
        $doc->addSection($section);

        $path = $this->tmpDir . '/custom.txt';
        DocumentManager::save($doc, $path, 'txt');

        $this->assertFileExists($path);
    }

    /* -------------------------------------------------------------
     | Error Cases
     |------------------------------------------------------------- */

    public function test_open_unsupported_format_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DocumentManager::open('/path/to/file.xyz');
    }

    public function test_save_unsupported_format_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $doc = DocumentManager::create('pdf');
        DocumentManager::save($doc, '/tmp/out.odt', 'odt');
    }
}
