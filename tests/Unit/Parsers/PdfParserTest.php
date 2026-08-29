<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Parsers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\ParserInterface;
use Paperdoc\Document\Paragraph;
use Paperdoc\Document\Style\TextStyle;
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

    public function test_parse_pdf_with_digits_only_cmap_codes(): void
    {
        $pdf = <<<'PDF'
        %PDF-1.4
        1 0 obj
        << /Type /Catalog /Pages 2 0 R >>
        endobj
        2 0 obj
        << /Type /Pages /Kids [3 0 R] /Count 1 >>
        endobj
        3 0 obj
        << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Font << /F1 5 0 R >> /Contents 4 0 R >>
        endobj
        4 0 obj
        << /Length 44 >>
        stream
        BT
        /F1 12 Tf
        72 770 Td
        <30003100> Tj
        ET
        endstream
        endobj
        5 0 obj
        << /Type /Font /Subtype /Type0 /BaseFont /AAAAAA+Subset /ToUnicode 6 0 R >>
        endobj
        6 0 obj
        << /Length 200 >>
        stream
        /CIDInit /ProcSet findresource begin
        12 dict begin
        begincmap
        1 begincodespacerange
        <0000> <FFFF>
        endcodespacerange
        2 beginbfchar
        <3000> <0048>
        <3100> <0049>
        endbfchar
        endcmap
        end
        end
        endstream
        endobj
        trailer
        << /Root 1 0 R >>
        %%EOF
        PDF;

        $path = $this->tmpDir . '/digits-only-cmap.pdf';
        file_put_contents($path, $pdf);

        $parsed = $this->parser->parse($path);

        $this->assertStringContainsString('HI', $this->collectText($parsed));
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

    public function test_tj_array_keeps_word_spaces(): void
    {
        $pdf = <<<'PDF'
        %PDF-1.4
        1 0 obj
        << /Type /Catalog /Pages 2 0 R >>
        endobj
        2 0 obj
        << /Type /Pages /Kids [3 0 R] /Count 1 >>
        endobj
        3 0 obj
        << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Font << /F1 5 0 R >> /Contents 4 0 R >>
        endobj
        4 0 obj
        << /Length 200 >>
        stream
        BT
        /F1 12 Tf
        1 0 0 1 72 770 Tm
        [(C)5(o)-8(m)7(m)7(e)-8(r)7(ce)-8(l)5(y )17(a)-8(l)5(r)7(e)-8(a)13(d)-8(y )17(e)-8(xi)5(st)-4(s )17(a)-8(s )17(a)-8( )-4(w)5(o)-8(r)7(ki)5(n)13(g)-8( )17(p)-8(l)5(a)-8(t)17(f)-4(o)-8(r)7(m)7(.)17( )-4(Th)-6(e)13( )-4(p)13(o)-8(i)5(n)-8(t)-4( )17(o)13(f)-4( )-4(sa)-8(l)5(e)13( )-4(a)13(n)-8(d)13( )] TJ
        ET
        endstream
        endobj
        5 0 obj
        << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>
        endobj
        trailer
        << /Root 1 0 R >>
        %%EOF
        PDF;

        $path = $this->tmpDir . '/tj-spaces.pdf';
        file_put_contents($path, $pdf);

        $parsed = $this->parser->parse($path);
        $texts = $this->collectText($parsed);

        $this->assertStringContainsString('Commercely already exists as a working platform.', $texts);
        $this->assertStringContainsString('The point of sale and', $texts);
    }

    public function test_winansi_high_range_survives_round_trip(): void
    {
        // 0x80–0x9F est imprimable en WinAnsi (l'encodage que PdfEngine
        // déclare) mais vide de sens en ISO-8859-1.
        $source = 'Le tarif — dit “premium” — passe à 30 € par cœur de calcul…';

        $doc = DocumentManager::create('pdf', 'WinAnsi');
        $section = \Paperdoc\Document\Section::make('main');
        $section->addText($source);
        $doc->addSection($section);

        $path = $this->tmpDir . '/winansi.pdf';
        DocumentManager::save($doc, $path);

        $this->assertStringContainsString($source, $this->collectText($this->parser->parse($path)));
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

    public function test_multi_column_prose_is_not_detected_as_table(): void
    {
        $pdf = <<<'PDF'
        %PDF-1.4
        1 0 obj
        << /Type /Catalog /Pages 2 0 R >>
        endobj
        2 0 obj
        << /Type /Pages /Kids [3 0 R] /Count 1 >>
        endobj
        3 0 obj
        << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Font << /F1 5 0 R >> /Contents 4 0 R >>
        endobj
        4 0 obj
        << /Length 900 >>
        stream
        BT
        /F1 12 Tf
        72 770 Td
        (realisation) Tj
        ET
        BT
        /F1 12 Tf
        160 770 Td
        (d) Tj
        ET
        BT
        /F1 12 Tf
        190 770 Td
        (es) Tj
        ET
        BT
        /F1 12 Tf
        230 770 Td
        (taches) Tj
        ET
        BT
        /F1 12 Tf
        72 750 Td
        (realisation) Tj
        ET
        BT
        /F1 12 Tf
        160 750 Td
        (d) Tj
        ET
        BT
        /F1 12 Tf
        190 750 Td
        (es) Tj
        ET
        BT
        /F1 12 Tf
        230 750 Td
        (taches) Tj
        ET
        endstream
        endobj
        5 0 obj
        << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>
        endobj
        trailer
        << /Root 1 0 R >>
        %%EOF
        PDF;

        $path = $this->tmpDir . '/prose-columns.pdf';
        file_put_contents($path, $pdf);

        $parsed = $this->parser->parse($path);
        $tables = [];

        foreach ($parsed->getSections() as $section) {
            foreach ($section->getElements() as $el) {
                if ($el instanceof \Paperdoc\Document\Table) {
                    $tables[] = $el;
                }
            }
        }

        $this->assertCount(0, $tables);
        $this->assertStringContainsString('realisation', $this->collectText($parsed));
        $this->assertStringContainsString('taches', $this->collectText($parsed));
    }

    public function test_highlight_is_preserved_when_parsing_generated_pdf(): void
    {
        $doc = DocumentManager::create('pdf', 'Highlight Parse');
        $section = \Paperdoc\Document\Section::make('main');
        $section->addText('Texte surligne', TextStyle::make()->setHighlight('#FFF3B0'));
        $doc->addSection($section);

        $pdfPath = $this->tmpDir . '/highlight-parse.pdf';
        DocumentManager::save($doc, $pdfPath);

        $parsed = $this->parser->parse($pdfPath);
        $highlight = null;

        foreach ($parsed->getSections() as $section) {
            foreach ($section->getElements() as $el) {
                if (! $el instanceof Paragraph) {
                    continue;
                }

                foreach ($el->getRuns() as $run) {
                    $style = $run->getStyle();

                    if ($style?->getHighlight() !== null) {
                        $highlight = $style->getHighlight();
                    }
                }
            }
        }

        $this->assertNotNull($highlight, 'Expected a highlight style on parsed text');
        $this->assertStringStartsWith('#FFF', strtoupper($highlight));
    }

    public function test_word_export_tj_kerning_does_not_split_words(): void
    {
        // Pattern extracted from Word-generated PDFs: large negative TJ kerning
        // values adjust glyph spacing but must not insert word breaks.
        $pdf = <<<'PDF'
        %PDF-1.4
        1 0 obj
        << /Type /Catalog /Pages 2 0 R >>
        endobj
        2 0 obj
        << /Type /Pages /Kids [3 0 R] /Count 1 >>
        endobj
        3 0 obj
        << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Font << /F1 5 0 R >> /Contents 4 0 R >>
        endobj
        4 0 obj
        << /Length 500 >>
        stream
        BT
        /F1 12 Tf
        1 0 0 1 90 480.91 Tm
        [(L')-8(objecti)] TJ
        ET
        BT
        /F1 12 Tf
        1 0 0 1 133.94 480.91 Tm
        [(f )-296(de)4( )-299(c)-5(e)4(tt)-3(e)4( )-299(a)4(ppl)-11(ica)6(ti)-3(on )-299(e)4(st )-302(de)4( )-299(four)6(nis )-302(a)4(ux)-9( )-299(c)4(li)-3(e)4(nts )-302(une)4( )-299(sim)-4(pl)8(ific)3(a)4(ti)-3(on )-299(de)4( )-299(g)-4(esti)] TJ
        ET
        BT
        1 0 0 1 529.66 480.91 Tm
        [(on)] TJ
        ET
        BT
        /F1 12 Tf
        1 0 0 1 90 460 Tm
        [(managem)4(ent )-299(of )-302(the)] TJ
        ET
        endstream
        endobj
        5 0 obj
        << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>
        endobj
        trailer
        << /Root 1 0 R >>
        %%EOF
        PDF;

        $path = $this->tmpDir . '/word-tj-kerning.pdf';
        file_put_contents($path, $pdf);

        $texts = $this->collectText($this->parser->parse($path));

        $this->assertStringContainsString('objectif', $texts);
        $this->assertStringContainsString('gestion', $texts);
        $this->assertStringContainsString('management', $texts);
        $this->assertStringNotContainsString('objecti f', $texts);
        $this->assertStringNotContainsString('gesti on', $texts);
        $this->assertStringNotContainsString('managem ent', $texts);
    }

    public function test_tj_trailing_spaces_preserve_word_boundaries(): void
    {
        // Word encodes inter-word spaces inside TJ literals: (s ), (et ), ( B)
        $pdf = <<<'PDF'
        %PDF-1.4
        1 0 obj
        << /Type /Catalog /Pages 2 0 R >>
        endobj
        2 0 obj
        << /Type /Pages /Kids [3 0 R] /Count 1 >>
        endobj
        3 0 obj
        << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Font << /F1 5 0 R >> /Contents 4 0 R >>
        endobj
        4 0 obj
        << /Length 600 >>
        stream
        BT
        /F1 12 Tf
        1 0 0 1 90 526.63 Tm
        [(frère)4(s )] TJ
        ET
        BT
        /F1 12 Tf
        1 0 0 1 121.3 526.63 Tm
        [(N)-8(a)4(d)4(i)-3(r )-299(B)-5(e)4(rrouane )-302(et )] TJ
        ET
        BT
        /F1 12 Tf
        1 0 0 1 219.5 526.63 Tm
        [(A)-5(i)-3(m)-4(e)-5(n)-8(e)4( B)] TJ
        ET
        BT
        /F1 12 Tf
        1 0 0 1 266.2 526.63 Tm
        [(ahri)] TJ
        ET
        BT
        /F1 12 Tf
        1 0 0 1 424.3 526.63 Tm
        [(eam Open M)] TJ
        ET
        BT
        /F1 12 Tf
        1 0 0 1 486.9 526.63 Tm
        [(ind)] TJ
        ET
        BT
        /F1 12 Tf
        1 0 0 1 502.3 526.63 Tm
        [(s)] TJ
        ET
        endstream
        endobj
        5 0 obj
        << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>
        endobj
        trailer
        << /Root 1 0 R >>
        %%EOF
        PDF;

        $path = $this->tmpDir . '/tj-trailing-spaces.pdf';
        file_put_contents($path, $pdf);

        $texts = $this->collectText($this->parser->parse($path));

        $this->assertStringContainsString('frères Nadir Berrouane et Aimene Bahri', $texts);
        $this->assertStringContainsString('Open Minds', $texts);
        $this->assertStringNotContainsString('frèresNadir', $texts);
        $this->assertStringNotContainsString('M ind', $texts);
        $this->assertStringNotContainsString('B ahri', $texts);
    }

    public function test_flatedecode_image_png_ihdr_uses_32bit_dimensions(): void
    {
        $width = 4;
        $height = 2;
        $pixels = '';
        for ($i = 0; $i < $width * $height * 3; $i++) {
            $pixels .= chr(255);
        }

        $stream = gzcompress($pixels);
        $streamLen = strlen($stream);

        $pdf = <<<PDF
        %PDF-1.4
        1 0 obj
        << /Type /Catalog /Pages 2 0 R >>
        endobj
        2 0 obj
        << /Type /Pages /Kids [3 0 R] /Count 1 >>
        endobj
        3 0 obj
        << /Type /Page /Parent 2 0 R /MediaBox [0 0 200 200]
           /Resources << /XObject << /Im1 4 0 R >> >>
           /Contents 5 0 R >>
        endobj
        4 0 obj
        << /Type /XObject /Subtype /Image /Width {$width} /Height {$height}
           /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /FlateDecode /Length {$streamLen} >>
        stream
        {$stream}
        endstream
        endobj
        5 0 obj
        << /Length 44 >>
        stream
        q 100 0 0 50 50 100 cm /Im1 Do Q
        endstream
        endobj
        xref
        0 6
        trailer
        << /Root 1 0 R /Size 6 >>
        %%EOF
        PDF;

        $path = $this->tmpDir . '/flatedecode-image.pdf';
        file_put_contents($path, $pdf);

        $doc = $this->parser->parse($path);
        $elements = $doc->getSections()[0]->getElements();
        $image = null;

        foreach ($elements as $element) {
            if ($element instanceof \Paperdoc\Document\Image) {
                $image = $element;
                break;
            }
        }

        $this->assertNotNull($image);
        $this->assertSame($width, $image->getWidth());
        $this->assertSame($height, $image->getHeight());

        $info = getimagesizefromstring($image->getData());
        $this->assertSame($width, $info[0]);
        $this->assertSame($height, $info[1]);
    }

    public function test_font_size_bold_and_family_are_preserved(): void
    {
        $pdf = <<<'PDF'
        %PDF-1.4
        1 0 obj
        << /Type /Catalog /Pages 2 0 R >>
        endobj
        2 0 obj
        << /Type /Pages /Kids [3 0 R] /Count 1 >>
        endobj
        3 0 obj
        << /Type /Page /Parent 2 0 R /MediaBox [0 0 200 200]
           /Resources << /Font << /F1 5 0 R /F2 7 0 R >> >>
           /Contents 4 0 R >>
        endobj
        4 0 obj
        << /Length 180 >>
        stream
        BT
        /F2 20 Tf
        1 0 0 1 50 150 Tm
        (Title Bold) Tj
        ET
        BT
        /F1 12 Tf
        1 0 0 1 50 120 Tm
        (Body text) Tj
        ET
        endstream
        endobj
        5 0 obj
        << /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>
        endobj
        7 0 obj
        << /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>
        endobj
        xref
        0 8
        trailer
        << /Root 1 0 R /Size 8 >>
        %%EOF
        PDF;

        $path = $this->tmpDir . '/font-styles.pdf';
        file_put_contents($path, $pdf);

        $parsed = $this->parser->parse($path);
        $runs = [];

        foreach ($parsed->getSections()[0]->getElements() as $element) {
            if ($element instanceof Paragraph) {
                foreach ($element->getRuns() as $run) {
                    $runs[] = $run;
                }
            }
        }

        $this->assertGreaterThanOrEqual(2, count($runs));

        $titleRun = null;
        $bodyRun = null;

        foreach ($runs as $run) {
            if (str_contains($run->getText(), 'Title Bold')) {
                $titleRun = $run;
            }

            if (str_contains($run->getText(), 'Body text')) {
                $bodyRun = $run;
            }
        }

        $this->assertNotNull($titleRun);
        $this->assertNotNull($bodyRun);
        $this->assertSame(20.0, $titleRun->getStyle()?->getFontSize());
        $this->assertTrue($titleRun->getStyle()?->isBold());
        $this->assertNull($bodyRun->getStyle());
    }

    public function test_escaped_closing_paren_in_tj_string(): void
    {
        $pdf = <<<'PDF'
        %PDF-1.4
        1 0 obj
        << /Type /Catalog /Pages 2 0 R >>
        endobj
        2 0 obj
        << /Type /Pages /Kids [3 0 R] /Count 1 >>
        endobj
        3 0 obj
        << /Type /Page /Parent 2 0 R /MediaBox [0 0 200 200]
           /Resources << /Font << /F1 5 0 R >> >>
           /Contents 4 0 R >>
        endobj
        4 0 obj
        << /Length 160 >>
        stream
        BT
        /F1 12 Tf
        1 0 0 1 50 120 Tm
        (depart\)) Tj
        ET
        BT
        1 0 0 1 50 100 Tm
        (TIC\)) Tj
        ET
        endstream
        endobj
        5 0 obj
        << /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>
        endobj
        xref
        0 6
        trailer
        << /Root 1 0 R /Size 6 >>
        %%EOF
        PDF;

        $path = $this->tmpDir . '/escaped-paren.pdf';
        file_put_contents($path, $pdf);

        $text = $this->collectText($this->parser->parse($path));

        $this->assertStringContainsString('depart)', $text);
        $this->assertStringContainsString('TIC)', $text);
        $this->assertStringNotContainsString('depart\\', $text);
    }

    public function test_font_state_persists_across_bt_blocks_for_hex_tj(): void
    {
        $cmap = <<<'CMAP'
        /CIDInit /ProcSet findresource begin
        12 dict begin
        begincmap
        /CIDSystemInfo << /Registry (Adobe) /Ordering (UCS) /Supplement 0 >> def
        /CMapName /Test def
        /CMapType 2 def
        1 begincodespacerange
        <0000> <FFFF>
        endcodespacerange
        1 beginbfchar
        <0044> <0064>
        endbfchar
        endcmap
        CMapName currentdict /CMap defineresource pop
        end
        end
        CMAP;

        $cmapLen = strlen($cmap);

        $pdf = <<<PDF
        %PDF-1.4
        1 0 obj
        << /Type /Catalog /Pages 2 0 R >>
        endobj
        2 0 obj
        << /Type /Pages /Kids [3 0 R] /Count 1 >>
        endobj
        3 0 obj
        << /Type /Page /Parent 2 0 R /MediaBox [0 0 200 200]
           /Resources << /Font << /F3 6 0 R >> >>
           /Contents 4 0 R >>
        endobj
        4 0 obj
        << /Length 120 >>
        stream
        BT
        /F3 12 Tf
        1 0 0 1 50 120 Tm
        [<0044>] TJ
        ET
        BT
        1 0 0 1 50 100 Tm
        [<0044>] TJ
        ET
        endstream
        endobj
        6 0 obj
        << /Type /Font /Subtype /Type0 /BaseFont /Times-Roman /Encoding /Identity-H
           /DescendantFonts [7 0 R] /ToUnicode 8 0 R >>
        endobj
        7 0 obj
        << /Type /Font /Subtype /CIDFontType2 /BaseFont /Times-Roman /CIDToGIDMap /Identity >>
        endobj
        8 0 obj
        << /Length {$cmapLen} >>
        stream
        {$cmap}
        endstream
        endobj
        xref
        0 9
        trailer
        << /Root 1 0 R /Size 9 >>
        %%EOF
        PDF;

        $path = $this->tmpDir . '/font-carry-hex.pdf';
        file_put_contents($path, $pdf);

        $text = $this->collectText($this->parser->parse($path));

        $this->assertSame("d\nd\n", $text);
    }

    public function test_word_export_mixed_bt_blocks_decode_identity_h_text(): void
    {
        $pdfPath = '/home/akramzerarka/Téléchargements/ATT00209.pdf';

        if (! is_readable($pdfPath)) {
            $this->markTestSkipped('ATT00209.pdf fixture not available');
        }

        $parsed = $this->parser->parse($pdfPath);
        $text = $this->collectText($parsed);

        $this->assertStringContainsString('La convergence des données', $text);
        $this->assertStringContainsString('la transformation numérique', $text);
        $this->assertStringNotContainsString('ODWUDQV', $text);
        $this->assertStringNotContainsString('qUL', $text);
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
