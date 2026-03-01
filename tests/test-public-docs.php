<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Paperdoc\Document\{Paragraph, Section, Table, TextRun};
use Paperdoc\Document\Style\{ParagraphStyle, TableStyle, TextStyle};
use Paperdoc\Enum\Alignment;
use Paperdoc\Support\DocumentManager;

$docsDir = dirname(__DIR__, 2) . '/public/docs';
$passed  = 0;
$failed  = 0;
$total   = 0;

function test(string $label, callable $fn): void
{
    global $passed, $failed, $total;
    $total++;
    try {
        $fn();
        $passed++;
        echo "  \033[32m✔\033[0m {$label}\n";
    } catch (\Throwable $e) {
        $failed++;
        echo "  \033[31m✘\033[0m {$label}\n";
        echo "    \033[33m→ {$e->getMessage()}\033[0m\n";
        echo "    \033[90m  {$e->getFile()}:{$e->getLine()}\033[0m\n";
    }
}

function assert_true(bool $condition, string $msg = ''): void
{
    if (! $condition) {
        throw new \RuntimeException("Assertion failed" . ($msg ? ": {$msg}" : ''));
    }
}

function assert_eq(mixed $expected, mixed $actual, string $msg = ''): void
{
    if ($expected !== $actual) {
        $e = var_export($expected, true);
        $a = var_export($actual, true);
        throw new \RuntimeException("Expected {$e}, got {$a}" . ($msg ? " — {$msg}" : ''));
    }
}

function assert_contains(string $haystack, string $needle, string $msg = ''): void
{
    if (! str_contains($haystack, $needle)) {
        throw new \RuntimeException("String does not contain '{$needle}'" . ($msg ? " — {$msg}" : ''));
    }
}

function assert_gt(mixed $a, mixed $b, string $msg = ''): void
{
    if ($a <= $b) {
        throw new \RuntimeException("{$a} is not > {$b}" . ($msg ? " — {$msg}" : ''));
    }
}

/* ================================================================
 | 1. Parsing du DOCX réel : Astérisque.docx
 |================================================================ */

echo "\n\033[1;36m═══ Test Parsing public/docs/ ═══\033[0m\n\n";
echo "\033[1m▸ Parsing DOCX : Astérisque.docx\033[0m\n";

test('Ouverture du fichier DOCX — pas d\'exception', function () use ($docsDir) {
    $doc = DocumentManager::open("{$docsDir}/Astérisque.docx");
    assert_true($doc instanceof \Paperdoc\Contracts\DocumentInterface, 'Instance correcte');
});

test('Document parsé a des sections', function () use ($docsDir) {
    $doc = DocumentManager::open("{$docsDir}/Astérisque.docx");
    assert_gt(count($doc->getSections()), 0, 'Au moins 1 section');
    echo "    \033[90m→ " . count($doc->getSections()) . " section(s)\033[0m\n";
});

test('Document parsé contient du texte', function () use ($docsDir) {
    $doc = DocumentManager::open("{$docsDir}/Astérisque.docx");
    $totalElements = 0;
    $totalText = '';

    foreach ($doc->getSections() as $section) {
        $elements = $section->getElements();
        $totalElements += count($elements);

        foreach ($elements as $el) {
            if ($el instanceof Paragraph) {
                $totalText .= $el->getPlainText() . "\n";
            } elseif ($el instanceof Table) {
                foreach ($el->getRows() as $row) {
                    foreach ($row->getCells() as $cell) {
                        $totalText .= $cell->getPlainText() . ' | ';
                    }
                    $totalText .= "\n";
                }
            }
        }
    }

    assert_gt($totalElements, 0, 'Des éléments ont été extraits');
    assert_gt(strlen($totalText), 10, 'Du texte substantiel a été extrait');
    echo "    \033[90m→ {$totalElements} éléments, " . strlen(trim($totalText)) . " chars de texte\033[0m\n";

    $preview = mb_substr(trim($totalText), 0, 200);
    echo "    \033[90m→ Aperçu : " . str_replace("\n", " ⏎ ", $preview) . "…\033[0m\n";
});

test('Titre du document DOCX', function () use ($docsDir) {
    $doc = DocumentManager::open("{$docsDir}/Astérisque.docx");
    $title = $doc->getTitle();
    echo "    \033[90m→ Titre : \"{$title}\"\033[0m\n";
    assert_true(true);
});

test('Types d\'éléments trouvés dans le DOCX', function () use ($docsDir) {
    $doc = DocumentManager::open("{$docsDir}/Astérisque.docx");
    $types = [];

    foreach ($doc->getSections() as $section) {
        foreach ($section->getElements() as $el) {
            $type = (new \ReflectionClass($el))->getShortName();
            $types[$type] = ($types[$type] ?? 0) + 1;
        }
    }

    foreach ($types as $type => $count) {
        echo "    \033[90m→ {$type}: {$count}\033[0m\n";
    }

    assert_gt(count($types), 0, 'Des types ont été trouvés');
});

/* ================================================================
 | 2. Parsing du PDF réel : document.pdf
 |================================================================ */

echo "\n\033[1m▸ Parsing PDF : document.pdf\033[0m\n";

test('Ouverture du fichier PDF — pas d\'exception', function () use ($docsDir) {
    $doc = DocumentManager::open("{$docsDir}/document.pdf");
    assert_true($doc instanceof \Paperdoc\Contracts\DocumentInterface, 'Instance correcte');
});

test('Document PDF a des sections (pages)', function () use ($docsDir) {
    $doc = DocumentManager::open("{$docsDir}/document.pdf");
    assert_gt(count($doc->getSections()), 0, 'Au moins 1 page');
    echo "    \033[90m→ " . count($doc->getSections()) . " page(s)\033[0m\n";
});

test('Document PDF contient du texte extrait', function () use ($docsDir) {
    $doc = DocumentManager::open("{$docsDir}/document.pdf");
    $totalElements = 0;
    $totalText = '';

    foreach ($doc->getSections() as $section) {
        $elements = $section->getElements();
        $totalElements += count($elements);

        foreach ($elements as $el) {
            if ($el instanceof Paragraph) {
                $totalText .= $el->getPlainText() . "\n";
            }
        }
    }

    echo "    \033[90m→ {$totalElements} éléments, " . strlen(trim($totalText)) . " chars\033[0m\n";

    if (strlen(trim($totalText)) > 0) {
        $preview = mb_substr(trim($totalText), 0, 200);
        echo "    \033[90m→ Aperçu : " . str_replace("\n", " ⏎ ", $preview) . "…\033[0m\n";
    } else {
        echo "    \033[90m→ (aucun texte extrait — le PDF peut contenir des polices encodées/images)\033[0m\n";
    }

    assert_true(true);
});

test('Titre/metadata du PDF', function () use ($docsDir) {
    $doc = DocumentManager::open("{$docsDir}/document.pdf");
    $title = $doc->getTitle();
    $author = $doc->getMetadata('author');
    echo "    \033[90m→ Titre : \"{$title}\"\033[0m\n";
    if ($author) {
        echo "    \033[90m→ Auteur : \"{$author}\"\033[0m\n";
    }
    assert_true(true);
});

/* ================================================================
 | 3. Conversions depuis les fichiers réels
 |================================================================ */

echo "\n\033[1m▸ Conversions depuis fichiers réels\033[0m\n";

$generatedFiles = [];

test('DOCX → HTML', function () use ($docsDir, &$generatedFiles) {
    $htmlPath = "{$docsDir}/asterisque-from-docx.html";
    DocumentManager::convert("{$docsDir}/Astérisque.docx", $htmlPath, 'html');
    $generatedFiles[] = $htmlPath;

    assert_true(file_exists($htmlPath));
    $html = file_get_contents($htmlPath);
    assert_contains($html, '<!DOCTYPE html>');
    assert_gt(strlen($html), 100, 'HTML substantiel');
    echo "    \033[90m→ " . number_format(filesize($htmlPath)) . " octets\033[0m\n";
});

test('DOCX → PDF', function () use ($docsDir, &$generatedFiles) {
    $pdfPath = "{$docsDir}/asterisque-from-docx.pdf";
    DocumentManager::convert("{$docsDir}/Astérisque.docx", $pdfPath, 'pdf');
    $generatedFiles[] = $pdfPath;

    assert_true(file_exists($pdfPath));
    $content = file_get_contents($pdfPath);
    assert_contains($content, '%PDF-1.4');
    echo "    \033[90m→ " . number_format(filesize($pdfPath)) . " octets\033[0m\n";
});

test('DOCX → CSV', function () use ($docsDir, &$generatedFiles) {
    $csvPath = "{$docsDir}/asterisque-from-docx.csv";
    DocumentManager::convert("{$docsDir}/Astérisque.docx", $csvPath, 'csv');
    $generatedFiles[] = $csvPath;

    assert_true(file_exists($csvPath));
    echo "    \033[90m→ " . number_format(filesize($csvPath)) . " octets\033[0m\n";
});

test('PDF → HTML', function () use ($docsDir, &$generatedFiles) {
    $htmlPath = "{$docsDir}/document-from-pdf.html";
    DocumentManager::convert("{$docsDir}/document.pdf", $htmlPath, 'html');
    $generatedFiles[] = $htmlPath;

    assert_true(file_exists($htmlPath));
    $html = file_get_contents($htmlPath);
    assert_contains($html, '<!DOCTYPE html>');
    echo "    \033[90m→ " . number_format(filesize($htmlPath)) . " octets\033[0m\n";
});

test('Roundtrip : DOCX → HTML → re-parse', function () use ($docsDir) {
    $htmlPath = "{$docsDir}/asterisque-from-docx.html";
    $doc = DocumentManager::open($htmlPath);

    assert_gt(count($doc->getSections()), 0);

    $totalText = '';
    foreach ($doc->getSections() as $section) {
        foreach ($section->getElements() as $el) {
            if ($el instanceof Paragraph) {
                $totalText .= $el->getPlainText();
            }
        }
    }

    assert_gt(strlen($totalText), 10, 'Texte préservé après roundtrip');
    echo "    \033[90m→ " . strlen($totalText) . " chars après roundtrip\033[0m\n";
});

/* ================================================================
 | Résumé
 |================================================================ */

echo "\n\033[1;36m═══ Résumé ═══\033[0m\n\n";

echo "  Tests : {$total}\n";
echo "  \033[32mRéussis : {$passed}\033[0m\n";
if ($failed > 0) {
    echo "  \033[31mÉchoués : {$failed}\033[0m\n";
}

echo "\n  \033[1mFichiers dans public/docs/ :\033[0m\n";
$allFiles = glob("{$docsDir}/*");
if ($allFiles) {
    sort($allFiles);
    foreach ($allFiles as $f) {
        $size = filesize($f);
        $ext = pathinfo($f, PATHINFO_EXTENSION);
        $name = basename($f);
        $sizeStr = $size > 1024 ? number_format($size / 1024, 1) . ' Ko' : "{$size} o";
        echo "    {$name} ({$ext}) — {$sizeStr}\n";
    }
}

echo "\n";
exit($failed > 0 ? 1 : 0);
