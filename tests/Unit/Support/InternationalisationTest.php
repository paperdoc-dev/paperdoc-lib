<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\Section;
use Paperdoc\Parsers\HtmlParser;
use Paperdoc\Parsers\PdfParser;
use Paperdoc\Support\DocumentManager;
use Paperdoc\Support\TextDirection;

/**
 * Ce que la bibliothèque doit tenir pour ne pas être « latine seulement ».
 */
class InternationalisationTest extends TestCase
{
    private string $tmpDir;

    /** @var array<string, string> */
    private const SCRIPTS = [
        'latin'      => 'Le total des ventes est de 1234 euros',
        'cyrillique' => 'Общая сумма продаж составляет 1234 евро',
        'grec'       => 'Το συνολικό ποσό πωλήσεων είναι 1234 ευρώ',
        'hebreu'     => 'סך המכירות הוא 1234 אירו',
        'arabe'      => 'إجمالي المبيعات هو 1234 يورو',
        'japonais'   => '売上高の合計は1234ユーロです',
        'coreen'     => '총 매출액은 1234 유로입니다',
        'devanagari' => 'कुल बिक्री 1234 यूरो है',
    ];

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/paperdoc_i18n_' . uniqid();
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

    /**
     * Chaque format textuel doit rendre puis relire les huit écritures.
     * Le PDF en est exclu : les 14 polices standard sont bornées à
     * WinAnsi, il faut y embarquer une police (voir PdfEngineEmbeddedFontTest).
     */
    public function test_every_text_format_round_trips_every_script(): void
    {
        foreach (['docx', 'html', 'md', 'doc', 'pptx', 'ppt'] as $format) {
            foreach (self::SCRIPTS as $script => $source) {
                $doc = DocumentManager::create($format, 'i18n');
                $section = Section::make('main');
                $section->addText($source);
                $doc->addSection($section);

                $path = $this->tmpDir . "/i18n.{$format}";
                DocumentManager::save($doc, $path);

                $text = '';
                foreach (DocumentManager::open($path)->getSections() as $parsed) {
                    foreach ($parsed->getElements() as $element) {
                        if (method_exists($element, 'getPlainText')) {
                            $text .= $element->getPlainText() . ' ';
                        }
                    }
                }

                $this->assertStringContainsString(
                    mb_substr($source, 0, 8),
                    $text,
                    "{$format} a perdu l'écriture {$script}"
                );

                unlink($path);
            }
        }
    }

    /**
     * L'ancien test cherchait « encoding » dans tout le fichier : le mot
     * dans le corps du texte suffisait à faire retomber libxml sur
     * ISO-8859-1.
     */
    public function test_word_encoding_in_body_does_not_break_utf8(): void
    {
        $path = $this->tmpDir . '/enc.html';
        file_put_contents(
            $path,
            '<html><body><p>Résumé: пример — see the encoding section</p></body></html>'
        );

        $doc = (new HtmlParser())->parse($path);
        $text = '';
        foreach ($doc->getSections()[0]->getElements() as $element) {
            if (method_exists($element, 'getPlainText')) {
                $text .= $element->getPlainText();
            }
        }

        $this->assertStringContainsString('Résumé', $text);
        $this->assertStringContainsString('пример', $text);
    }

    public function test_declared_charset_is_still_respected(): void
    {
        foreach ([
            '<html><head><meta charset="utf-8"></head><body><p>Résumé пример</p></body></html>',
            '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">'
                . '</head><body><p>Résumé пример</p></body></html>',
            '<?xml version="1.0" encoding="UTF-8"?><html><body><p>Résumé пример</p></body></html>',
        ] as $i => $html) {
            $path = $this->tmpDir . "/declared{$i}.html";
            file_put_contents($path, $html);

            $doc = (new HtmlParser())->parse($path);
            $text = '';
            foreach ($doc->getSections()[0]->getElements() as $element) {
                if (method_exists($element, 'getPlainText')) {
                    $text .= $element->getPlainText();
                }
            }

            $this->assertStringContainsString('Résumé пример', $text);
            unlink($path);
        }
    }

    /**
     * isGarbageText ne comptait que [a-zA-Z] : une page entièrement en
     * russe ou en japonais était jetée comme illisible.
     */
    public function test_non_latin_text_is_not_treated_as_garbage(): void
    {
        $parser = new PdfParser();
        $method = new \ReflectionMethod($parser, 'isGarbageText');

        foreach (self::SCRIPTS as $script => $source) {
            $this->assertFalse(
                $method->invoke($parser, $source),
                "l'écriture {$script} est prise pour du bruit"
            );
        }

        // Le filtre doit rester utile sur du vrai bruit.
        $this->assertTrue($method->invoke($parser, '~~ ^^ ## @@ %% && ** (( ))'));
    }

    /**
     * L'heuristique ne comptait que les octets de poids fort nuls, ce qui
     * ne vaut que pour l'ASCII.
     */
    public function test_utf16be_detection_covers_non_latin_scripts(): void
    {
        $parser = new PdfParser();
        $method = new \ReflectionMethod($parser, 'looksLikeUtf16BE');

        foreach ([
            'Hello world',
            'Привет мир всем',
            'Γειά σου κόσμε',
            'שלום עולם כאן',
            'مرحبا بالعالم',
        ] as $source) {
            $this->assertTrue(
                $method->invoke($parser, mb_convert_encoding($source, 'UTF-16BE', 'UTF-8')),
                "UTF-16BE non détecté : {$source}"
            );
        }

        // Aucun faux positif sur du simple octet : ce serait bien pire,
        // tout le latin deviendrait du charabia CJK.
        foreach (['Hello world!', 'Le total des ventes.', 'ABCDEFGHIJKLMNOP', '29.99 | 150 | 75'] as $source) {
            $bytes = mb_convert_encoding($source, 'Windows-1252', 'UTF-8');

            if (strlen($bytes) % 2 !== 0) {
                $bytes .= ' ';
            }

            $this->assertFalse($method->invoke($parser, $bytes), "faux positif : {$source}");
        }
    }

    public function test_text_direction_detection(): void
    {
        $this->assertSame(TextDirection::RTL, TextDirection::detect('إجمالي المبيعات هو 1234 يورو'));
        $this->assertSame(TextDirection::RTL, TextDirection::detect('סך המכירות הוא 1234 אירו'));
        $this->assertSame(TextDirection::RTL, TextDirection::detect('Rapport 2024 — إجمالي المبيعات للسنة'));
        $this->assertSame(TextDirection::LTR, TextDirection::detect('Le total des ventes'));
        $this->assertSame(TextDirection::LTR, TextDirection::detect('Общая сумма продаж'));
        $this->assertSame(TextDirection::LTR, TextDirection::detect('売上高の合計'));
        $this->assertSame(TextDirection::LTR, TextDirection::detect('Annual report on the Arabic market: مبيعات'));
        $this->assertSame(TextDirection::LTR, TextDirection::detect(''));
    }

    public function test_rtl_document_declares_its_direction(): void
    {
        $doc = DocumentManager::create('html', 'RTL');
        $section = Section::make('main');
        $section->addText('إجمالي المبيعات هو 1234 يورو');
        $doc->addSection($section);

        $this->assertStringContainsString('dir="rtl"', DocumentManager::renderAs($doc, 'html'));

        $ltr = DocumentManager::create('html', 'LTR');
        $ltrSection = Section::make('main');
        $ltrSection->addText('Le total des ventes');
        $ltr->addSection($ltrSection);

        $this->assertStringContainsString('dir="ltr"', DocumentManager::renderAs($ltr, 'html'));
    }

    public function test_docx_marks_rtl_paragraphs_and_runs(): void
    {
        $doc = DocumentManager::create('docx', 'RTL');
        $section = Section::make('main');
        $section->addText('إجمالي المبيعات هو 1234 يورو');
        $doc->addSection($section);

        $path = $this->tmpDir . '/rtl.docx';
        DocumentManager::save($doc, $path);

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($path));
        $xml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();

        $this->assertStringContainsString('<w:bidi/>', $xml);
        $this->assertStringContainsString('<w:rtl/>', $xml);
    }
}
