<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Support;

use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;
use Paperdoc\Document\Image;
use Paperdoc\Support\ThumbnailGenerator;

class ThumbnailGeneratorTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/paperdoc_thumb_test_' . uniqid();
        @mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        $files = glob($this->tmpDir . '/*');

        if ($files !== false) {
            foreach ($files as $file) {
                @unlink($file);
            }
        }

        @rmdir($this->tmpDir);
    }

    /* =============================================================
     | capabilities()
     |============================================================= */

    public function test_capabilities_returns_expected_keys(): void
    {
        $caps = ThumbnailGenerator::capabilities();

        $this->assertArrayHasKey('gd', $caps);
        $this->assertArrayHasKey('zip', $caps);
        $this->assertArrayHasKey('imagick', $caps);
        $this->assertArrayHasKey('ghostscript', $caps);
        $this->assertArrayHasKey('libreoffice', $caps);
        $this->assertIsBool($caps['gd']);
        $this->assertIsBool($caps['zip']);
    }

    /* =============================================================
     | resize()
     |============================================================= */

    #[RequiresPhpExtension('gd')]
    public function test_resize_returns_valid_structure(): void
    {
        $imageData = $this->createPngData(100, 80);
        $result = ThumbnailGenerator::resize($imageData, 50, 50);

        $this->assertNotNull($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('mimeType', $result);
        $this->assertArrayHasKey('width', $result);
        $this->assertArrayHasKey('height', $result);
        $this->assertLessThanOrEqual(50, $result['width']);
        $this->assertLessThanOrEqual(50, $result['height']);
    }

    #[RequiresPhpExtension('gd')]
    public function test_resize_preserves_aspect_ratio(): void
    {
        $imageData = $this->createPngData(200, 100);
        $result = ThumbnailGenerator::resize($imageData, 100, 100);

        $this->assertNotNull($result);
        $this->assertSame(100, $result['width']);
        $this->assertSame(50, $result['height']);
    }

    #[RequiresPhpExtension('gd')]
    public function test_resize_no_upscale(): void
    {
        $imageData = $this->createPngData(20, 10);
        $result = ThumbnailGenerator::resize($imageData, 100, 100);

        $this->assertNotNull($result);
        $this->assertSame(20, $result['width']);
        $this->assertSame(10, $result['height']);
    }

    public function test_resize_returns_null_for_invalid_data(): void
    {
        $result = ThumbnailGenerator::resize('not-an-image');

        $this->assertNull($result);
    }

    /* =============================================================
     | fromFile() — Image
     |============================================================= */

    #[RequiresPhpExtension('gd')]
    public function test_from_file_with_png(): void
    {
        $path = $this->tmpDir . '/test.png';
        $this->writePngFile($path, 200, 150);

        $result = ThumbnailGenerator::fromFile($path, 100, 100);

        $this->assertNotNull($result);
        $this->assertLessThanOrEqual(100, $result['width']);
        $this->assertLessThanOrEqual(100, $result['height']);
        $this->assertStringContainsString('image/', $result['mimeType']);
    }

    #[RequiresPhpExtension('gd')]
    public function test_from_file_with_jpeg(): void
    {
        $path = $this->tmpDir . '/test.jpg';
        $this->writeJpegFile($path, 200, 150);

        $result = ThumbnailGenerator::fromFile($path, 100, 100);

        $this->assertNotNull($result);
        $this->assertLessThanOrEqual(100, $result['width']);
    }

    public function test_from_file_returns_null_for_nonexistent(): void
    {
        $this->assertNull(ThumbnailGenerator::fromFile('/nonexistent/file.png'));
    }

    public function test_from_file_returns_null_for_unsupported_extension(): void
    {
        $path = $this->tmpDir . '/file.xyz';
        file_put_contents($path, 'random data');

        $this->assertNull(ThumbnailGenerator::fromFile($path));
    }

    /* =============================================================
     | fromFileDataUri()
     |============================================================= */

    #[RequiresPhpExtension('gd')]
    public function test_from_file_data_uri_returns_valid_string(): void
    {
        $path = $this->tmpDir . '/test.png';
        $this->writePngFile($path, 100, 80);

        $result = ThumbnailGenerator::fromFileDataUri($path, 50, 50);

        $this->assertNotNull($result);
        $this->assertStringStartsWith('data:image/', $result);
        $this->assertStringContainsString(';base64,', $result);
    }

    public function test_from_file_data_uri_returns_null_for_nonexistent(): void
    {
        $this->assertNull(ThumbnailGenerator::fromFileDataUri('/nonexistent.png'));
    }

    /* =============================================================
     | fromFileBase64()
     |============================================================= */

    #[RequiresPhpExtension('gd')]
    public function test_from_file_base64_returns_raw_base64(): void
    {
        $path = $this->tmpDir . '/test.png';
        $this->writePngFile($path, 100, 80);

        $result = ThumbnailGenerator::fromFileBase64($path, 50, 50);

        $this->assertNotNull($result);
        $this->assertFalse(str_starts_with($result, 'data:'));
        $this->assertNotFalse(base64_decode($result, true));
    }

    #[RequiresPhpExtension('gd')]
    public function test_from_file_base64_produces_valid_image(): void
    {
        $path = $this->tmpDir . '/test.png';
        $this->writePngFile($path, 200, 150);

        $base64 = ThumbnailGenerator::fromFileBase64($path, 100, 100);

        $this->assertNotNull($base64);
        $decoded = base64_decode($base64, true);
        $this->assertNotFalse($decoded);

        $img = @imagecreatefromstring($decoded);
        $this->assertNotFalse($img);
        $this->assertLessThanOrEqual(100, imagesx($img));
        $this->assertLessThanOrEqual(100, imagesy($img));
    }

    public function test_from_file_base64_returns_null_for_nonexistent(): void
    {
        $this->assertNull(ThumbnailGenerator::fromFileBase64('/nonexistent.png'));
    }

    #[RequiresPhpExtension('gd')]
    public function test_from_file_base64_works_for_docx(): void
    {
        $path = $this->tmpDir . '/test.docx';
        $this->writeMinimalDocx($path);

        $result = ThumbnailGenerator::fromFileBase64($path, 200, 200);

        $this->assertNotNull($result);
        $this->assertNotFalse(base64_decode($result, true));
    }

    /* =============================================================
     | generateBase64()
     |============================================================= */

    #[RequiresPhpExtension('gd')]
    public function test_generate_base64_from_image(): void
    {
        $path = $this->tmpDir . '/photo.png';
        $this->writePngFile($path, 100, 80);

        $image = new Image($path, 100, 80);
        $result = ThumbnailGenerator::generateBase64($image, 50, 50);

        $this->assertNotNull($result);
        $this->assertFalse(str_starts_with($result, 'data:'));
        $this->assertNotFalse(base64_decode($result, true));
    }

    public function test_generate_base64_returns_null_for_missing(): void
    {
        $image = new Image('/nonexistent.png');
        $this->assertNull(ThumbnailGenerator::generateBase64($image));
    }

    /* =============================================================
     | fromFile() — PDF native
     |============================================================= */

    #[RequiresPhpExtension('gd')]
    public function test_from_file_pdf_with_text(): void
    {
        $path = $this->tmpDir . '/test.pdf';
        $this->writeMinimalPdf($path);

        $result = ThumbnailGenerator::fromFile($path, 300, 300);

        $this->assertNotNull($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('width', $result);
        $this->assertArrayHasKey('height', $result);
        $this->assertStringContainsString('image/', $result['mimeType']);
    }

    #[RequiresPhpExtension('gd')]
    public function test_from_file_pdf_with_embedded_image(): void
    {
        $path = $this->tmpDir . '/img.pdf';
        $this->writePdfWithEmbeddedJpeg($path);

        $result = ThumbnailGenerator::fromFile($path, 200, 200);

        $this->assertNotNull($result);
        $this->assertLessThanOrEqual(200, $result['width']);
        $this->assertLessThanOrEqual(200, $result['height']);
    }

    /* =============================================================
     | fromFile() — DOCX native
     |============================================================= */

    #[RequiresPhpExtension('gd')]
    #[RequiresPhpExtension('zip')]
    public function test_from_file_docx_native(): void
    {
        $path = $this->tmpDir . '/test.docx';
        $this->writeMinimalDocx($path);

        $result = ThumbnailGenerator::fromFile($path, 300, 300);

        $this->assertNotNull($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertStringContainsString('image/', $result['mimeType']);
        $this->assertLessThanOrEqual(300, $result['width']);
    }

    #[RequiresPhpExtension('gd')]
    #[RequiresPhpExtension('zip')]
    public function test_from_file_docx_with_embedded_thumbnail(): void
    {
        $path = $this->tmpDir . '/thumb.docx';
        $this->writeDocxWithThumbnail($path);

        $result = ThumbnailGenerator::fromFile($path, 200, 200);

        $this->assertNotNull($result);
        $this->assertLessThanOrEqual(200, $result['width']);
    }

    /* =============================================================
     | fromFile() — XLSX native
     |============================================================= */

    #[RequiresPhpExtension('gd')]
    #[RequiresPhpExtension('zip')]
    public function test_from_file_xlsx_native(): void
    {
        $path = $this->tmpDir . '/test.xlsx';
        $this->writeMinimalXlsx($path);

        $result = ThumbnailGenerator::fromFile($path, 300, 300);

        $this->assertNotNull($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertStringContainsString('image/', $result['mimeType']);
    }

    /* =============================================================
     | generate() — from Image element
     |============================================================= */

    #[RequiresPhpExtension('gd')]
    public function test_generate_from_image_with_file_src(): void
    {
        $path = $this->tmpDir . '/photo.png';
        $this->writePngFile($path, 100, 80);

        $image = new Image($path, 100, 80, 'test');
        $result = ThumbnailGenerator::generate($image, 50, 50);

        $this->assertNotNull($result);
        $this->assertLessThanOrEqual(50, $result['width']);
    }

    #[RequiresPhpExtension('gd')]
    public function test_generate_from_image_with_data(): void
    {
        $pngData = $this->createPngData(80, 60);
        $image = new Image('');
        $image->setData($pngData, 'image/png');

        $result = ThumbnailGenerator::generate($image, 40, 40);

        $this->assertNotNull($result);
        $this->assertLessThanOrEqual(40, $result['width']);
    }

    #[RequiresPhpExtension('gd')]
    public function test_generate_data_uri_from_image(): void
    {
        $path = $this->tmpDir . '/photo.png';
        $this->writePngFile($path, 100, 80);

        $image = new Image($path, 100, 80);
        $uri = ThumbnailGenerator::generateDataUri($image, 50, 50);

        $this->assertNotNull($uri);
        $this->assertStringStartsWith('data:image/', $uri);
    }

    public function test_generate_returns_null_for_missing_image(): void
    {
        $image = new Image('/nonexistent.png');
        $this->assertNull(ThumbnailGenerator::generate($image));
    }

    /* =============================================================
     | Edge cases
     |============================================================= */

    #[RequiresPhpExtension('gd')]
    public function test_from_file_respects_quality_parameter(): void
    {
        $path = $this->tmpDir . '/test.png';
        $this->writePngFile($path, 200, 150);

        $high = ThumbnailGenerator::fromFile($path, 100, 100, 95);
        $low = ThumbnailGenerator::fromFile($path, 100, 100, 10);

        $this->assertNotNull($high);
        $this->assertNotNull($low);
        $this->assertSame($high['width'], $low['width']);
    }

    #[RequiresPhpExtension('gd')]
    public function test_from_file_with_custom_dimensions(): void
    {
        $path = $this->tmpDir . '/test.png';
        $this->writePngFile($path, 400, 300);

        $result = ThumbnailGenerator::fromFile($path, 150, 100);

        $this->assertNotNull($result);
        $this->assertLessThanOrEqual(150, $result['width']);
        $this->assertLessThanOrEqual(100, $result['height']);
    }

    /* =============================================================
     | Fixture helpers
     |============================================================= */

    private function createPngData(int $w, int $h): string
    {
        $img = imagecreatetruecolor($w, $h);
        $color = imagecolorallocate($img, 70, 130, 180);
        imagefill($img, 0, 0, $color);
        imagestring($img, 3, 4, 4, 'Test', imagecolorallocate($img, 255, 255, 255));

        ob_start();
        imagepng($img);
        $data = ob_get_clean();

        return $data;
    }

    private function writePngFile(string $path, int $w, int $h): void
    {
        file_put_contents($path, $this->createPngData($w, $h));
    }

    private function writeJpegFile(string $path, int $w, int $h): void
    {
        $img = imagecreatetruecolor($w, $h);
        imagefill($img, 0, 0, imagecolorallocate($img, 100, 150, 200));
        imagejpeg($img, $path, 80);
    }

    private function writeMinimalPdf(string $path): void
    {
        $stream = "BT /F1 12 Tf 72 720 Td (Hello Paperdoc!) Tj 0 -20 Td (This is a test PDF document for thumbnail generation.) Tj ET";
        $streamLen = strlen($stream);

        $objects = [];
        $objects[] = "1 0 obj\n<</Type /Catalog /Pages 2 0 R>>\nendobj";
        $objects[] = "2 0 obj\n<</Type /Pages /Kids [3 0 R] /Count 1>>\nendobj";
        $objects[] = "3 0 obj\n<</Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources <</Font <</F1 5 0 R>>>>>>\nendobj";
        $objects[] = "4 0 obj\n<</Length {$streamLen}>>\nstream\n{$stream}\nendstream\nendobj";
        $objects[] = "5 0 obj\n<</Type /Font /Subtype /Type1 /BaseFont /Helvetica>>\nendobj";

        $body = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $obj) {
            $offsets[] = strlen($body);
            $body .= $obj . "\n";
        }

        $xrefOffset = strlen($body);
        $xref = "xref\n0 6\n";
        $xref .= "0000000000 65535 f \n";

        foreach ($offsets as $off) {
            $xref .= sprintf("%010d 00000 n \n", $off);
        }

        $body .= $xref;
        $body .= "trailer\n<</Size 6 /Root 1 0 R>>\n";
        $body .= "startxref\n{$xrefOffset}\n%%EOF";

        file_put_contents($path, $body);
    }

    private function writePdfWithEmbeddedJpeg(string $path): void
    {
        $jpegData = $this->createJpegData(80, 60);
        $jpegLen = strlen($jpegData);

        $objects = [];
        $objects[] = "1 0 obj\n<</Type /Catalog /Pages 2 0 R>>\nendobj";
        $objects[] = "2 0 obj\n<</Type /Pages /Kids [3 0 R] /Count 1>>\nendobj";
        $objects[] = "3 0 obj\n<</Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources <</XObject <</Img0 4 0 R>>>>>>\nendobj";
        $objects[] = "4 0 obj\n<</Type /XObject /Subtype /Image /Width 80 /Height 60 /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length {$jpegLen}>>\nstream\n{$jpegData}\nendstream\nendobj";

        $body = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $obj) {
            $offsets[] = strlen($body);
            $body .= $obj . "\n";
        }

        $xrefOffset = strlen($body);
        $xref = "xref\n0 5\n";
        $xref .= "0000000000 65535 f \n";

        foreach ($offsets as $off) {
            $xref .= sprintf("%010d 00000 n \n", $off);
        }

        $body .= $xref;
        $body .= "trailer\n<</Size 5 /Root 1 0 R>>\n";
        $body .= "startxref\n{$xrefOffset}\n%%EOF";

        file_put_contents($path, $body);
    }

    private function createJpegData(int $w, int $h): string
    {
        $img = imagecreatetruecolor($w, $h);
        imagefill($img, 0, 0, imagecolorallocate($img, 200, 100, 50));
        imagestring($img, 2, 4, 4, 'IMG', imagecolorallocate($img, 255, 255, 255));

        ob_start();
        imagejpeg($img, null, 80);
        $data = ob_get_clean();

        return $data;
    }

    private function writeMinimalDocx(string $path): void
    {
        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
            . '</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
            . '</Relationships>');

        $ns = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $zip->addFromString('word/document.xml', '<?xml version="1.0" encoding="UTF-8"?>'
            . '<w:document xmlns:w="' . $ns . '">'
            . '<w:body>'
            . '<w:p><w:r><w:t>Paperdoc Test Document</w:t></w:r></w:p>'
            . '<w:p><w:r><w:t>This is a paragraph to verify the DOCX thumbnail renderer works correctly without LibreOffice.</w:t></w:r></w:p>'
            . '<w:p><w:r><w:t>Third paragraph with additional content for the preview.</w:t></w:r></w:p>'
            . '</w:body>'
            . '</w:document>');

        $zip->close();
    }

    private function writeDocxWithThumbnail(string $path): void
    {
        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Default Extension="jpeg" ContentType="image/jpeg"/>'
            . '</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
            . '</Relationships>');

        $ns = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $zip->addFromString('word/document.xml', '<?xml version="1.0" encoding="UTF-8"?>'
            . '<w:document xmlns:w="' . $ns . '"><w:body><w:p><w:r><w:t>Doc</w:t></w:r></w:p></w:body></w:document>');

        $zip->addFromString('docProps/thumbnail.jpeg', $this->createJpegData(120, 90));

        $zip->close();
    }

    private function writeMinimalXlsx(string $path): void
    {
        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
            . '</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>');

        $ssNs = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
        $zip->addFromString('xl/sharedStrings.xml', '<?xml version="1.0" encoding="UTF-8"?>'
            . '<sst xmlns="' . $ssNs . '" count="4" uniqueCount="4">'
            . '<si><t>Name</t></si>'
            . '<si><t>Format</t></si>'
            . '<si><t>Status</t></si>'
            . '<si><t>report.pdf</t></si>'
            . '</sst>');

        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8"?>'
            . '<worksheet xmlns="' . $ssNs . '">'
            . '<sheetData>'
            . '<row r="1"><c r="A1" t="s"><v>0</v></c><c r="B1" t="s"><v>1</v></c><c r="C1" t="s"><v>2</v></c></row>'
            . '<row r="2"><c r="A2" t="s"><v>3</v></c><c r="B2"><v>PDF</v></c><c r="C2"><v>OK</v></c></row>'
            . '</sheetData>'
            . '</worksheet>');

        $zip->close();
    }
}
