<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\RendererInterface;
use Paperdoc\Document\{Document, HorizontalRule, Image, Paragraph, Section, Table, TextRun};
use Paperdoc\Document\Style\{PageSetup, ParagraphStyle, RunningElement, TextStyle};
use Paperdoc\Enum\{Alignment, PageSize, VerticalAlignment};
use Paperdoc\Renderers\PdfRenderer;

class PdfRendererTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/paperdoc_pdf_' . uniqid();
        mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->tmpDir . '/*'));
        @rmdir($this->tmpDir);
    }

    private function outputPath(string $name = 'test.pdf'): string
    {
        return $this->tmpDir . '/' . $name;
    }

    public function test_implements_renderer_interface(): void
    {
        $this->assertInstanceOf(RendererInterface::class, new PdfRenderer());
    }

    public function test_format_is_pdf(): void
    {
        $this->assertSame('pdf', (new PdfRenderer())->getFormat());
    }

    public function test_render_returns_pdf_string(): void
    {
        $doc = Document::make('pdf', 'Test PDF');
        $section = Section::make('s1');
        $section->addText('Hello PDF World');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);

        $this->assertStringStartsWith('%PDF-1.4', $content);
        $this->assertStringContainsString('%%EOF', $content);
    }

    public function test_generates_valid_pdf(): void
    {
        $doc = Document::make('pdf', 'Test PDF');
        $section = Section::make('s1');
        $section->addText('Hello PDF World');
        $doc->addSection($section);

        $renderer = new PdfRenderer();
        $renderer->save($doc, $this->outputPath());

        $this->assertFileExists($this->outputPath());

        $content = file_get_contents($this->outputPath());
        $this->assertStringStartsWith('%PDF-1.4', $content);
        $this->assertStringContainsString('%%EOF', $content);
    }

    public function test_pdf_contains_title(): void
    {
        $doc = Document::make('pdf', 'Mon Rapport');
        $section = Section::make('s1');
        $section->addText('Content');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);
        $this->assertStringContainsString('Mon Rapport', $content);
    }

    public function test_pdf_contains_text(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $section->addText('Le texte du document');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);
        $this->assertStringContainsString('Le texte du document', $content);
    }

    public function test_pdf_has_catalog_and_pages(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $section->addText('Test');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);
        $this->assertStringContainsString('/Type /Catalog', $content);
        $this->assertStringContainsString('/Type /Pages', $content);
        $this->assertStringContainsString('/Type /Page', $content);
    }

    public function test_pdf_has_font_resources(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $section->addText('Font test');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);
        $this->assertStringContainsString('/Type /Font', $content);
        $this->assertStringContainsString('/BaseFont /Helvetica', $content);
    }

    public function test_pdf_with_bold_text(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $section->addText('Bold text', TextStyle::make()->setBold());
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);
        $this->assertStringContainsString('/BaseFont /Helvetica-Bold', $content);
    }

    public function test_pdf_with_table(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $table = Table::make();
        $table->setHeaders(['Col1', 'Col2']);
        $table->addRowFromArray(['A', 'B']);
        $section->addElement($table);
        $doc->addSection($section);

        $renderer = new PdfRenderer();
        $renderer->save($doc, $this->outputPath());

        $this->assertFileExists($this->outputPath());
        $this->assertGreaterThan(100, filesize($this->outputPath()));
    }

    public function test_pdf_multiple_sections_create_pages(): void
    {
        $doc = Document::make('pdf');
        $doc->addSection(Section::make('page1'));
        $doc->addSection(Section::make('page2'));
        $doc->addSection(Section::make('page3'));

        foreach ($doc->getSections() as $section) {
            $section->addText('Content on this page');
        }

        $content = (new PdfRenderer())->render($doc);
        $this->assertSame(3, substr_count($content, '/Type /Page '));
    }

    public function test_pdf_has_xref_and_trailer(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $section->addText('Test');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);
        $this->assertStringContainsString('xref', $content);
        $this->assertStringContainsString('trailer', $content);
        $this->assertStringContainsString('startxref', $content);
        $this->assertStringContainsString('/Root', $content);
    }

    public function test_pdf_with_paragraph_style(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $paraStyle = ParagraphStyle::make()
            ->setAlignment(Alignment::JUSTIFY)
            ->setSpaceBefore(10.0)
            ->setSpaceAfter(20.0);
        $p = Paragraph::make($paraStyle);
        $p->addRun(new TextRun('Styled paragraph'));
        $section->addElement($p);
        $doc->addSection($section);

        $renderer = new PdfRenderer();
        $renderer->save($doc, $this->outputPath());

        $this->assertFileExists($this->outputPath());
        $this->assertGreaterThan(100, filesize($this->outputPath()));
    }

    /**
     * Regression — v0.7.1 originally only honoured ParagraphStyle::alignment
     * inside a TextZone, so top-level Section paragraphs were silently
     * left-aligned even when set to CENTER / RIGHT / JUSTIFY.
     *
     * This test renders the *same* sentence four times (once per
     * alignment), extracts the first text-positioning operator
     * (`<x> <y> Td`) of each paragraph, and verifies they are
     * positioned distinctly (left < center < right). Justified text is
     * checked separately by the presence of the PDF word-spacing
     * operator (`Tw`).
     */
    public function test_paragraph_alignment_is_honored_at_section_root(): void
    {
        $renderer = new PdfRenderer();
        $sentence = 'Quick brown fox jumps over the lazy dog.';

        $tdX = [];
        foreach ([Alignment::LEFT, Alignment::CENTER, Alignment::RIGHT] as $alignment) {
            $doc = Document::make('pdf');
            $section = Section::make('s');
            $section->addElement(
                Paragraph::make(ParagraphStyle::make()->setAlignment($alignment))
                    ->addRun(new TextRun($sentence))
            );
            $doc->addSection($section);

            $content = $renderer->render($doc);
            $this->assertMatchesRegularExpression('/(?<x>-?\d+\.\d+) -?\d+\.\d+ Td/', $content);
            preg_match('/(?<x>-?\d+\.\d+) -?\d+\.\d+ Td/', $content, $m);
            $tdX[$alignment->value] = (float) $m['x'];
        }

        $this->assertLessThan($tdX['center'], $tdX['left'],
            'CENTER paragraph should start further right than LEFT');
        $this->assertLessThan($tdX['right'], $tdX['center'],
            'RIGHT paragraph should start further right than CENTER');

        $doc = Document::make('pdf');
        $section = Section::make('s');
        $section->addElement(
            Paragraph::make(ParagraphStyle::make()->setAlignment(Alignment::JUSTIFY))
                ->addRun(new TextRun(str_repeat($sentence . ' ', 5)))
        );
        $doc->addSection($section);
        $content = $renderer->render($doc);
        $this->assertMatchesRegularExpression('/\d+\.\d+ Tw/', $content,
            'JUSTIFY paragraphs should emit a word-spacing (Tw) operator');
    }

    /**
     * Regression — v0.7.3 fixes the visible overlap between a small
     * "eyebrow" line and a much-larger title that follows it. The
     * symptom: the title's ascender extends above the eyebrow's
     * baseline because the renderer only advanced by `lineHeight` of
     * the previous (small) line. Now the renderer reserves additional
     * vertical space when the new ascender is taller than the previous
     * line height.
     *
     * We render an 8pt eyebrow followed by a 28pt title, extract the
     * vertical positions of the two `Td` operators (Y = baseline in
     * PDF coords, larger Y = higher on page), and assert that the
     * baseline gap is at least equal to the title's ascender — i.e.
     * the title's top sits AT MOST at the eyebrow's baseline (no
     * overlap), not above it.
     */
    public function test_small_eyebrow_then_large_title_does_not_overlap(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s');

        $section->addElement(
            Paragraph::make()->addRun(new TextRun(
                'TABLE',
                TextStyle::make()->setFontSize(8.0)->setColor('#888888'),
            ))
        );

        $section->addElement(
            Paragraph::make()->addRun(new TextRun(
                'Sommaire',
                TextStyle::make()->setBold()->setFontSize(28.0),
            ))
        );

        $doc->addSection($section);
        $content = (new PdfRenderer())->render($doc);

        preg_match_all('/(?<x>-?\d+\.\d+) (?<y>-?\d+\.\d+) Td/', $content, $matches, PREG_SET_ORDER);
        $this->assertGreaterThanOrEqual(2, count($matches),
            'Expected at least two Td operators (eyebrow + title)');

        // PDF Y axis: bigger = higher. The eyebrow is the FIRST Td
        // (drawn at top of page) so it has a LARGER Y than the title.
        $eyebrowBaselineY = (float) $matches[0]['y'];
        $titleBaselineY   = (float) $matches[1]['y'];

        $baselineGap = $eyebrowBaselineY - $titleBaselineY;

        // The title is 28pt with ascender ~770/1000em → ~21.6pt above
        // its baseline. We need at least that much gap so glyph tops
        // don't punch through the eyebrow's baseline.
        $this->assertGreaterThanOrEqual(21.0, $baselineGap,
            "Eyebrow→title baseline gap ({$baselineGap}pt) must accommodate "
            . 'the title ascender (~21.6pt) to prevent glyph overlap');
    }

    /**
     * Regression — C1: the page background image must be drawn BEFORE
     * the running header/footer so the latter is visible on top of
     * the image (otherwise the footer disappears under the image on
     * full-bleed/cover pages).
     */
    public function test_background_image_is_drawn_before_header_footer(): void
    {
        // Generate a tiny 1px synthetic JPEG to serve as page background.
        $bgPath = sys_get_temp_dir() . '/paperdoc_bg_smoke_' . uniqid() . '.jpg';
        $im = imagecreatetruecolor(2, 2);
        imagefilledrectangle($im, 0, 0, 1, 1, imagecolorallocate($im, 255, 0, 0));
        imagejpeg($im, $bgPath, 90);
        unset($im);

        try {
            $doc = Document::make('pdf');
            $doc->setFooter(
                RunningElement::make('Page {page}')
                    ->setStyle(TextStyle::make()->setFontSize(10))
                    ->setAlignment(Alignment::CENTER)
            );

            $section = Section::make('cover')->setPageSetup(
                PageSetup::fromSize(PageSize::A5)
                    ->setBackgroundImage(Image::make($bgPath))
                    ->setBackgroundSize(PageSetup::BG_SIZE_COVER)
            );
            $doc->addSection($section);

            $content = (new PdfRenderer())->render($doc);

            // The image XObject must be invoked (`/Im1 Do`) before the
            // footer text (`(Page 1) Tj`) in the page stream. We locate
            // both and assert their offsets.
            $imagePos  = strpos($content, ' Do');
            $footerPos = strpos($content, '(Page 1)');

            $this->assertNotFalse($imagePos, 'expected image XObject draw');
            $this->assertNotFalse($footerPos, 'expected footer text');
            $this->assertLessThan($footerPos, $imagePos,
                'background image must be drawn BEFORE footer text');
        } finally {
            @unlink($bgPath);
        }
    }

    /**
     * Regression — C2: when both backgroundColor and backgroundImage
     * are set, the COLOR is laid down first (entire page) then the
     * IMAGE is drawn on top. With `BG_SIZE_CONTAIN` this means the
     * color shows through in the empty bands left by the contained
     * image — a useful affordance documented for users.
     */
    public function test_background_color_underlays_background_image(): void
    {
        $bgPath = sys_get_temp_dir() . '/paperdoc_bg_color_' . uniqid() . '.jpg';
        $im = imagecreatetruecolor(2, 2);
        imagefilledrectangle($im, 0, 0, 1, 1, imagecolorallocate($im, 0, 255, 0));
        imagejpeg($im, $bgPath, 90);
        unset($im);

        try {
            $doc = Document::make('pdf');
            $section = Section::make('p')->setPageSetup(
                PageSetup::fromSize(PageSize::A5)
                    ->setBackgroundColor('#FFEEDD')
                    ->setBackgroundImage(Image::make($bgPath))
                    ->setBackgroundSize(PageSetup::BG_SIZE_CONTAIN)
            );
            $doc->addSection($section);

            $content = (new PdfRenderer())->render($doc);

            // The color rectangle (rg + re + f) must come before the
            // image draw (` Do`) — otherwise the color would punch
            // out the image instead of underlaying it.
            // #FFEEDD → 255/255 = 1.00 ; 238/255 = 0.93 ; 221/255 = 0.87
            $colorPos = strpos($content, '1.00 0.93 0.87 rg');
            $imagePos = strpos($content, ' Do');

            $this->assertNotFalse($colorPos, 'expected page background color fill');
            $this->assertNotFalse($imagePos, 'expected page background image draw');
            $this->assertLessThan($imagePos, $colorPos,
                'background color must underlay the image');
        } finally {
            @unlink($bgPath);
        }
    }

    public function test_pdf_with_multiple_text_runs(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $p = new Paragraph();
        $p->addRun(new TextRun('Normal '));
        $p->addRun(new TextRun('Bold ', TextStyle::make()->setBold()));
        $p->addRun(new TextRun('Italic', TextStyle::make()->setItalic()));
        $section->addElement($p);
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);
        $this->assertStringContainsString('Normal', $content);
        $this->assertStringContainsString('Bold', $content);
        $this->assertStringContainsString('Italic', $content);
    }

    public function test_creates_directory_if_needed(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s1');
        $section->addText('Deep directory');
        $doc->addSection($section);

        $path = $this->tmpDir . '/deep/nested/dir/output.pdf';
        $renderer = new PdfRenderer();
        $renderer->save($doc, $path);

        $this->assertFileExists($path);

        unlink($path);
        rmdir($this->tmpDir . '/deep/nested/dir');
        rmdir($this->tmpDir . '/deep/nested');
        rmdir($this->tmpDir . '/deep');
    }

    /* =============================================================
     | v0.8.0 — A3 / A4 / B3 / B4 / B5 regressions
     |============================================================= */

    /**
     * A3 — A section that calls hideFooter() must NOT emit the
     * document-level footer text on its page, while sections that
     * don't override still render it.
     */
    public function test_hide_footer_suppresses_document_footer_on_section(): void
    {
        $doc = Document::make('pdf');
        $doc->setFooter(RunningElement::make('PAGE-FOOTER-{page}'));

        // Cover section — footer is suppressed.
        $cover = Section::make('cover')->hideFooter();
        $cover->addText('Cover');
        $doc->addSection($cover);

        // Body section — footer inherits from document.
        $body = Section::make('body');
        $body->addText('Body');
        $doc->addSection($body);

        $content = (new PdfRenderer())->render($doc);

        // Page 1 should NOT contain its footer literal; page 2 should.
        $this->assertStringNotContainsString('(PAGE-FOOTER-1)', $content);
        $this->assertStringContainsString('(PAGE-FOOTER-2)', $content);
    }

    /**
     * A3 — Section::setFooter() overrides the document-level footer
     * for that section's page.
     */
    public function test_section_footer_override_replaces_document_footer(): void
    {
        $doc = Document::make('pdf');
        $doc->setFooter(RunningElement::make('DOC-{page}'));

        $section = Section::make('s')->setFooter(RunningElement::make('SECTION-{page}'));
        $section->addText('hi');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);

        $this->assertStringContainsString('(SECTION-1)', $content);
        $this->assertStringNotContainsString('(DOC-1)', $content);
    }

    /**
     * A4 — A section with verticalAlignment=CENTER triggers a
     * `q ... 1 0 0 1 0 dy cm ... Q` translation block in the page
     * stream so the (small) content is shifted down towards the page
     * centre. We don't measure the exact dy here (that depends on
     * font metrics), only that the wrapping was inserted.
     */
    public function test_vertical_alignment_center_emits_cm_translation(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s')
            ->setPageSetup(PageSetup::fromSize(PageSize::A4))
            ->setVerticalAlignment(VerticalAlignment::CENTER);
        $section->addText('Just one line of text on a big page.');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);

        $this->assertMatchesRegularExpression(
            '/q\s+1 0 0 1 0 -\d+\.\d+ cm/',
            $content,
            'expected a `q 1 0 0 1 0 -dy cm` translation wrapping the section content'
        );
    }

    public function test_vertical_alignment_top_emits_no_cm_translation(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s')
            ->setPageSetup(PageSetup::fromSize(PageSize::A4));
        $section->addText('hi');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);

        $this->assertDoesNotMatchRegularExpression(
            '/q\s+1 0 0 1 0 -\d+\.\d+ cm/',
            $content,
            'top-aligned sections must not emit any vertical translation'
        );
    }

    /**
     * B3 — ParagraphStyle::setFirstLineIndent shifts the first line
     * to the right by the requested number of points. We capture the
     * `Td` operator of the first text line and assert its X
     * coordinate differs by exactly the indent amount between the
     * two variants. This is more robust than counting wraps (which
     * may or may not change depending on word boundaries).
     */
    public function test_first_line_indent_shifts_first_line_x(): void
    {
        $build = function (float $indent) {
            $doc = Document::make('pdf');
            $section = Section::make('s')->setPageSetup(PageSetup::fromSize(PageSize::A5));
            $section->addElement(
                (new Paragraph())
                    ->setStyle(ParagraphStyle::make()->setFirstLineIndent($indent))
                    ->addRun(new TextRun('First-line indent test.'))
            );
            $doc->addSection($section);

            $content = (new PdfRenderer())->render($doc);

            // Pull out the first `<x> <y> Td` after a BT.
            preg_match('/BT\s+[^Z]*?(\d+\.\d+) (\d+\.\d+) Td/', $content, $m);

            return $m[1] ?? null;
        };

        $xWithout = (float) $build(0.0);
        $xWith    = (float) $build(36.0);

        $this->assertGreaterThan(0.0, $xWithout);
        $this->assertEqualsWithDelta(36.0, $xWith - $xWithout, 0.05,
            'a 36pt firstLineIndent should move the first line\'s X by 36pt');
    }

    /**
     * B4 — TextStyle::setLetterSpacing emits a `Tc` operator in the
     * page stream.
     */
    public function test_letter_spacing_emits_tc_operator(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s');
        $section->addElement(
            (new Paragraph())->addRun(new TextRun(
                'WIDE',
                TextStyle::make()->setLetterSpacing(2.0)
            ))
        );
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);

        $this->assertMatchesRegularExpression('/2\.000 Tc/', $content,
            'letter-spacing of 2pt should emit a `2.000 Tc` operator');
        $this->assertStringContainsString('0 Tc', $content,
            'character spacing should be reset to 0 after the BT/ET block');
    }

    /**
     * B5 — HorizontalRule renders as a stroked PDF line.
     */
    public function test_horizontal_rule_emits_stroked_line(): void
    {
        $doc = Document::make('pdf');
        $section = Section::make('s');
        $section->addText('Above');
        $section->addRule()->setWidth(200.0)->setColor('#FF0000')->setThickness(1.5);
        $section->addText('Below');
        $doc->addSection($section);

        $content = (new PdfRenderer())->render($doc);

        // 1.50 w → line width op
        $this->assertMatchesRegularExpression('/1\.50 w/', $content,
            'rule thickness should be set via `w` operator');
        // 1.00 0.00 0.00 RG → red stroke colour
        $this->assertStringContainsString('1.00 0.00 0.00 RG', $content,
            'rule colour should be applied via `RG`');
        // m / l / S → moveto + lineto + stroke
        $this->assertMatchesRegularExpression('/[\d\.]+ [\d\.]+ m [\d\.]+ [\d\.]+ l S/', $content,
            'rule should be drawn with moveto+lineto+stroke');
    }
}
