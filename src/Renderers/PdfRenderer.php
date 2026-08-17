<?php

declare(strict_types=1);

namespace Paperdoc\Renderers;

use Paperdoc\Contracts\{DocumentElementInterface, DocumentInterface};
use Paperdoc\Document\{
    Blockquote,
    Bookmark,
    CodeBlock,
    Document,
    Heading,
    HorizontalRule,
    Image,
    ListBlock,
    ListItem,
    Metadata,
    PageBreak,
    Paragraph,
    Section,
    Table,
    TextRun,
    TextZone,
};
use Paperdoc\Document\Style\{PageSetup, RunningElement, TextStyle, Watermark};
use Paperdoc\Document\TableOfContents;
use Paperdoc\Enum\{Alignment, VerticalAlignment};
use Paperdoc\Support\Cast;
use Paperdoc\Support\Pdf\PdfEngine;
use Paperdoc\Support\TocResolver;

/**
 * Renderer PDF natif sans aucune dépendance tierce.
 *
 * Utilise le PdfEngine interne pour générer des fichiers PDF 1.4 valides
 * incluant les éléments du modèle core v0.4.0 : titres typés, listes
 * ordonnées/à puces (nested), blockquotes, code blocks, tables et images.
 * Les hyperliens sont rendus avec un style visuel (souligné bleu) ;
 * les annotations cliquables seront ajoutées dans une itération future.
 */
class PdfRenderer extends AbstractRenderer
{
    private PdfEngine $engine;

    /** @var array<string, array{string, int}> alias => [chemin, index dans la collection] */
    private array $embeddedFonts = [];

    /**
     * Footnotes collected per physical page number (1-indexed).
     *
     * @var array<int, list<array{num: int, text: string}>>
     */
    private array $pageFootnotes = [];

    private int $footnoteCounter = 0;

    /**
     * Document-level header/footer fallback. The header/footer
     * actually rendered on each page is the section's
     * resolveHeader/Footer() applied to these (so a section's
     * setHeader/setFooter/hideHeader/hideFooter overrides take
     * precedence — see Section v0.8.0).
     */
    private ?RunningElement $documentHeader = null;
    private ?RunningElement $documentFooter = null;
    private string $documentTitle = '';
    private int $totalPages = 1;
    private ?Section $currentSection = null;

    /**
     * Effective line height (in points) of the last drawn line on the
     * current page — used to compute how much extra vertical space to
     * reserve when a new paragraph uses a noticeably bigger font, so
     * the top of the new glyphs doesn't collide with the previous
     * baseline. Zero means "first block on the page, no reservation
     * needed". Reset on every newPage().
     */
    private float $lastBlockLineHeight = 0.0;

    private ?TocResolver $tocResolver = null;
    private ?Watermark $watermark = null;

    public function getFormat(): string { return 'pdf'; }

    public function render(DocumentInterface $document): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'paperdoc_pdf_');

        try {
            $this->buildPdf($document, $tmp);

            return file_get_contents($tmp) ?: '';
        } finally {
            @unlink($tmp);
        }
    }

    public function save(DocumentInterface $document, string $filename): void
    {
        $this->ensureDirectoryWritable($filename);
        $this->buildPdf($document, $filename);
    }

    /**
     * Rend une police disponible sous $alias, à employer comme famille de
     * police dans un TextStyle. Sans cela le rendu PDF est borné aux 14
     * polices standard, donc au latin-1 : cyrillique, grec, arabe, hébreu
     * et CJK y deviennent « ? ».
     *
     * Accepte .ttf, .otf (contours CFF) et .ttc — pour une collection,
     * $fontIndex désigne la police voulue.
     *
     * La bibliothèque ne livre aucune donnée de police ; l'appelant
     * fournit le fichier et répond de sa licence d'incorporation.
     */
    public function registerTrueTypeFont(string $alias, string $filename, int $fontIndex = 0): void
    {
        $this->embeddedFonts[$alias] = [$filename, $fontIndex];
    }

    /**
     * getPdfFontName() ramène toute famille inconnue à Helvetica, ce qui
     * effacerait l'alias d'une police embarquée. On garde donc la famille
     * telle quelle quand elle en désigne une.
     */
    private function resolveFontName(?TextStyle $style): string
    {
        if ($style === null) {
            return 'Helvetica';
        }

        $family = $style->getFontFamily();

        if ($family !== '' && isset($this->embeddedFonts[$family])) {
            return $family;
        }

        return $style->getPdfFontName();
    }

    private function buildPdf(DocumentInterface $document, string $filename): void
    {
        $this->engine = new PdfEngine();

        foreach ($this->embeddedFonts as $alias => [$fontPath, $fontIndex]) {
            $this->engine->registerTrueTypeFont($alias, $fontPath, $fontIndex);
        }

        $this->engine->setTitle($document->getTitle());
        $this->documentTitle = $document->getTitle();
        $this->lastBlockLineHeight = 0.0;
        $this->tocResolver = new TocResolver($document);
        $this->pageFootnotes = [];
        $this->footnoteCounter = 0;

        $author = null;
        $this->documentHeader = null;
        $this->documentFooter = null;

        $this->watermark = null;

        if ($document instanceof Document) {
            $properties = $document->getProperties();
            $author = $properties?->getAuthor();
            $this->documentHeader = $document->getHeader();
            $this->documentFooter = $document->getFooter();
            $this->watermark = $document->getWatermark();
            $this->engine->setProtection($document->getProtection());

            // Full typed metadata → PDF /Info dictionary (v1.0.0).
            if ($properties !== null) {
                $this->engine->setAuthor($properties->getAuthor());
                $this->engine->setSubject($properties->getSubject());
                $this->engine->setKeywords($properties->getKeywords());
                $this->engine->setCreationDate($properties->getCreatedAt());
                $this->engine->setModificationDate($properties->getModifiedAt());
            }
        }

        $this->engine->setCreator($author !== null && $author !== ''
            ? $author
            : Cast::asString($document->getMetadata()['creator'] ?? null, 'Paperdoc'));

        $this->totalPages = $this->countDeclaredPages($document);

        // Single source of truth for "what every brand-new physical page
        // must receive before any body content lands on it". Branched on
        // the engine so a mid-paragraph overflow page (writeWrappedText
        // → newPage), or any auto-pagination from tables / images /
        // rules, repaints background + header + footer just like the
        // explicit section break paths do. Without this hook those
        // continuation pages came out blank-chromed (bug fixed in
        // v0.8.3).
        $this->engine->setOnNewPage(function (): void {
            $this->paintPageChrome();
        });
        $this->engine->setBeforeFlushPage(function (): void {
            $this->paintPageFootnotes();
        });

        $isFirst = true;

        foreach ($document->getSections() as $section) {
            $this->currentSection = $section;

            if (! $isFirst) {
                // The hook will paint chrome for the new section's page.
                $this->engine->newPage();
            } else {
                // The very first page was created by the engine
                // constructor BEFORE we registered the hook, so paint
                // its chrome explicitly here.
                $this->paintPageChrome();
            }

            $this->writeSection($section, $document);
            $isFirst = false;
        }

        $this->currentSection = null;
        $this->engine->save($filename);
        $this->engine->setOnNewPage(null);
        $this->engine->setBeforeFlushPage(null);
    }

    /**
     * Compte les pages "déclarées" : sections + PageBreaks explicites.
     * Les sauts de page automatiques (déclenchés par needsNewPage()
     * pour des images ou tableaux qui débordent) ne sont pas comptés
     * ici — l'estimation reste suffisante pour le placeholder {pages}
     * dans la grande majorité des cas.
     */
    private function countDeclaredPages(DocumentInterface $document): int
    {
        $total = 0;

        foreach ($document->getSections() as $section) {
            $total++;
            foreach ($section->getElements() as $element) {
                if ($element instanceof PageBreak) {
                    $total++;
                }
            }
        }

        return max(1, $total);
    }

    /**
     * Re-paints every "per-page" element on the page the engine just
     * opened: geometry + background + header/footer. Called exactly
     * once per physical page — either explicitly for the very first
     * page of the document, or implicitly via the {@see PdfEngine}
     * onNewPage hook for every subsequent page (whether the break was
     * caused by an explicit `PageBreak`, a new section, or an
     * automatic overflow from a long paragraph / table / image).
     *
     * Order matters: geometry first (so width/height/margins are
     * already correct), then the background fill (so it lands at the
     * head of the content stream and is overdrawn by everything that
     * follows), then header/footer (so they sit on top of the
     * background but below the body).
     *
     * Also resets the trailing-line metric so the first paragraph of
     * the new page can compute its ascent reservation against an empty
     * baseline.
     */
    private function paintPageChrome(): void
    {
        $this->applyPageSetup($this->currentSection?->getPageSetup());

        if ($this->watermark !== null) {
            $this->engine->drawWatermarkText(
                $this->watermark->getText(),
                TextStyle::make()->setFontFamily($this->watermark->getFontFamily())->setBold()->getPdfFontName(),
                $this->watermark->getFontSize(),
                $this->watermark->getColor(),
                $this->watermark->getOpacity(),
                $this->watermark->getAngle(),
            );
        }

        $this->drawHeaderFooter();
        $this->lastBlockLineHeight = 0.0;
    }

    /**
     * Applique la configuration de page (taille, marges, fonds) à la
     * page courante du PdfEngine.
     *
     * Convention `null` (héritée — section sans `PageSetup`) : on ne
     * réinitialise NI la géométrie NI le fond. La page conserve donc
     * la géométrie courante du moteur et reste sur son fond par défaut
     * (transparent / blanc). Le header/footer, lui, est traité
     * séparément dans {@see paintPageChrome()} et reste appliqué même
     * sans `PageSetup`.
     *
     * Cette méthode est volontairement bas-niveau ; les appelants
     * devraient passer par {@see paintPageChrome()} pour bénéficier de
     * l'ordre correct (geometry → background → header/footer → body).
     */
    private function applyPageSetup(?PageSetup $setup): void
    {
        if ($setup === null) {
            return;
        }

        $this->engine->setPageGeometry(
            width:        $setup->getWidth(),
            height:       $setup->getHeight(),
            marginTop:    $setup->getPaddingTop(),
            marginRight:  $setup->getPaddingRight(),
            marginBottom: $setup->getPaddingBottom(),
            marginLeft:   $setup->getPaddingLeft(),
        );
        $this->engine->setColumns($setup->getColumnCount(), $setup->getColumnGap());

        if ($setup->getBackgroundColor() !== null) {
            $this->engine->drawPageBackgroundColor($setup->getBackgroundColor());
        }

        $bgImage = $setup->getBackgroundImage();
        if ($bgImage !== null) {
            $tmpPath = $this->resolveImagePath($bgImage);
            if ($tmpPath !== null) {
                $this->engine->drawPageBackgroundImage($tmpPath, $setup->getBackgroundSize());
                if ($this->isTempFile($tmpPath, $bgImage)) {
                    @unlink($tmpPath);
                }
            }
        }
    }

    private function writeSection(Section $section, DocumentInterface $document): void
    {
        $verticalAlignment = $section->getVerticalAlignment();

        // Top-aligned (default) — render straight to the page, no
        // measurement pass needed.
        if ($verticalAlignment === VerticalAlignment::TOP) {
            foreach ($section->getElements() as $element) {
                $this->writeBlock($element, $document, 0);
            }
            // Footnotes are painted per-page via beforeFlushPage.
            return;
        }

        // CENTER / BOTTOM — capture a "before" anchor, render the
        // section, then post-wrap that slice in a `q ... cm ... Q`
        // graphics state to vertically translate it. We only honour the
        // requested alignment when the section fits in the current page
        // (no auto-paginated overflow) — otherwise the cm transform
        // would silently shift content meant for a SECOND page.
        $beforeOffset = $this->engine->getPageContentLength();
        $beforePage   = $this->engine->getCurrentPageNumber();
        $startCursorY = $this->engine->getCursorY();

        foreach ($section->getElements() as $element) {
            $this->writeBlock($element, $document, 0);
        }

        $afterPage  = $this->engine->getCurrentPageNumber();
        if ($afterPage !== $beforePage) {
            // Section overflowed onto another page during rendering. We
            // can't safely apply CTM translation across pages, so leave
            // the content top-aligned and bail out silently.
            return;
        }

        $endCursorY    = $this->engine->getCursorY();
        $contentHeight = max(0.0, $startCursorY - $endCursorY);

        $setup            = $section->getPageSetup();
        $contentAreaTopY  = $startCursorY;
        // When the section has no PageSetup, fall back to the
        // engine's actual bottom margin (since v0.8.2). The previous
        // 40.0 fallback was incorrect for any non-default gutter.
        $contentAreaBottomY = $setup !== null
            ? $setup->getPaddingBottom()
            : $this->engine->getBottomMargin();
        $contentAreaBottomY += $this->engine->getReservedBottom();
        $available = max(0.0, $contentAreaTopY - $contentAreaBottomY);
        $slack     = $available - $contentHeight;

        if ($slack <= 0.5) {
            return; // Content already fills the area; nothing to centre.
        }

        $dy = $verticalAlignment === VerticalAlignment::CENTER
            ? -($slack / 2.0)
            : -$slack;

        if (abs($dy) < 0.5) {
            return;
        }

        $this->engine->wrapPageContentSince(
            offset: $beforeOffset,
            prefix: sprintf('1 0 0 1 0 %.2f cm', $dy),
        );
    }

    /* =============================================================
     | Header / Footer
     |============================================================= */

    /**
     * Resolves and draws the running elements for the current page,
     * taking into account any per-section override / hide flag set
     * via {@see Section::setHeader()}/{@see Section::hideFooter()} etc.
     */
    private function drawHeaderFooter(): void
    {
        $header = $this->currentSection !== null
            ? $this->currentSection->resolveHeader($this->documentHeader)
            : $this->documentHeader;

        $footer = $this->currentSection !== null
            ? $this->currentSection->resolveFooter($this->documentFooter)
            : $this->documentFooter;

        if ($header !== null) {
            $this->drawRunningElement($header, true);
        }

        if ($footer !== null) {
            $this->drawRunningElement($footer, false);
        }
    }

    private function drawRunningElement(RunningElement $element, bool $isHeader): void
    {
        $text = $element->resolve(
            $this->engine->getCurrentPageNumber(),
            $this->totalPages,
            $this->documentTitle,
        );

        if ($text === '') {
            return;
        }

        $style    = $element->getStyle();
        $fontName = $this->resolveFontName($style);
        $fontSize = $style->getFontSize();
        [$r, $g, $b] = $style->getColorRgb();

        // Drawn within the page padding area, with a small inner margin
        // so it doesn't kiss the page edge.
        $sideMargin   = 24.0;
        $verticalGap  = 12.0;
        $pageWidth    = $this->engine->getPageWidth();
        $pageHeight   = $this->engine->getPageHeight();
        $textWidth    = $this->engine->measureTextWidth($text, $fontName, $fontSize);
        $available    = max(1.0, $pageWidth - 2 * $sideMargin);

        $x = match ($element->getAlignment()) {
            Alignment::CENTER => ($pageWidth - $textWidth) / 2,
            Alignment::RIGHT  => $pageWidth - $sideMargin - $textWidth,
            default           => $sideMargin,
        };

        // Ensure x stays within page bounds (handles oversized text gracefully).
        $x = max($sideMargin, min($x, $pageWidth - $sideMargin - 1));

        // Top-left coordinate (we use the engine's top-left helper to convert).
        $yTopLeft = $isHeader
            ? $verticalGap
            : $pageHeight - $verticalGap - $element->getHeight();

        $this->engine->writeWrappedTextAt(
            text:     $text,
            fontName: $fontName,
            fontSize: $fontSize,
            x:        $x,
            yTopLeft: $yTopLeft,
            maxWidth: $available,
            r:        $r,
            g:        $g,
            b:        $b,
            lineSpacing: 1.15,
            maxHeight: $element->getHeight(),
            ellipsis: true,
        );
    }

    private function writeBlock(DocumentElementInterface $element, DocumentInterface $document, float $indent): void
    {
        match (true) {
            $element instanceof Heading        => $this->writeHeading($element, $document, $indent),
            $element instanceof Paragraph      => $this->writeParagraph($element, $document, $indent),
            $element instanceof ListBlock      => $this->writeList($element, $document, 0, $indent),
            $element instanceof Blockquote     => $this->writeBlockquote($element, $document, $indent),
            $element instanceof CodeBlock      => $this->writeCodeBlock($element, $document, $indent),
            $element instanceof Bookmark       => $this->engine->registerAnchor($element->getId()),
            $element instanceof Table          => $this->writeTable($element, $document),
            $element instanceof Image          => $this->writeImage($element),
            $element instanceof TextZone       => $this->writeTextZone($element, $document),
            $element instanceof HorizontalRule => $this->writeHorizontalRule($element),
            $element instanceof TableOfContents => $this->writeTableOfContents($element, $document),
            $element instanceof PageBreak      => $this->handlePageBreak(),
            default                            => null,
        };

        // Non-paragraph blocks (heading, image, table, list, …) reset
        // the trailing line metric — paragraphs handle the bookkeeping
        // themselves inside writeParagraph(), and the next text block
        // doesn't need ascent reservation against a non-text or
        // already-spaced block (writeHeading bakes its own clearance).
        //
        // HorizontalRule is exempt (v0.8.1): writeHorizontalRule sets
        // lastBlockLineHeight = marginBottom so a following paragraph
        // with a much larger ascender (e.g. a 28pt title after a 6pt
        // marginBottom rule) gets the right ascent reservation rather
        // than punching through the rule.
        if (! ($element instanceof Paragraph) && ! ($element instanceof HorizontalRule)) {
            $this->lastBlockLineHeight = 0.0;
        }
    }

    private function handlePageBreak(): void
    {
        // The engine's onNewPage hook (set in buildPdf) calls
        // paintPageChrome(), which already replays geometry +
        // background + header/footer AND resets lastBlockLineHeight.
        // So an explicit PageBreak now reduces to a single newPage().
        $this->engine->newPage();
    }

    /**
     * Reserves enough vertical space before drawing a new block so the
     * top of its first glyphs (ascender) doesn't collide with the
     * previous baseline. Returns the actual amount of `spaceBefore` to
     * apply (= max of the user-requested spaceBefore and the geometric
     * minimum). When this is the first block on a page, the engine's
     * cursor already sits at the ideal first-baseline position so we
     * just honour the user's spaceBefore as-is.
     *
     * Computation:
     *  - top of new glyphs (in PDF coords) = cursorY + ascender_pt
     *  - previous baseline                 = cursorY + lastBlockLineHeight
     *  - overlap if `ascender_pt > lastBlockLineHeight`
     *  - reservation = max(0, ascender_pt - lastBlockLineHeight)
     */
    private function reserveAscentFor(float $fontSize, string $fontName, float $userSpaceBefore): float
    {
        if ($this->lastBlockLineHeight <= 0.0) {
            return $userSpaceBefore;
        }

        $ascentEm  = $this->engine->getFontMetrics($fontName)['ascender'];
        $ascentPt  = $ascentEm * $fontSize / 1000.0;
        $needed    = max(0.0, $ascentPt - $this->lastBlockLineHeight);

        return max($userSpaceBefore, $needed);
    }

    /* =============================================================
     | Headings
     |============================================================= */

    private function writeHeading(Heading $heading, DocumentInterface $document, float $indent): void
    {
        $level = max(1, min(6, $heading->getLevel()));
        $size = match ($level) {
            1 => 24.0,
            2 => 20.0,
            3 => 16.0,
            4 => 14.0,
            5 => 13.0,
            default => 12.0,
        };

        // Visible top-of-glyph sits ~80% of font size ABOVE the baseline (cursor).
        // We compensate so the requested gap is what actually appears on the page,
        // otherwise the heading visually overlaps the previous block (table, image…).
        $ascent      = $size * 0.8;
        $spaceBefore = match ($level) {
            1 => 18.0,
            2 => 14.0,
            3 => 12.0,
            default => 10.0,
        };
        $spaceAfter  = match ($level) {
            1 => 10.0,
            2 => 8.0,
            default => 6.0,
        };

        // Outline entry + named destination BEFORE moving the cursor,
        // so the viewport target includes the heading's breathing space.
        $anchorY = min($this->engine->getPageHeight(), $this->engine->getCursorY() + 4.0);
        $this->engine->addOutlineEntry($level, $heading->getPlainText(), $anchorY);

        $anchor = $this->tocResolver?->anchorFor($heading) ?? ($heading->hasId() ? $heading->getId() : '');
        if ($anchor !== '') {
            $this->engine->registerAnchor($anchor, $anchorY);
        }

        $this->engine->moveCursorY(-($spaceBefore + $ascent));

        foreach ($heading->getRuns() as $run) {
            $style = $run->getStyle() ?? $document->getDefaultTextStyle();
            $headingStyle = TextStyle::make()
                ->setFontFamily($style->getFontFamily())
                ->setFontSize($size)
                ->setColor($style->getColor() === '#000000' ? '#1F3763' : $style->getColor())
                ->setBold();
            $styledRun = new TextRun($run->getText(), $headingStyle, $run->getLink());
            $this->writeTextRun($styledRun, $document, 1.15, $indent);
        }

        $this->engine->moveCursorY(-$spaceAfter);
    }

    /* =============================================================
     | Paragraph
     |============================================================= */

    private function writeParagraph(Paragraph $paragraph, DocumentInterface $document, float $indent): void
    {
        $paraStyle = $paragraph->getStyle();
        $headingLevel = $paraStyle?->getHeadingLevel();
        $lineSpacing = $paraStyle?->getLineSpacing() ?? 1.15;
        $spaceBefore = $paraStyle?->getSpaceBefore() ?? 0;
        $spaceAfter  = $paraStyle?->getSpaceAfter() ?? 6;
        // ParagraphStyle::alignment is propagated all the way to the
        // PDF engine so left/center/right/justify work for top-level
        // paragraphs (not only inside a TextZone).
        $align       = $paraStyle?->getAlignment()->value ?? 'left';
        // First-line indent (since v0.8.0). Only applied to the very
        // first run of the paragraph so a multi-run paragraph keeps a
        // single visual indent (typical use: italic first word, then
        // body text — they should share one indent, not two).
        $firstLineIndent = $paraStyle?->getFirstLineIndent() ?? 0.0;

        $headingFont = null;

        if ($headingLevel !== null) {
            $headingFont = match ($headingLevel) {
                1 => 24.0,
                2 => 20.0,
                3 => 16.0,
                default => 14.0,
            };
            // Match writeHeading() so legacy "Paragraph + headingLevel" paragraphs
            // get the same visual top-of-glyph clearance as native Heading elements.
            $spaceBefore = max($spaceBefore, 12.0) + $headingFont * 0.8;
            $spaceAfter  = max($spaceAfter, 8.0);
        }

        // Reserve enough vertical space so the new paragraph's ascender
        // doesn't overlap the previous paragraph's baseline (otherwise a
        // small "eyebrow" line followed by a 28pt title would visibly
        // collide on the eyebrow's baseline). See reserveAscentFor().
        $firstRun       = $paragraph->getRuns()[0] ?? null;
        $firstRunStyle  = ($headingFont !== null && ($firstRun?->getStyle()) === null)
            ? TextStyle::make()->setFontSize($headingFont)->setBold()
            : ($firstRun?->getStyle() ?? $document->getDefaultTextStyle());
        $effectiveSpaceBefore = $this->reserveAscentFor(
            $firstRunStyle->getFontSize(),
            $this->resolveFontName($firstRunStyle),
            $spaceBefore,
        );

        if ($effectiveSpaceBefore > 0) {
            $this->engine->moveCursorY(-$effectiveSpaceBefore);
        }

        $lastFontSize = $firstRunStyle->getFontSize();

        foreach ($paragraph->getRuns() as $i => $run) {
            $runStyle = $run->getStyle();
            $isFirstRun = ($i === 0);
            $runIndent  = $isFirstRun ? $firstLineIndent : 0.0;

            if ($headingFont !== null && $runStyle === null) {
                $headingStyle = TextStyle::make()
                    ->setFontSize($headingFont)
                    ->setBold();
                $styledRun = new TextRun($run->getText(), $headingStyle);
                $this->writeTextRun($styledRun, $document, $lineSpacing, $indent, $align, $runIndent);
                $lastFontSize = $headingStyle->getFontSize();
            } else {
                $this->writeTextRun($run, $document, $lineSpacing, $indent, $align, $runIndent);
                $lastFontSize = ($runStyle ?? $document->getDefaultTextStyle())->getFontSize();
            }
        }

        // Track the trailing line height so the next block can compute
        // its own ascent reservation (only meaningful if the paragraph
        // actually rendered something — empty paragraphs leave the
        // previous trailing metric unchanged).
        if ($paragraph->getRuns() !== []) {
            $this->lastBlockLineHeight = $lastFontSize * $lineSpacing;
        }

        if ($spaceAfter > 0) {
            $this->engine->moveCursorY(-$spaceAfter);
        }
    }

    private function writeTextRun(
        TextRun $run,
        DocumentInterface $document,
        float $lineSpacing,
        float $indent = 0,
        string $align = 'left',
        float $firstLineIndent = 0.0,
    ): void {
        $style = $run->getStyle() ?? $document->getDefaultTextStyle();
        $link = $run->getLink();

        if ($link !== null) {
            $style = TextStyle::make()
                ->setFontFamily($style->getFontFamily())
                ->setFontSize($style->getFontSize())
                ->setBold($style->isBold())
                ->setItalic($style->isItalic())
                ->setUnderline(true)
                ->setStrikethrough($style->isStrikethrough())
                ->setHighlight($style->getHighlight())
                ->setLetterSpacing($style->getLetterSpacing())
                ->setColor($style->getColor() === '#000000' ? '#0563C1' : $style->getColor());
        }

        $fontName = $this->resolveFontName($style);
        $fontSize = $style->getFontSize();
        [$r, $g, $b] = $style->getColorRgb();

        // Open a link scope so every line the engine emits for this run
        // records a clickable rectangle (multi-line and page-spanning
        // links included). External URLs become URI actions; anchor-only
        // links resolve to the named destination registered by the
        // matching Bookmark / Heading id.
        if ($link !== null) {
            if ($link->getUrl() !== '') {
                $this->engine->beginLink($link->getHref());
            } else {
                $this->engine->beginLink(null, $link->getAnchor());
            }
        }

        $this->engine->setTextDecorations(
            $style->isUnderline(),
            $style->isStrikethrough(),
            $style->getHighlight(),
        );

        try {
            $this->engine->writeWrappedText(
                text:            $run->getText(),
                fontName:        $fontName,
                fontSize:        $fontSize,
                r:               $r,
                g:               $g,
                b:               $b,
                maxWidth:        max(0.0, $this->engine->getColumnWidth() - $indent),
                lineSpacing:     $lineSpacing,
                x:               $this->engine->getColumnOriginX() + $indent,
                align:           $align,
                letterSpacing:   $style->getLetterSpacing(),
                firstLineIndent: $firstLineIndent,
            );

            if ($run->getFootnote() !== null) {
                $this->writeFootnoteMarker($run->getFootnote()->getText(), $document, $lineSpacing, $indent, $align);
            }
        } finally {
            $this->engine->clearTextDecorations();
            if ($link !== null) {
                $this->engine->endLink();
            }
        }
    }

    private function writeFootnoteMarker(
        string $text,
        DocumentInterface $document,
        float $lineSpacing,
        float $indent,
        string $align,
    ): void {
        $this->footnoteCounter++;
        $number = $this->footnoteCounter;
        $page = $this->engine->getCurrentPageNumber();
        $this->pageFootnotes[$page][] = ['num' => $number, 'text' => $text];
        $this->engine->setReservedBottom($this->estimatePageFootnotesHeight($page));

        $base = $document->getDefaultTextStyle();
        $markerStyle = TextStyle::make()
            ->setFontFamily($base->getFontFamily())
            ->setFontSize(max(7.0, $base->getFontSize() - 3.0))
            ->setColor($base->getColor());

        $this->writeTextRun(
            new TextRun(sprintf('[%d]', $number), $markerStyle),
            $document,
            $lineSpacing,
            $indent,
            $align,
        );
    }

    /**
     * @return float Estimated height of the footnote band for a page
     */
    private function estimatePageFootnotesHeight(int $page): float
    {
        $notes = $this->pageFootnotes[$page] ?? [];
        if ($notes === []) {
            return 0.0;
        }

        // Separator + gap + ~1.25 lines per note at ~9pt.
        return 18.0 + (count($notes) * 12.0);
    }

    /**
     * Paint footnotes into the reserved bottom band of the current page.
     * Invoked from the engine's beforeFlushPage hook.
     */
    private function paintPageFootnotes(): void
    {
        $page = $this->engine->getCurrentPageNumber();
        $notes = $this->pageFootnotes[$page] ?? [];
        if ($notes === []) {
            return;
        }

        $fontName = 'Helvetica';
        $fontSize = 9.0;
        $lineSpacing = 1.1;
        $lineHeight = $fontSize * $lineSpacing;
        $left = $this->engine->getLeftMargin();
        $width = $this->engine->getContentWidth();
        $bandHeight = $this->estimatePageFootnotesHeight($page);
        $bottom = $this->engine->getBottomMargin();
        $separatorY = $bottom + $bandHeight;
        $cursorY = $separatorY - 10.0;

        $this->engine->drawLine($left, $separatorY, min($left + 120.0, $left + $width), $separatorY, 0.5);

        foreach ($notes as $note) {
            $text = sprintf('[%d] %s', $note['num'], $note['text']);
            $yTop = $this->engine->getPageHeight() - $cursorY;
            $this->engine->writeWrappedTextAt(
                text: $text,
                fontName: $fontName,
                fontSize: $fontSize,
                x: $left,
                yTopLeft: $yTop,
                maxWidth: $width,
                r: 0.2,
                g: 0.2,
                b: 0.2,
                lineSpacing: $lineSpacing,
                maxHeight: max($lineHeight, $cursorY - $bottom),
            );
            $cursorY -= $lineHeight * max(1, (int) ceil(strlen($text) / 90));
            if ($cursorY < $bottom + $lineHeight) {
                break;
            }
        }

        unset($this->pageFootnotes[$page]);
        $this->engine->setReservedBottom(0.0);
    }

    /* =============================================================
     | Lists
     |============================================================= */

    private function writeList(ListBlock $list, DocumentInterface $document, int $depth, float $baseIndent): void
    {
        $counter = $list->getStart();
        $indent = $baseIndent + ($depth * 18);

        foreach ($list->getItems() as $item) {
            $marker = $list->isOrdered() ? ($counter . '. ') : '• ';
            $this->writeListItemLine($item, $marker, $document, $indent);

            foreach ($item->getBlocks() as $child) {
                if ($child instanceof ListBlock) {
                    $this->writeList($child, $document, $depth + 1, $baseIndent);
                } else {
                    $this->writeBlock($child, $document, $indent + 18);
                }
            }

            if ($list->isOrdered()) {
                $counter++;
            }
        }

        if ($depth === 0) {
            $this->engine->moveCursorY(-10.0);
        }
    }

    private function writeListItemLine(ListItem $item, string $marker, DocumentInterface $document, float $indent): void
    {
        $style = $document->getDefaultTextStyle();
        $fontName = $this->resolveFontName($style);
        $fontSize = $style->getFontSize();
        [$r, $g, $b] = $style->getColorRgb();

        $this->engine->writeWrappedText(
            text: $marker . $item->getPlainText(),
            fontName: $fontName,
            fontSize: $fontSize,
            r: $r,
            g: $g,
            b: $b,
            lineSpacing: 1.15,
            x: $this->engine->getLeftMargin() + $indent,
        );
    }

    /* =============================================================
     | Blockquote
     |============================================================= */

    private function writeBlockquote(Blockquote $quote, DocumentInterface $document, float $indent): void
    {
        $quoteIndent = $indent + 18;

        $this->engine->moveCursorY(-6.0);

        foreach ($quote->getElements() as $element) {
            if ($element instanceof Paragraph) {
                $this->writeQuotedParagraph($element, $document, $quoteIndent);
                continue;
            }

            $this->writeBlock($element, $document, $quoteIndent);
        }

        $this->engine->moveCursorY(-10.0);
    }

    private function writeQuotedParagraph(Paragraph $paragraph, DocumentInterface $document, float $indent): void
    {
        $align = $paragraph->getStyle()?->getAlignment()->value ?? 'left';

        foreach ($paragraph->getRuns() as $run) {
            $style = $run->getStyle() ?? $document->getDefaultTextStyle();
            $quoteStyle = TextStyle::make()
                ->setFontFamily($style->getFontFamily())
                ->setFontSize($style->getFontSize())
                ->setItalic(true)
                ->setColor('#4B5563');

            $styled = new TextRun($run->getText(), $quoteStyle, $run->getLink());
            $this->writeTextRun($styled, $document, 1.15, $indent, $align);
        }
    }

    /* =============================================================
     | CodeBlock
     |============================================================= */

    private function writeCodeBlock(CodeBlock $code, DocumentInterface $document, float $indent): void
    {
        $style = $document->getDefaultTextStyle();
        $fontSize = max(9.0, $style->getFontSize() - 1);

        $this->engine->moveCursorY(-4.0);

        $lines = $code->getLines();

        if ($lines === []) {
            return;
        }

        foreach ($lines as $line) {
            $this->engine->writeWrappedText(
                text: $line,
                fontName: 'Courier',
                fontSize: $fontSize,
                r: 0.15,
                g: 0.16,
                b: 0.18,
                lineSpacing: 1.2,
                x: $this->engine->getLeftMargin() + $indent,
            );
        }

        $this->engine->moveCursorY(-4.0);
    }

    /* =============================================================
     | Tables
     |============================================================= */

    private function writeTable(Table $table, DocumentInterface $document): void
    {
        $tableStyle   = $table->getStyle();
        $contentWidth = $this->engine->getContentWidth();
        $colCount     = $table->getColumnCount();

        if ($colCount === 0) {
            return;
        }

        $this->engine->moveCursorY(-8.0);

        $colWidths = $table->getColumnWidths();

        if (empty($colWidths)) {
            $equalWidth = $contentWidth / $colCount;
            $colWidths = array_fill(0, $colCount, $equalWidth);
        } else {
            $total = array_sum($colWidths);
            $colWidths = array_map(fn (float $w) => ($w / $total) * $contentWidth, $colWidths);
        }

        $cellPadding  = $tableStyle?->getCellPadding() ?? 4.0;
        $borderWidth  = $tableStyle?->getBorderWidth() ?? 0.5;
        $borderColor  = $tableStyle?->getBorderColor() ?? '#000000';
        $headerBg     = $tableStyle?->getHeaderBg() ?? '#f3f4f6';

        $defaultStyle  = $document->getDefaultTextStyle();
        $fontSize      = $defaultStyle->getFontSize();
        $baseRowHeight = $fontSize * 1.15 + ($cellPadding * 2);
        $startX        = $this->engine->getLeftMargin();

        foreach ($table->getRows() as $row) {
            $cells = $row->getCells();

            // Cell images make the row taller: fit each image to the
            // cell's inner width, cap its height, stack below the text.
            $rowImages = [];
            $rowHeight = $baseRowHeight;

            foreach ($cells as $i => $cell) {
                $cw = $colWidths[$i] ?? $colWidths[0];
                $images = $this->cellImagesForPdf($cell, $cw - 2 * $cellPadding);
                if ($images === []) {
                    continue;
                }
                $rowImages[$i] = $images;

                $stackHeight = array_sum(array_column($images, 'h')) + 4.0 * count($images);
                $hasText = $this->cellTextForPdf($cell) !== '';
                $needed = ($hasText ? $baseRowHeight : $cellPadding * 2) + $stackHeight;
                $rowHeight = max($rowHeight, $needed);
            }

            if ($this->engine->needsNewPage($rowHeight)) {
                $this->engine->newPage();
            }

            $startY  = $this->engine->getCursorY();
            $fillBg  = $row->isHeader() ? $headerBg : null;

            $x = $startX;
            foreach ($colWidths as $cw) {
                $this->engine->drawRect($x, $startY - $rowHeight, $cw, $rowHeight, $fillBg, $borderColor, $borderWidth);
                $x += $cw;
            }

            $x = $startX;

            foreach ($cells as $i => $cell) {
                $cw   = $colWidths[$i] ?? $colWidths[0];

                $text = $this->cellTextForPdf($cell);
                $cellStyle = $this->cellStyleForPdf($cell, $defaultStyle);

                $cellSize  = $cellStyle->getFontSize() > 0 ? $cellStyle->getFontSize() : $fontSize;
                $fontName  = $this->resolveFontName($cellStyle);

                if ($row->isHeader() && ! str_contains($fontName, 'Bold')) {
                    $fontName = str_replace(
                        ['Helvetica', 'Times-Roman', 'Courier'],
                        ['Helvetica-Bold', 'Times-Bold', 'Courier-Bold'],
                        $fontName,
                    );
                }

                [$cr, $cg, $cb] = $cellStyle->getColorRgb();

                $textX = $x + $cellPadding;
                $textY = $startY - $cellPadding - $cellSize;

                $lines = $text !== ''
                    ? $this->engine->wrapText($text, $fontName, $cellSize, $cw - ($cellPadding * 2))
                    : [];

                foreach ($lines as $li => $line) {
                    $yPos = $textY - ($li * $cellSize * 1.15);
                    $this->engine->writeTextAt($line, $fontName, $cellSize, $textX, $yPos, $cr, $cg, $cb);
                }

                if (isset($rowImages[$i])) {
                    $imgY = $startY - $cellPadding - ($text !== '' ? $baseRowHeight - $cellPadding : 0.0);
                    foreach ($rowImages[$i] as $img) {
                        $this->engine->drawImage($img['src'], $x + $cellPadding, $imgY - $img['h'], $img['w'], $img['h']);
                        $imgY -= $img['h'] + 4.0;
                        if ($img['tmp'] !== null) {
                            @unlink($img['tmp']);
                        }
                    }
                }

                $x += $cw;
            }

            $this->engine->moveCursorY(-$rowHeight);
        }

        $this->engine->moveCursorY(-12);
    }

    /**
     * Resolves the drawable images of a cell: source path (embedded
     * data is materialised to a temp file the caller unlinks), scaled
     * to fit the cell's inner width, height capped at 80pt.
     *
     * @return list<array{src: string, w: float, h: float, tmp: ?string}>
     */
    private function cellImagesForPdf(\Paperdoc\Document\TableCell $cell, float $maxWidth): array
    {
        $result = [];

        foreach ($cell->getElements() as $el) {
            if (! $el instanceof Image) {
                continue;
            }

            $tmpPath = null;
            $src = $this->resolveImagePath($el, $tmpPath);
            if ($src === null || $maxWidth <= 1.0) {
                continue;
            }

            $info = @getimagesize($src);
            $natW = (float) max(1, $el->getWidth() ?: ($info[0] ?? 100));
            $natH = (float) max(1, $el->getHeight() ?: ($info[1] ?? 75));

            $w = min($natW, $maxWidth);
            $h = $natH * ($w / $natW);
            if ($h > 80.0) {
                $w *= 80.0 / $h;
                $h = 80.0;
            }

            $result[] = ['src' => $src, 'w' => $w, 'h' => $h, 'tmp' => $tmpPath];
        }

        return $result;
    }

    /**
     * Flatten a cell's elements into a single line of text suitable for
     * the (single-font) PDF table renderer. Honours every block element
     * supported by the model so nothing is silently dropped.
     */
    private function cellTextForPdf(\Paperdoc\Document\TableCell $cell): string
    {
        $parts = [];

        foreach ($cell->getElements() as $el) {
            if ($el instanceof Paragraph || $el instanceof Heading) {
                $parts[] = $el->getPlainText();
                continue;
            }

            if ($el instanceof CodeBlock) {
                $parts[] = $el->getCode();
                continue;
            }

            if ($el instanceof ListBlock) {
                $i = $el->getStart();
                foreach ($el->getItems() as $item) {
                    $parts[] = $el->isOrdered()
                        ? sprintf('%d. %s', $i++, $item->getPlainText())
                        : sprintf('• %s', $item->getPlainText());
                }
                continue;
            }

            if ($el instanceof Blockquote) {
                foreach ($el->getElements() as $inner) {
                    if ($inner instanceof Paragraph || $inner instanceof Heading) {
                        $parts[] = $inner->getPlainText();
                    }
                }
                continue;
            }

            if ($el instanceof Image) {
                $tmpPath = null;
                $src = $this->resolveImagePath($el, $tmpPath);

                if ($tmpPath !== null) {
                    @unlink($tmpPath);
                }

                if ($src === null && $el->getAlt() !== '') {
                    $parts[] = $el->getAlt();
                }

                continue;
            }

            // Bookmarks have no visible representation; PageBreak inside
            // a cell is meaningless.
        }

        $text = implode(' ', array_filter($parts, fn (string $s) => $s !== ''));

        // PDF text engine renders single-line strings — collapse any
        // newlines that bubbled up from code blocks or quotes.
        return preg_replace('/\s*\n+\s*/', ' ', $text) ?? $text;
    }

    /**
     * Pick a representative TextStyle for a cell. When every run inside
     * the cell shares the same style (or there is exactly one run), we
     * use it; otherwise we fall back to the document default. This
     * preserves bold/italic/color/font-size for the common case where a
     * cell carries a single visual treatment.
     */
    private function cellStyleForPdf(\Paperdoc\Document\TableCell $cell, TextStyle $default): TextStyle
    {
        $candidate = null;

        foreach ($cell->getElements() as $el) {
            if (! $el instanceof Paragraph) {
                continue;
            }
            foreach ($el->getRuns() as $run) {
                $style = $run->getStyle();
                if ($style === null) {
                    continue;
                }
                if ($candidate === null) {
                    $candidate = $style;
                    continue;
                }
                if (! $this->stylesEquivalent($candidate, $style)) {
                    return $default;
                }
            }
        }

        return $candidate ?? $default;
    }

    private function stylesEquivalent(TextStyle $a, TextStyle $b): bool
    {
        return $a->isBold() === $b->isBold()
            && $a->isItalic() === $b->isItalic()
            && $a->getColor() === $b->getColor()
            && $a->getFontFamily() === $b->getFontFamily()
            && abs($a->getFontSize() - $b->getFontSize()) < 0.001;
    }

    /* =============================================================
     | TextZone (positioned text frame)
     |============================================================= */

    private function writeTextZone(TextZone $zone, DocumentInterface $document): void
    {
        $bg     = $zone->getBackgroundColor();
        $border = $zone->getBorderColor();
        $bw     = $zone->getBorderWidth();

        if ($bg !== null || $border !== null) {
            $this->engine->drawRectTopLeft(
                $zone->getX(),
                $zone->getY(),
                $zone->getWidth(),
                $zone->getHeight(),
                $bg,
                $border,
                $bw > 0 ? $bw : 0.5,
            );
        }

        $padding  = $zone->getPadding();
        $textX    = $zone->getX() + $padding;
        $textTopY = $zone->getY() + $padding;
        $maxWidth = max(0.0, $zone->getWidth() - 2 * $padding);

        $overflow = $zone->getOverflow();
        $hasMaxHeight = $overflow !== TextZone::OVERFLOW_VISIBLE;
        $maxHeight = $hasMaxHeight ? max(0.0, $zone->getHeight() - 2 * $padding) : null;
        $ellipsis  = $overflow === TextZone::OVERFLOW_ELLIPSIS;

        $cursorOffset      = 0.0;
        $prevLineHeightPt  = 0.0;
        // Local mirror of lastBlockLineHeight for in-zone stacking —
        // each TextZone is independent so we track its own "tail line"
        // separately and don't pollute the page-level state.

        foreach ($zone->getParagraphs() as $paragraph) {
            $paraStyle       = $paragraph->getStyle();
            $lineSpacing     = $paraStyle?->getLineSpacing() ?? 1.15;
            $spaceAfter      = $paraStyle?->getSpaceAfter() ?? 4.0;
            $spaceBefore     = $paraStyle?->getSpaceBefore() ?? 0.0;
            $align           = $paraStyle?->getAlignment()->value ?? 'left';
            $firstLineIndent = $paraStyle?->getFirstLineIndent() ?? 0.0;

            // Same overlap-prevention logic as writeParagraph(): if the
            // first run's font is much bigger than the previous tail
            // line, push the cursor down enough so its ascender clears
            // the previous baseline. cursorOffset uses top-down
            // coordinates here, so we ADD the reservation.
            $firstRun      = $paragraph->getRuns()[0] ?? null;
            $firstRunStyle = $firstRun?->getStyle() ?? $document->getDefaultTextStyle();
            if ($prevLineHeightPt > 0.0) {
                $ascentEm = $this->engine->getFontMetrics($this->resolveFontName($firstRunStyle))['ascender'];
                $ascentPt = $ascentEm * $firstRunStyle->getFontSize() / 1000.0;
                $needed   = max(0.0, $ascentPt - $prevLineHeightPt);
                $cursorOffset += max($spaceBefore, $needed);
            } elseif ($spaceBefore > 0.0) {
                $cursorOffset += $spaceBefore;
            }

            foreach ($paragraph->getRuns() as $runIndex => $run) {
                if ($maxHeight !== null && $cursorOffset >= $maxHeight) {
                    break 2;
                }

                $remaining = $maxHeight !== null ? $maxHeight - $cursorOffset : null;

                $style = $run->getStyle() ?? $document->getDefaultTextStyle();
                [$r, $g, $b] = $style->getColorRgb();
                $isFirstRun  = ($runIndex === 0);
                $runIndent   = $isFirstRun ? $firstLineIndent : 0.0;

                $link = $run->getLink();
                if ($link !== null) {
                    $link->getUrl() !== ''
                        ? $this->engine->beginLink($link->getHref())
                        : $this->engine->beginLink(null, $link->getAnchor());
                }

                $this->engine->setTextDecorations(
                    $style->isUnderline(),
                    $style->isStrikethrough(),
                    $style->getHighlight(),
                );

                try {
                    $result = $this->engine->writeWrappedTextAt(
                        text:            $run->getText(),
                        fontName:        $this->resolveFontName($style),
                        fontSize:        $style->getFontSize(),
                        x:               $textX,
                        yTopLeft:        $textTopY + $cursorOffset,
                        maxWidth:        $maxWidth,
                        r:               $r,
                        g:               $g,
                        b:               $b,
                        lineSpacing:     $lineSpacing,
                        maxHeight:       $remaining,
                        ellipsis:        $ellipsis,
                        align:           $align,
                        letterSpacing:   $style->getLetterSpacing(),
                        firstLineIndent: $runIndent,
                    );
                } finally {
                    $this->engine->clearTextDecorations();
                    if ($link !== null) {
                        $this->engine->endLink();
                    }
                }

                $cursorOffset    += $result['consumed'];
                $prevLineHeightPt = $style->getFontSize() * $lineSpacing;

                if ($result['truncated']) {
                    break 2;
                }
            }

            $cursorOffset += $spaceAfter;
        }
    }

    /* =============================================================
     | Table of contents
     |============================================================= */

    private function writeTableOfContents(TableOfContents $toc, DocumentInterface $document): void
    {
        $entries = $this->tocResolver?->entries($toc->getMaxLevel()) ?? [];

        if ($entries === []) {
            return;
        }

        if ($toc->getTitle() !== '') {
            $this->engine->moveCursorY(-24.0);
            $this->engine->writeWrappedText(
                text:     $toc->getTitle(),
                fontName: 'Helvetica-Bold',
                fontSize: 16.0,
                r:        0.12,
                g:        0.22,
                b:        0.39,
                x:        $this->engine->getLeftMargin(),
            );
            $this->engine->moveCursorY(-8.0);
        }

        foreach ($entries as $entry) {
            $indent   = ($entry['level'] - 1) * 14.0;
            $fontSize = $entry['level'] === 1 ? 12.0 : 11.0;
            $fontName = $entry['level'] === 1 ? 'Helvetica-Bold' : 'Helvetica';

            if ($this->engine->needsNewPage($fontSize * 1.4)) {
                $this->engine->newPage();
            }

            $this->engine->beginLink(null, $entry['anchor']);
            try {
                $this->engine->writeWrappedText(
                    text:        $entry['text'],
                    fontName:    $fontName,
                    fontSize:    $fontSize,
                    r:           0.12,
                    g:           0.22,
                    b:           0.39,
                    lineSpacing: 1.4,
                    x:           $this->engine->getLeftMargin() + $indent,
                );
            } finally {
                $this->engine->endLink();
            }
        }

        $this->engine->moveCursorY(-12.0);
        $this->lastBlockLineHeight = 0.0;
    }

    /* =============================================================
     | Horizontal rule (v0.8.0)
     |============================================================= */

    private function writeHorizontalRule(HorizontalRule $rule): void
    {
        $contentWidth = $this->engine->getContentWidth();
        $width        = $rule->resolveWidth($contentWidth);
        $thickness    = $rule->getThickness();
        $marginBottom = $rule->getMarginBottom();

        // We want the rule to break to a new page if there isn't enough
        // room for it plus its bottom margin. The engine's onNewPage
        // hook (set in buildPdf) handles chrome repaint and the
        // lastBlockLineHeight reset.
        $totalNeeded = $rule->getMarginTop() + $thickness + $marginBottom;
        if ($this->engine->needsNewPage($totalNeeded)) {
            $this->engine->newPage();
        }

        if ($rule->getMarginTop() > 0) {
            $this->engine->moveCursorY(-$rule->getMarginTop());
        }
        // Horizontal placement uses the engine's ACTUAL left margin
        // (since v0.8.2). Previously this was hardcoded to 40pt, which
        // misaligned the rule with the body text whenever the section's
        // gutter differed from 40pt — e.g. a custom 18mm (≈51pt) gutter
        // would centre the rule 11pt to the LEFT of the title that
        // follows it (because writeWrappedText inherits cursorX = the
        // real marginLeft, while this method assumed 40).
        $contentLeftX = $this->engine->getLeftMargin();
        $x = match ($rule->getAlignment()) {
            Alignment::CENTER => $contentLeftX + max(0.0, ($contentWidth - $width) / 2.0),
            Alignment::RIGHT  => $contentLeftX + max(0.0, $contentWidth - $width),
            default           => $contentLeftX,
        };

        $cursorY  = $this->engine->getCursorY();
        $bottomY  = $cursorY - ($thickness / 2.0);

        [$rr, $gg, $bb] = $this->hexToRgb($rule->getColor());

        // We can't easily call drawHorizontalLine() here (it uses
        // top-left coordinates) because the renderer cursor is in
        // PDF-native bottom-up. Drop down to drawLine() which expects
        // the same convention as cursorY.
        $this->engine->drawColoredLine($x, $bottomY, $x + $width, $bottomY, $thickness, $rr, $gg, $bb);

        $this->engine->moveCursorY(-($thickness + $marginBottom));

        // Trailing-line bookkeeping (v0.8.1): the rule's bottom edge sits
        // exactly `marginBottom` ABOVE the post-rule cursor, so a
        // following paragraph whose ascender > marginBottom would punch
        // through the rule visually. Storing marginBottom here lets
        // reserveAscentFor() compute the right deficit for the NEXT
        // text block — `needed = max(0, ascender - marginBottom)` — and
        // automatically pad spaceBefore so the title's top of glyphs
        // lands exactly at the rule's bottom edge.
        //
        // (The rule's own thickness is excluded on purpose: the rule
        //  line is drawn centred on cursorY+marginBottom+thickness/2, so
        //  the BOTTOM of the rule already sits at cursorY+marginBottom.
        //  Adding thickness back in would over-reserve.)
        $this->lastBlockLineHeight = $marginBottom;
    }

    /**
     * Hex `#RRGGBB` (or `#RGB`) → 0..1 RGB triplet.
     *
     * @return array{0: float, 1: float, 2: float}
     */
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) !== 6) {
            return [0.6, 0.6, 0.6]; // sensible grey fallback
        }
        return [
            (float) (hexdec(substr($hex, 0, 2)) / 255.0),
            (float) (hexdec(substr($hex, 2, 2)) / 255.0),
            (float) (hexdec(substr($hex, 4, 2)) / 255.0),
        ];
    }

    /* =============================================================
     | Images
     |============================================================= */

    private function writeImage(Image $image): void
    {
        $tmpPath = null;
        $src = $this->resolveImagePath($image, $tmpPath);

        if ($src === null) {
            return;
        }

        $w = $image->getWidth() ?: 200;
        $h = $image->getHeight() ?: 150;

        $pdfW = (float) $w;
        $pdfH = (float) $h;

        $maxW = $this->engine->getContentWidth();
        if ($pdfW > $maxW) {
            $ratio = $maxW / $pdfW;
            $pdfW = $maxW;
            $pdfH *= $ratio;
        }

        if ($this->engine->needsNewPage($pdfH)) {
            $this->engine->newPage();
        }

        $y = $this->engine->getCursorY() - $pdfH;

        $this->engine->drawImage($src, $this->engine->getLeftMargin(), $y, $pdfW, $pdfH);
        $this->engine->moveCursorY(-($pdfH + 10));

        if ($tmpPath !== null) {
            @unlink($tmpPath);
        }
    }

    /**
     * Résout le chemin d'une Image utilisable par le PdfEngine. Si
     * l'image n'a qu'un buffer en mémoire, écrit un fichier temporaire
     * (que l'appelant doit ensuite supprimer).
     */
    private function resolveImagePath(Image $image, ?string &$tmpPath = null): ?string
    {
        $src = $image->getSrc();
        $tmpPath = null;

        if ((! $src || ! file_exists($src)) && $image->hasData()) {
            $extension = match ($image->getMimeType()) {
                'image/jpeg', 'image/jpg' => 'jpg',
                'image/gif' => 'gif',
                default => 'png',
            };
            $tmpPath = tempnam(sys_get_temp_dir(), 'paperdoc_img_') . '.' . $extension;
            if (@file_put_contents($tmpPath, $image->getData()) !== false) {
                $src = $tmpPath;
            } else {
                $tmpPath = null;
            }
        }

        if (! $src || ! file_exists($src)) {
            return null;
        }

        return $src;
    }

    private function isTempFile(string $path, Image $image): bool
    {
        return $path !== $image->getSrc();
    }
}
