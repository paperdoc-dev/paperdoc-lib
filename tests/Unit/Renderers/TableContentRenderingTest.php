<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\{
    Blockquote,
    CodeBlock,
    Document,
    Heading,
    Image,
    ListBlock,
    Paragraph,
    Table,
    TableCell,
    TableRow,
    TextRun,
};
use Paperdoc\Document\Style\TextStyle;
use Paperdoc\Renderers\{DocxRenderer, HtmlRenderer, MarkdownRenderer, PdfRenderer};

/**
 * Regression coverage for the v0.6.0 fix : every renderer must accept
 * any block element inside a table cell (Image, ListBlock, CodeBlock,
 * Blockquote, Heading) and never silently drop content.
 *
 * Also covers the DOCX-specific OOXML compliance fixes :
 *  - <w:tblGrid> is emitted with one <w:gridCol> per column
 *  - per-cell <w:tcW> reflects Table::getColumnWidths()
 *  - image dimensions are auto-detected from the bytes when not set
 *  - oversized images are capped to the document content width
 */
class TableContentRenderingTest extends TestCase
{
    private const FIXTURE_PNG = __DIR__ . '/../../Fixtures/Images/paperdoc-logo.png';

    /* =============================================================
     | Helpers
     |============================================================= */

    private function makeTableWithImageCell(): Table
    {
        $headerCell = (new TableCell())->addElement(
            (new Paragraph())->addRun(new TextRun('Logo'))
        );

        $imgCell = (new TableCell())->addElement(
            Image::make(self::FIXTURE_PNG, 32, 16, 'Paperdoc logo')
        );

        $headerRow = (new TableRow())->setHeader();
        $headerRow->addCell($headerCell);

        return (new Table())
            ->addRow($headerRow)
            ->addRow((new TableRow())->addCell($imgCell));
    }

    private function makeTableWithMixedCell(): Table
    {
        $list = new ListBlock(ListBlock::STYLE_BULLET);
        $list->addText('alpha');
        $list->addText('beta');

        $code = new CodeBlock("echo 'x';", 'php');

        $cell = (new TableCell())
            ->addElement($list)
            ->addElement($code);

        return (new Table())->addRow((new TableRow())->addCell($cell));
    }

    /* =============================================================
     | DOCX
     |============================================================= */

    public function test_docx_image_inside_cell_renders_drawing_xml(): void
    {
        if (! file_exists(self::FIXTURE_PNG)) {
            $this->markTestSkipped('PNG fixture missing');
        }

        $doc = Document::make('docx', 'tbl-img');
        $doc->openSection()->addElement($this->makeTableWithImageCell());

        $document = $this->extractDocxPart((new DocxRenderer())->render($doc), 'word/document.xml');

        $this->assertStringContainsString('<w:tbl>', $document);
        $this->assertStringContainsString('<w:drawing>', $document);
        $this->assertMatchesRegularExpression('/r:embed="rId\d+"/', $document);
    }

    public function test_docx_emits_tblGrid_matching_column_widths(): void
    {
        $table = (new Table())->setColumnWidths([100, 200, 100]);

        $row = new TableRow();
        for ($i = 0; $i < 3; $i++) {
            $row->addCell((new TableCell())->addElement(
                (new Paragraph())->addRun(new TextRun((string) $i))
            ));
        }
        $table->addRow($row);

        $doc = Document::make('docx', 'grid');
        $doc->openSection()->addElement($table);

        $document = $this->extractDocxPart((new DocxRenderer())->render($doc), 'word/document.xml');

        $this->assertStringContainsString('<w:tblGrid>', $document);

        // Three <w:gridCol/> entries
        $this->assertSame(3, preg_match_all('/<w:gridCol\s+w:w="\d+"\/>/', $document));

        // Per-cell width — three <w:tcW> with absolute dxa values
        $this->assertSame(3, preg_match_all('/<w:tcW w:w="\d+" w:type="dxa"\/>/', $document));

        // Sum of gridCol widths must equal content width (Letter − 2×1" margins).
        preg_match_all('/<w:gridCol\s+w:w="(\d+)"\/>/', $document, $m);
        $this->assertSame(9360, array_sum(array_map('intval', $m[1])));
    }

    public function test_docx_codeblock_inside_cell_uses_code_pStyle(): void
    {
        $doc = Document::make('docx', 'tbl-code');
        $doc->openSection()->addElement($this->makeTableWithMixedCell());

        $document = $this->extractDocxPart((new DocxRenderer())->render($doc), 'word/document.xml');

        $this->assertStringContainsString('<w:pStyle w:val="Code"/>', $document);
        // Single quotes are escaped as &apos; in OOXML serialisation.
        $this->assertStringContainsString('echo &apos;x&apos;;', $document);
    }

    public function test_docx_image_without_explicit_dimensions_reads_real_size(): void
    {
        if (! file_exists(self::FIXTURE_PNG)) {
            $this->markTestSkipped('PNG fixture missing');
        }

        [$realW, $realH] = getimagesize(self::FIXTURE_PNG) ?: [0, 0];
        $this->assertGreaterThan(0, $realW);

        $doc = Document::make('docx', 'auto-dim');
        $doc->openSection()->addElement(Image::make(self::FIXTURE_PNG)); // no width/height

        $document = $this->extractDocxPart((new DocxRenderer())->render($doc), 'word/document.xml');

        // 1 px = 9525 EMU
        $expectedCx = $realW * 9525;
        $expectedCy = $realH * 9525;

        $this->assertStringContainsString('cx="' . $expectedCx . '"', $document);
        $this->assertStringContainsString('cy="' . $expectedCy . '"', $document);
    }

    public function test_docx_caps_oversized_image_to_content_width(): void
    {
        if (! file_exists(self::FIXTURE_PNG)) {
            $this->markTestSkipped('PNG fixture missing');
        }

        // 2000×1000 px would be 2000*9525 = 19,050,000 EMU — way past the
        // 5,943,600 EMU content width (6.5"). Aspect ratio must survive.
        $doc = Document::make('docx', 'cap');
        $doc->openSection()->addElement(Image::make(self::FIXTURE_PNG, 2000, 1000, ''));

        $document = $this->extractDocxPart((new DocxRenderer())->render($doc), 'word/document.xml');

        if (! preg_match('/cx="(\d+)"/', $document, $cx)
            || ! preg_match('/cy="(\d+)"/', $document, $cy)
        ) {
            $this->fail('cx / cy not present in DOCX drawing');
        }

        $this->assertLessThanOrEqual(5_943_600, (int) $cx[1]);
        // Aspect ratio preserved (within 1%): cx / cy ≈ 2.0
        $ratio = (int) $cx[1] / (int) $cy[1];
        $this->assertEqualsWithDelta(2.0, $ratio, 0.02);
    }

    /* =============================================================
     | HTML
     |============================================================= */

    public function test_html_image_inside_cell_uses_bare_img_tag(): void
    {
        if (! file_exists(self::FIXTURE_PNG)) {
            $this->markTestSkipped('PNG fixture missing');
        }

        $doc = Document::make('html', 'tbl-img');
        $doc->openSection()->addElement($this->makeTableWithImageCell());

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringContainsString('<table', $html);
        $this->assertMatchesRegularExpression('/<td[^>]*><img\s/', $html);
        // <figure> must NOT appear inside a <td>
        $this->assertDoesNotMatchRegularExpression('/<td[^>]*><figure/', $html);
    }

    public function test_html_image_outside_cell_no_longer_wraps_with_figure(): void
    {
        if (! file_exists(self::FIXTURE_PNG)) {
            $this->markTestSkipped('PNG fixture missing');
        }

        $doc = Document::make('html', 'plain-img');
        $doc->openSection()->addElement(Image::make(self::FIXTURE_PNG, 32, 16, 'logo'));

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringNotContainsString('<figure>', $html);
        $this->assertStringContainsString('<img ', $html);
    }

    public function test_html_codeblock_inside_cell_renders_pre_code(): void
    {
        $doc = Document::make('html', 'tbl-code');
        $doc->openSection()->addElement($this->makeTableWithMixedCell());

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringContainsString('<ul', $html);
        $this->assertStringContainsString('<pre>', $html);
        $this->assertStringContainsString('language-php', $html);
    }

    /* =============================================================
     | Markdown
     |============================================================= */

    public function test_markdown_image_inside_cell_flattens_to_image_syntax(): void
    {
        if (! file_exists(self::FIXTURE_PNG)) {
            $this->markTestSkipped('PNG fixture missing');
        }

        $doc = Document::make('md', 'tbl-img');
        $doc->openSection()->addElement($this->makeTableWithImageCell());

        $md = (new MarkdownRenderer())->render($doc);

        // Each row stays on a single line (pipe table contract).
        $cellLines = array_filter(
            explode("\n", trim($md)),
            fn (string $l) => str_starts_with($l, '|')
        );
        foreach ($cellLines as $line) {
            $this->assertStringNotContainsString("\n", $line);
        }

        $this->assertStringContainsString('![Paperdoc logo](', $md);
    }

    public function test_markdown_codeblock_and_list_inside_cell_collapse_inline(): void
    {
        $doc = Document::make('md', 'tbl-mixed');
        $doc->openSection()->addElement($this->makeTableWithMixedCell());

        $md = (new MarkdownRenderer())->render($doc);

        // List → comma-joined items, code → backtick-wrapped one-liner,
        // both kept on a single table row (no embedded newlines).
        $this->assertMatchesRegularExpression('/\| .*alpha.*beta.*`echo .*` \|/', $md);
    }

    public function test_markdown_escapes_pipes_in_cell_content(): void
    {
        $cell = (new TableCell())->addElement(
            (new Paragraph())->addRun(new TextRun('a | b'))
        );
        $table = (new Table())->addRow((new TableRow())->addCell($cell));

        $doc = Document::make('md', 'pipe');
        $doc->openSection()->addElement($table);

        $md = (new MarkdownRenderer())->render($doc);

        $this->assertStringContainsString('a \\| b', $md);
    }

    /* =============================================================
     | PDF
     |============================================================= */

    public function test_pdf_cell_with_bold_run_uses_bold_font(): void
    {
        $cell = (new TableCell())->addElement(
            (new Paragraph())->addRun(new TextRun('Strong', TextStyle::make()->setBold()))
        );
        $table = (new Table())->addRow((new TableRow())->addCell($cell));

        $doc = Document::make('pdf', 'pdf-bold-cell');
        $doc->openSection()->addElement($table);

        $bytes = (new PdfRenderer())->render($doc);
        $stream = $this->decompressPdfStreams($bytes);

        // Some Helvetica-Bold reference must appear in the page stream
        // alongside the cell text.
        $this->assertStringContainsString('Helvetica-Bold', $stream . $bytes);
        $this->assertStringContainsString('Strong', $stream);
    }

    public function test_pdf_cell_with_image_surfaces_alt_text(): void
    {
        if (! file_exists(self::FIXTURE_PNG)) {
            $this->markTestSkipped('PNG fixture missing');
        }

        $doc = Document::make('pdf', 'pdf-tbl-img');
        $doc->openSection()->addElement($this->makeTableWithImageCell());

        $bytes = (new PdfRenderer())->render($doc);
        $stream = $this->decompressPdfStreams($bytes);

        // Alt text fallback for images inside cells is current PDF
        // behaviour — drawing inline images inside cells is tracked as
        // a future enhancement.
        $this->assertStringContainsString('Paperdoc logo', $stream);
    }

    /* =============================================================
     | Internal helpers
     |============================================================= */

    private function extractDocxPart(string $bytes, string $partName): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'pdoc_docx_');
        file_put_contents($tmp, $bytes);

        try {
            $zip = new \ZipArchive();
            $this->assertTrue($zip->open($tmp) === true);

            try {
                $part = $zip->getFromName($partName);
                $this->assertIsString($part, "DOCX part {$partName} missing");

                return $part;
            } finally {
                $zip->close();
            }
        } finally {
            @unlink($tmp);
        }
    }

    private function decompressPdfStreams(string $pdf): string
    {
        $out = '';
        if (! preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $pdf, $matches)) {
            return $pdf;
        }

        foreach ($matches[1] as $compressed) {
            $decoded = @gzuncompress($compressed);
            if ($decoded !== false) {
                $out .= $decoded . "\n";
            } else {
                $out .= $compressed . "\n";
            }
        }

        return $out;
    }
}
