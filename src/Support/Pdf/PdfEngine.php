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

    /**
     * Callback fired right after {@see newPage()} has flushed the
     * previous page and reset the cursor — i.e. when a brand-new empty
     * page is ready to receive content. The renderer uses this to
     * repaint the per-page "chrome" (page background, header, footer)
     * on EVERY physical page, including those created mid-paragraph by
     * automatic text overflow in {@see writeWrappedText()}.
     *
     * The hook is NOT called by the constructor's initial newPage()
     * because it is set after construction — callers are expected to
     * paint the chrome of the first page manually (the renderer does).
     *
     * Set to null to disable.
     */
    private ?\Closure $onNewPage = null;

    /**
     * Coarse per-font average width fallback (1000em units), used only
     * when neither a per-glyph table from {@see Core14Widths::FONTS}
     * nor the requested glyph itself is available. Centring,
     * right-alignment, justification and word wrapping all read from
     * the precise per-glyph table by default — these averages exist
     * just so the engine still produces something for unknown fonts.
     */
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
        'Symbol' => 500,
        'ZapfDingbats' => 800,
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

    /**
     * Registers a hook fired on every new page started by the engine,
     * EXCEPT the first one created by the constructor (because at that
     * point no caller has had the chance to register a hook yet).
     *
     * The hook runs AFTER `flushPage()` has stored the previous page
     * and AFTER the cursor has been reset to the top-left of the new
     * page — meaning {@see getCurrentPageNumber()} already returns the
     * new page number and `cursorY` is at its initial value. Anything
     * the hook draws lands on the new page, at the very beginning of
     * its content stream, so a page-background fill emitted by the
     * hook is guaranteed to sit UNDER the body text.
     *
     * Pass `null` to disable a previously-registered hook.
     */
    public function setOnNewPage(?\Closure $callback): void
    {
        $this->onNewPage = $callback;
    }

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

        // Fire the per-page hook AFTER cursor reset so getCurrentPageNumber()
        // already returns the new page index and any drawing the hook does
        // (e.g. page background fill) lands at the head of the content stream
        // — i.e. under the body text drawn next by the caller.
        if ($this->onNewPage !== null) {
            ($this->onNewPage)();
        }
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

    /* -------------------------------------------------------------
     | Page margins — getters (read-only)
     |
     | The setters are bundled into setPageGeometry(); these getters
     | exist so the renderer can place absolute-coordinate elements
     | (tables, images, horizontal rules, …) flush against the actual
     | left/right margins of the current page instead of relying on
     | hardcoded values. Critical for documents with non-default
     | gutters.
     |------------------------------------------------------------- */

    public function getLeftMargin(): float   { return $this->marginLeft; }
    public function getRightMargin(): float  { return $this->marginRight; }
    public function getTopMargin(): float    { return $this->marginTop; }
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
     * Dessine une image en arrière-plan de la page. À appeler tout en
     * début de page (avant tout autre dessin) pour que le fond reste
     * sous le contenu.
     *
     * Le paramètre `$size` calque la sémantique CSS :
     *
     *  - `'cover'`     : l'image remplit la page en préservant son
     *                    ratio ; l'excédent est rogné via un clip path.
     *  - `'contain'`   : l'image tient en entier dans la page en
     *                    préservant son ratio (peut laisser des bandes).
     *  - `'auto'`      : taille naturelle de l'image, centrée (rognée
     *                    si plus grande que la page).
     *  - `'100% 100%'`
     *    ou `'stretch'`: l'image est étirée pour remplir la page sans
     *                    préserver le ratio (comportement historique).
     *
     * Toute autre valeur retombe sur `'100% 100%'`.
     */
    public function drawPageBackgroundImage(string $path, string $size = 'cover'): void
    {
        [$iw, $ih] = $this->getImageNaturalSize($path);

        // Sans dimensions exploitables, retombe sur l'étirement
        // (comportement précédent — aucune régression possible).
        if ($iw <= 0 || $ih <= 0) {
            $this->drawImage($path, 0, 0, $this->pageWidth, $this->pageHeight);

            return;
        }

        [$x, $y, $w, $h, $needsClip] = $this->computeBackgroundPlacement(
            $iw,
            $ih,
            $this->pageWidth,
            $this->pageHeight,
            $size,
        );

        // En mode `cover` ou `auto`, l'image peut déborder. On pose un
        // clip path rectangulaire à la taille de la page pour rogner
        // proprement (équivalent CSS `overflow: hidden` sur la page).
        if ($needsClip) {
            $this->currentPageContent .= "q\n";
            $this->currentPageContent .= sprintf(
                "%.2f %.2f %.2f %.2f re W n\n",
                0.0,
                0.0,
                $this->pageWidth,
                $this->pageHeight,
            );
        }

        $this->drawImage($path, $x, $y, $w, $h);

        if ($needsClip) {
            $this->currentPageContent .= "Q\n";
        }
    }

    /**
     * Calcule la position (en coordonnées PDF bottom-left) et la taille
     * cible d'une image de fond selon la stratégie demandée. Renvoie
     * également un drapeau indiquant si un clip path est nécessaire.
     *
     * @return array{0:float,1:float,2:float,3:float,4:bool} [x, y, w, h, needsClip]
     */
    private function computeBackgroundPlacement(
        float $iw,
        float $ih,
        float $pw,
        float $ph,
        string $size,
    ): array {
        $arImg  = $iw / $ih;
        $arPage = $pw / $ph;

        switch ($size) {
            case 'cover':
                if ($arImg > $arPage) {
                    $h = $ph;
                    $w = $h * $arImg;
                } else {
                    $w = $pw;
                    $h = $w / $arImg;
                }
                $x = ($pw - $w) / 2.0;
                $y = ($ph - $h) / 2.0;
                $needsClip = ($w > $pw + 0.01) || ($h > $ph + 0.01);
                break;

            case 'contain':
                if ($arImg > $arPage) {
                    $w = $pw;
                    $h = $w / $arImg;
                } else {
                    $h = $ph;
                    $w = $h * $arImg;
                }
                $x = ($pw - $w) / 2.0;
                $y = ($ph - $h) / 2.0;
                $needsClip = false;
                break;

            case 'auto':
                $w = $iw;
                $h = $ih;
                $x = ($pw - $w) / 2.0;
                $y = ($ph - $h) / 2.0;
                $needsClip = ($w > $pw + 0.01) || ($h > $ph + 0.01);
                break;

            case 'stretch':
            case '100% 100%':
            default:
                return [0.0, 0.0, $pw, $ph, false];
        }

        return [$x, $y, $w, $h, $needsClip];
    }

    /**
     * Lit les dimensions naturelles d'une image (en pixels). Renvoie
     * `[0, 0]` si l'image est illisible.
     *
     * @return array{0:int,1:int}
     */
    private function getImageNaturalSize(string $path): array
    {
        if (! is_file($path) || ! is_readable($path)) {
            return [0, 0];
        }

        $info = @getimagesize($path);

        if ($info === false || ! isset($info[0], $info[1])) {
            return [0, 0];
        }

        return [(int) $info[0], (int) $info[1]];
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
     * Le paramètre `$align` accepte `left` (défaut), `center`, `right`
     * et `justify`. Pour `justify`, l'espace résiduel est réparti via
     * l'opérateur PDF `Tw` (word spacing) ; la dernière ligne reste
     * alignée à gauche pour ne pas étirer une ligne courte.
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
        string $align = 'left',
        float $letterSpacing = 0.0,
        float $firstLineIndent = 0.0,
    ): float {
        if ($maxWidth <= 0) {
            $maxWidth = $this->getContentWidth();
        }

        if ($x > 0) {
            $this->cursorX = $x;
        }

        // L'origine X de la ligne est figée AVANT la boucle : on veut
        // que toutes les lignes utilisent la même boîte d'alignement
        // (sinon une ligne wrappée hériterait du décalage de la
        // précédente).
        $startX = $this->cursorX;

        // First-line indent: the wrapping engine sees a narrower budget
        // for the first line; the emit phase shifts the first line's
        // X by the same amount. Negative indent (hanging) extends the
        // first line to the LEFT — we don't currently widen the wrap
        // budget for that case (a paragraph with hanging indent is
        // typically used for short list-item style text where wrapping
        // matters less).
        $firstLineMaxWidth = ($firstLineIndent > 0.0)
            ? max(0.0, $maxWidth - $firstLineIndent)
            : $maxWidth;

        $lines       = $this->wrapText($text, $fontName, $fontSize, $maxWidth, $letterSpacing, $firstLineMaxWidth);
        $lineHeight  = $fontSize * $lineSpacing;
        $totalHeight = 0;
        $totalLines  = count($lines);

        $fontRef = $this->ensureFont($fontName);

        foreach ($lines as $i => $line) {
            if ($this->needsNewPage($lineHeight)) {
                $this->newPage();
            }

            $isLastLine = ($i + 1 >= $totalLines);
            $isFirstLine = ($i === 0);

            $lineX     = $isFirstLine ? $startX + $firstLineIndent : $startX;
            $lineWidth = $isFirstLine ? $firstLineMaxWidth          : $maxWidth;

            $this->emitTextLine(
                line:          $line,
                fontRef:       $fontRef,
                fontName:      $fontName,
                fontSize:      $fontSize,
                x:             $lineX,
                baselineY:     $this->cursorY,
                maxWidth:      $lineWidth,
                r:             $r,
                g:             $g,
                b:             $b,
                align:         $align,
                isLastLine:    $isLastLine,
                letterSpacing: $letterSpacing,
            );

            $this->cursorY -= $lineHeight;
            $totalHeight   += $lineHeight;
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

    /**
     * Like {@see drawLine()} but emits a stroke-colour change before
     * drawing and resets it to black afterwards. Useful for a single
     * coloured rule without leaking the colour into subsequent ops.
     */
    public function drawColoredLine(
        float $x1,
        float $y1,
        float $x2,
        float $y2,
        float $width,
        float $r,
        float $g,
        float $b,
    ): void {
        $this->currentPageContent .= sprintf("%.2f %.2f %.2f RG\n", $r, $g, $b);
        $this->drawLine($x1, $y1, $x2, $y2, $width);
        $this->currentPageContent .= "0 0 0 RG\n";
    }

    /**
     * Draws a single coloured horizontal stroke. The y coordinate is
     * interpreted in user/CSS convention (origin at the top-left,
     * grows downwards) for symmetry with `drawImageAt` and
     * `writeWrappedTextAt`.
     */
    public function drawHorizontalLine(
        float $x,
        float $yTopLeft,
        float $width,
        float $thickness,
        string $color,
    ): void {
        [$r, $g, $bcol] = $this->hexToRgb($color);
        $bottomY = $this->pageHeight - $yTopLeft;

        $this->currentPageContent .= sprintf("%.2f %.2f %.2f RG\n", $r, $g, $bcol);
        $this->drawLine($x, $bottomY, $x + $width, $bottomY, $thickness);
        // Reset stroke colour to black so subsequent draws don't inherit it.
        $this->currentPageContent .= "0 0 0 RG\n";
    }

    /* -------------------------------------------------------------
     | Page-content windowing — used by Section vertical alignment
     | (v0.8.0) and any future "wrap an arbitrary slice in a CTM
     | transform" use case.
     |------------------------------------------------------------- */

    /**
     * Returns the current byte length of the in-flight page content
     * stream. Use it to mark a position before rendering a span of
     * elements, then later call {@see wrapPageContentSince()} with
     * that mark to wrap the rendered span in `q ... Q` graphics
     * state and an optional CTM (for translation/rotation).
     */
    public function getPageContentLength(): int
    {
        return strlen($this->currentPageContent);
    }

    /**
     * Wraps the page content emitted since {@see getPageContentLength()}
     * was sampled, by inserting `q\n<prefix>` at the offset and
     * appending `<suffix>\nQ\n` at the tail. Used by the renderer to
     * apply a translation matrix (`1 0 0 1 dx dy cm`) to a section's
     * body when it should be vertically centred or bottom-anchored.
     *
     * The graphics-state push/pop is mandatory: PDF content streams
     * accumulate transforms, so without `q ... Q` the cm would leak
     * into all subsequent content (header, footer, page background of
     * the next page, …).
     */
    public function wrapPageContentSince(int $offset, string $prefix = '', string $suffix = ''): void
    {
        $head = substr($this->currentPageContent, 0, $offset);
        $tail = substr($this->currentPageContent, $offset);

        $this->currentPageContent = $head
            . "q\n"
            . ($prefix !== '' ? rtrim($prefix, "\n") . "\n" : '')
            . $tail
            . ($suffix !== '' ? rtrim($suffix, "\n") . "\n" : '')
            . "Q\n";
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

    /**
     * Mesure la largeur réelle d'une chaîne dans une police PDF en
     * utilisant la table de métriques per-glyphe Core 14 (générée à
     * partir des AFM URW Base35, métriquement compatibles avec les
     * polices standard PDF). Les calculs de centrage,
     * d'alignement-droit, de justification et de wrapping reposent
     * tous sur cette mesure, donc une bonne précision ici fait
     * littéralement la différence entre un titre centré et un titre
     * « presque centré ».
     *
     * Pipeline :
     *   1. La chaîne UTF-8 est convertie en WinAnsi (cp1252) — c'est
     *      l'encodage qu'on émet réellement dans le PDF, donc c'est
     *      bien sur cette représentation qu'il faut additionner.
     *   2. Pour chaque octet, on lit la largeur dans la table Core 14.
     *   3. Si la police n'est pas dans la table (police custom future)
     *      ou si l'octet n'a pas de largeur (cas .notdef), on retombe
     *      sur la largeur moyenne par police (CHAR_WIDTHS).
     */
    public function measureTextWidth(
        string $text,
        string $fontName,
        float $fontSize,
        float $letterSpacing = 0.0,
    ): float {
        $fontTable = Core14Widths::FONTS[$fontName] ?? null;

        if ($fontTable === null) {
            $avgWidth = self::CHAR_WIDTHS[$fontName] ?? 550;
            $glyphCount = mb_strlen($text);
            $base = $glyphCount * $avgWidth * $fontSize / 1000;

            return $base + max(0, $glyphCount - 1) * $letterSpacing;
        }

        $widths   = $fontTable['widths'];
        $default  = $fontTable['default'];
        $winAnsi  = mb_convert_encoding($text, 'Windows-1252', 'UTF-8');
        // mb_convert_encoding can fall back to PHP's substitute character
        // (often '?', code 0x3F) for any UTF-8 codepoint not representable
        // in cp1252; we just measure that substitute, which is exactly
        // what we'll be drawing anyway.
        $units    = 0;
        $len      = strlen($winAnsi);

        for ($i = 0; $i < $len; $i++) {
            $code = ord($winAnsi[$i]);
            $units += $widths[$code] ?? $default;
        }

        $base = $units * $fontSize / 1000.0;

        // Tc is applied between every pair of adjacent glyphs, hence
        // (len - 1) extra advances. Negative letterSpacing shrinks
        // the line.
        return $base + max(0, $len - 1) * $letterSpacing;
    }

    /**
     * Returns vertical metrics for a font in 1000em units. Used by the
     * renderer to reserve enough vertical space when the next paragraph
     * uses a much larger font size than the previous one (otherwise the
     * top of the new glyphs collides with the previous baseline).
     *
     * @return array{ascender:int, descender:int, capHeight:int}
     */
    public function getFontMetrics(string $fontName): array
    {
        $t = Core14Widths::FONTS[$fontName] ?? null;
        if ($t === null) {
            return ['ascender' => 718, 'descender' => -207, 'capHeight' => 718];
        }

        return [
            'ascender'  => $t['ascender'],
            'descender' => $t['descender'],
            'capHeight' => $t['capHeight'],
        ];
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
        string $align = 'left',
        float $letterSpacing = 0.0,
        float $firstLineIndent = 0.0,
    ): array {
        $fontRef    = $this->ensureFont($fontName);
        $lineHeight = $fontSize * $lineSpacing;

        $firstLineMaxWidth = ($firstLineIndent > 0.0)
            ? max(0.0, $maxWidth - $firstLineIndent)
            : $maxWidth;

        $lines      = $this->wrapText($text, $fontName, $fontSize, $maxWidth, $letterSpacing, $firstLineMaxWidth);
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

            $isLastLine = ($i + 1 >= $totalLines) || $isLastDrawable;
            $isFirstLine = ($i === 0);

            $lineX     = $isFirstLine ? $x + $firstLineIndent : $x;
            $lineWidth = $isFirstLine ? $firstLineMaxWidth     : $maxWidth;

            $this->emitTextLine(
                line:          $line,
                fontRef:       $fontRef,
                fontName:      $fontName,
                fontSize:      $fontSize,
                x:             $lineX,
                baselineY:     $baselineY,
                maxWidth:      $lineWidth,
                r:             $r,
                g:             $g,
                b:             $b,
                align:         $align,
                isLastLine:    $isLastLine,
                letterSpacing: $letterSpacing,
            );

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
     * Émet une ligne de texte au content stream PDF en gérant
     * l'alignement horizontal :
     *
     *  - `left`    : la ligne commence à `$x` (comportement par défaut).
     *  - `right`   : la ligne se termine à `$x + $maxWidth`.
     *  - `center`  : la ligne est centrée dans `[x, x+maxWidth]`.
     *  - `justify` : les espaces sont étirés via l'opérateur PDF `Tw`
     *                pour que la ligne occupe toute la largeur. La
     *                dernière ligne d'un paragraphe (`$isLastLine`)
     *                garde l'alignement gauche pour ne pas étirer une
     *                ligne courte.
     */
    /**
     * If a justified line ends up needing more than this much extra
     * word-spacing per inter-word gap, we silently fall back to
     * left-alignment for that line. Stretching beyond ~3pt produces
     * the visible "rivers" you can see in cheap typesetting; better to
     * accept a ragged right edge than a galleried look.
     */
    private const JUSTIFY_MAX_EXTRA_TW_PT = 3.0;

    /**
     * Cap on character-spacing assistance for justification, in 1000em
     * units. We split the leftover space between Tw (word spacing) and
     * Tc (character spacing) — Tc is per glyph so even small values
     * compound; we conservatively limit it to 0.10 em (= 1.2pt at 12pt
     * size) so individual letters never look unnaturally pulled apart.
     */
    private const JUSTIFY_MAX_TC_EM = 0.10;

    private function emitTextLine(
        string $line,
        string $fontRef,
        string $fontName,
        float $fontSize,
        float $x,
        float $baselineY,
        float $maxWidth,
        float $r,
        float $g,
        float $b,
        string $align,
        bool $isLastLine,
        float $letterSpacing = 0.0,
    ): void {
        $lineWidth = $this->measureTextWidth($line, $fontName, $fontSize, $letterSpacing);
        $drawX     = $x;
        $extraTw   = 0.0;
        // The Tc operator we'll actually emit is the SUM of the
        // user-requested letterSpacing (from TextStyle) and any extra
        // character-spacing that justification might add. We emit one
        // Tc, then reset to 0 at the end of the BT/ET block.
        $extraTc   = 0.0;

        switch ($align) {
            case 'right':
                $drawX = $x + max(0.0, $maxWidth - $lineWidth);
                break;
            case 'center':
                $drawX = $x + max(0.0, ($maxWidth - $lineWidth) / 2.0);
                break;
            case 'justify':
                if (! $isLastLine && $lineWidth < $maxWidth) {
                    [$extraTw, $extraTc] = $this->computeJustifySpacing($line, $maxWidth, $lineWidth, $fontSize);
                    // Couldn't reach a reasonable Tw under the threshold —
                    // fall back to left-alignment for this single line so
                    // we don't produce a visually ugly "river".
                    if ($extraTw === null) {
                        $extraTw = 0.0;
                        $extraTc = 0.0;
                    }
                }
                break;
            case 'left':
            default:
                break;
        }

        $totalTc = $extraTc + $letterSpacing;

        $this->currentPageContent .= "BT\n";
        $this->currentPageContent .= sprintf("%.2f %.2f %.2f rg\n", $r, $g, $b);
        $this->currentPageContent .= sprintf("%s %.1f Tf\n", $fontRef, $fontSize);
        if ($extraTw > 0.0) {
            $this->currentPageContent .= sprintf("%.3f Tw\n", $extraTw);
        }
        if (abs($totalTc) > 1e-4) {
            $this->currentPageContent .= sprintf("%.3f Tc\n", $totalTc);
        }
        $this->currentPageContent .= sprintf("%.2f %.2f Td\n", $drawX, $baselineY);
        $this->currentPageContent .= sprintf("(%s) Tj\n", $this->escapePdfString($line));
        if ($extraTw > 0.0) {
            // Always reset Tw so subsequent text isn't accidentally justified.
            $this->currentPageContent .= "0 Tw\n";
        }
        if (abs($totalTc) > 1e-4) {
            $this->currentPageContent .= "0 Tc\n";
        }
        $this->currentPageContent .= "ET\n";
    }

    /**
     * Splits the missing horizontal space between word-spacing (Tw)
     * and character-spacing (Tc), so a justified line doesn't
     * concentrate all of the extra slack on inter-word gaps (which is
     * what creates the visual "rivers" of whitespace classic to cheap
     * justification).
     *
     * Strategy:
     *   1. Try pure word-spacing first (visually preferred).
     *   2. If Tw alone would exceed JUSTIFY_MAX_EXTRA_TW_PT, give part
     *      of the gap to Tc (character spacing). Tc is bounded to
     *      JUSTIFY_MAX_TC_EM × fontSize — beyond that, individual
     *      letters look unnaturally stretched.
     *   3. If even with Tc capped we still need Tw above the threshold,
     *      give up: return [null, 0] and the caller falls back to
     *      left-alignment for this single line.
     *
     * @return array{0:?float,1:float} [Tw_pt, Tc_pt] or [null, 0] when
     *         the line should fall back to flush-left
     */
    private function computeJustifySpacing(string $line, float $maxWidth, float $lineWidth, float $fontSize): array
    {
        $spaces = substr_count(rtrim($line), ' ');
        // Approximate glyph count (without trying to be exact about
        // multi-byte) — we only use it to spread the Tc adjustment.
        $glyphs = max(0, mb_strlen($line) - 1);

        $missing = $maxWidth - $lineWidth;
        if ($missing <= 0.0) {
            return [0.0, 0.0];
        }

        // First, attempt with Tw only.
        if ($spaces > 0) {
            $twOnly = $missing / $spaces;
            if ($twOnly <= self::JUSTIFY_MAX_EXTRA_TW_PT) {
                return [$twOnly, 0.0];
            }
        }

        // Tw alone would be too large. Cap Tw at the threshold and use
        // Tc to absorb the remainder.
        $maxTcPt = self::JUSTIFY_MAX_TC_EM * $fontSize;

        $tw = ($spaces > 0) ? min(self::JUSTIFY_MAX_EXTRA_TW_PT, $missing / $spaces) : 0.0;
        $contributedByTw = $tw * $spaces;
        $remaining       = $missing - $contributedByTw;

        if ($glyphs > 0 && $remaining > 0.0) {
            $tcNeeded = $remaining / $glyphs;
            $tc = min($maxTcPt, max(0.0, $tcNeeded));

            // After applying capped Tc, is the residual gap still too
            // big? If so, this line is just too short to justify
            // gracefully; fall back to flush-left.
            $contributedByTc = $tc * $glyphs;
            if ($contributedByTw + $contributedByTc + 0.5 < $missing) {
                return [null, 0.0];
            }

            return [$tw, $tc];
        }

        // Lonely-word lines (0 spaces, 0 inter-glyph candidates) — never justify.
        return [null, 0.0];
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
    public function wrapText(
        string $text,
        string $fontName,
        float $fontSize,
        float $maxWidth,
        float $letterSpacing = 0.0,
        ?float $firstLineMaxWidth = null,
    ): array {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine  = $currentLine === '' ? $word : $currentLine . ' ' . $word;
            $testWidth = $this->measureTextWidth($testLine, $fontName, $fontSize, $letterSpacing);

            // For the first emitted line, a possibly tighter budget applies
            // (text-indent shrinks the first line's available width).
            $budget = (count($lines) === 0 && $firstLineMaxWidth !== null)
                ? $firstLineMaxWidth
                : $maxWidth;

            if ($testWidth > $budget && $currentLine !== '') {
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

    /**
     * Encode a PHP UTF-8 string for inclusion in a PDF text string
     * literal. PDF Type 1 fonts using /WinAnsiEncoding can only
     * represent characters present in cp1252; anything outside
     * (Greek, CJK, mathematical operators not in WinAnsi, …) is
     * replaced by '?' rather than silently dropped, so the missing
     * characters are visible in the output instead of producing
     * mysterious word-spacing or wrong widths downstream.
     *
     * Backslash, '(' and ')' are escaped per PDF 1.7 §7.3.4.2.
     */
    private function escapePdfString(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace('(', '\\(', $text);
        $text = str_replace(')', '\\)', $text);

        // mb_convert_encoding() drops un-representable UTF-8 sequences
        // by default. Substitute them with '?' explicitly so the user
        // can see something is missing instead of getting silent gaps.
        $prevSubstitute = mb_substitute_character();
        mb_substitute_character(0x3F); // '?'
        try {
            $encoded = mb_convert_encoding($text, 'Windows-1252', 'UTF-8');
        } finally {
            mb_substitute_character($prevSubstitute);
        }

        return $encoded;
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
