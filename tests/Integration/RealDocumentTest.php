<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\{Paragraph, Section, Table, Image, TextRun};
use Paperdoc\Document\Style\{ParagraphStyle, TableStyle, TextStyle};
use Paperdoc\Enum\{Alignment, BorderStyle};
use Paperdoc\Support\DocumentManager;

/**
 * Tests d'integration avec des fichiers reels dans public/docs/.
 */
class RealDocumentTest extends TestCase
{
    private string $outputDir;

    protected function setUp(): void
    {
        $this->outputDir = dirname(__DIR__, 2) . '/test-output';

        if (! is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }

    protected function tearDown(): void
    {
        $files = glob($this->outputDir . '/*');
        if ($files) {
            array_map('unlink', $files);
        }
        @rmdir($this->outputDir);
    }

    /* =============================================================
     | Scenario 1 : Generer un rapport complet en PDF
     |============================================================= */

    public function test_generate_full_report_pdf(): void
    {
        $doc = DocumentManager::create('pdf', 'Rapport Mensuel — Février 2026');
        $doc->setMetadata('author', 'Akram Zerarka');
        $doc->setMetadata('creator', 'Paperdoc');

        $intro = Section::make('introduction');

        $intro->addHeading('Rapport Mensuel — Février 2026', 1);

        $boldStyle = TextStyle::make()->setBold()->setColor('#1A5276')->setFontSize(13);
        $paraStyle = ParagraphStyle::make()
            ->setAlignment(Alignment::JUSTIFY)
            ->setSpaceAfter(12);

        $p = Paragraph::make($paraStyle);
        $p->addRun(TextRun::make('Ce document présente '));
        $p->addRun(TextRun::make('les résultats clés', $boldStyle));
        $p->addRun(TextRun::make(' du mois de février 2026.'));
        $intro->addElement($p);

        $table = Table::make(TableStyle::make()
            ->setCellPadding(6)
            ->setBorderColor('#374151')
            ->setHeaderBg('#e5e7eb'));

        $table->setHeaders(['Métrique', 'Valeur', 'Tendance']);
        $table->addRowFromArray(["Chiffre d'affaires", '120 000 €', '+12%']);
        $table->addRowFromArray(['Nouveaux clients', '34', '+5%']);
        $table->addRowFromArray(['Taux de satisfaction', '94%', '→']);
        $table->addRowFromArray(['Tickets résolus', '187', '+23%']);
        $intro->addElement($table);

        $intro->addText('Source : Dashboard interne — données consolidées au 28/02/2026.');

        $doc->addSection($intro);

        $conclusion = Section::make('conclusion');
        $conclusion->addHeading('Conclusion', 2);
        $conclusion->addText('Les objectifs du mois ont été largement atteints.');
        $conclusion->addText('La croissance du CA est en hausse constante depuis 3 mois.');
        $doc->addSection($conclusion);

        $path = $this->outputDir . '/rapport.pdf';
        DocumentManager::save($doc, $path);

        $this->assertFileExists($path);
        $content = file_get_contents($path);
        $this->assertStringStartsWith('%PDF-1.4', $content);
        $this->assertStringContainsString('Rapport Mensuel', $content);
        $this->assertStringContainsString('/Type /Catalog', $content);
        $this->assertStringContainsString('/Type /Pages', $content);
        $this->assertGreaterThan(1000, strlen($content));
    }

    /* =============================================================
     | Scenario 2 : Generer le meme rapport en HTML
     |============================================================= */

    public function test_generate_full_report_html(): void
    {
        $doc = $this->buildSampleReport();

        $path = $this->outputDir . '/rapport.html';
        DocumentManager::save($doc, $path, 'html');

        $this->assertFileExists($path);
        $html = file_get_contents($path);

        $this->assertStringContainsString('<!DOCTYPE html>', $html);
        $this->assertStringContainsString('Rapport Complet', $html);
        $this->assertStringContainsString('<thead>', $html);
        $this->assertStringContainsString('<th>Métrique</th>', $html);
        $this->assertStringContainsString("Chiffre d&#039;affaires", $html);
        $this->assertStringContainsString('120 000', $html);
        $this->assertStringContainsString('font-weight:bold', $html);
        $this->assertStringContainsString('text-align:justify', $html);
        $this->assertStringContainsString('id="introduction"', $html);
        $this->assertStringContainsString('id="conclusion"', $html);
    }

    /* =============================================================
     | Scenario 3 : HTML Roundtrip — Write puis Parse
     |============================================================= */

    public function test_html_roundtrip_preserves_content(): void
    {
        $doc = $this->buildSampleReport();

        $htmlPath = $this->outputDir . '/roundtrip.html';
        DocumentManager::save($doc, $htmlPath, 'html');

        $parsed = DocumentManager::open($htmlPath);

        $this->assertSame('Rapport Complet', $parsed->getTitle());
        $this->assertGreaterThanOrEqual(2, count($parsed->getSections()));

        $introSection = $parsed->getSections()[0];
        $this->assertSame('introduction', $introSection->getName());

        $elements = $introSection->getElements();
        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $tables = array_values(array_filter($elements, fn ($e) => $e instanceof Table));

        $this->assertNotEmpty($paragraphs, 'Le HTML parse doit contenir des paragraphes');
        $this->assertNotEmpty($tables, 'Le HTML parse doit contenir des tableaux');

        $table = $tables[0];
        $rows = $table->getRows();
        $this->assertGreaterThanOrEqual(4, count($rows));

        $headerRow = $rows[0];
        $this->assertTrue($headerRow->isHeader());

        $headerTexts = array_map(fn ($c) => $c->getPlainText(), $headerRow->getCells());
        $this->assertContains('Métrique', $headerTexts);
        $this->assertContains('Valeur', $headerTexts);
        $this->assertContains('Tendance', $headerTexts);

        $dataRow = $rows[1];
        $cellTexts = array_map(fn ($c) => $c->getPlainText(), $dataRow->getCells());
        $this->assertContains("Chiffre d'affaires", $cellTexts);
        $this->assertContains('120 000 €', $cellTexts);
    }

    /* =============================================================
     | Scenario 4 : CSV Roundtrip complet
     |============================================================= */

    public function test_csv_roundtrip_preserves_data(): void
    {
        $doc = DocumentManager::create('csv', 'Export Ventes');
        $section = Section::make('data');

        $table = Table::make();
        $table->setHeaders(['Mois', 'Revenus', 'Dépenses', 'Bénéfice']);
        $table->addRowFromArray(['Janvier', '50000', '30000', '20000']);
        $table->addRowFromArray(['Février', '60000', '32000', '28000']);
        $table->addRowFromArray(['Mars', '55000', '29000', '26000']);
        $table->addRowFromArray(['Avril', '70000', '35000', '35000']);

        $section->addElement($table);
        $doc->addSection($section);

        $csvPath = $this->outputDir . '/ventes.csv';
        DocumentManager::save($doc, $csvPath);

        $parsed = DocumentManager::open($csvPath);
        $parsedElements = $parsed->getSections()[0]->getElements();
        $parsedTables = array_values(array_filter($parsedElements, fn ($e) => $e instanceof Table));

        $this->assertCount(1, $parsedTables);

        $parsedTable = $parsedTables[0];
        $rows = $parsedTable->getRows();

        $this->assertCount(5, $rows);
        $this->assertTrue($rows[0]->isHeader());

        $headers = array_map(fn ($c) => $c->getPlainText(), $rows[0]->getCells());
        $this->assertSame(['Mois', 'Revenus', 'Dépenses', 'Bénéfice'], $headers);

        $janData = array_map(fn ($c) => $c->getPlainText(), $rows[1]->getCells());
        $this->assertSame(['Janvier', '50000', '30000', '20000'], $janData);

        $avrData = array_map(fn ($c) => $c->getPlainText(), $rows[4]->getCells());
        $this->assertSame(['Avril', '70000', '35000', '35000'], $avrData);
    }

    /* =============================================================
     | Scenario 5 : Conversion CSV → HTML
     |============================================================= */

    public function test_convert_csv_to_html(): void
    {
        $csvPath = $this->outputDir . '/source.csv';
        file_put_contents($csvPath, "\xEF\xBB\xBFProduit,Prix,Stock\nWidget A,29.99,150\nWidget B,49.99,75\nWidget C,99.99,30\n");

        $htmlPath = $this->outputDir . '/from_csv.html';
        DocumentManager::convert($csvPath, $htmlPath, 'html');

        $this->assertFileExists($htmlPath);
        $html = file_get_contents($htmlPath);

        $this->assertStringContainsString('<!DOCTYPE html>', $html);
        $this->assertStringContainsString('<th>', $html);
        $this->assertStringContainsString('Produit', $html);
        $this->assertStringContainsString('Widget A', $html);
        $this->assertStringContainsString('29.99', $html);
        $this->assertStringContainsString('Widget C', $html);
    }

    /* =============================================================
     | Scenario 6 : Conversion CSV → PDF
     |============================================================= */

    public function test_convert_csv_to_pdf(): void
    {
        $csvPath = $this->outputDir . '/metrics.csv';
        file_put_contents($csvPath, "Metric,Value,Change\nRevenue,120000,+12%\nUsers,5400,+8%\nChurn,2.1%,-0.3%\n");

        $pdfPath = $this->outputDir . '/metrics.pdf';
        DocumentManager::convert($csvPath, $pdfPath, 'pdf');

        $this->assertFileExists($pdfPath);
        $content = file_get_contents($pdfPath);
        $this->assertStringStartsWith('%PDF-1.4', $content);
        $this->assertStringContainsString('Revenue', $content);
        $this->assertStringContainsString('120000', $content);
    }

    /* =============================================================
     | Scenario 7 : Conversion HTML → PDF
     |============================================================= */

    public function test_convert_html_to_pdf(): void
    {
        $htmlPath = $this->outputDir . '/source.html';
        file_put_contents($htmlPath, '
            <!DOCTYPE html>
            <html><head><title>Document Source</title></head>
            <body>
                <h1>Titre Principal</h1>
                <p>Premier paragraphe avec du <strong>texte gras</strong> et du <em>texte italique</em>.</p>
                <table>
                    <thead><tr><th>Colonne A</th><th>Colonne B</th></tr></thead>
                    <tbody>
                        <tr><td>Valeur 1</td><td>Valeur 2</td></tr>
                        <tr><td>Valeur 3</td><td>Valeur 4</td></tr>
                    </tbody>
                </table>
                <p>Paragraphe de conclusion.</p>
            </body></html>
        ');

        $pdfPath = $this->outputDir . '/from_html.pdf';
        DocumentManager::convert($htmlPath, $pdfPath, 'pdf');

        $this->assertFileExists($pdfPath);
        $content = file_get_contents($pdfPath);
        $this->assertStringStartsWith('%PDF-1.4', $content);
        $this->assertStringContainsString('Titre Principal', $content);
        $this->assertStringContainsString('Valeur 1', $content);
    }

    /* =============================================================
     | Scenario 8 : HTML complexe avec styles inline
     |============================================================= */

    public function test_parse_complex_styled_html(): void
    {
        $htmlPath = $this->outputDir . '/styled.html';
        file_put_contents($htmlPath, '
            <!DOCTYPE html>
            <html><head><title>Styled Document</title></head>
            <body>
                <section id="header">
                    <h1>Document avec styles</h1>
                </section>
                <section id="content">
                    <p style="text-align:center;margin-bottom:20pt">
                        <span style="font-weight:bold;color:#E74C3C;font-size:16pt">Alerte importante</span>
                    </p>
                    <p style="text-align:justify;line-height:1.8">
                        Ce paragraphe utilise un alignement justifié avec un interligne de 1.8.
                        <span style="font-style:italic;text-decoration:underline">Ce texte est en italique souligné.</span>
                    </p>
                    <table>
                        <tr><th>Statut</th><th>Nombre</th></tr>
                        <tr><td>Actif</td><td>245</td></tr>
                        <tr><td>Inactif</td><td>18</td></tr>
                        <tr><td colspan="2">Total : 263</td></tr>
                    </table>
                    <figure><img src="/images/chart.png" alt="Graphique" width="600" height="400"></figure>
                </section>
            </body></html>
        ');

        $doc = DocumentManager::open($htmlPath);

        $this->assertSame('Styled Document', $doc->getTitle());
        $this->assertCount(2, $doc->getSections());
        $this->assertSame('header', $doc->getSections()[0]->getName());
        $this->assertSame('content', $doc->getSections()[1]->getName());

        $contentSection = $doc->getSections()[1];
        $elements = $contentSection->getElements();

        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $tables = array_values(array_filter($elements, fn ($e) => $e instanceof Table));
        $images = array_values(array_filter($elements, fn ($e) => $e instanceof Image));

        $this->assertNotEmpty($paragraphs);
        $this->assertNotEmpty($tables);
        $this->assertNotEmpty($images);

        $alertPara = $paragraphs[0];
        $this->assertNotNull($alertPara->getStyle());
        $this->assertSame(Alignment::CENTER, $alertPara->getStyle()->getAlignment());

        $alertRun = $alertPara->getRuns()[0];
        $this->assertTrue($alertRun->getStyle()->isBold());
        $this->assertSame('#E74C3C', $alertRun->getStyle()->getColor());
        $this->assertSame(16.0, $alertRun->getStyle()->getFontSize());

        $table = $tables[0];
        $this->assertCount(4, $table->getRows());

        $lastRow = $table->getRows()[3];
        $this->assertSame(2, $lastRow->getCells()[0]->getColspan());

        $image = $images[0];
        $this->assertSame('/images/chart.png', $image->getSrc());
        $this->assertSame('Graphique', $image->getAlt());
        $this->assertSame(600, $image->getWidth());
        $this->assertSame(400, $image->getHeight());
    }

    /* =============================================================
     | Scenario 9 : Document multi-styles vers tous les formats
     |============================================================= */

    public function test_multi_style_document_all_formats(): void
    {
        $doc = DocumentManager::create('pdf', 'Multi-Style');

        $section = Section::make('content');

        $section->addHeading('Titre H1', 1);
        $section->addHeading('Sous-titre H2', 2);
        $section->addHeading('Section H3', 3);

        $p = Paragraph::make(ParagraphStyle::make()->setAlignment(Alignment::RIGHT)->setSpaceBefore(10)->setSpaceAfter(20));
        $p->addRun(TextRun::make('Texte normal '));
        $p->addRun(TextRun::make('en gras', TextStyle::make()->setBold()));
        $p->addRun(TextRun::make(' puis '));
        $p->addRun(TextRun::make('en italique', TextStyle::make()->setItalic()));
        $p->addRun(TextRun::make(' puis '));
        $p->addRun(TextRun::make('souligné', TextStyle::make()->setUnderline()));
        $p->addRun(TextRun::make(' puis '));
        $p->addRun(TextRun::make('tout combiné', TextStyle::make()->setBold()->setItalic()->setUnderline()->setColor('#2E86C1')->setFontSize(16)));
        $section->addElement($p);

        $table = Table::make(TableStyle::make()
            ->setBorderStyle(BorderStyle::DASHED)
            ->setBorderWidth(1.0)
            ->setBorderColor('#2E86C1')
            ->setCellPadding(8)
            ->setHeaderBg('#D4E6F1'));
        $table->setHeaders(['Propriété', 'Valeur', 'Description']);
        $table->addRowFromArray(['font-family', 'Helvetica', 'Police par défaut']);
        $table->addRowFromArray(['font-size', '12pt', 'Taille standard']);
        $table->addRowFromArray(['color', '#000000', 'Noir']);
        $table->setColumnWidths([25, 25, 50]);
        $section->addElement($table);

        $doc->addSection($section);

        $pdfPath = $this->outputDir . '/multi_style.pdf';
        DocumentManager::save($doc, $pdfPath, 'pdf');
        $this->assertFileExists($pdfPath);
        $this->assertStringStartsWith('%PDF-1.4', file_get_contents($pdfPath));

        $htmlPath = $this->outputDir . '/multi_style.html';
        DocumentManager::save($doc, $htmlPath, 'html');
        $html = file_get_contents($htmlPath);
        $this->assertStringContainsString('font-weight:bold', $html);
        $this->assertStringContainsString('font-style:italic', $html);
        $this->assertStringContainsString('text-decoration:underline', $html);
        $this->assertStringContainsString('color:#2E86C1', $html);
        $this->assertStringContainsString('text-align:right', $html);

        $csvPath = $this->outputDir . '/multi_style.csv';
        DocumentManager::save($doc, $csvPath, 'csv');
        $this->assertFileExists($csvPath);
    }

    /* =============================================================
     | Scenario 10 : Gros CSV (beaucoup de lignes)
     |============================================================= */

    public function test_large_csv_roundtrip(): void
    {
        $doc = DocumentManager::create('csv', 'Large Dataset');
        $section = Section::make('data');

        $table = Table::make();
        $table->setHeaders(['ID', 'Nom', 'Email', 'Score', 'Statut']);

        for ($i = 1; $i <= 200; $i++) {
            $table->addRowFromArray([
                (string) $i,
                "Utilisateur {$i}",
                "user{$i}@example.com",
                (string) rand(0, 100),
                $i % 3 === 0 ? 'Inactif' : 'Actif',
            ]);
        }

        $section->addElement($table);
        $doc->addSection($section);

        $csvPath = $this->outputDir . '/large.csv';
        DocumentManager::save($doc, $csvPath);

        $parsed = DocumentManager::open($csvPath);
        $parsedTable = array_values(array_filter(
            $parsed->getSections()[0]->getElements(),
            fn ($e) => $e instanceof Table
        ))[0];

        $rows = $parsedTable->getRows();
        $this->assertCount(201, $rows);
        $this->assertTrue($rows[0]->isHeader());

        $headers = array_map(fn ($c) => $c->getPlainText(), $rows[0]->getCells());
        $this->assertSame(['ID', 'Nom', 'Email', 'Score', 'Statut'], $headers);

        $firstData = array_map(fn ($c) => $c->getPlainText(), $rows[1]->getCells());
        $this->assertSame('1', $firstData[0]);
        $this->assertSame('Utilisateur 1', $firstData[1]);
        $this->assertSame('user1@example.com', $firstData[2]);

        $lastData = array_map(fn ($c) => $c->getPlainText(), $rows[200]->getCells());
        $this->assertSame('200', $lastData[0]);
    }

    /* =============================================================
     | Scenario 11 : PDF roundtrip — Write puis Parse
     |============================================================= */

    public function test_pdf_roundtrip(): void
    {
        $doc = DocumentManager::create('pdf', 'PDF Roundtrip');
        $section = Section::make('content');
        $section->addHeading('Titre Roundtrip', 1);
        $section->addText('Premier paragraphe du roundtrip.');
        $section->addText('Deuxième paragraphe avec des accents : é, è, ê, à.');
        $doc->addSection($section);

        $pdfPath = $this->outputDir . '/roundtrip.pdf';
        DocumentManager::save($doc, $pdfPath);

        $parsed = DocumentManager::open($pdfPath);

        $this->assertSame('pdf', $parsed->getFormat());
        $this->assertGreaterThanOrEqual(1, count($parsed->getSections()));

        $allText = '';
        foreach ($parsed->getSections() as $s) {
            foreach ($s->getElements() as $e) {
                if ($e instanceof Paragraph) {
                    $allText .= $e->getPlainText() . "\n";
                }
            }
        }

        $this->assertStringContainsString('Titre Roundtrip', $allText);
        $this->assertStringContainsString('Premier paragraphe', $allText);
    }

    /* =============================================================
     | Scenario 12 : Format non supporté lève une exception
     |============================================================= */

    public function test_unsupported_format_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Aucun parser disponible');

        DocumentManager::open('/any/path/file.xyz');
    }

    /* =============================================================
     | Helper
     |============================================================= */

    private function buildSampleReport(): \Paperdoc\Contracts\DocumentInterface
    {
        $doc = DocumentManager::create('pdf', 'Rapport Complet');
        $doc->setMetadata('author', 'Akram Zerarka');

        $intro = Section::make('introduction');
        $intro->addHeading('Introduction', 1);

        $boldStyle = TextStyle::make()->setBold()->setColor('#1A5276');
        $paraStyle = ParagraphStyle::make()->setAlignment(Alignment::JUSTIFY);
        $p = Paragraph::make($paraStyle);
        $p->addRun(TextRun::make('Ce rapport présente '));
        $p->addRun(TextRun::make('les résultats clés', $boldStyle));
        $p->addRun(TextRun::make(' du trimestre.'));
        $intro->addElement($p);

        $table = Table::make();
        $table->setHeaders(['Métrique', 'Valeur', 'Tendance']);
        $table->addRowFromArray(["Chiffre d'affaires", '120 000 €', '+12%']);
        $table->addRowFromArray(['Nouveaux clients', '34', '+5%']);
        $table->addRowFromArray(['Satisfaction', '94%', '→']);
        $intro->addElement($table);

        $doc->addSection($intro);

        $conclusion = Section::make('conclusion');
        $conclusion->addHeading('Conclusion', 2);
        $conclusion->addText('Objectifs atteints.');
        $doc->addSection($conclusion);

        return $doc;
    }
}
