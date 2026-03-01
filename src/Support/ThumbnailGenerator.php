<?php

declare(strict_types=1);

namespace Paperdoc\Support;

use Paperdoc\Document\Image;

/**
 * Generic thumbnail generator for any document type.
 *
 * Pipeline:
 *   1. Image files (jpg, png, gif, webp, bmp, tiff, svg) → GD resize
 *   2. PDF → Imagick or Ghostscript
 *   3. Office docs (doc, docx, xls, xlsx, ppt, pptx, odt, ods, odp, rtf)
 *      → LibreOffice headless → PDF → step 2
 *   4. Text/markup (html, csv, md, txt) → LibreOffice headless → PDF → step 2
 */
class ThumbnailGenerator
{
    public const DEFAULT_WIDTH = 300;
    public const DEFAULT_HEIGHT = 300;
    public const DEFAULT_QUALITY = 85;

    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff', 'tif', 'svg'];

    private const OFFICE_EXTENSIONS = [
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'odt', 'ods', 'odp', 'odg',
        'rtf', 'txt',
        'html', 'htm',
        'csv', 'tsv',
        'md', 'markdown',
    ];

    /* =============================================================
     | Public API — from Image element
     |============================================================= */

    /**
     * Generate a thumbnail from an Image element.
     * Always recomputes from current data — changes are reflected immediately.
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

        if ($sourceData === null) {
            return null;
        }

        return self::resize($sourceData, $maxWidth, $maxHeight, $quality);
    }

    /**
     * Generate a thumbnail from an Image element as a data URI.
     */
    public static function generateDataUri(
        Image $image,
        int $maxWidth = self::DEFAULT_WIDTH,
        int $maxHeight = self::DEFAULT_HEIGHT,
        int $quality = self::DEFAULT_QUALITY,
    ): ?string {
        return self::toDataUri(self::generate($image, $maxWidth, $maxHeight, $quality));
    }

    /* =============================================================
     | Public API — from file path (generic for all formats)
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

        if (in_array($ext, self::OFFICE_EXTENSIONS, true)) {
            return self::thumbnailViaLibreOffice($filePath, $page, $maxWidth, $maxHeight, $quality);
        }

        return null;
    }

    /**
     * Same as fromFile() but returns a data URI string.
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
     * Check which rendering backends are available on this system.
     *
     * @return array{imagick: bool, ghostscript: bool, libreoffice: bool, gd: bool}
     */
    public static function capabilities(): array
    {
        return [
            'imagick' => extension_loaded('imagick'),
            'ghostscript' => self::findBinary(['gs', 'ghostscript']) !== null,
            'libreoffice' => self::findBinary(['libreoffice', 'soffice']) !== null,
            'gd' => extension_loaded('gd'),
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

        $origWidth = imagesx($source);
        $origHeight = imagesy($source);

        if ($origWidth <= $maxWidth && $origHeight <= $maxHeight) {
            imagedestroy($source);

            return [
                'data' => $imageData,
                'mimeType' => self::detectMimeType($imageData),
                'width' => $origWidth,
                'height' => $origHeight,
            ];
        }

        [$newWidth, $newHeight] = self::fitDimensions($origWidth, $origHeight, $maxWidth, $maxHeight);

        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        imagefill($thumb, 0, 0, imagecolorallocatealpha($thumb, 0, 0, 0, 127));

        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        imagedestroy($source);

        ob_start();
        imagepng($thumb, null, min(9, (int) round((100 - $quality) / 11)));
        $data = ob_get_clean();
        imagedestroy($thumb);

        if ($data === false || $data === '') {
            return null;
        }

        return [
            'data' => $data,
            'mimeType' => 'image/png',
            'width' => $newWidth,
            'height' => $newHeight,
        ];
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
     | Strategy: PDF files (Imagick → Ghostscript fallback)
     |============================================================= */

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function thumbnailFromPdf(string $path, int $page, int $maxW, int $maxH, int $quality): ?array
    {
        if (extension_loaded('imagick')) {
            $result = self::renderWithImagick($path, $page, $maxW, $maxH, $quality);
            if ($result !== null) {
                return $result;
            }
        }

        return self::renderWithGhostscript($path, $page, $maxW, $maxH, $quality);
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
     | Strategy: Office documents via LibreOffice → PDF → image
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

        $tmpDir = sys_get_temp_dir() . '/paperdoc_lo_' . md5($path . microtime(true));
        @mkdir($tmpDir, 0755, true);

        $cmd = sprintf(
            '%s --headless --norestore --convert-to pdf --outdir %s %s 2>/dev/null',
            escapeshellarg($lo),
            escapeshellarg($tmpDir),
            escapeshellarg($path),
        );

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            self::cleanupDir($tmpDir);
            return null;
        }

        $baseName = pathinfo($path, PATHINFO_FILENAME) . '.pdf';
        $pdfPath = $tmpDir . '/' . $baseName;

        if (! file_exists($pdfPath)) {
            $pdfFiles = glob($tmpDir . '/*.pdf');
            $pdfPath = $pdfFiles[0] ?? null;
        }

        if ($pdfPath === null || ! file_exists($pdfPath)) {
            self::cleanupDir($tmpDir);
            return null;
        }

        $result = self::thumbnailFromPdf($pdfPath, $page, $maxW, $maxH, $quality);

        self::cleanupDir($tmpDir);

        return $result;
    }

    /* =============================================================
     | Helpers
     |============================================================= */

    /**
     * @param array{data: string, mimeType: string, width: int, height: int}|null $result
     */
    private static function toDataUri(?array $result): ?string
    {
        if ($result === null) {
            return null;
        }

        return 'data:' . $result['mimeType'] . ';base64,' . base64_encode($result['data']);
    }

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
     * @param string[] $names
     */
    private static function findBinary(array $names): ?string
    {
        foreach ($names as $name) {
            $path = trim((string) shell_exec("which {$name} 2>/dev/null"));

            if ($path !== '' && is_executable($path)) {
                return $path;
            }
        }

        return null;
    }

    private static function tempPath(string $ext): string
    {
        return tempnam(sys_get_temp_dir(), 'paperdoc_thumb_') . '.' . $ext;
    }

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private static function readTempAndResize(
        string $tmpFile,
        bool $success,
        int $maxW,
        int $maxH,
        int $quality,
    ): ?array {
        if (! $success || ! file_exists($tmpFile)) {
            @unlink($tmpFile);
            return null;
        }

        $data = file_get_contents($tmpFile);
        @unlink($tmpFile);

        if ($data === false || $data === '') {
            return null;
        }

        return self::resize($data, $maxW, $maxH, $quality);
    }

    private static function cleanupDir(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $files = glob($dir . '/*');
        if ($files !== false) {
            foreach ($files as $file) {
                @unlink($file);
            }
        }

        @rmdir($dir);
    }
}
