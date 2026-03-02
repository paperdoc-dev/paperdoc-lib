<?php

declare(strict_types=1);

namespace Paperdoc\Support;

use Paperdoc\Document\Image;

/**
 * Generates thumbnail previews from documents and files.
 *
 * Pipeline (per format):
 *   1. Image (jpg, png, gif, webp, bmp, tiff, svg) → GD resize
 *   2. PDF → native text/image extract + GD → Imagick → Ghostscript
 *   3. OOXML (docx, xlsx, pptx) → embedded thumbnail → GD content render → LibreOffice
 *   4. Other Office/text → LibreOffice headless → PDF → step 2
 */
final class ThumbnailGenerator
{
    public const DEFAULT_WIDTH = 300;
    public const DEFAULT_HEIGHT = 300;
    public const DEFAULT_QUALITY = 85;

    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff', 'tif', 'svg'];

    private const OOXML_EXTENSIONS = ['docx', 'xlsx', 'pptx'];

    private const OFFICE_EXTENSIONS = [
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'odt', 'ods', 'odp', 'odg',
        'rtf', 'txt', 'html', 'htm',
        'csv', 'tsv', 'md', 'markdown',
    ];

    private const PREVIEW_HEADER_HEIGHT = 24;
    private const PREVIEW_PADDING = 16;
    private const PREVIEW_LINE_HEIGHT = 16;
    private const PREVIEW_PARAGRAPH_GAP = 6;
    private const PREVIEW_FONT_SIZE = 2;
    private const PREVIEW_MAX_W = 400;
    private const PREVIEW_MAX_H = 500;

    private const GRID_CELL_W = 60;
    private const GRID_CELL_H = 18;
    private const GRID_MAX_COLS = 8;
    private const GRID_MAX_ROWS = 20;
    private const GRID_CELL_CHARS = 8;

    private const PDF_MIN_JPEG_SIZE = 1000;
    private const PDF_MIN_PNG_SIZE = 500;
    private const PDF_MAX_TEXT_LENGTH = 2000;

    private const OOXML_THUMBNAIL_PATHS = [
        'docProps/thumbnail.jpeg',
        'docProps/thumbnail.jpg',
        'docProps/thumbnail.png',
    ];

    private const OOXML_NS_WP = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    private const OOXML_NS_SS = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
    private const OOXML_NS_DML = 'http://schemas.openxmlformats.org/drawingml/2006/main';

    /* =============================================================
     | Public API — from Image element
     |============================================================= */

    /**
     * Generate a thumbnail from an Image element.
     *
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    public static function generate(
        Image $image,
        int $maxWidth = self::DEFAULT_WIDTH,
        int $maxHeight = self::DEFAULT_HEIGHT,
        int $quality = self::DEFAULT_QUALITY,
    ): ?array {
        $sourceData = self::resolveImageData($image);

        return $sourceData !== null ? self::resize($sourceData, $maxWidth, $maxHeight, $quality) : null;
    }

    /**
     * Generate a thumbnail as a data URI (`data:image/…;base64,…`).
     */
    public static function generateDataUri(
        Image $image,
        int $maxWidth = self::DEFAULT_WIDTH,
        int $maxHeight = self::DEFAULT_HEIGHT,
        int $quality = self::DEFAULT_QUALITY,
    ): ?string {
        return self::toDataUri(self::generate($image, $maxWidth, $maxHeight, $quality));
    }

    /**
     * Generate a thumbnail as a raw base64 string (no data URI prefix).
     */
    public static function generateBase64(
        Image $image,
        int $maxWidth = self::DEFAULT_WIDTH,
        int $maxHeight = self::DEFAULT_HEIGHT,
        int $quality = self::DEFAULT_QUALITY,
    ): ?string {
        return self::toBase64(self::generate($image, $maxWidth, $maxHeight, $quality));
    }

    /* =============================================================
     | Public API — from file path
     |============================================================= */

    /**
     * Generate a thumbnail from any supported file.
     *
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    public static function fromFile(
        string $filePath,
        int $maxWidth = self::DEFAULT_WIDTH,
        int $maxHeight = self::DEFAULT_HEIGHT,
        int $quality = self::DEFAULT_QUALITY,
        int $page = 0,
    ): ?array {
        if (! file_exists($filePath) || ! is_file($filePath)) {
            return null;
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($ext, self::IMAGE_EXTENSIONS, true)) {
            return self::thumbnailFromImage($filePath, $maxWidth, $maxHeight, $quality);
        }

        if ($ext === 'pdf') {
            return self::thumbnailFromPdf($filePath, $page, $maxWidth, $maxHeight, $quality);
        }

        if (in_array($ext, self::OOXML_EXTENSIONS, true)) {
            return self::thumbnailFromOoxml($filePath, $ext, $maxWidth, $maxHeight, $quality)
                ?? self::thumbnailViaLibreOffice($filePath, $page, $maxWidth, $maxHeight, $quality);
        }

        if (in_array($ext, self::OFFICE_EXTENSIONS, true)) {
            return self::thumbnailViaLibreOffice($filePath, $page, $maxWidth, $maxHeight, $quality);
        }

        return null;
    }

    /**
     * Generate a thumbnail as a data URI (`data:image/…;base64,…`).
     */
    public static function fromFileDataUri(
        string $filePath,
        int $maxWidth = self::DEFAULT_WIDTH,
        int $maxHeight = self::DEFAULT_HEIGHT,
        int $quality = self::DEFAULT_QUALITY,
        int $page = 0,
    ): ?string {
        return self::toDataUri(self::fromFile($filePath, $maxWidth, $maxHeight, $quality, $page));
    }

    /**
     * Generate a thumbnail as a raw base64 string (no data URI prefix).
     *
     * Useful for JSON APIs, storage, or when you build the img tag yourself.
     */
    public static function fromFileBase64(
        string $filePath,
        int $maxWidth = self::DEFAULT_WIDTH,
        int $maxHeight = self::DEFAULT_HEIGHT,
        int $quality = self::DEFAULT_QUALITY,
        int $page = 0,
    ): ?string {
        return self::toBase64(self::fromFile($filePath, $maxWidth, $maxHeight, $quality, $page));
    }

    /**
     * Check which rendering backends are available.
     *
     * @return array{gd: bool, zip: bool, imagick: bool, ghostscript: bool, libreoffice: bool}
     */
    public static function capabilities(): array
    {
        return [
            'gd' => extension_loaded('gd'),
            'zip' => class_exists(\ZipArchive::class),
            'imagick' => extension_loaded('imagick'),
            'ghostscript' => self::findBinary(['gs', 'ghostscript']) !== null,
            'libreoffice' => self::findBinary(['libreoffice', 'soffice']) !== null,
        ];
    }

    /* =============================================================
     | Resize (public utility)
     |============================================================= */

    /**
     * Resize raw image data while preserving aspect ratio.
     *
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    public static function resize(
        string $imageData,
        int $maxWidth = self::DEFAULT_WIDTH,
        int $maxHeight = self::DEFAULT_HEIGHT,
        int $quality = self::DEFAULT_QUALITY,
    ): ?array {
        $source = @imagecreatefromstring($imageData);

        if ($source === false) {
            return null;
        }

        $origW = imagesx($source);
        $origH = imagesy($source);

        if ($origW <= $maxWidth && $origH <= $maxHeight) {
            return [
                'data' => $imageData,
                'mimeType' => self::detectMimeType($imageData),
                'width' => $origW,
                'height' => $origH,
            ];
        }

        [$newW, $newH] = self::fitDimensions($origW, $origH, $maxWidth, $maxHeight);

        $thumb = imagecreatetruecolor($newW, $newH);
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        imagefill($thumb, 0, 0, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        return self::gdToPng($thumb, $newW, $newH, $quality);
    }

    /* =============================================================
     | Strategy: Image files
     |============================================================= */

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function thumbnailFromImage(string $path, int $maxW, int $maxH, int $quality): ?array
    {
        $data = file_get_contents($path);

        return $data !== false ? self::resize($data, $maxW, $maxH, $quality) : null;
    }

    /* =============================================================
     | Strategy: PDF (native → Imagick → Ghostscript)
     |============================================================= */

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function thumbnailFromPdf(string $path, int $page, int $maxW, int $maxH, int $quality): ?array
    {
        if (extension_loaded('gd')) {
            $result = self::renderPdfNative($path, $page, $maxW, $maxH, $quality);

            if ($result !== null) {
                return $result;
            }
        }

        if (extension_loaded('imagick')) {
            $result = self::renderWithImagick($path, $page, $maxW, $maxH, $quality);

            if ($result !== null) {
                return $result;
            }
        }

        return self::renderWithGhostscript($path, $page, $maxW, $maxH, $quality);
    }

    /**
     * Pure-PHP PDF thumbnail: extract embedded images or parse text, render via GD.
     *
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function renderPdfNative(string $path, int $page, int $maxW, int $maxH, int $quality): ?array
    {
        $content = file_get_contents($path);

        if ($content === false || ! str_starts_with($content, '%PDF')) {
            return null;
        }

        $result = self::extractPdfEmbeddedImage($content, $maxW, $maxH, $quality);

        if ($result !== null) {
            return $result;
        }

        $paragraphs = self::extractPdfParagraphs($content, $page);

        return $paragraphs !== [] ? self::renderTextPreview($paragraphs, $maxW, $maxH, $quality, 'PDF') : null;
    }

    /**
     * Scan PDF binary for an embedded JPEG or PNG image.
     *
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function extractPdfEmbeddedImage(string $content, int $maxW, int $maxH, int $quality): ?array
    {
        $signatures = [
            ["\xFF\xD8\xFF", "\xFF\xD9", 2, self::PDF_MIN_JPEG_SIZE],
            ["\x89PNG\r\n\x1a\n", 'IEND', 8, self::PDF_MIN_PNG_SIZE],
        ];

        foreach ($signatures as [$header, $footer, $footerExtra, $minSize]) {
            $pos = strpos($content, $header);

            if ($pos === false) {
                continue;
            }

            $end = strpos($content, $footer, $pos);

            if ($end === false) {
                continue;
            }

            $imgData = substr($content, $pos, $end - $pos + strlen($footer) + $footerExtra);

            if (strlen($imgData) < $minSize) {
                continue;
            }

            $result = self::resize($imgData, $maxW, $maxH, $quality);

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Extract text paragraphs from a PDF for preview rendering.
     *
     * @return string[]
     */
    private static function extractPdfParagraphs(string $content, int $page): array
    {
        $text = self::extractPdfText($content, $page);

        if ($text === null) {
            return [];
        }

        return array_values(array_filter(
            preg_split('/\n{2,}/', $text),
            static fn (string $p): bool => trim($p) !== '',
        ));
    }

    /**
     * Extract raw text from PDF streams (decompress + parse text operators).
     */
    private static function extractPdfText(string $content, int $page): ?string
    {
        $text = '';

        if (preg_match_all('/stream\r?\n(.+?)\r?\nendstream/s', $content, $matches)) {
            $textStreamIndex = 0;

            foreach ($matches[1] as $streamData) {
                $decoded = self::decompressStream($streamData);
                $extracted = self::extractTextOperators($decoded);

                if ($extracted === '') {
                    continue;
                }

                if ($textStreamIndex >= $page && $textStreamIndex < $page + 5) {
                    $text .= $extracted . "\n\n";
                }

                $textStreamIndex++;

                if ($textStreamIndex > $page && strlen($text) > self::PDF_MAX_TEXT_LENGTH) {
                    break;
                }
            }
        }

        if (trim($text) === '' && preg_match_all('/\bBT\b(.+?)\bET\b/s', $content, $btMatches)) {
            foreach ($btMatches[1] as $btBlock) {
                $text .= self::extractTextOperators($btBlock) . "\n";

                if (strlen($text) > self::PDF_MAX_TEXT_LENGTH) {
                    break;
                }
            }
        }

        $trimmed = trim($text);

        return $trimmed !== '' ? $trimmed : null;
    }

    /**
     * Parse PDF text operators (Tj, TJ, ') from a content stream.
     */
    private static function extractTextOperators(string $stream): string
    {
        $parts = [];

        if (preg_match_all('/\(([^)]*)\)\s*Tj/s', $stream, $m)) {
            foreach ($m[1] as $str) {
                $parts[] = self::decodePdfString($str);
            }
        }

        if (preg_match_all('/\[([^\]]*)\]\s*TJ/s', $stream, $m)) {
            foreach ($m[1] as $array) {
                if (preg_match_all('/\(([^)]*)\)/', $array, $sub)) {
                    $parts[] = implode('', array_map(self::decodePdfString(...), $sub[1]));
                }
            }
        }

        if (preg_match_all("/\(([^)]*)\)\s*'/s", $stream, $m)) {
            foreach ($m[1] as $str) {
                $parts[] = self::decodePdfString($str) . "\n";
            }
        }

        return trim(implode(' ', $parts));
    }

    private static function decodePdfString(string $str): string
    {
        return str_replace(
            ['\\n', '\\r', '\\t', '\\(', '\\)', '\\\\'],
            ["\n", "\r", "\t", '(', ')', '\\'],
            $str,
        );
    }

    private static function decompressStream(string $data): string
    {
        $decoded = @gzuncompress($data);

        if ($decoded !== false) {
            return $decoded;
        }

        $decoded = @gzinflate($data);

        return $decoded !== false ? $decoded : $data;
    }

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function renderWithImagick(string $path, int $page, int $maxW, int $maxH, int $quality): ?array
    {
        try {
            $im = new \Imagick();
            $im->setResolution(150, 150);
            $im->readImage($path . "[{$page}]");
            $im->setImageFormat('png');
            $im->setImageBackgroundColor('white');
            $im->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
            $im->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);

            $origW = $im->getImageWidth();
            $origH = $im->getImageHeight();

            if ($origW > $maxW || $origH > $maxH) {
                [$newW, $newH] = self::fitDimensions($origW, $origH, $maxW, $maxH);
                $im->thumbnailImage($newW, $newH);
            } else {
                [$newW, $newH] = [$origW, $origH];
            }

            $im->setImageCompressionQuality($quality);
            $data = $im->getImageBlob();
            $im->clear();
            $im->destroy();

            return ['data' => $data, 'mimeType' => 'image/png', 'width' => $newW, 'height' => $newH];
        } catch (\ImagickException) {
            return null;
        }
    }

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function renderWithGhostscript(string $path, int $page, int $maxW, int $maxH, int $quality): ?array
    {
        $gs = self::findBinary(['gs', 'ghostscript']);

        if ($gs === null) {
            return null;
        }

        $tmpFile = self::tempPath('png');
        $firstPage = $page + 1;

        $cmd = sprintf(
            '%s -dBATCH -dNOPAUSE -dQUIET -sDEVICE=png16m -r150 -dFirstPage=%d -dLastPage=%d -sOutputFile=%s %s 2>/dev/null',
            escapeshellarg($gs),
            $firstPage,
            $firstPage,
            escapeshellarg($tmpFile),
            escapeshellarg($path),
        );

        exec($cmd, $output, $exitCode);

        return self::readTempAndResize($tmpFile, $exitCode === 0, $maxW, $maxH, $quality);
    }

    /* =============================================================
     | Strategy: OOXML native (ZipArchive + GD)
     |============================================================= */

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function thumbnailFromOoxml(string $path, string $ext, int $maxW, int $maxH, int $quality): ?array
    {
        $result = self::extractOoxmlEmbeddedThumbnail($path, $maxW, $maxH, $quality);

        if ($result !== null) {
            return $result;
        }

        if (! extension_loaded('gd')) {
            return null;
        }

        return match ($ext) {
            'docx' => self::renderDocxPreview($path, $maxW, $maxH, $quality),
            'xlsx' => self::renderXlsxPreview($path, $maxW, $maxH, $quality),
            'pptx' => self::renderPptxPreview($path, $maxW, $maxH, $quality),
            default => null,
        };
    }

    /**
     * Extract the pre-rendered thumbnail stored in OOXML files.
     *
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function extractOoxmlEmbeddedThumbnail(string $path, int $maxW, int $maxH, int $quality): ?array
    {
        $zip = self::openZip($path);

        if ($zip === null) {
            return null;
        }

        $data = null;

        foreach (self::OOXML_THUMBNAIL_PATHS as $entry) {
            $data = $zip->getFromName($entry);

            if ($data !== false && $data !== '') {
                break;
            }

            $data = null;
        }

        $data ??= self::resolveRelsThumbnail($zip);

        $zip->close();

        return ($data !== null && $data !== '' && $data !== false)
            ? self::resize($data, $maxW, $maxH, $quality)
            : null;
    }

    /**
     * Parse _rels/.rels for a thumbnail relationship target.
     */
    private static function resolveRelsThumbnail(\ZipArchive $zip): ?string
    {
        $rels = $zip->getFromName('_rels/.rels');

        if ($rels === false) {
            return null;
        }

        $xml = @simplexml_load_string($rels);

        if ($xml === false) {
            return null;
        }

        foreach ($xml->Relationship as $rel) {
            if (! str_contains((string) ($rel['Type'] ?? ''), '/thumbnail')) {
                continue;
            }

            $target = (string) ($rel['Target'] ?? '');

            if ($target === '') {
                continue;
            }

            $data = $zip->getFromName(ltrim($target, '/'));

            if ($data !== false && $data !== '') {
                return $data;
            }
        }

        return null;
    }

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function renderDocxPreview(string $path, int $maxW, int $maxH, int $quality): ?array
    {
        $xml = self::readFromZip($path, 'word/document.xml');

        if ($xml === null) {
            return null;
        }

        $paragraphs = self::extractXmlTextNodes($xml, self::OOXML_NS_WP, 'p', 't');

        return $paragraphs !== [] ? self::renderTextPreview($paragraphs, $maxW, $maxH, $quality, 'DOCX') : null;
    }

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function renderXlsxPreview(string $path, int $maxW, int $maxH, int $quality): ?array
    {
        $zip = self::openZip($path);

        if ($zip === null) {
            return null;
        }

        $sharedStrings = self::parseXlsxSharedStrings($zip);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            return null;
        }

        [$rows, $maxCols] = self::parseXlsxSheet($sheetXml, $sharedStrings);

        return $rows !== [] ? self::renderGridPreview($rows, $maxCols, $maxW, $maxH, $quality) : null;
    }

    /**
     * @return string[]
     */
    private static function parseXlsxSharedStrings(\ZipArchive $zip): array
    {
        $ssXml = $zip->getFromName('xl/sharedStrings.xml');

        if ($ssXml === false) {
            return [];
        }

        $dom = new \DOMDocument();
        @$dom->loadXML($ssXml);

        $strings = [];

        foreach ($dom->getElementsByTagNameNS(self::OOXML_NS_SS, 'si') as $si) {
            $text = '';

            foreach ($si->getElementsByTagNameNS(self::OOXML_NS_SS, 't') as $t) {
                $text .= $t->textContent;
            }

            $strings[] = $text;
        }

        return $strings;
    }

    /**
     * @param  string[]  $sharedStrings
     * @return array{0: array<int, array<int, string>>, 1: int}
     */
    private static function parseXlsxSheet(string $sheetXml, array $sharedStrings): array
    {
        $dom = new \DOMDocument();
        @$dom->loadXML($sheetXml);

        $rows = [];
        $maxCols = 0;

        foreach ($dom->getElementsByTagNameNS(self::OOXML_NS_SS, 'row') as $rowNode) {
            $row = [];

            foreach ($rowNode->getElementsByTagNameNS(self::OOXML_NS_SS, 'c') as $cNode) {
                $type = $cNode->getAttribute('t');
                $vNodes = $cNode->getElementsByTagNameNS(self::OOXML_NS_SS, 'v');
                $value = $vNodes->length > 0 ? $vNodes->item(0)->textContent : '';

                if ($type === 's' && isset($sharedStrings[(int) $value])) {
                    $value = $sharedStrings[(int) $value];
                }

                $colIdx = self::columnIndex($cNode->getAttribute('r'));
                $row[$colIdx] = $value;
            }

            if ($row !== []) {
                ksort($row);
                $rows[] = $row;
                $maxCols = max($maxCols, max(array_keys($row)) + 1);
            }

            if (count($rows) >= self::GRID_MAX_ROWS) {
                break;
            }
        }

        return [$rows, $maxCols];
    }

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function renderPptxPreview(string $path, int $maxW, int $maxH, int $quality): ?array
    {
        $xml = self::readFromZip($path, 'ppt/slides/slide1.xml');

        if ($xml === null) {
            return null;
        }

        $dom = new \DOMDocument();
        @$dom->loadXML($xml);

        $paragraphs = [];

        foreach ($dom->getElementsByTagNameNS(self::OOXML_NS_DML, 't') as $tNode) {
            $text = trim($tNode->textContent);

            if ($text !== '') {
                $paragraphs[] = $text;
            }
        }

        return $paragraphs !== [] ? self::renderTextPreview($paragraphs, $maxW, $maxH, $quality, 'PPTX') : null;
    }

    /* =============================================================
     | Strategy: LibreOffice → PDF → image
     |============================================================= */

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function thumbnailViaLibreOffice(
        string $path,
        int $page,
        int $maxW,
        int $maxH,
        int $quality,
    ): ?array {
        $lo = self::findBinary(['libreoffice', 'soffice']);

        if ($lo === null) {
            return null;
        }

        $realPath = realpath($path);

        if ($realPath === false) {
            return null;
        }

        $uid = md5($realPath . microtime(true) . mt_rand());
        $tmpDir = sys_get_temp_dir() . '/paperdoc_lo_' . $uid;
        $profileDir = sys_get_temp_dir() . '/paperdoc_lo_profile_' . $uid;
        @mkdir($tmpDir, 0755, true);
        @mkdir($profileDir, 0755, true);

        $cmd = sprintf(
            '%s --headless --norestore --nofirststartwizard "-env:UserInstallation=file://%s" --convert-to pdf --outdir %s %s 2>&1',
            escapeshellarg($lo),
            $profileDir,
            escapeshellarg($tmpDir),
            escapeshellarg($realPath),
        );

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            self::cleanupDir($tmpDir);
            self::cleanupDir($profileDir);

            return null;
        }

        $pdfPath = $tmpDir . '/' . pathinfo($realPath, PATHINFO_FILENAME) . '.pdf';

        if (! file_exists($pdfPath)) {
            $pdfFiles = glob($tmpDir . '/*.pdf');
            $pdfPath = $pdfFiles[0] ?? null;
        }

        if ($pdfPath === null || ! file_exists($pdfPath)) {
            self::cleanupDir($tmpDir);
            self::cleanupDir($profileDir);

            return null;
        }

        $result = self::thumbnailFromPdf($pdfPath, $page, $maxW, $maxH, $quality);

        self::cleanupDir($tmpDir);
        self::cleanupDir($profileDir);

        return $result;
    }

    /* =============================================================
     | GD preview renderers
     |============================================================= */

    /**
     * Render text paragraphs as a document-like preview image.
     *
     * @param  string[]  $paragraphs
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function renderTextPreview(array $paragraphs, int $maxW, int $maxH, int $quality, string $badge = ''): ?array
    {
        $w = min($maxW, self::PREVIEW_MAX_W);
        $h = min($maxH, self::PREVIEW_MAX_H);

        $img = self::createPreviewCanvas($w, $h, $badge);

        if ($img === null) {
            return null;
        }

        $colors = self::previewColors($img);
        $y = self::PREVIEW_HEADER_HEIGHT + self::PREVIEW_PADDING;
        $contentW = $w - (self::PREVIEW_PADDING * 2);
        $charsPerLine = max(10, (int) floor($contentW / imagefontwidth(self::PREVIEW_FONT_SIZE)));
        $maxY = $h - self::PREVIEW_PADDING;

        foreach ($paragraphs as $i => $para) {
            if ($y + self::PREVIEW_LINE_HEIGHT > $maxY) {
                imagestring($img, 1, self::PREVIEW_PADDING, $y, '...', $colors['muted']);

                break;
            }

            $color = $i === 0 ? $colors['fg'] : $colors['muted'];
            $fontSize = $i === 0 ? 3 : self::PREVIEW_FONT_SIZE;

            foreach (explode("\n", wordwrap($para, $charsPerLine, "\n", true)) as $line) {
                if ($y + self::PREVIEW_LINE_HEIGHT > $maxY) {
                    break 2;
                }

                imagestring($img, $fontSize, self::PREVIEW_PADDING, $y, $line, $color);
                $y += self::PREVIEW_LINE_HEIGHT;
            }

            $y += self::PREVIEW_PARAGRAPH_GAP;
        }

        return self::gdToPngAndResize($img, $maxW, $maxH, $quality);
    }

    /**
     * Render spreadsheet data as a grid preview image.
     *
     * @param  array<int, array<int, string>>  $rows
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function renderGridPreview(array $rows, int $maxCols, int $maxW, int $maxH, int $quality): ?array
    {
        $maxCols = min($maxCols, self::GRID_MAX_COLS);
        $rowCount = min(count($rows), self::GRID_MAX_ROWS);

        $w = min($maxW, self::PREVIEW_PADDING * 2 + $maxCols * self::GRID_CELL_W);
        $h = min($maxH, self::PREVIEW_HEADER_HEIGHT + self::PREVIEW_PADDING + $rowCount * self::GRID_CELL_H + self::PREVIEW_PADDING);

        $img = self::createPreviewCanvas($w, $h, 'XLSX');

        if ($img === null) {
            return null;
        }

        $colors = self::previewColors($img);
        $gridTop = self::PREVIEW_HEADER_HEIGHT + self::PREVIEW_PADDING;
        $gridLeft = self::PREVIEW_PADDING;
        $gridRight = $gridLeft + $maxCols * self::GRID_CELL_W;

        for ($col = 0; $col < $maxCols; $col++) {
            imagestring($img, 1, $gridLeft + $col * self::GRID_CELL_W + 2, $gridTop - 12, chr(65 + $col), $colors['muted']);
        }

        foreach ($rows as $ri => $row) {
            if ($ri >= $rowCount) {
                break;
            }

            $y = $gridTop + $ri * self::GRID_CELL_H;

            if ($y + self::GRID_CELL_H > $h - 4) {
                break;
            }

            imageline($img, $gridLeft, $y, $gridRight, $y, $colors['border']);

            for ($col = 0; $col < $maxCols; $col++) {
                $x = $gridLeft + $col * self::GRID_CELL_W;
                imageline($img, $x, $y, $x, $y + self::GRID_CELL_H, $colors['border']);

                $val = mb_substr($row[$col] ?? '', 0, self::GRID_CELL_CHARS);
                imagestring($img, 1, $x + 3, $y + 4, $val, $ri === 0 ? $colors['fg'] : $colors['muted']);
            }

            imageline($img, $gridRight, $y, $gridRight, $y + self::GRID_CELL_H, $colors['border']);
        }

        $lastY = $gridTop + min($rowCount, count($rows)) * self::GRID_CELL_H;
        imageline($img, $gridLeft, $lastY, $gridRight, $lastY, $colors['border']);

        return self::gdToPngAndResize($img, $maxW, $maxH, $quality);
    }

    /* =============================================================
     | GD helpers
     |============================================================= */

    /**
     * Create a GD canvas with a header bar, border, and optional badge label.
     */
    private static function createPreviewCanvas(int $w, int $h, string $badge): ?\GdImage
    {
        $img = imagecreatetruecolor($w, $h);

        if ($img === false) {
            return null;
        }

        $bg = imagecolorallocate($img, 255, 255, 255);
        $border = imagecolorallocate($img, 210, 210, 210);
        $headerBg = imagecolorallocate($img, 245, 245, 245);
        $muted = imagecolorallocate($img, 140, 140, 140);

        imagefill($img, 0, 0, $bg);
        imagerectangle($img, 0, 0, $w - 1, $h - 1, $border);
        imagefilledrectangle($img, 1, 1, $w - 2, self::PREVIEW_HEADER_HEIGHT, $headerBg);
        imageline($img, 0, self::PREVIEW_HEADER_HEIGHT, $w - 1, self::PREVIEW_HEADER_HEIGHT, $border);

        if ($badge !== '') {
            imagestring($img, 2, 8, 6, $badge, $muted);
        }

        return $img;
    }

    /**
     * Allocate the standard preview color palette on a GD image.
     *
     * @return array{fg: int, muted: int, border: int}
     */
    private static function previewColors(\GdImage $img): array
    {
        return [
            'fg' => imagecolorallocate($img, 40, 40, 40),
            'muted' => imagecolorallocate($img, 140, 140, 140),
            'border' => imagecolorallocate($img, 210, 210, 210),
        ];
    }

    /**
     * Encode a GD image to PNG and return the thumbnail array.
     *
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function gdToPng(\GdImage $img, int $w, int $h, int $quality): ?array
    {
        ob_start();
        imagepng($img, null, min(9, (int) round((100 - $quality) / 11)));
        $data = ob_get_clean();

        if ($data === false || $data === '') {
            return null;
        }

        return ['data' => $data, 'mimeType' => 'image/png', 'width' => $w, 'height' => $h];
    }

    /**
     * Encode a GD image to PNG, then optionally resize to fit max dimensions.
     *
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function gdToPngAndResize(\GdImage $img, int $maxW, int $maxH, int $quality): ?array
    {
        ob_start();
        imagepng($img, null, min(9, (int) round((100 - $quality) / 11)));
        $data = ob_get_clean();

        if ($data === false || $data === '') {
            return null;
        }

        return self::resize($data, $maxW, $maxH, $quality);
    }

    /* =============================================================
     | XML / ZIP helpers
     |============================================================= */

    private static function openZip(string $path): ?\ZipArchive
    {
        if (! class_exists(\ZipArchive::class)) {
            return null;
        }

        $zip = new \ZipArchive();

        return $zip->open($path, \ZipArchive::RDONLY) === true ? $zip : null;
    }

    /**
     * Open a ZIP, read a single entry, close.
     */
    private static function readFromZip(string $path, string $entry): ?string
    {
        $zip = self::openZip($path);

        if ($zip === null) {
            return null;
        }

        $data = $zip->getFromName($entry);
        $zip->close();

        return ($data !== false && $data !== '') ? $data : null;
    }

    /**
     * Extract text from XML nodes grouped by a container element.
     *
     * For DOCX: container = `w:p`, text = `w:t` → paragraphs.
     *
     * @return string[]
     */
    private static function extractXmlTextNodes(string $xml, string $ns, string $containerTag, string $textTag): array
    {
        $dom = new \DOMDocument();
        @$dom->loadXML($xml);

        $result = [];

        foreach ($dom->getElementsByTagNameNS($ns, $containerTag) as $container) {
            $text = '';

            foreach ($container->getElementsByTagNameNS($ns, $textTag) as $tNode) {
                $text .= $tNode->textContent;
            }

            $trimmed = trim($text);

            if ($trimmed !== '') {
                $result[] = $trimmed;
            }
        }

        return $result;
    }

    /**
     * Convert an Excel cell reference (e.g. "B3") to a 0-based column index.
     */
    private static function columnIndex(string $ref): int
    {
        $letters = preg_replace('/[^A-Z]/i', '', strtoupper($ref));

        if ($letters === '' || $letters === null) {
            return 0;
        }

        $index = 0;

        for ($i = 0, $len = strlen($letters); $i < $len; $i++) {
            $index = $index * 26 + (ord($letters[$i]) - 64);
        }

        return $index - 1;
    }

    /* =============================================================
     | Encoding helpers
     |============================================================= */

    /**
     * @param  array{data: string, mimeType: string, width: int, height: int}|null  $result
     */
    private static function toDataUri(?array $result): ?string
    {
        if ($result === null) {
            return null;
        }

        return 'data:' . $result['mimeType'] . ';base64,' . base64_encode($result['data']);
    }

    /**
     * Extract raw base64 from a thumbnail result (no data URI prefix).
     *
     * @param  array{data: string, mimeType: string, width: int, height: int}|null  $result
     */
    private static function toBase64(?array $result): ?string
    {
        return $result !== null ? base64_encode($result['data']) : null;
    }

    /* =============================================================
     | General helpers
     |============================================================= */

    /**
     * @return array{int, int}
     */
    private static function fitDimensions(int $origW, int $origH, int $maxW, int $maxH): array
    {
        $ratio = min($maxW / $origW, $maxH / $origH);

        return [max(1, (int) round($origW * $ratio)), max(1, (int) round($origH * $ratio))];
    }

    private static function resolveImageData(Image $image): ?string
    {
        if ($image->hasData()) {
            return $image->getData();
        }

        $src = $image->getSrc();

        if ($src !== '' && file_exists($src) && is_file($src)) {
            $data = file_get_contents($src);

            return $data !== false ? $data : null;
        }

        return null;
    }

    private static function detectMimeType(string $data): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($data);

        return $mime !== false ? $mime : 'image/png';
    }

    /**
     * @param  string[]  $names
     */
    private static function findBinary(array $names): ?string
    {
        foreach ($names as $name) {
            $path = trim((string) shell_exec(sprintf('which %s 2>/dev/null', escapeshellarg($name))));

            if ($path !== '' && is_executable($path)) {
                return $path;
            }
        }

        return null;
    }

    private static function tempPath(string $ext): string
    {
        $base = tempnam(sys_get_temp_dir(), 'paperdoc_thumb_');
        $path = $base . '.' . $ext;

        if ($base !== $path) {
            @unlink($base);
        }

        return $path;
    }

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function readTempAndResize(string $tmpFile, bool $success, int $maxW, int $maxH, int $quality): ?array
    {
        if (! $success || ! file_exists($tmpFile)) {
            @unlink($tmpFile);

            return null;
        }

        $data = file_get_contents($tmpFile);
        @unlink($tmpFile);

        return ($data !== false && $data !== '') ? self::resize($data, $maxW, $maxH, $quality) : null;
    }

    private static function cleanupDir(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($iterator as $item) {
            $item->isDir() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
        }

        @rmdir($dir);
    }
}
