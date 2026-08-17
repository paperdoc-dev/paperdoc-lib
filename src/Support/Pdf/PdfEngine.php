<?php

declare(strict_types=1);

namespace Paperdoc\Support\Pdf;

use Paperdoc\Document\Style\PdfProtection;
use Paperdoc\Support\Text\ArabicShaper;
use Paperdoc\Support\Text\Bidi;

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

    /**
     * Polices TrueType embarquées, par alias. Les 14 polices standard sont
     * limitées à WinAnsiEncoding ; embarquer un fichier est la seule façon
     * d'écrire autre chose que du latin-1.
     *
     * @var array<string, TrueTypeFont>
     */
    private array $embeddedFonts = [];

    /**
     * Points de code réellement écrits avec chaque police embarquée, et
     * l'identifiant de glyphe du sous-ensemble qui leur correspond. Sert
     * à bâtir la CMap ToUnicode.
     *
     * @var array<string, array<int, int>>
     */
    private array $embeddedFontCodepoints = [];

    /**
     * Renumérotation des glyphes, par police : ancien identifiant →
     * identifiant dans le sous-ensemble. Attribuée au fil de l'écriture
     * pour que le flux de contenu et la police réduite concordent.
     *
     * @var array<string, array<int, int>>
     */
    private array $embeddedFontGlyphs = [];

    /** @var array<string, int> alias => numéro d'objet Type0 à remplir */
    private array $pendingEmbeddedFonts = [];

    /** @var array<string, int> Image hash => object number */
    private array $images = [];

    /** @var array<string, string> Image hash => PDF reference */
    private array $imageRefs = [];

    private int $imageCounter = 0;

    private string $title    = '';
    private string $creator  = 'Paperdoc';
    private string $author   = '';
    private string $subject  = '';
    private string $keywords = '';
    private ?\DateTimeInterface $creationDate = null;
    private ?\DateTimeInterface $modificationDate = null;

    /**
     * Link annotations recorded for already-flushed pages, indexed by
     * page index (0-based, aligned with $pageObjects). Each record is
     * either `['rect' => [x1,y1,x2,y2], 'uri' => string]` (external
     * URI action) or `['rect' => [...], 'anchor' => string]` (internal
     * GoTo destination, resolved by name at output() time so forward
     * references Just Work).
     *
     * @var array<int, list<array{rect: array{float,float,float,float}, uri?: string, anchor?: string}>>
     */
    private array $pageAnnotations = [];

    /** @var list<array{rect: array{float,float,float,float}, uri?: string, anchor?: string}> */
    private array $currentPageAnnotations = [];

    /**
     * Active link target while text is being emitted. When non-null,
     * every line drawn by emitTextLine() also records a clickable
     * rectangle on the current page — which is what makes multi-line
     * (wrapped) and page-spanning links work without the renderer
     * having to know anything about line geometry.
     *
     * @var array{uri?: string, anchor?: string}|null
     */
    private ?array $activeLink = null;

    /**
     * Named internal destinations: anchor name => 0-based page index
     * and the Y coordinate (PDF bottom-left) the viewport should
     * scroll to.
     *
     * @var array<string, array{page: int, y: float}>
     */
    private array $anchors = [];

    /**
     * Flat list of document outline (bookmarks panel) entries in
     * reading order. The tree is reconstructed from the levels at
     * output() time.
     *
     * @var list<array{level: int, title: string, page: int, y: float}>
     */
    private array $outlineEntries = [];

    private bool $decoUnderline = false;
    private bool $decoStrikethrough = false;
    private ?string $decoHighlight = null;

    /** @var array<string, array{obj: int, ref: string}> */
    private array $extGStates = [];
    private int $gsCounter = 0;

    private bool $compressStreams = true;
    private ?PdfProtection $protection = null;
    private ?PdfStandardSecurity $security = null;
    private ?string $fileId = null;

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
     * Fired at the start of {@see flushPage()} while the page content
     * stream is still open — used to paint footnotes into the reserved
     * bottom band before the page is sealed.
     */
    private ?\Closure $beforeFlushPage = null;

    /** Extra bottom inset reserved for footnotes (points). */
    private float $reservedBottom = 0.0;

    private int $columnCount = 1;
    private float $columnGap = 18.0;
    private int $currentColumn = 0;
    private float $columnTopY = 0.0;

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
     * Content streams are Flate-compressed by default (v1.0.0). Turn
     * off to produce human-readable streams (debugging, diffing).
     */
    public function setCompression(bool $enabled): void { $this->compressStreams = $enabled; }
    public function setAuthor(string $author): void { $this->author = $author; }
    public function setSubject(string $subject): void { $this->subject = $subject; }
    public function setKeywords(string $keywords): void { $this->keywords = $keywords; }
    public function setCreationDate(?\DateTimeInterface $date): void { $this->creationDate = $date; }
    public function setModificationDate(?\DateTimeInterface $date): void { $this->modificationDate = $date; }
    public function setProtection(?PdfProtection $protection): void { $this->protection = $protection?->isEnabled() ? $protection : null; }

    /* -------------------------------------------------------------
     | Links, anchors & outline (v1.0.0)
     |------------------------------------------------------------- */

    /**
     * Opens a link scope: every text line emitted until {@see endLink()}
     * records a clickable rectangle on its page. Pass `$uri` for an
     * external URI action, or `$anchor` for an internal jump to a
     * destination registered (before OR after this call) via
     * {@see registerAnchor()}. When both are given, the URI wins.
     */
    public function beginLink(?string $uri, ?string $anchor = null): void
    {
        if ($uri !== null && $uri !== '') {
            $this->activeLink = ['uri' => $uri];
        } elseif ($anchor !== null && $anchor !== '') {
            $this->activeLink = ['anchor' => $anchor];
        } else {
            $this->activeLink = null;
        }
    }

    public function endLink(): void
    {
        $this->activeLink = null;
    }

    /**
     * Text decorations applied to every line emitted until
     * {@see clearTextDecorations()}: real drawn underline / strike
     * lines and a highlight rectangle painted behind the text.
     */
    public function setTextDecorations(
        bool $underline = false,
        bool $strikethrough = false,
        ?string $highlight = null,
    ): void {
        $this->decoUnderline     = $underline;
        $this->decoStrikethrough = $strikethrough;
        $this->decoHighlight     = $highlight;
    }

    public function clearTextDecorations(): void
    {
        $this->setTextDecorations();
    }

    /**
     * Draws a rotated, semi-transparent text centred on the current
     * page — used for document watermarks. Call once per page, right
     * after the page background so it sits under the body content.
     */
    public function drawWatermarkText(
        string $text,
        string $fontName,
        float $fontSize,
        string $color,
        float $opacity,
        float $angleDegrees,
    ): void {
        if ($text === '') {
            return;
        }

        $gsRef   = $this->ensureExtGState(max(0.0, min(1.0, $opacity)));
        $fontRef = $this->ensureFont($fontName);
        [$r, $g, $b] = $this->hexToRgb($color);

        $rad = deg2rad($angleDegrees);
        $cos = cos($rad);
        $sin = sin($rad);
        $width = $this->measureTextWidth($text, $fontName, $fontSize);

        $this->currentPageContent .= "q\n{$gsRef} gs\n";
        $this->currentPageContent .= sprintf(
            "%.4f %.4f %.4f %.4f %.2f %.2f cm\n",
            $cos,
            $sin,
            -$sin,
            $cos,
            $this->pageWidth / 2.0,
            $this->pageHeight / 2.0,
        );
        $this->currentPageContent .= "BT\n";
        $this->currentPageContent .= sprintf("%.2f %.2f %.2f rg\n", $r, $g, $b);
        $this->currentPageContent .= sprintf("%s %.1f Tf\n", $fontRef, $fontSize);
        $this->currentPageContent .= sprintf("%.2f %.2f Td\n", -$width / 2.0, -$fontSize / 3.0);
        $this->currentPageContent .= sprintf("%s Tj\n", $this->showTextOperand($text, $fontName));
        $this->currentPageContent .= "ET\nQ\n";
    }

    private function ensureExtGState(float $alpha): string
    {
        $key = 'gs_' . sprintf('%.2f', $alpha);

        if (! isset($this->extGStates[$key])) {
            $objNum = $this->allocateObject();
            $ref = '/GS' . (++$this->gsCounter);
            $this->objects[$objNum] = new PdfObject($objNum, sprintf(
                '<< /Type /ExtGState /ca %.2f /CA %.2f >>',
                $alpha,
                $alpha,
            ));
            $this->extGStates[$key] = ['obj' => $objNum, 'ref' => $ref];
        }

        return $this->extGStates[$key]['ref'];
    }

    /**
     * Registers a named internal destination at the current cursor
     * position. `$y` (PDF bottom-left coordinate) overrides the
     * default "current baseline plus one line" target — pass the top
     * of the element you want the viewport to scroll to.
     */
    public function registerAnchor(string $name, ?float $y = null): void
    {
        if ($name === '') {
            return;
        }

        $this->anchors[$name] = [
            'page' => count($this->pageObjects),
            'y'    => $y ?? min($this->pageHeight, $this->cursorY + 14.0),
        ];
    }

    /**
     * Appends an entry to the document outline (the "bookmarks" panel
     * of PDF viewers). Entries must be added in reading order; the
     * hierarchy is rebuilt from `$level` (1 = top) at output() time.
     */
    public function addOutlineEntry(int $level, string $title, ?float $y = null): void
    {
        if (trim($title) === '') {
            return;
        }

        $this->outlineEntries[] = [
            'level' => max(1, $level),
            'title' => $title,
            'page'  => count($this->pageObjects),
            'y'     => $y ?? min($this->pageHeight, $this->cursorY + 14.0),
        ];
    }

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

    public function setBeforeFlushPage(?\Closure $callback): void
    {
        $this->beforeFlushPage = $callback;
    }

    public function setReservedBottom(float $height): void
    {
        $this->reservedBottom = max(0.0, $height);
    }

    public function getReservedBottom(): float
    {
        return $this->reservedBottom;
    }

    /**
     * Configure multi-column body flow. Call after {@see setPageGeometry()}.
     */
    public function setColumns(int $count, float $gap = 18.0): void
    {
        $this->columnCount = max(1, $count);
        $this->columnGap = max(0.0, $gap);
        $this->currentColumn = 0;
        $this->columnTopY = $this->pageHeight - $this->marginTop;
        $this->cursorX = $this->getColumnOriginX();
    }

    public function getColumnCount(): int
    {
        return $this->columnCount;
    }

    public function getColumnWidth(): float
    {
        if ($this->columnCount <= 1) {
            return $this->getContentWidth();
        }

        $gaps = ($this->columnCount - 1) * $this->columnGap;

        return max(0.0, ($this->getContentWidth() - $gaps) / $this->columnCount);
    }

    public function getColumnOriginX(): float
    {
        return $this->marginLeft + ($this->currentColumn * ($this->getColumnWidth() + $this->columnGap));
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
        $this->currentColumn = 0;
        $this->columnTopY = $this->pageHeight - $this->marginTop;
        $this->cursorX = $this->getColumnOriginX();
        $this->cursorY = $this->columnTopY;
        $this->reservedBottom = 0.0;

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
        $this->currentColumn = 0;
        $this->columnTopY = $this->cursorY;
        $this->reservedBottom = 0.0;
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
        return $this->cursorY - $requiredHeight < ($this->marginBottom + $this->reservedBottom);
    }

    /**
     * Advance to the next column, or start a new page when the last
     * column of the current page is full.
     */
    public function advanceColumnOrPage(): void
    {
        if ($this->columnCount > 1 && $this->currentColumn < $this->columnCount - 1) {
            $this->currentColumn++;
            $this->cursorX = $this->getColumnOriginX();
            $this->cursorY = $this->columnTopY;

            return;
        }

        $this->newPage();
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

        if ($info === false) {
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
        $this->currentPageContent .= sprintf("%s Tj\n", $this->showTextOperand($text, $fontName));
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
            $maxWidth = $this->getColumnWidth();
        }

        $bodyIndent = 0.0;
        if ($x > 0) {
            $bodyIndent = max(0.0, $x - $this->getColumnOriginX());
            $this->cursorX = $this->getColumnOriginX() + $bodyIndent;
        } else {
            $this->cursorX = $this->getColumnOriginX();
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
                $this->advanceColumnOrPage();
                $startX = $this->getColumnOriginX() + $bodyIndent;
                $this->cursorX = $startX;
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
        $embedded = $this->embeddedFonts[$fontName] ?? null;

        if ($embedded !== null) {
            // Mesurer les formes réellement dessinées : une lettre arabe
            // médiane n'a pas la chasse de sa forme isolée. Le
            // réordonnancement, lui, ne change pas la largeur totale.
            $text = ArabicShaper::shape($text);

            $units = 0;
            $glyphCount = 0;

            foreach ($embedded->codepoints($text) as $codepoint) {
                $units += $embedded->glyphWidth($embedded->glyphForCodepoint($codepoint));
                $glyphCount++;
            }

            return $units * $fontSize / 1000.0 + max(0, $glyphCount - 1) * $letterSpacing;
        }

        $fontTable = Core14Widths::FONTS[$fontName] ?? null;

        if ($fontTable === null) {
            $avgWidth = self::CHAR_WIDTHS[$fontName] ?? 550;
            $glyphCount = mb_strlen($text);
            $base = $glyphCount * $avgWidth * $fontSize / 1000;

            return $base + max(0, $glyphCount - 1) * $letterSpacing;
        }

        $widths   = $fontTable['widths'];
        $winAnsi  = mb_convert_encoding($text, 'Windows-1252', 'UTF-8');
        // mb_convert_encoding can fall back to PHP's substitute character
        // (often '?', code 0x3F) for any UTF-8 codepoint not representable
        // in cp1252; we just measure that substitute, which is exactly
        // what we'll be drawing anyway.
        $units    = 0;
        $len      = strlen($winAnsi);

        for ($i = 0; $i < $len; $i++) {
            $code = ord($winAnsi[$i]);
            $units += $widths[$code];
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
        $embedded = $this->embeddedFonts[$fontName] ?? null;

        if ($embedded !== null) {
            return [
                'ascender'  => $embedded->getAscender(),
                'descender' => $embedded->getDescender(),
                'capHeight' => $embedded->getCapHeight(),
            ];
        }

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
        $this->currentPageContent .= sprintf("%s Tj\n", $this->showTextOperand($text, $fontName));
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

        $visualWidth = ($extraTw > 0.0 || $extraTc > 0.0) ? $maxWidth : $lineWidth;
        $metrics = $this->getFontMetrics($fontName);

        if ($this->decoHighlight !== null && trim($line) !== '') {
            $this->drawRect(
                $drawX,
                $baselineY + $metrics['descender'] * $fontSize / 1000.0,
                $visualWidth,
                ($metrics['ascender'] - $metrics['descender']) * $fontSize / 1000.0,
                $this->decoHighlight,
            );
        }

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
        $this->currentPageContent .= sprintf("%s Tj\n", $this->showTextOperand($line, $fontName));
        if ($extraTw > 0.0) {
            // Always reset Tw so subsequent text isn't accidentally justified.
            $this->currentPageContent .= "0 Tw\n";
        }
        if (abs($totalTc) > 1e-4) {
            $this->currentPageContent .= "0 Tc\n";
        }
        $this->currentPageContent .= "ET\n";

        if (($this->decoUnderline || $this->decoStrikethrough) && trim($line) !== '') {
            $thickness = max(0.4, $fontSize * 0.055);

            if ($this->decoUnderline) {
                $y = $baselineY - $fontSize * 0.11;
                $this->drawColoredLine($drawX, $y, $drawX + $visualWidth, $y, $thickness, $r, $g, $b);
            }
            if ($this->decoStrikethrough) {
                $y = $baselineY + ($metrics['capHeight'] * $fontSize / 1000.0) / 2.0;
                $this->drawColoredLine($drawX, $y, $drawX + $visualWidth, $y, $thickness, $r, $g, $b);
            }
        }

        // Link scope open? Record a clickable rectangle covering this
        // line on the CURRENT page (annotations follow automatic page
        // breaks for free because they're recorded per in-flight page).
        if ($this->activeLink !== null && trim($line) !== '') {
            $this->currentPageAnnotations[] = $this->activeLink + ['rect' => [
                $drawX,
                $baselineY + $metrics['descender'] * $fontSize / 1000.0,
                $drawX + $visualWidth,
                $baselineY + $metrics['ascender'] * $fontSize / 1000.0,
            ]];
        }
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

        // For the first emitted line, a possibly tighter budget applies
        // (text-indent shrinks the first line's available width).
        $budgetFor = static fn (int $emitted): float => ($emitted === 0 && $firstLineMaxWidth !== null)
            ? $firstLineMaxWidth
            : $maxWidth;

        foreach ($words as $word) {
            $testLine  = $currentLine === '' ? $word : $currentLine . ' ' . $word;
            $testWidth = $this->measureTextWidth($testLine, $fontName, $fontSize, $letterSpacing);
            $budget    = $budgetFor(count($lines));

            if ($testWidth <= $budget) {
                $currentLine = $testLine;

                continue;
            }

            if ($currentLine !== '') {
                $lines[] = $currentLine;
                $currentLine = '';
                $budget = $budgetFor(count($lines));
            }

            if ($this->measureTextWidth($word, $fontName, $fontSize, $letterSpacing) <= $budget) {
                $currentLine = $word;

                continue;
            }

            // Le japonais, le chinois et le thaï ne séparent pas les mots
            // par des espaces : tout le paragraphe arrive ici en un seul
            // « mot ». Sans coupure interne il tenait sur une ligne unique
            // qui débordait de la page. Sert aussi aux URL interminables.
            $pieces = $this->breakOversizedWord(
                $word,
                $fontName,
                $fontSize,
                $letterSpacing,
                $budget,
                $maxWidth,
            );

            $currentLine = (string) array_pop($pieces);

            foreach ($pieces as $piece) {
                $lines[] = $piece;
            }
        }

        if ($currentLine !== '') {
            $lines[] = $currentLine;
        }

        return $lines ?: [''];
    }

    /**
     * Découpe un mot plus large que la ligne, sur des limites de groupes
     * de graphèmes — une voyelle thaïe ou une matra devanagari doit rester
     * attachée à sa consonne.
     *
     * @return string[] jamais vide ; seul le dernier élément est incomplet
     */
    private function breakOversizedWord(
        string $word,
        string $fontName,
        float $fontSize,
        float $letterSpacing,
        float $firstBudget,
        float $restBudget,
    ): array {
        preg_match_all('/\X/u', $word, $matches);

        $clusters = $matches[0] ?: [$word];
        $pieces = [];
        $piece = '';

        foreach ($clusters as $cluster) {
            $candidate = $piece . $cluster;
            $budget = $pieces === [] ? $firstBudget : $restBudget;

            // Le test sur $piece garantit au moins un groupe par ligne,
            // sinon un caractère plus large que la ligne bouclerait.
            if ($piece !== ''
                && $this->measureTextWidth($candidate, $fontName, $fontSize, $letterSpacing) > $budget) {
                $pieces[] = $piece;
                $piece = $cluster;

                continue;
            }

            $piece = $candidate;
        }

        $pieces[] = $piece;

        return $pieces;
    }

    /* -------------------------------------------------------------
     | Output
     |------------------------------------------------------------- */

    public function output(): string
    {
        $this->flushPage();
        $this->flushEmbeddedFonts();
        $this->bootstrapSecurity();

        $pageCount = count($this->pageObjects);

        // Page object numbers are allocated up-front so internal link
        // destinations and outline entries can reference pages that
        // come AFTER the element that points at them.
        $pageObjNumbers = [];
        foreach ($this->pageObjects as $i => $contentObj) {
            $pageObjNumbers[] = $this->allocateObject();
        }

        foreach ($this->pageObjects as $i => $contentObj) {
            $pageObj = $pageObjNumbers[$i];

            $resourceDict = $this->buildResourceDict($i);

            $geometry = $this->pageGeometries[$i] ?? [
                'width'  => $this->pageWidth,
                'height' => $this->pageHeight,
            ];

            $annots = $this->buildPageAnnotations($i, $pageObjNumbers);

            $this->objects[$pageObj] = new PdfObject($pageObj, sprintf(
                "<< /Type /Page /Parent %d 0 R /MediaBox [0 0 %.2f %.2f] /Contents %d 0 R /Resources %s%s >>",
                $this->pagesObj,
                $geometry['width'],
                $geometry['height'],
                $contentObj,
                $resourceDict,
                $annots !== '' ? " /Annots {$annots}" : ''
            ));
        }

        $kids = implode(' ', array_map(fn (int $n) => "{$n} 0 R", $pageObjNumbers));
        $this->objects[$this->pagesObj] = new PdfObject(
            $this->pagesObj,
            "<< /Type /Pages /Kids [{$kids}] /Count {$pageCount} >>"
        );

        $outlinesObj = $this->buildOutlines($pageObjNumbers);

        $this->objects[$this->catalogObj] = new PdfObject(
            $this->catalogObj,
            $outlinesObj !== null
                ? "<< /Type /Catalog /Pages {$this->pagesObj} 0 R /Outlines {$outlinesObj} 0 R /PageMode /UseOutlines >>"
                : "<< /Type /Catalog /Pages {$this->pagesObj} 0 R >>"
        );

        return $this->buildPdf($this->buildInfoObject(), $this->buildEncryptObject());
    }

    /**
     * Builds the /Annots array for a page: one Link annotation object
     * per rectangle recorded while a link scope was open. Internal
     * anchors are resolved by name here — an annotation pointing at an
     * anchor that was never registered is silently dropped (the text
     * itself was still drawn, it just isn't clickable).
     *
     * @param int[] $pageObjNumbers
     * @return string PDF array literal (e.g. "[12 0 R 13 0 R]") or ''
     */
    private function buildPageAnnotations(int $pageIndex, array $pageObjNumbers): string
    {
        $records = $this->pageAnnotations[$pageIndex] ?? [];

        if ($records === []) {
            return '';
        }

        $refs = [];

        foreach ($records as $record) {
            $objNum = $this->allocateObject();
            [$x1, $y1, $x2, $y2] = $record['rect'];
            $rect = sprintf('[%.2f %.2f %.2f %.2f]', $x1, $y1, $x2, $y2);

            if (isset($record['uri'])) {
                $target = sprintf(
                    '/A << /S /URI /URI %s >>',
                    $this->formatLiteralString($record['uri'], $objNum)
                );
            } else {
                $anchor = $this->anchors[$record['anchor'] ?? ''] ?? null;
                if ($anchor === null || ! isset($pageObjNumbers[$anchor['page']])) {
                    continue;
                }
                $target = sprintf(
                    '/Dest [%d 0 R /XYZ null %.2f null]',
                    $pageObjNumbers[$anchor['page']],
                    $anchor['y']
                );
            }

            $this->objects[$objNum] = new PdfObject($objNum, sprintf(
                "<< /Type /Annot /Subtype /Link /Rect %s /Border [0 0 0] %s >>",
                $rect,
                $target
            ));
            $refs[] = "{$objNum} 0 R";
        }

        return $refs === [] ? '' : '[' . implode(' ', $refs) . ']';
    }

    /**
     * Builds the document outline tree (the "bookmarks" panel) from
     * the flat, reading-ordered entry list. Returns the root Outlines
     * object number, or null when no entry was registered.
     *
     * Levels deeper than `parent level + 1` are clamped (an H4 right
     * after an H2 becomes a direct child of the H2) so the tree stays
     * well-formed whatever the heading sequence.
     *
     * @param int[] $pageObjNumbers
     */
    private function buildOutlines(array $pageObjNumbers): ?int
    {
        if ($this->outlineEntries === []) {
            return null;
        }

        $rootObj = $this->allocateObject();
        $items = $this->materializeOutlineItems();
        $topLevel = [];

        foreach ($items as $idx => $item) {
            if ($item['parent'] === null) {
                $topLevel[] = $idx;
            }

            $pageObj = $pageObjNumbers[$item['page']] ?? $pageObjNumbers[0];

            $dict = sprintf(
                '<< /Title %s /Parent %d 0 R /Dest [%d 0 R /XYZ null %.2f null]',
                $this->formatTextString($item['title'], $item['obj']),
                $item['parent'] !== null ? $items[$item['parent']]['obj'] : $rootObj,
                $pageObj,
                $item['y']
            );

            if ($item['prev'] !== null) {
                $dict .= sprintf(' /Prev %d 0 R', $items[$item['prev']]['obj']);
            }
            if ($item['next'] !== null) {
                $dict .= sprintf(' /Next %d 0 R', $items[$item['next']]['obj']);
            }
            if ($item['children'] !== []) {
                $firstChildIdx = $item['children'][0];
                $lastChildIdx = $item['children'][count($item['children']) - 1];
                $first = $items[$firstChildIdx]['obj'];
                $last  = $items[$lastChildIdx]['obj'];
                // Negative count = children are collapsed by default.
                $dict .= sprintf(' /First %d 0 R /Last %d 0 R /Count %d', $first, $last, count($item['children']));
            }

            $dict .= ' >>';

            $this->objects[$item['obj']] = new PdfObject($item['obj'], $dict);
        }

        $firstTop = $items[$topLevel[0]]['obj'];
        $lastTop  = $items[$topLevel[count($topLevel) - 1]]['obj'];

        $this->objects[$rootObj] = new PdfObject($rootObj, sprintf(
            '<< /Type /Outlines /First %d 0 R /Last %d 0 R /Count %d >>',
            $firstTop,
            $lastTop,
            count($topLevel)
        ));

        return $rootObj;
    }

    /**
     * @return list<array{obj: int, title: string, page: int, y: float, parent: int|null, children: list<int>, prev: int|null, next: int|null}>
     */
    private function materializeOutlineItems(): array
    {
        /** @var list<int> $objs */
        $objs = [];
        /** @var list<string> $titles */
        $titles = [];
        /** @var list<int> $pages */
        $pages = [];
        /** @var list<float> $ys */
        $ys = [];
        /** @var list<int|null> $parents */
        $parents = [];
        /** @var list<list<int>> $children */
        $children = [];
        /** @var list<int|null> $prevs */
        $prevs = [];
        /** @var list<int|null> $nexts */
        $nexts = [];
        /** @var array<int, int> $stack */
        $stack = [];

        foreach ($this->outlineEntries as $entry) {
            $idx = count($objs);
            $depth = min($entry['level'], count($stack) + 1);
            $parentIdx = $depth > 1 ? ($stack[$depth - 1] ?? null) : null;

            $objs[] = $this->allocateObject();
            $titles[] = $entry['title'];
            $pages[] = $entry['page'];
            $ys[] = $entry['y'];
            $parents[] = $parentIdx;
            $children[] = [];
            $prevs[] = null;
            $nexts[] = null;

            if ($parentIdx !== null) {
                $siblings = $children[$parentIdx];
                if ($siblings !== []) {
                    $prevIdx = $siblings[array_key_last($siblings)];
                    $prevs[$idx] = $prevIdx;
                    $nexts[$prevIdx] = $idx;
                }
                $children[$parentIdx][] = $idx;
            } else {
                for ($j = $idx - 1; $j >= 0; $j--) {
                    if ($parents[$j] === null) {
                        $prevs[$idx] = $j;
                        $nexts[$j] = $idx;
                        break;
                    }
                }
            }

            $stack = array_slice($stack, 0, $depth - 1, true);
            $stack[$depth] = $idx;
        }

        $items = [];
        foreach ($objs as $idx => $obj) {
            $items[] = [
                'obj'      => $obj,
                'title'    => $titles[$idx],
                'page'     => $pages[$idx],
                'y'        => $ys[$idx],
                'parent'   => $parents[$idx],
                'children' => $children[$idx],
                'prev'     => $prevs[$idx],
                'next'     => $nexts[$idx],
            ];
        }

        return $items;
    }

    /**
     * Builds the /Info dictionary from the typed metadata setters.
     * Empty fields are omitted so a minimal document keeps a minimal
     * Info dict.
     */
    private function buildInfoObject(): int
    {
        $entries = [];
        $infoObj = $this->allocateObject();

        if ($this->title !== '') {
            $entries[] = sprintf('/Title %s', $this->formatTextString($this->title, $infoObj));
        }
        if ($this->author !== '') {
            $entries[] = sprintf('/Author %s', $this->formatTextString($this->author, $infoObj));
        }
        if ($this->subject !== '') {
            $entries[] = sprintf('/Subject %s', $this->formatTextString($this->subject, $infoObj));
        }
        if ($this->keywords !== '') {
            $entries[] = sprintf('/Keywords %s', $this->formatTextString($this->keywords, $infoObj));
        }

        $entries[] = sprintf('/Creator %s', $this->formatTextString($this->creator, $infoObj));
        $entries[] = sprintf('/Producer %s', $this->formatTextString('Paperdoc PHP Library', $infoObj));

        $creation = $this->creationDate?->format('YmdHis') ?? date('YmdHis');
        $entries[] = sprintf('/CreationDate %s', $this->formatLiteralString("D:{$creation}", $infoObj));

        if ($this->modificationDate !== null) {
            $entries[] = sprintf('/ModDate %s', $this->formatLiteralString('D:' . $this->modificationDate->format('YmdHis'), $infoObj));
        }

        $this->objects[$infoObj] = new PdfObject($infoObj, '<< ' . implode(' ', $entries) . ' >>');

        return $infoObj;
    }

    /**
     * Escapes a byte string for a PDF literal string WITHOUT charset
     * conversion — used for URIs, which must keep their original bytes
     * (they are typically ASCII / percent-encoded already).
     */
    private function escapePdfLiteral(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private function bootstrapSecurity(): void
    {
        if ($this->protection === null || $this->security !== null) {
            return;
        }

        $seed = $this->title . '|' . $this->creator . '|' . microtime(true) . '|' . random_int(0, PHP_INT_MAX);
        $this->fileId = md5($seed, true);
        $this->security = new PdfStandardSecurity($this->protection, $this->fileId);
    }

    private function buildEncryptObject(): ?int
    {
        if ($this->security === null) {
            return null;
        }

        $encryptObj = $this->allocateObject();
        $this->objects[$encryptObj] = $this->security->buildEncryptDictionary($encryptObj);

        return $encryptObj;
    }

    private function encryptBytesIfNeeded(string $bytes, int $objectNumber): string
    {
        return $this->security?->encryptString($bytes, $objectNumber) ?? $bytes;
    }

    private function formatTextString(string $text, int $objectNumber): string
    {
        return $this->formatLiteralString($this->escapePdfString($text), $objectNumber, true);
    }

    private function formatLiteralString(string $text, int $objectNumber, bool $alreadyEscaped = false): string
    {
        if ($this->security === null) {
            return '(' . ($alreadyEscaped ? $text : $this->escapePdfLiteral($text)) . ')';
        }

        $bytes = $alreadyEscaped ? $text : $this->escapePdfLiteral($text);

        return '<' . bin2hex($this->security->encryptString($bytes, $objectNumber)) . '>';
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

        if ($this->beforeFlushPage !== null) {
            ($this->beforeFlushPage)();
        }

        $streamObj = $this->allocateObject();

        if ($this->compressStreams) {
            $data = gzcompress($this->currentPageContent, 6);
            if ($data === false) {
                throw new \RuntimeException('Failed to compress PDF page stream.');
            }
            $data = $this->encryptBytesIfNeeded($data, $streamObj);
            $length = strlen($data);
            $body = "<< /Length {$length} /Filter /FlateDecode >>\nstream\n{$data}\nendstream";
        } else {
            $data = $this->encryptBytesIfNeeded($this->currentPageContent, $streamObj);
            $length = strlen($data);
            $body = "<< /Length {$length} >>\nstream\n{$data}endstream";
        }

        $this->objects[$streamObj] = new PdfObject($streamObj, $body);

        $this->pageObjects[] = $streamObj;
        $this->pageAnnotations[] = $this->currentPageAnnotations;
        $this->currentPageAnnotations = [];
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

        $dict = "/Font {$fontDict}";

        if (! empty($imageEntries)) {
            $dict .= ' /XObject << ' . implode(' ', $imageEntries) . ' >>';
        }

        if ($this->extGStates !== []) {
            $gsEntries = array_map(
                static fn (array $gs): string => "{$gs['ref']} {$gs['obj']} 0 R",
                array_values($this->extGStates),
            );
            $dict .= ' /ExtGState << ' . implode(' ', $gsEntries) . ' >>';
        }

        return "<< {$dict} >>";
    }

    /**
     * Rend une police TrueType disponible sous $alias. Le texte rendu avec
     * cet alias est encodé en identifiants de glyphes, ce qui lève la
     * limite du latin-1 des 14 polices standard.
     *
     * La bibliothèque ne livre aucune donnée de police : c'est à l'appelant
     * de fournir le fichier, et de s'assurer que sa licence en autorise
     * l'incorporation dans un document.
     *
     * @throws \RuntimeException si le fichier n'est pas un TrueType exploitable
     */
    public function registerTrueTypeFont(string $alias, string $filename, int $fontIndex = 0): void
    {
        $this->embeddedFonts[$alias] = TrueTypeFont::fromFile($filename, $fontIndex);
    }

    public function hasEmbeddedFont(string $alias): bool
    {
        return isset($this->embeddedFonts[$alias]);
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

        if (isset($this->embeddedFonts[$fontName])) {
            // Le contenu n'est écrit qu'à output() : la CMap ToUnicode a
            // besoin de connaître tous les points de code employés, or on
            // est ici au premier usage, avant que le texte ne soit émis.
            $this->pendingEmbeddedFonts[$fontName] = $objNum;

            return $ref;
        }

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
     * Graphe d'objets d'une police embarquée : Type0 (Identity-H) →
     * CIDFontType2 → FontDescriptor → FontFile2, plus un CMap ToUnicode
     * pour que le texte reste sélectionnable et extractible.
     */
    private function flushEmbeddedFonts(): void
    {
        foreach ($this->pendingEmbeddedFonts as $alias => $objNum) {
            $this->writeEmbeddedFontObjects($objNum, $this->embeddedFonts[$alias], $alias);
        }

        $this->pendingEmbeddedFonts = [];
    }

    private function writeEmbeddedFontObjects(int $type0Obj, TrueTypeFont $font, string $alias): void
    {
        $cidObj        = $this->allocateObject();
        $descriptorObj = $this->allocateObject();
        $fileObj       = $this->allocateObject();
        $toUnicodeObj  = $this->allocateObject();

        $name = $font->getPostScriptName();
        [$x0, $y0, $x1, $y1] = $font->getBoundingBox();

        $this->objects[$type0Obj] = new PdfObject($type0Obj, sprintf(
            '<< /Type /Font /Subtype /Type0 /BaseFont /%s /Encoding /Identity-H '
            . '/DescendantFonts [%d 0 R] /ToUnicode %d 0 R >>',
            $name,
            $cidObj,
            $toUnicodeObj,
        ));

        // Réduire la police aux seuls glyphes employés : embarquer le
        // fichier entier coûte 423 Ko en latin et plus de 4 Mo en CJK,
        // quelle que soit la quantité de texte réellement écrite.
        $subsetter = new TrueTypeSubset($font);
        $glyphMap = $this->embeddedFontGlyphs[$alias] ?? [0 => 0];
        $isCff = $font->isCff();

        if (! $isCff && $subsetter->isSupported()) {
            $glyphMap = $subsetter->closeOverComponents($glyphMap);
            $raw = $subsetter->build($glyphMap);
        } else {
            // Contours CFF : les découper demanderait de réécrire le CFF
            // lui-même. On embarque le fichier tel quel, identifiants de
            // glyphes d'origine compris.
            $raw = $font->getData();
        }

        // Un CFF s'embarque en CIDFontType0 : le PDF prend alors les CID
        // pour des indices de glyphes, ce que fait déjà Identity-H.
        $this->objects[$cidObj] = new PdfObject($cidObj, sprintf(
            '<< /Type /Font /Subtype /%s /BaseFont /%s '
            . '/CIDSystemInfo << /Registry (Adobe) /Ordering (Identity) /Supplement 0 >> '
            . '/FontDescriptor %d 0 R /DW 1000 /W %s%s >>',
            $isCff ? 'CIDFontType0' : 'CIDFontType2',
            $name,
            $descriptorObj,
            $this->buildCidWidthArray($font, $glyphMap),
            $isCff ? '' : ' /CIDToGIDMap /Identity',
        ));

        $this->objects[$descriptorObj] = new PdfObject($descriptorObj, sprintf(
            '<< /Type /FontDescriptor /FontName /%s /Flags %d /FontBBox [%d %d %d %d] '
            . '/ItalicAngle %d /Ascent %d /Descent %d /CapHeight %d /StemV 80 /%s %d 0 R >>',
            $name,
            $font->getFlags(),
            $x0,
            $y0,
            $x1,
            $y1,
            $font->getItalicAngle(),
            $font->getAscender(),
            $font->getDescender(),
            $font->getCapHeight(),
            $isCff ? 'FontFile3' : 'FontFile2',
            $fileObj,
        ));

        $this->objects[$fileObj] = new PdfObject($fileObj, $isCff
            ? sprintf(
                "<< /Length %d /Subtype /OpenType >>\nstream\n%s\nendstream",
                strlen($raw),
                $raw,
            )
            : sprintf(
                "<< /Length %d /Length1 %d >>\nstream\n%s\nendstream",
                strlen($raw),
                strlen($raw),
                $raw,
            ));

        $cmap = $this->buildToUnicodeCMap($font, $alias);

        $this->objects[$toUnicodeObj] = new PdfObject($toUnicodeObj, sprintf(
            "<< /Length %d >>\nstream\n%s\nendstream",
            strlen($cmap),
            $cmap,
        ));
    }

    /**
     * Tableau /W : les glyphes dont la chasse diffère de /DW 1000. Écrit
     * en plages consécutives pour ne pas gonfler le fichier.
     *
     * @param array<int, int> $glyphMap ancien identifiant → nouveau
     */
    private function buildCidWidthArray(TrueTypeFont $font, array $glyphMap): string
    {
        // Les identifiants du sous-ensemble, dans l'ordre.
        $byNewGid = array_flip($glyphMap);
        ksort($byNewGid);

        // Seuls les glyphes dont la chasse diffère du /DW ont besoin d'une
        // entrée.
        $widths = [];

        foreach ($byNewGid as $gid => $oldGid) {
            $width = $font->glyphWidth($oldGid);

            if ($width !== 1000) {
                $widths[$gid] = $width;
            }
        }

        $entries = [];
        $runStart = null;
        $expected = null;
        $run = [];

        foreach ($widths as $gid => $width) {
            // Une plage « C [w1 w2 …] » attribue ses largeurs à des CID
            // consécutifs. Ils le sont dans un sous-ensemble, mais épars
            // pour une police CFF embarquée telle quelle : rompre la plage
            // évite d'attribuer les largeurs aux mauvais glyphes.
            if ($runStart !== null && $gid !== $expected) {
                $entries[] = $runStart . ' [' . implode(' ', $run) . ']';
                $runStart = null;
                $run = [];
            }

            $runStart ??= $gid;
            $run[] = $width;
            $expected = $gid + 1;
        }

        if ($runStart !== null) {
            $entries[] = $runStart . ' [' . implode(' ', $run) . ']';
        }

        return '[' . implode(' ', $entries) . ']';
    }

    /**
     * CMap ToUnicode : sans elle le texte du PDF n'est qu'une suite
     * d'identifiants de glyphes, illisible pour un copier-coller comme
     * pour un extracteur.
     */
    private function buildToUnicodeCMap(TrueTypeFont $font, string $alias): string
    {
        $pairs = [];

        foreach ($this->embeddedFontCodepoints[$alias] ?? [] as $codepoint => $gid) {
            if ($codepoint > 0xFFFF) {
                // Hors BMP : paire de substitution UTF-16BE
                $adjusted = $codepoint - 0x10000;
                $high = 0xD800 + ($adjusted >> 10);
                $low  = 0xDC00 + ($adjusted & 0x3FF);
                $value = sprintf('%04X%04X', $high, $low);
            } else {
                $value = sprintf('%04X', $codepoint);
            }

            $pairs[] = sprintf('<%04X> <%s>', $gid, $value);
        }

        $body = '';

        // bfchar n'accepte que 100 entrées par bloc (PDF 1.7 §9.10.3).
        foreach (array_chunk($pairs, 100) as $chunk) {
            $body .= count($chunk) . " beginbfchar\n" . implode("\n", $chunk) . "\nendbfchar\n";
        }

        return "/CIDInit /ProcSet findresource begin\n"
            . "12 dict begin\nbegincmap\n"
            . "/CIDSystemInfo << /Registry (Adobe) /Ordering (UCS) /Supplement 0 >> def\n"
            . "/CMapName /Adobe-Identity-UCS def\n/CMapType 2 def\n"
            . "1 begincodespacerange\n<0000> <FFFF>\nendcodespacerange\n"
            . $body
            . "endcmap\nCMapName currentdict /CMap defineresource pop\nend\nend";
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
        $iType  = (int) $info[2];

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
        $jpeg = $this->encryptBytesIfNeeded($jpeg, $objNum);
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

    private function buildPdf(int $infoObj, ?int $encryptObj): string
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
        $trailer = sprintf(
            "<< /Size %d /Root %d 0 R /Info %d 0 R",
            $objectCount,
            $this->catalogObj,
            $infoObj
        );
        if ($encryptObj !== null && $this->fileId !== null) {
            $trailer .= sprintf(' /Encrypt %d 0 R /ID [<%s> <%s>]', $encryptObj, bin2hex($this->fileId), bin2hex($this->fileId));
        }
        $pdf .= $trailer . " >>\n";
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
    /**
     * Opérande de l'opérateur Tj. Une police standard reçoit une chaîne
     * littérale WinAnsi ; une police embarquée reçoit une chaîne
     * hexadécimale d'identifiants de glyphes, seule façon d'adresser un
     * caractère hors latin-1.
     */
    private function showTextOperand(string $text, string $fontName): string
    {
        $text = $this->prepareForDisplay($text);

        $font = $this->embeddedFonts[$fontName] ?? null;

        if ($font === null) {
            return '(' . $this->escapePdfString($text) . ')';
        }

        $hex = '';

        foreach ($font->codepoints($text) as $codepoint) {
            $newGid = $this->mapGlyph($fontName, $font->glyphForCodepoint($codepoint));
            $this->embeddedFontCodepoints[$fontName][$codepoint] = $newGid;
            $hex .= sprintf('%04X', $newGid);
        }

        return '<' . $hex . '>';
    }

    /**
     * Identifiant d'un glyphe dans le sous-ensemble, attribué à la volée.
     * .notdef occupe toujours la place 0, comme l'exige le format.
     */
    private function mapGlyph(string $alias, int $oldGid): int
    {
        // Une police CFF s'embarque telle quelle : ses identifiants doivent
        // rester ceux d'origine, sans quoi le CFF ne s'y retrouve plus.
        if (! ($this->embeddedFonts[$alias] ?? null)?->isSubsettable()) {
            $this->embeddedFontGlyphs[$alias][$oldGid] = $oldGid;

            return $oldGid;
        }

        if (! isset($this->embeddedFontGlyphs[$alias])) {
            $this->embeddedFontGlyphs[$alias] = [0 => 0];
        }

        return $this->embeddedFontGlyphs[$alias][$oldGid]
            ??= count($this->embeddedFontGlyphs[$alias]);
    }

    /**
     * Passe du texte en ordre logique à l'ordre visuel.
     *
     * HTML et DOCX déclarent le sens et laissent le navigateur ou Word
     * faire ce travail. PDF pose les glyphes exactement là où on le lui
     * dit : sans cette étape, une phrase arabe ou hébraïque sort à
     * l'envers, et les lettres arabes restent détachées.
     *
     * Sans caractère droite-à-gauche, les deux passes rendent la chaîne
     * telle quelle.
     */
    private function prepareForDisplay(string $text): string
    {
        return Bidi::reorder(ArabicShaper::shape($text));
    }

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
