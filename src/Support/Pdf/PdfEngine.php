<?php

declare(strict_types=1);

namespace Paperdoc\Support\Pdf;

/**
 * Moteur PDF natif sans dépendance tierce.
 *
 * Génère des fichiers PDF valides en implémentant directement
 * la spécification PDF 1.4. Supporte les 14 polices standard,
 * le texte stylisé, les tableaux et les images JPEG/PNG.
 */
class PdfEngine
{
    /** @var PdfObject[] */
    private array $objects = [];

    private int $objectCounter = 0;

    /** @var int[] Object numbers for each page content */
    private array $pageObjects = [];

    /** @var int[] Object numbers for each page resource dict */
    private array $pageResourceObjects = [];

    private int $catalogObj;
    private int $pagesObj;

    private float $pageWidth;
    private float $pageHeight;
    private float $marginTop;
    private float $marginBottom;
    private float $marginLeft;
    private float $marginRight;

    private float $cursorX;
    private float $cursorY;
    private string $currentPageContent = '';

    /** @var array<string, int> Font name => object number */
    private array $fonts = [];

    /** @var array<string, string> Font name => PDF reference (e.g. /F1) */
    private array $fontRefs = [];

    private int $fontCounter = 0;

    /** @var array<string, int> Image hash => object number */
    private array $images = [];

    /** @var array<string, string> Image hash => PDF reference */
    private array $imageRefs = [];

    private int $imageCounter = 0;

    private string $title   = '';
    private string $creator = 'Paperdoc';

    /** Approximate character widths for standard fonts (per 1000 units) */
    private const CHAR_WIDTHS = [
        'Helvetica' => 550,
        'Helvetica-Bold' => 580,
        'Helvetica-Oblique' => 550,
        'Helvetica-BoldOblique' => 580,
        'Times-Roman' => 500,
        'Times-Bold' => 530,
        'Times-Italic' => 500,
        'Times-BoldItalic' => 530,
        'Courier' => 600,
        'Courier-Bold' => 600,
        'Courier-Oblique' => 600,
        'Courier-BoldOblique' => 600,
    ];

    public function __construct(
        float $pageWidth = 595.28,
        float $pageHeight = 841.89,
        float $marginTop = 40,
        float $marginBottom = 40,
        float $marginLeft = 40,
        float $marginRight = 40,
    ) {
        $this->pageWidth    = $pageWidth;
        $this->pageHeight   = $pageHeight;
        $this->marginTop    = $marginTop;
        $this->marginBottom = $marginBottom;
        $this->marginLeft   = $marginLeft;
        $this->marginRight  = $marginRight;

        $this->catalogObj = $this->allocateObject();
        $this->pagesObj   = $this->allocateObject();

        $this->newPage();
    }

    /* -------------------------------------------------------------
     | Configuration
     |------------------------------------------------------------- */

    public function setTitle(string $title): void { $this->title = $title; }
    public function setCreator(string $creator): void { $this->creator = $creator; }

    /* -------------------------------------------------------------
     | Page Management
     |------------------------------------------------------------- */

    public function newPage(): void
    {
        if ($this->currentPageContent !== '') {
            $this->flushPage();
        }

        $this->currentPageContent = '';
        $this->cursorX = $this->marginLeft;
        $this->cursorY = $this->pageHeight - $this->marginTop;
    }

    public function getContentWidth(): float
    {
        return $this->pageWidth - $this->marginLeft - $this->marginRight;
    }

    public function getCursorY(): float { return $this->cursorY; }

    public function getBottomMargin(): float { return $this->marginBottom; }

    public function moveCursorY(float $delta): void
    {
        $this->cursorY += $delta;
    }

    public function needsNewPage(float $requiredHeight): bool
    {
        return $this->cursorY - $requiredHeight < $this->marginBottom;
    }

    /* -------------------------------------------------------------
     | Text Operations
     |------------------------------------------------------------- */

    /**
     * Écrit une ligne de texte à la position courante.
     */
    public function writeText(
        string $text,
        string $fontName = 'Helvetica',
        float $fontSize = 12,
        float $r = 0,
        float $g = 0,
        float $b = 0,
    ): void {
        $fontRef = $this->ensureFont($fontName);

        $this->currentPageContent .= "BT\n";
        $this->currentPageContent .= sprintf("%.2f %.2f %.2f rg\n", $r, $g, $b);
        $this->currentPageContent .= sprintf("%s %.1f Tf\n", $fontRef, $fontSize);
        $this->currentPageContent .= sprintf("%.2f %.2f Td\n", $this->cursorX, $this->cursorY);
        $this->currentPageContent .= sprintf("(%s) Tj\n", $this->escapePdfString($text));
        $this->currentPageContent .= "ET\n";
    }

    /**
     * Écrit du texte avec retour à la ligne automatique.
     *
     * @return float Hauteur totale consommée
     */
    public function writeWrappedText(
        string $text,
        string $fontName = 'Helvetica',
        float $fontSize = 12,
        float $r = 0,
        float $g = 0,
        float $b = 0,
        float $maxWidth = 0,
        float $lineSpacing = 1.15,
        float $x = 0,
    ): float {
        if ($maxWidth <= 0) {
            $maxWidth = $this->getContentWidth();
        }

        if ($x > 0) {
            $this->cursorX = $x;
        }

        $lines = $this->wrapText($text, $fontName, $fontSize, $maxWidth);
        $lineHeight = $fontSize * $lineSpacing;
        $totalHeight = 0;

        $fontRef = $this->ensureFont($fontName);

        foreach ($lines as $line) {
            if ($this->needsNewPage($lineHeight)) {
                $this->newPage();
            }

            $this->currentPageContent .= "BT\n";
            $this->currentPageContent .= sprintf("%.2f %.2f %.2f rg\n", $r, $g, $b);
            $this->currentPageContent .= sprintf("%s %.1f Tf\n", $fontRef, $fontSize);
            $this->currentPageContent .= sprintf("%.2f %.2f Td\n", $this->cursorX, $this->cursorY);
            $this->currentPageContent .= sprintf("(%s) Tj\n", $this->escapePdfString($line));
            $this->currentPageContent .= "ET\n";

            $this->cursorY -= $lineHeight;
            $totalHeight += $lineHeight;
        }

        $this->cursorX = $this->marginLeft;

        return $totalHeight;
    }

    /* -------------------------------------------------------------
     | Drawing Operations
     |------------------------------------------------------------- */

    public function drawLine(float $x1, float $y1, float $x2, float $y2, float $width = 0.5): void
    {
        $this->currentPageContent .= sprintf("%.2f w\n", $width);
        $this->currentPageContent .= sprintf("%.2f %.2f m %.2f %.2f l S\n", $x1, $y1, $x2, $y2);
    }

    public function drawRect(
        float $x,
        float $y,
        float $w,
        float $h,
        ?string $fillColor = null,
        ?string $strokeColor = null,
        float $strokeWidth = 0.5,
    ): void {
        $this->currentPageContent .= sprintf("%.2f w\n", $strokeWidth);

        if ($fillColor !== null) {
            [$fr, $fg, $fb] = $this->hexToRgb($fillColor);
            $this->currentPageContent .= sprintf("%.2f %.2f %.2f rg\n", $fr, $fg, $fb);
        }

        if ($strokeColor !== null) {
            [$sr, $sg, $sb] = $this->hexToRgb($strokeColor);
            $this->currentPageContent .= sprintf("%.2f %.2f %.2f RG\n", $sr, $sg, $sb);
        }

        $this->currentPageContent .= sprintf("%.2f %.2f %.2f %.2f re\n", $x, $y, $w, $h);

        if ($fillColor !== null && $strokeColor !== null) {
            $this->currentPageContent .= "B\n";
        } elseif ($fillColor !== null) {
            $this->currentPageContent .= "f\n";
        } else {
            $this->currentPageContent .= "S\n";
        }
    }

    /* -------------------------------------------------------------
     | Image Operations
     |------------------------------------------------------------- */

    public function drawImage(string $path, float $x, float $y, float $w, float $h): void
    {
        $hash = md5($path);

        if (! isset($this->images[$hash])) {
            $this->registerImage($path, $hash);
        }

        if (! isset($this->imageRefs[$hash])) {
            return;
        }

        $this->currentPageContent .= "q\n";
        $this->currentPageContent .= sprintf("%.2f 0 0 %.2f %.2f %.2f cm\n", $w, $h, $x, $y);
        $this->currentPageContent .= sprintf("%s Do\n", $this->imageRefs[$hash]);
        $this->currentPageContent .= "Q\n";
    }

    /* -------------------------------------------------------------
     | Text Measurement
     |------------------------------------------------------------- */

    public function measureTextWidth(string $text, string $fontName, float $fontSize): float
    {
        $avgWidth = self::CHAR_WIDTHS[$fontName] ?? 550;

        return mb_strlen($text) * $avgWidth * $fontSize / 1000;
    }

    /**
     * Écrit du texte à une position absolue sans déplacer le curseur.
     */
    public function writeTextAt(
        string $text,
        string $fontName,
        float $fontSize,
        float $x,
        float $y,
        float $r = 0,
        float $g = 0,
        float $b = 0,
    ): void {
        $fontRef = $this->ensureFont($fontName);

        $this->currentPageContent .= "BT\n";
        $this->currentPageContent .= sprintf("%.2f %.2f %.2f rg\n", $r, $g, $b);
        $this->currentPageContent .= sprintf("%s %.1f Tf\n", $fontRef, $fontSize);
        $this->currentPageContent .= sprintf("%.2f %.2f Td\n", $x, $y);
        $this->currentPageContent .= sprintf("(%s) Tj\n", $this->escapePdfString($text));
        $this->currentPageContent .= "ET\n";
    }

    /**
     * @return string[]
     */
    public function wrapText(string $text, string $fontName, float $fontSize, float $maxWidth): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine === '' ? $word : $currentLine . ' ' . $word;
            $testWidth = $this->measureTextWidth($testLine, $fontName, $fontSize);

            if ($testWidth > $maxWidth && $currentLine !== '') {
                $lines[] = $currentLine;
                $currentLine = $word;
            } else {
                $currentLine = $testLine;
            }
        }

        if ($currentLine !== '') {
            $lines[] = $currentLine;
        }

        return $lines ?: [''];
    }

    /* -------------------------------------------------------------
     | Output
     |------------------------------------------------------------- */

    public function output(): string
    {
        $this->flushPage();

        $pageCount = count($this->pageObjects);
        $pageObjNumbers = [];

        foreach ($this->pageObjects as $i => $contentObj) {
            $pageObj = $this->allocateObject();
            $pageObjNumbers[] = $pageObj;

            $resourceDict = $this->buildResourceDict($i);

            $this->objects[$pageObj] = new PdfObject($pageObj, sprintf(
                "<< /Type /Page /Parent %d 0 R /MediaBox [0 0 %.2f %.2f] /Contents %d 0 R /Resources %s >>",
                $this->pagesObj,
                $this->pageWidth,
                $this->pageHeight,
                $contentObj,
                $resourceDict
            ));
        }

        $kids = implode(' ', array_map(fn (int $n) => "{$n} 0 R", $pageObjNumbers));
        $this->objects[$this->pagesObj] = new PdfObject(
            $this->pagesObj,
            "<< /Type /Pages /Kids [{$kids}] /Count {$pageCount} >>"
        );

        $this->objects[$this->catalogObj] = new PdfObject(
            $this->catalogObj,
            "<< /Type /Catalog /Pages {$this->pagesObj} 0 R >>"
        );

        $infoObj = $this->allocateObject();
        $this->objects[$infoObj] = new PdfObject($infoObj, sprintf(
            "<< /Title (%s) /Creator (%s) /Producer (Paperdoc PHP Library) /CreationDate (D:%s) >>",
            $this->escapePdfString($this->title),
            $this->escapePdfString($this->creator),
            date('YmdHis')
        ));

        return $this->buildPdf($infoObj);
    }

    public function save(string $filename): void
    {
        file_put_contents($filename, $this->output());
    }

    /* -------------------------------------------------------------
     | Internal: Object Management
     |------------------------------------------------------------- */

    private function allocateObject(): int
    {
        return ++$this->objectCounter;
    }

    private function flushPage(): void
    {
        if ($this->currentPageContent === '') {
            return;
        }

        $streamObj = $this->allocateObject();
        $length = strlen($this->currentPageContent);

        $this->objects[$streamObj] = new PdfObject(
            $streamObj,
            "<< /Length {$length} >>\nstream\n{$this->currentPageContent}endstream"
        );

        $this->pageObjects[] = $streamObj;
        $this->currentPageContent = '';
    }

    private function buildResourceDict(int $pageIndex): string
    {
        $fontEntries = [];
        foreach ($this->fontRefs as $name => $ref) {
            $objNum = $this->fonts[$name];
            $fontEntries[] = "{$ref} {$objNum} 0 R";
        }

        $fontDict = '<< ' . implode(' ', $fontEntries) . ' >>';

        $imageEntries = [];
        foreach ($this->imageRefs as $hash => $ref) {
            $objNum = $this->images[$hash];
            $imageEntries[] = "{$ref} {$objNum} 0 R";
        }

        if (! empty($imageEntries)) {
            $xObjDict = '<< ' . implode(' ', $imageEntries) . ' >>';
            return "<< /Font {$fontDict} /XObject {$xObjDict} >>";
        }

        return "<< /Font {$fontDict} >>";
    }

    private function ensureFont(string $fontName): string
    {
        if (isset($this->fontRefs[$fontName])) {
            return $this->fontRefs[$fontName];
        }

        $objNum = $this->allocateObject();
        $this->fonts[$fontName] = $objNum;

        $ref = '/F' . (++$this->fontCounter);
        $this->fontRefs[$fontName] = $ref;

        $this->objects[$objNum] = new PdfObject(
            $objNum,
            sprintf(
                "<< /Type /Font /Subtype /Type1 /BaseFont /%s /Encoding /WinAnsiEncoding >>",
                $fontName
            )
        );

        return $ref;
    }

    private function registerImage(string $path, string $hash): void
    {
        if (! file_exists($path) || ! is_readable($path)) {
            return;
        }

        $info = @getimagesize($path);

        if ($info === false) {
            return;
        }

        [$imgWidth, $imgHeight, $type] = $info;
        $data = file_get_contents($path);

        if ($data === false) {
            return;
        }

        $objNum = $this->allocateObject();
        $ref = '/Im' . (++$this->imageCounter);

        $this->images[$hash] = $objNum;
        $this->imageRefs[$hash] = $ref;

        if ($type === IMAGETYPE_JPEG) {
            $length = strlen($data);
            $this->objects[$objNum] = new PdfObject($objNum, sprintf(
                "<< /Type /XObject /Subtype /Image /Width %d /Height %d /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length %d >>\nstream\n%sendstream",
                $imgWidth,
                $imgHeight,
                $length,
                $data
            ));
        } elseif ($type === IMAGETYPE_PNG) {
            $im = @imagecreatefrompng($path);

            if ($im === false) {
                return;
            }

            ob_start();
            imagejpeg($im, null, 90);
            $jpegData = ob_get_clean();
            imagedestroy($im);

            $length = strlen($jpegData);
            $this->objects[$objNum] = new PdfObject($objNum, sprintf(
                "<< /Type /XObject /Subtype /Image /Width %d /Height %d /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length %d >>\nstream\n%sendstream",
                $imgWidth,
                $imgHeight,
                $length,
                $jpegData
            ));
        }
    }

    /* -------------------------------------------------------------
     | Internal: PDF Assembly
     |------------------------------------------------------------- */

    private function buildPdf(int $infoObj): string
    {
        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];

        ksort($this->objects);

        foreach ($this->objects as $obj) {
            $offsets[$obj->getNumber()] = strlen($pdf);
            $pdf .= $obj->render() . "\n";
        }

        $xrefOffset = strlen($pdf);
        $objectCount = $this->objectCounter + 1;

        $pdf .= "xref\n0 {$objectCount}\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i < $objectCount; $i++) {
            if (isset($offsets[$i])) {
                $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
            } else {
                $pdf .= "0000000000 00000 f \n";
            }
        }

        $pdf .= "trailer\n";
        $pdf .= sprintf(
            "<< /Size %d /Root %d 0 R /Info %d 0 R >>\n",
            $objectCount,
            $this->catalogObj,
            $infoObj
        );
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    /* -------------------------------------------------------------
     | Internal: Helpers
     |------------------------------------------------------------- */

    private function escapePdfString(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace('(', '\\(', $text);
        $text = str_replace(')', '\\)', $text);

        return mb_convert_encoding($text, 'Windows-1252', 'UTF-8');
    }

    /**
     * @return array{float, float, float}
     */
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            hexdec(substr($hex, 0, 2)) / 255,
            hexdec(substr($hex, 2, 2)) / 255,
            hexdec(substr($hex, 4, 2)) / 255,
        ];
    }
}
