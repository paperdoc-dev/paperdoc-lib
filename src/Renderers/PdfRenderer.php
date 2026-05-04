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
use Paperdoc\Document\Style\{PageSetup, RunningElement, TextStyle};
use Paperdoc\Enum\Alignment;
use Paperdoc\Support\Pdf\PdfEngine;

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

    private ?RunningElement $header = null;
    private ?RunningElement $footer = null;
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

    private function buildPdf(DocumentInterface $document, string $filename): void
    {
        $this->engine = new PdfEngine();
        $this->engine->setTitle($document->getTitle());
        $this->documentTitle = $document->getTitle();
        $this->lastBlockLineHeight = 0.0;

        $author = null;
        $this->header = null;
        $this->footer = null;

        if ($document instanceof Document) {
            $author = $document->getProperties()?->getAuthor();
            $this->header = $document->getHeader();
            $this->footer = $document->getFooter();
        }

        $this->engine->setCreator($author !== null && $author !== ''
            ? $author
            : ($document->getMetadata()['creator'] ?? 'Paperdoc'));

        $this->totalPages = $this->countDeclaredPages($document);

        $isFirst = true;

        foreach ($document->getSections() as $section) {
            if (! $isFirst) {
                $this->engine->newPage();
                $this->lastBlockLineHeight = 0.0;
            }

            $this->currentSection = $section;
            $this->applyPageSetup($section->getPageSetup());
            $this->drawHeaderFooter();
            $this->writeSection($section, $document);
            $isFirst = false;
        }

        $this->currentSection = null;
        $this->engine->save($filename);
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
     * Applique la configuration de page (taille, marges, fonds) à la
     * page courante du PdfEngine. Doit être appelé après newPage() et
     * avant tout autre dessin.
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
        foreach ($section->getElements() as $element) {
            $this->writeBlock($element, $document, 0);
        }
    }

    /* =============================================================
     | Header / Footer
     |============================================================= */

    private function drawHeaderFooter(): void
    {
        if ($this->header !== null) {
            $this->drawRunningElement($this->header, true);
        }

        if ($this->footer !== null) {
            $this->drawRunningElement($this->footer, false);
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
        $fontName = $style->getPdfFontName();
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
            $element instanceof Heading    => $this->writeHeading($element, $document, $indent),
            $element instanceof Paragraph  => $this->writeParagraph($element, $document, $indent),
            $element instanceof ListBlock  => $this->writeList($element, $document, 0, $indent),
            $element instanceof Blockquote => $this->writeBlockquote($element, $document, $indent),
            $element instanceof CodeBlock  => $this->writeCodeBlock($element, $document, $indent),
            $element instanceof Bookmark   => null, // invisible target; reserved for future link annotations
            $element instanceof Table      => $this->writeTable($element, $document),
            $element instanceof Image      => $this->writeImage($element),
            $element instanceof TextZone   => $this->writeTextZone($element, $document),
            $element instanceof PageBreak  => $this->handlePageBreak(),
            default                        => null,
        };

        // Non-paragraph blocks (heading, image, table, list, …) reset
        // the trailing line metric — paragraphs handle the bookkeeping
        // themselves inside writeParagraph(), and the next text block
        // doesn't need ascent reservation against a non-text or
        // already-spaced block (writeHeading bakes its own clearance).
        if (! ($element instanceof Paragraph)) {
            $this->lastBlockLineHeight = 0.0;
        }
    }

    private function handlePageBreak(): void
    {
        $this->engine->newPage();
        $this->lastBlockLineHeight = 0.0;
        $this->applyPageSetup($this->currentSection?->getPageSetup());
        $this->drawHeaderFooter();
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

        $this->engine->moveCursorY(-($spaceBefore + $ascent));

        foreach ($heading->getRuns() as $run) {
            $style = $run->getStyle() ?? $document->getDefaultTextStyle();
            $headingStyle = TextStyle::make()
                ->setFontFamily($style->getFontFamily())
                ->setFontSize($size)
                ->setColor($style->getColor() === '#000000' ? '#1F3763' : $style->getColor())
                ->setBold();
            $styledRun = new TextRun($run->getText(), $headingStyle);
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
            $firstRunStyle->getPdfFontName(),
            $spaceBefore,
        );

        if ($effectiveSpaceBefore > 0) {
            $this->engine->moveCursorY(-$effectiveSpaceBefore);
        }

        $lastFontSize = $firstRunStyle->getFontSize();

        foreach ($paragraph->getRuns() as $run) {
            $runStyle = $run->getStyle();

            if ($headingFont !== null && $runStyle === null) {
                $headingStyle = TextStyle::make()
                    ->setFontSize($headingFont)
                    ->setBold();
                $styledRun = new TextRun($run->getText(), $headingStyle);
                $this->writeTextRun($styledRun, $document, $lineSpacing, $indent, $align);
                $lastFontSize = $headingStyle->getFontSize();
            } else {
                $this->writeTextRun($run, $document, $lineSpacing, $indent, $align);
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
                ->setColor($style->getColor() === '#000000' ? '#0563C1' : $style->getColor());
        }

        $fontName = $style->getPdfFontName();
        $fontSize = $style->getFontSize();
        [$r, $g, $b] = $style->getColorRgb();

        $this->engine->writeWrappedText(
            text: $run->getText(),
            fontName: $fontName,
            fontSize: $fontSize,
            r: $r,
            g: $g,
            b: $b,
            lineSpacing: $lineSpacing,
            x: $indent > 0 ? 40 + $indent : 0,
            align: $align,
        );
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
        $fontName = $style->getPdfFontName();
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
            x: 40 + $indent,
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

            if ($element instanceof DocumentElementInterface) {
                $this->writeBlock($element, $document, $quoteIndent);
            }
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
                x: 40 + $indent,
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

        $defaultStyle = $document->getDefaultTextStyle();
        $fontSize     = $defaultStyle->getFontSize();
        $rowHeight    = $fontSize * 1.15 + ($cellPadding * 2);
        $startX       = 40.0;

        foreach ($table->getRows() as $row) {
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

            $cells = $row->getCells();
            $x = $startX;

            foreach ($cells as $i => $cell) {
                $cw   = $colWidths[$i] ?? $colWidths[0];

                $text = $this->cellTextForPdf($cell);
                $cellStyle = $this->cellStyleForPdf($cell, $defaultStyle);

                $cellSize  = $cellStyle->getFontSize() > 0 ? $cellStyle->getFontSize() : $fontSize;
                $fontName  = $cellStyle->getPdfFontName();

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

                $lines = $this->engine->wrapText($text, $fontName, $cellSize, $cw - ($cellPadding * 2));

                foreach ($lines as $li => $line) {
                    $yPos = $textY - ($li * $cellSize * 1.15);
                    $this->engine->writeTextAt($line, $fontName, $cellSize, $textX, $yPos, $cr, $cg, $cb);
                }

                $x += $cw;
            }

            $this->engine->moveCursorY(-$rowHeight);
        }

        $this->engine->moveCursorY(-12);
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
                $marker = $el->isOrdered() ? '%d. %s' : '• %s';
                $i = $el->getStart();
                foreach ($el->getItems() as $item) {
                    $parts[] = $el->isOrdered()
                        ? sprintf($marker, $i++, $item->getPlainText())
                        : sprintf($marker, $item->getPlainText());
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
                // Inline images inside table cells are not yet drawn by
                // the PDF engine; surface the alt text so the cell is
                // never empty.
                $alt = $el->getAlt();
                if ($alt !== '') {
                    $parts[] = '[' . $alt . ']';
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
            $paraStyle   = $paragraph->getStyle();
            $lineSpacing = $paraStyle?->getLineSpacing() ?? 1.15;
            $spaceAfter  = $paraStyle?->getSpaceAfter() ?? 4.0;
            $spaceBefore = $paraStyle?->getSpaceBefore() ?? 0.0;
            $align       = $paraStyle?->getAlignment()->value ?? 'left';

            // Same overlap-prevention logic as writeParagraph(): if the
            // first run's font is much bigger than the previous tail
            // line, push the cursor down enough so its ascender clears
            // the previous baseline. cursorOffset uses top-down
            // coordinates here, so we ADD the reservation.
            $firstRun      = $paragraph->getRuns()[0] ?? null;
            $firstRunStyle = $firstRun?->getStyle() ?? $document->getDefaultTextStyle();
            if ($prevLineHeightPt > 0.0) {
                $ascentEm = $this->engine->getFontMetrics($firstRunStyle->getPdfFontName())['ascender'];
                $ascentPt = $ascentEm * $firstRunStyle->getFontSize() / 1000.0;
                $needed   = max(0.0, $ascentPt - $prevLineHeightPt);
                $cursorOffset += max($spaceBefore, $needed);
            } elseif ($spaceBefore > 0.0) {
                $cursorOffset += $spaceBefore;
            }

            foreach ($paragraph->getRuns() as $run) {
                if ($maxHeight !== null && $cursorOffset >= $maxHeight) {
                    break 2;
                }

                $remaining = $maxHeight !== null ? $maxHeight - $cursorOffset : null;

                $style = $run->getStyle() ?? $document->getDefaultTextStyle();
                [$r, $g, $b] = $style->getColorRgb();

                $result = $this->engine->writeWrappedTextAt(
                    text:        $run->getText(),
                    fontName:    $style->getPdfFontName(),
                    fontSize:    $style->getFontSize(),
                    x:           $textX,
                    yTopLeft:    $textTopY + $cursorOffset,
                    maxWidth:    $maxWidth,
                    r:           $r,
                    g:           $g,
                    b:           $b,
                    lineSpacing: $lineSpacing,
                    maxHeight:   $remaining,
                    ellipsis:    $ellipsis,
                    align:       $align,
                );

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

        $this->engine->drawImage($src, 40, $y, $pdfW, $pdfH);
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
