<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Tests\Support\InflatesPdfStreams;
use Paperdoc\Document\{Document, Image};
use Paperdoc\Renderers\{DocxRenderer, HtmlRenderer, MarkdownRenderer, PdfRenderer};

/**
 * v0.5.0 — Cross-renderer image rendering coverage.
 *
 * Exercises both modes supported by `Paperdoc\Document\Image` :
 *  - `Image::make($path, ...)` referencing an on-disk fixture file
 *  - `Image::fromData($bytes, $mime, ...)` for embedded image data
 *
 * Each renderer (DOCX, PDF, HTML, Markdown) must produce *something*
 * recognisable for the image — not a silent drop, not a malformed file.
 */
class ImageRenderingTest extends TestCase
{
    use InflatesPdfStreams;
    private const FIXTURE_PNG = __DIR__ . '/../../Fixtures/Images/paperdoc-logo.png';
    private const FIXTURE_GIF = __DIR__ . '/../../Fixtures/Images/dot.gif';

    protected function setUp(): void
    {
        if (! file_exists(self::FIXTURE_PNG)) {
            $this->markTestSkipped('Missing PNG fixture: ' . self::FIXTURE_PNG);
        }
    }

    /* =============================================================
     | DOCX
     |============================================================= */

    public function test_docx_renders_on_disk_png_image_with_relationship_and_media_part(): void
    {
        $doc = Document::make('docx', 'Image — file');
        $doc->openSection()
            ->addElement(Image::make(self::FIXTURE_PNG, 64, 32, 'Paperdoc logo'));

        $bytes = (new DocxRenderer())->render($doc);

        $tmp = tempnam(sys_get_temp_dir(), 'pdoc_img_docx_');
        file_put_contents($tmp, $bytes);

        try {
            $zip = new \ZipArchive();
            $this->assertTrue($zip->open($tmp) === true);

            try {
                $document = $zip->getFromName('word/document.xml');
                $rels     = $zip->getFromName('word/_rels/document.xml.rels');

                $this->assertIsString($document);
                $this->assertIsString($rels);

                $this->assertStringContainsString('<w:drawing>', $document);
                $this->assertStringContainsString('<wp:extent', $document);
                $this->assertMatchesRegularExpression('/r:embed="rId\d+"/', $document);
                $this->assertStringContainsString('Picture 1', $document);
                $this->assertStringContainsString('Paperdoc logo', $document);

                $this->assertMatchesRegularExpression(
                    '/Type="[^"]*\/image"[^>]*Target="media\/image1\.png"/',
                    $rels
                );

                $found = false;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);
                    if ($name === 'word/media/image1.png') {
                        $found = true;
                        break;
                    }
                }
                $this->assertTrue($found, 'word/media/image1.png must be present in the package');

                $contentTypes = $zip->getFromName('[Content_Types].xml');
                $this->assertIsString($contentTypes);
                $this->assertStringContainsString('Extension="png"', $contentTypes);
                $this->assertStringContainsString('image/png', $contentTypes);
            } finally {
                $zip->close();
            }
        } finally {
            @unlink($tmp);
        }
    }

    public function test_docx_embedded_image_data_is_written_to_media(): void
    {
        $bytes = (string) file_get_contents(self::FIXTURE_PNG);
        $this->assertNotSame('', $bytes);

        $doc = Document::make('docx');
        $doc->openSection()->addElement(
            Image::fromData($bytes, 'image/png', 64, 32, 'Embedded')
        );

        $payload = (new DocxRenderer())->render($doc);
        $tmp = tempnam(sys_get_temp_dir(), 'pdoc_img_docx2_');
        file_put_contents($tmp, $payload);

        try {
            $zip = new \ZipArchive();
            $this->assertTrue($zip->open($tmp) === true);

            try {
                $media = $zip->getFromName('word/media/image1.png');
                $this->assertIsString($media);
                $this->assertSame($bytes, $media);
            } finally {
                $zip->close();
            }
        } finally {
            @unlink($tmp);
        }
    }

    public function test_docx_can_render_two_images_with_distinct_relationships(): void
    {
        $bytes = (string) file_get_contents(self::FIXTURE_PNG);

        $doc = Document::make('docx');
        $section = $doc->openSection();
        $section->addElement(Image::make(self::FIXTURE_PNG, 64, 32, 'A'));
        $section->addElement(Image::fromData($bytes, 'image/png', 64, 32, 'B'));

        $payload = (new DocxRenderer())->render($doc);
        $tmp = tempnam(sys_get_temp_dir(), 'pdoc_img_docx3_');
        file_put_contents($tmp, $payload);

        try {
            $zip = new \ZipArchive();
            $this->assertTrue($zip->open($tmp) === true);

            try {
                $document = (string) $zip->getFromName('word/document.xml');
                preg_match_all('/r:embed="(rId\d+)"/', $document, $matches);
                $this->assertCount(2, $matches[1] ?? []);
                $this->assertNotSame($matches[1][0], $matches[1][1], 'each image must own its own rId');

                $this->assertIsString($zip->getFromName('word/media/image1.png'));
                $this->assertIsString($zip->getFromName('word/media/image2.png'));
            } finally {
                $zip->close();
            }
        } finally {
            @unlink($tmp);
        }
    }

    /* =============================================================
     | PDF
     |============================================================= */

    public function test_pdf_renders_on_disk_png_image_as_xobject(): void
    {
        $doc = Document::make('pdf');
        $doc->openSection()->addElement(Image::make(self::FIXTURE_PNG, 64, 32, 'Logo'));

        $pdf = $this->inflatePdf((new PdfRenderer())->render($doc));

        $this->assertStringStartsWith('%PDF-1.4', $pdf);
        $this->assertStringContainsString('/Subtype /Image', $pdf);
        $this->assertStringContainsString('/Filter /DCTDecode', $pdf);
        $this->assertMatchesRegularExpression('/\/Im1\s+Do/', $pdf);
        $this->assertStringContainsString('/XObject', $pdf);
    }

    public function test_pdf_renders_embedded_png_data_via_temp_file(): void
    {
        $bytes = (string) file_get_contents(self::FIXTURE_PNG);

        $doc = Document::make('pdf');
        $doc->openSection()->addElement(
            Image::fromData($bytes, 'image/png', 64, 32, 'Embedded')
        );

        $pdf = $this->inflatePdf((new PdfRenderer())->render($doc));

        $this->assertStringContainsString('/Subtype /Image', $pdf);
        $this->assertMatchesRegularExpression('/\/Im1\s+Do/', $pdf);
    }

    public function test_pdf_renders_gif_through_gd_re_encoding(): void
    {
        if (! file_exists(self::FIXTURE_GIF)) {
            $this->markTestSkipped('Missing GIF fixture');
        }
        if (! function_exists('imagecreatefromstring')) {
            $this->markTestSkipped('GD imagecreatefromstring is required for GIF support');
        }

        $doc = Document::make('pdf');
        $doc->openSection()->addElement(Image::make(self::FIXTURE_GIF, 8, 8, 'Dot'));

        $pdf = $this->inflatePdf((new PdfRenderer())->render($doc));

        // GIF must be re-encoded to JPEG (DCT) — we never emit raw GIF in PDF.
        $this->assertStringContainsString('/Subtype /Image', $pdf);
        $this->assertStringContainsString('/Filter /DCTDecode', $pdf);
    }

    public function test_pdf_unknown_image_path_is_silent_no_crash(): void
    {
        $doc = Document::make('pdf');
        $section = $doc->openSection();
        $section->addParagraph('Before');
        $section->addElement(Image::make('/does/not/exist.png', 10, 10, 'missing'));
        $section->addParagraph('After');

        $pdf = $this->inflatePdf((new PdfRenderer())->render($doc));

        $this->assertStringStartsWith('%PDF-1.4', $pdf);
        $this->assertStringNotContainsString('/Subtype /Image', $pdf);
    }

    /* =============================================================
     | HTML
     |============================================================= */

    public function test_html_renders_on_disk_image_as_img_tag(): void
    {
        $doc = Document::make('html');
        $doc->openSection()->addElement(Image::make(self::FIXTURE_PNG, 64, 32, 'Logo'));

        $html = (new HtmlRenderer())->render($doc);

        // v0.6.0: <img> is no longer wrapped in <figure> — it now stays
        // valid inside <td>/<li> contexts and lets the host page wrap
        // it in its own semantic container when needed.
        $this->assertMatchesRegularExpression('/<img\s/', $html);
        $this->assertStringNotContainsString('<figure>', $html);
        $this->assertStringContainsString('alt="Logo"', $html);
        $this->assertStringContainsString('width="64"', $html);
        $this->assertStringContainsString('height="32"', $html);
        $this->assertStringContainsString('paperdoc-logo.png', $html);
    }

    public function test_html_renders_embedded_image_as_data_uri(): void
    {
        $bytes = (string) file_get_contents(self::FIXTURE_PNG);

        $doc = Document::make('html');
        $doc->openSection()->addElement(
            Image::fromData($bytes, 'image/png', 64, 32, 'Inline')
        );

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringContainsString('src="data:image/png;base64,', $html);
        $this->assertStringContainsString(base64_encode($bytes), $html);
        $this->assertStringContainsString('alt="Inline"', $html);
    }

    /* =============================================================
     | Markdown
     |============================================================= */

    public function test_markdown_renders_on_disk_image_as_md_tag(): void
    {
        $doc = Document::make('md');
        $doc->openSection()->addElement(Image::make(self::FIXTURE_PNG, 64, 32, 'Logo'));

        $md = (new MarkdownRenderer())->render($doc);

        $this->assertStringContainsString('![Logo](', $md);
        $this->assertStringContainsString('paperdoc-logo.png)', $md);
    }

    public function test_markdown_renders_embedded_image_as_data_uri(): void
    {
        $bytes = (string) file_get_contents(self::FIXTURE_PNG);

        $doc = Document::make('md');
        $doc->openSection()->addElement(
            Image::fromData($bytes, 'image/png', 64, 32, 'Inline')
        );

        $md = (new MarkdownRenderer())->render($doc);

        $this->assertStringContainsString('![Inline](data:image/png;base64,', $md);
        $this->assertStringContainsString(base64_encode($bytes), $md);
    }
}
