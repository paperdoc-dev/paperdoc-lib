<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Support\Pdf;

use PHPUnit\Framework\TestCase;
use Paperdoc\Parsers\PdfParser;
use Paperdoc\Support\Pdf\PdfEngine;
use Paperdoc\Support\Pdf\TrueTypeFont;

/**
 * Les 14 polices standard sont bornées à WinAnsiEncoding : sans police
 * embarquée, tout ce qui sort du latin-1 devient « ? » dans un PDF.
 *
 * La bibliothèque ne livre aucune donnée de police — ces tests empruntent
 * une police du système et se sautent si aucune n'est disponible.
 */
class EmbeddedFontTest extends TestCase
{
    private string $tmpDir;

    private string $fontPath;

    protected function setUp(): void
    {
        $font = $this->locateSystemFont();

        if ($font === null) {
            $this->markTestSkipped('aucune police TrueType système trouvée');
        }

        $this->fontPath = $font;
        $this->tmpDir = sys_get_temp_dir() . '/paperdoc_ttf_' . uniqid();
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

    public function test_parses_font_metrics(): void
    {
        $font = TrueTypeFont::fromFile($this->fontPath);

        $this->assertGreaterThan(0, $font->getNumGlyphs());
        $this->assertGreaterThan(0, $font->getAscender());
        $this->assertLessThan(0, $font->getDescender());
        $this->assertNotSame('', $font->getPostScriptName());

        // 'A' doit exister dans toute police à usage général, avec une
        // chasse plausible en unités de 1000em.
        $glyph = $font->glyphForCodepoint(0x41);
        $this->assertGreaterThan(0, $glyph);
        $this->assertGreaterThan(100, $font->glyphWidth($glyph));
        $this->assertLessThan(2000, $font->glyphWidth($glyph));
    }

    /**
     * Certaines polices rangent l'ASCII dans la sous-table cmap format 4
     * et seulement le supplémentaire dans la format 12 : n'en lire qu'une
     * perd tout un pan du répertoire.
     */
    public function test_cmap_subtables_are_merged(): void
    {
        $font = TrueTypeFont::fromFile($this->fontPath);

        foreach ([0x41 => 'A', 0x31 => '1', 0x20 => 'espace'] as $codepoint => $label) {
            $this->assertGreaterThan(
                0,
                $font->glyphForCodepoint($codepoint),
                "glyphe absent pour {$label}"
            );
        }
    }

    public function test_non_latin_text_survives_a_pdf_round_trip(): void
    {
        $font = TrueTypeFont::fromFile($this->fontPath);

        $samples = array_values(array_filter([
            'Le total des ventes est de 1234 euros',
            'Общая сумма продаж составляет 1234 евро',
            'Το συνολικό ποσό είναι 1234 ευρώ',
        ], static fn (string $s): bool => self::isFullyCovered($font, $s)));

        if ($samples === []) {
            $this->markTestSkipped('la police système ne couvre aucun échantillon non latin');
        }

        $engine = new PdfEngine();
        $engine->registerTrueTypeFont('Universal', $this->fontPath);
        $engine->newPage();

        foreach ($samples as $i => $sample) {
            $engine->writeTextAt($sample, 'Universal', 14, 60, 780 - $i * 30);
        }

        $path = $this->tmpDir . '/embedded.pdf';
        file_put_contents($path, $engine->output());

        $raw = file_get_contents($path) ?: '';

        // Le graphe d'objets attendu pour une police embarquée.
        $this->assertStringContainsString('/Subtype /Type0', $raw);
        $this->assertStringContainsString('/Encoding /Identity-H', $raw);
        $this->assertStringContainsString('/Subtype /CIDFontType2', $raw);
        $this->assertStringContainsString('/FontFile2', $raw);
        $this->assertStringContainsString('beginbfchar', $raw);

        $text = '';
        foreach ((new PdfParser())->parse($path)->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getPlainText')) {
                    $text .= $element->getPlainText() . "\n";
                }
            }
        }

        foreach ($samples as $sample) {
            $this->assertStringContainsString($sample, $text);
        }
    }

    public function test_widths_come_from_the_embedded_font(): void
    {
        $engine = new PdfEngine();
        $engine->registerTrueTypeFont('Universal', $this->fontPath);

        $this->assertTrue($engine->hasEmbeddedFont('Universal'));

        // Comparer à la somme des chasses lues dans le fichier : le test
        // vaut aussi bien pour une police proportionnelle que monochasse.
        $font = TrueTypeFont::fromFile($this->fontPath);
        $text = 'Wiii';
        $units = 0;

        foreach ($font->codepoints($text) as $codepoint) {
            $units += $font->glyphWidth($font->glyphForCodepoint($codepoint));
        }

        $this->assertGreaterThan(0, $units);
        $this->assertEqualsWithDelta(
            $units * 12 / 1000,
            $engine->measureTextWidth($text, 'Universal', 12),
            0.001,
            'les chasses réelles de la police ne sont pas utilisées',
        );
    }

    /**
     * Le chemin public : PdfRenderer + un TextStyle dont la famille est
     * l'alias. getPdfFontName() ramenant toute famille inconnue à
     * Helvetica, l'alias doit être reconnu avant cette réduction.
     */
    public function test_renderer_uses_the_alias_as_a_font_family(): void
    {
        $font = TrueTypeFont::fromFile($this->fontPath);
        $sample = 'Общая сумма продаж составляет 1234 евро';

        if (! self::isFullyCovered($font, $sample)) {
            $this->markTestSkipped('la police système ne couvre pas le cyrillique');
        }

        $doc = \Paperdoc\Support\DocumentManager::create('pdf', 'Multilingue');
        $section = \Paperdoc\Document\Section::make('main');
        $section->addText($sample, \Paperdoc\Document\Style\TextStyle::make()->setFontFamily('Universal'));
        $doc->addSection($section);

        $renderer = new \Paperdoc\Renderers\PdfRenderer();
        $renderer->registerTrueTypeFont('Universal', $this->fontPath);

        $path = $this->tmpDir . '/document.pdf';
        $renderer->save($doc, $path);

        $raw = file_get_contents($path) ?: '';
        $this->assertStringContainsString('/FontFile2', $raw, 'la police n\'a pas été embarquée');

        $text = '';
        foreach ((new PdfParser())->parse($path)->getSections() as $parsed) {
            foreach ($parsed->getElements() as $element) {
                if (method_exists($element, 'getPlainText')) {
                    $text .= $element->getPlainText();
                }
            }
        }

        $this->assertStringContainsString($sample, $text);
    }

    /**
     * Le fichier entier pesait 423 Ko en latin et plus de 4 Mo en CJK,
     * quelle que soit la quantité de texte écrite.
     */
    public function test_only_the_used_glyphs_are_embedded(): void
    {
        $engine = new PdfEngine();
        $engine->registerTrueTypeFont('Universal', $this->fontPath);
        $engine->newPage();
        $engine->writeTextAt('Bonjour', 'Universal', 12, 50, 700);

        $pdf = $engine->output();
        $fontSize = filesize($this->fontPath) ?: PHP_INT_MAX;

        $this->assertLessThan(
            $fontSize / 4,
            strlen($pdf),
            'la police semble embarquée en entier'
        );
    }

    /**
     * Un « é » renvoie au « e » et à l'accent : oublier les composants
     * dessinerait du vide à leur place.
     */
    public function test_composite_glyph_components_are_pulled_in(): void
    {
        $font = TrueTypeFont::fromFile($this->fontPath);
        $sample = 'Résumé éàçüñ';

        if (! self::isFullyCovered($font, $sample)) {
            $this->markTestSkipped('la police système ne couvre pas les lettres accentuées');
        }

        $engine = new PdfEngine();
        $engine->registerTrueTypeFont('Universal', $this->fontPath);
        $engine->newPage();
        $engine->writeTextAt($sample, 'Universal', 14, 50, 700);

        $path = $this->tmpDir . '/composite.pdf';
        file_put_contents($path, $engine->output());

        $text = '';
        foreach ((new PdfParser())->parse($path)->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getPlainText')) {
                    $text .= $element->getPlainText();
                }
            }
        }

        $this->assertStringContainsString($sample, $text);
    }

    /**
     * Une collection embarque plusieurs polices dans un fichier ; l'index
     * choisit laquelle. La collection est fabriquée ici, les .ttc du
     * système étant des polices variables (CFF2) que PDF ne sait pas
     * embarquer telles quelles.
     */
    public function test_reads_a_font_from_a_collection(): void
    {
        $path = $this->tmpDir . '/collection.ttc';
        file_put_contents($path, $this->buildCollection([$this->fontPath, $this->fontPath]));

        foreach ([0, 1] as $index) {
            $font = TrueTypeFont::fromFile($path, $index);

            $this->assertGreaterThan(0, $font->getNumGlyphs());
            $this->assertGreaterThan(0, $font->glyphForCodepoint(0x41));
        }

        $this->expectException(\RuntimeException::class);
        TrueTypeFont::fromFile($path, 5);
    }

    public function test_rejects_a_file_that_is_not_truetype(): void
    {
        $path = $this->tmpDir . '/bogus.ttf';
        file_put_contents($path, str_repeat('not a font', 100));

        $this->expectException(\RuntimeException::class);
        TrueTypeFont::fromFile($path);
    }

    /**
     * Assemble des .ttf en une collection ttcf, en rebasant les décalages
     * de tables — ceux-ci sont absolus dans le fichier.
     *
     * @param string[] $paths
     */
    private function buildCollection(array $paths): string
    {
        $count = count($paths);
        $bodies = [];
        $offsets = [];
        $cursor = 12 + $count * 4;

        foreach ($paths as $path) {
            $ttf = file_get_contents($path) ?: '';
            $numTables = (int) (unpack('n', substr($ttf, 4, 2))[1] ?? 0);
            $offsets[] = $cursor;

            for ($i = 0; $i < $numTables; $i++) {
                $entry = 12 + $i * 16;
                $offset = (int) (unpack('N', substr($ttf, $entry + 8, 4))[1] ?? 0);
                $ttf = substr_replace($ttf, pack('N', $offset + $cursor), $entry + 8, 4);
            }

            $ttf .= str_repeat("\x00", (4 - (strlen($ttf) % 4)) % 4);
            $bodies[] = $ttf;
            $cursor += strlen($ttf);
        }

        $header = 'ttcf' . pack('N', 0x00010000) . pack('N', $count);

        foreach ($offsets as $offset) {
            $header .= pack('N', $offset);
        }

        return $header . implode('', $bodies);
    }

    private static function isFullyCovered(TrueTypeFont $font, string $text): bool
    {
        foreach ($font->codepoints($text) as $codepoint) {
            if ($font->glyphForCodepoint($codepoint) === 0) {
                return false;
            }
        }

        return true;
    }

    private function locateSystemFont(): ?string
    {
        $roots = ['/usr/share/fonts', '/usr/local/share/fonts', '/Library/Fonts', 'C:\\Windows\\Fonts'];

        foreach ($roots as $root) {
            if (! is_dir($root)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY,
            );

            foreach ($iterator as $file) {
                if (! $file instanceof \SplFileInfo || strtolower($file->getExtension()) !== 'ttf') {
                    continue;
                }

                try {
                    $font = TrueTypeFont::fromFile($file->getPathname());
                } catch (\RuntimeException) {
                    continue;
                }

                // Une police à usage général, pas un jeu d'icônes ni une
                // police de repli sans latin.
                if ($font->glyphForCodepoint(0x41) !== 0 && $font->glyphForCodepoint(0x69) !== 0) {
                    return $file->getPathname();
                }
            }
        }

        return null;
    }
}
