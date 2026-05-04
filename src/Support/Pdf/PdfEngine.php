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

    /**
     * Per-page geometry captured at flush time so each page's MediaBox
     * can vary (size & margins set via setPageGeometry()).
     *
     * @var array<int, array{width: float, height: float, marginTop: float, marginRight: float, marginBottom: float, marginLeft: float}>
     */
    private array $pageGeometries = [];

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

    /**
     * Met à jour la géométrie de la page courante (taille et marges).
     *
     * À appeler juste après {@see newPage()} pour que la nouvelle page
     * adopte les dimensions souhaitées avant d'y écrire quoi que ce soit.
     */
    public function setPageGeometry(
        float $width,
        float $height,
        float $marginTop,
        float $marginRight,
        float $marginBottom,
        float $marginLeft,
    ): void {
        $this->pageWidth    = $width;
        $this->pageHeight   = $height;
        $this->marginTop    = $marginTop;
        $this->marginRight  = $marginRight;
        $this->marginBottom = $marginBottom;
        $this->marginLeft   = $marginLeft;

        $this->cursorX = $this->marginLeft;
        $this->cursorY = $this->pageHeight - $this->marginTop;
    }

    public function getPageWidth(): float  { return $this->pageWidth; }
    public function getPageHeight(): float { return $this->pageHeight; }

    /**
     * Numéro de la page actuellement en cours d'écriture (1-indexé).
     * Tient compte des pages déjà flushées et de la page en cours de
     * remplissage.
     */
    public function getCurrentPageNumber(): int
    {
        $count = count($this->pageObjects);

        // currentPageContent !== '' means we have content on a fresh page
        // that hasn't been flushed yet. That page hasn't been counted in
        // pageObjects, so its number is count+1. When the page is empty
        // (just opened), it's also count+1.
        return $count + 1;
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
     | Page Background (image / color)
     |------------------------------------------------------------- */

    /**
     * Remplit la page entière avec une couleur unie. À appeler juste
     * après newPage() (avant tout autre dessin) pour que le fond reste
     * sous le contenu.
     */
    public function drawPageBackgroundColor(string $color): void
    {
        $this->drawRect(0, 0, $this->pageWidth, $this->pageHeight, $color, null, 0);
    }

    /**
     * Dessine une image qui couvre la totalité de la page. Idem que
     * drawPageBackgroundColor : à appeler en premier sur la page.
     */
    public function drawPageBackgroundImage(string $path): void
    {
        $this->drawImage($path, 0, 0, $this->pageWidth, $this->pageHeight);
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
     * Écrit un bloc de texte avec retour à la ligne automatique dans une
     * largeur donnée, à une position absolue (x, y) où l'origine est le
     * coin supérieur gauche de la page (convention utilisateur/CSS).
     *
     * Si $maxHeight est fourni, les lignes qui dépassent sont tronquées.
     * Si $ellipsis vaut true, la dernière ligne visible reçoit '…' lorsqu'il
     * reste du contenu non rendu.
     *
     * @return array{consumed: float, truncated: bool, totalLines: int, drawnLines: int}
     */
    public function writeWrappedTextAt(
        string $text,
        string $fontName,
        float $fontSize,
        float $x,
        float $yTopLeft,
        float $maxWidth,
        float $r = 0,
        float $g = 0,
        float $b = 0,
        float $lineSpacing = 1.15,
        ?float $maxHeight = null,
        bool $ellipsis = false,
    ): array {
        $fontRef    = $this->ensureFont($fontName);
        $lineHeight = $fontSize * $lineSpacing;

        $lines      = $this->wrapText($text, $fontName, $fontSize, $maxWidth);
        $totalLines = count($lines);

        $consumed   = 0.0;
        $drawn      = 0;
        $truncated  = false;

        foreach ($lines as $i => $line) {
            $topOffset = $i * $lineHeight;

            if ($maxHeight !== null && $topOffset + $lineHeight > $maxHeight) {
                $truncated = true;
                break;
            }

            $isLastDrawable = $maxHeight !== null
                && $i + 1 < $totalLines
                && $topOffset + 2 * $lineHeight > $maxHeight;

            if ($isLastDrawable && $ellipsis) {
                $line = $this->appendEllipsis($line, $fontName, $fontSize, $maxWidth);
                $truncated = true;
            }

            $baselineY = $this->pageHeight - $yTopLeft - $topOffset - $fontSize;

            $this->currentPageContent .= "BT\n";
            $this->currentPageContent .= sprintf("%.2f %.2f %.2f rg\n", $r, $g, $b);
            $this->currentPageContent .= sprintf("%s %.1f Tf\n", $fontRef, $fontSize);
            $this->currentPageContent .= sprintf("%.2f %.2f Td\n", $x, $baselineY);
            $this->currentPageContent .= sprintf("(%s) Tj\n", $this->escapePdfString($line));
            $this->currentPageContent .= "ET\n";

            $consumed = $topOffset + $lineHeight;
            $drawn++;
        }

        return [
            'consumed'   => $consumed,
            'truncated'  => $truncated,
            'totalLines' => $totalLines,
            'drawnLines' => $drawn,
        ];
    }

    /**
     * Tronque la fin d'une ligne pour y faire tenir '…' dans la largeur
     * disponible.
     */
    private function appendEllipsis(string $line, string $fontName, float $fontSize, float $maxWidth): string
    {
        $candidate = rtrim($line) . '…';

        while ($candidate !== '…' && $this->measureTextWidth($candidate, $fontName, $fontSize) > $maxWidth) {
            $candidate = mb_substr($candidate, 0, mb_strlen($candidate) - 2) . '…';
        }

        return $candidate;
    }

    /**
     * Dessine un rectangle aux coordonnées top-left (origine page haut-gauche),
     * en convertissant vers la convention PDF interne. Pratique pour
     * encadrer une zone de texte.
     */
    public function drawRectTopLeft(
        float $x,
        float $yTopLeft,
        float $width,
        float $height,
        ?string $fillColor = null,
        ?string $strokeColor = null,
        float $strokeWidth = 0.5,
    ): void {
        $bottomY = $this->pageHeight - $yTopLeft - $height;
        $this->drawRect($x, $bottomY, $width, $height, $fillColor, $strokeColor, $strokeWidth);
    }

    /**
     * Dessine une image aux coordonnées top-left.
     */
    public function drawImageTopLeft(string $path, float $x, float $yTopLeft, float $width, float $height): void
    {
        $bottomY = $this->pageHeight - $yTopLeft - $height;
        $this->drawImage($path, $x, $bottomY, $width, $height);
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

            $geometry = $this->pageGeometries[$i] ?? [
                'width'  => $this->pageWidth,
                'height' => $this->pageHeight,
            ];

            $this->objects[$pageObj] = new PdfObject($pageObj, sprintf(
                "<< /Type /Page /Parent %d 0 R /MediaBox [0 0 %.2f %.2f] /Contents %d 0 R /Resources %s >>",
                $this->pagesObj,
                $geometry['width'],
                $geometry['height'],
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
        $this->pageGeometries[] = [
            'width'        => $this->pageWidth,
            'height'       => $this->pageHeight,
            'marginTop'    => $this->marginTop,
            'marginRight'  => $this->marginRight,
            'marginBottom' => $this->marginBottom,
            'marginLeft'   => $this->marginLeft,
        ];

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

    /**
     * Enregistre l'image comme XObject DCT. N'écrit jamais de référence
     * partielle : GIF/WebP/PNG/… passent d'abord par GD (imagecreatefromstring
     * puis re-encodage JPEG), en secours DCT direct pour le JPEG, puis PNG via fichier.
     */
    private function registerImage(string $path, string $hash): void
    {
        if (! is_readable($path)) {
            return;
        }

        $data = @file_get_contents($path);
        if ($data === false || $data === '') {
            return;
        }

        $info = @getimagesizefromstring($data);
        if ($info === false) {
            $info = @getimagesize($path);
        }
        if ($info === false) {
            return;
        }

        $rawW   = (int) $info[0];
        $rawH   = (int) $info[1];
        $iType  = (int) ($info[2] ?? 0);

        $outW  = $rawW;
        $outH  = $rawH;
        $jpeg  = null;

        if (function_exists('imagecreatefromstring')) {
            $im = @imagecreatefromstring($data);
            if ($im !== false) {
                $outW = max(1, imagesx($im));
                $outH = max(1, imagesy($im));
                ob_start();
                imagejpeg($im, null, 90);
                $captured = (string) ob_get_clean();
                unset($im);
                if ($captured !== '') {
                    $jpeg = $captured;
                }
            }
        }

        if ($jpeg === null && $iType === IMAGETYPE_JPEG) {
            $jpeg = $data;
            $outW = max(1, $rawW);
            $outH = max(1, $rawH);
        }

        if ($jpeg === null && $iType === IMAGETYPE_PNG && function_exists('imagecreatefrompng')) {
            $im = @imagecreatefrompng($path);
            if ($im !== false) {
                $outW = max(1, imagesx($im));
                $outH = max(1, imagesy($im));
                ob_start();
                imagejpeg($im, null, 90);
                $jpeg = (string) ob_get_clean();
                unset($im);
                if ($jpeg === '') {
                    $jpeg = null;
                }
            }
        }

        if ($jpeg === null || $outW < 1 || $outH < 1) {
            return;
        }

        $objNum = $this->allocateObject();
        $ref    = '/Im' . (++$this->imageCounter);

        $this->images[$hash]  = $objNum;
        $this->imageRefs[$hash] = $ref;
        $len = strlen($jpeg);

        $this->objects[$objNum] = new PdfObject(
            $objNum,
            sprintf(
                "<< /Type /XObject /Subtype /Image /Width %d /Height %d /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length %d >>\nstream\n%sendstream",
                $outW,
                $outH,
                $len,
                $jpeg
            )
        );
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
