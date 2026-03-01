<?php

require __DIR__ . '/../vendor/autoload.php';

use Paperdoc\Support\DocumentManager;
use Paperdoc\Document\{Section, Paragraph, TextRun, Table, Image};
use Paperdoc\Document\Style\{TextStyle, ParagraphStyle, TableStyle};
use Paperdoc\Enum\Alignment;

// ──────────────────────────────────────────────
// 1. Créer un PDF depuis zéro
// ──────────────────────────────────────────────

$doc = DocumentManager::create('pdf', 'Rapport Mensuel');
$doc->setMetadata('author', 'Akram Zerarka');

$section = Section::make('introduction');

$section->addHeading('Rapport Mensuel — Février 2026', 1);

$boldStyle = TextStyle::make()
    ->setBold()
    ->setColor('#1A5276')
    ->setFontSize(13);

$paraStyle = ParagraphStyle::make()
    ->setAlignment(Alignment::JUSTIFY)
    ->setSpaceAfter(12);

$paragraph = Paragraph::make($paraStyle);
$paragraph->addRun(TextRun::make('Ce document présente '))
          ->addRun(TextRun::make('les résultats clés', $boldStyle))
          ->addRun(TextRun::make(' du mois de février.'));

$section->addElement($paragraph);

$table = Table::make();
$table->setHeaders(['Métrique', 'Valeur', 'Tendance']);
$table->addRowFromArray(['Chiffre d\'affaires', '120 000 €', '+12%']);
$table->addRowFromArray(['Nouveaux clients', '34', '+5%']);
$table->addRowFromArray(['Taux de satisfaction', '94%', '→']);

$section->addElement($table);

$doc->addSection($section);

DocumentManager::save($doc, __DIR__ . '/output/rapport.pdf');
echo "rapport.pdf créé\n";

// ──────────────────────────────────────────────
// 2. Exporter en HTML
// ──────────────────────────────────────────────

DocumentManager::save($doc, __DIR__ . '/output/rapport.html', 'html');
echo "rapport.html créé\n";

// ──────────────────────────────────────────────
// 3. Convertir HTML → PDF
// ──────────────────────────────────────────────

DocumentManager::convert(
    __DIR__ . '/output/rapport.html',
    __DIR__ . '/output/rapport_from_html.pdf',
    'pdf'
);
echo "HTML converti en PDF\n";

// ──────────────────────────────────────────────
// 4. Créer un CSV
// ──────────────────────────────────────────────

$csv = DocumentManager::create('csv', 'Export ventes');
$csvSection = Section::make('data');
$csvTable = Table::make();
$csvTable->setHeaders(['Mois', 'Revenus', 'Dépenses', 'Bénéfice']);
$csvTable->addRowFromArray(['Janvier', '50000', '30000', '20000']);
$csvTable->addRowFromArray(['Février', '60000', '32000', '28000']);
$csvSection->addElement($csvTable);
$csv->addSection($csvSection);

DocumentManager::save($csv, __DIR__ . '/output/ventes.csv');
echo "ventes.csv créé\n";

// ──────────────────────────────────────────────
// 5. Convertir CSV → HTML
// ──────────────────────────────────────────────

DocumentManager::convert(
    __DIR__ . '/output/ventes.csv',
    __DIR__ . '/output/ventes.html',
    'html'
);
echo "CSV converti en HTML\n";

// ──────────────────────────────────────────────
// 6. Thumbnails dynamiques (sans sauvegarde)
// ──────────────────────────────────────────────

$imgDoc = DocumentManager::create('html', 'Avec image');
$imgSection = Section::make('cover');
$imgSection->addElement(Image::make('/path/to/photo.jpg', 1920, 1080, 'Cover'));
$imgDoc->addSection($imgSection);

// Thumbnail always reflects the current image — change the image, thumbnail updates
$dataUri = $imgDoc->getThumbnailDataUri(200, 200);
if ($dataUri !== null) {
    echo "<img src=\"{$dataUri}\" alt=\"thumbnail\">\n";
}

// Swap the image → thumbnail automatically returns the new version
$imgDoc->getFirstImage()?->setData(file_get_contents('/path/to/new-photo.png'), 'image/png');
$newUri = $imgDoc->getThumbnailDataUri(200, 200);

// Via DocumentManager static API
$thumb = DocumentManager::thumbnail($imgDoc, 150, 150);
if ($thumb !== null) {
    echo "Thumbnail: {$thumb['width']}x{$thumb['height']} ({$thumb['mimeType']})\n";
}

// ──────────────────────────────────────────────
// 7. Extensibilité : enregistrer un nouveau format
// ──────────────────────────────────────────────

// DocumentManager::registerRenderer('odt', \App\Renderers\OdtRenderer::class);
// DocumentManager::registerParser(new \App\Parsers\OdtParser());
// DocumentManager::convert('document.odt', 'output.pdf', 'pdf');
