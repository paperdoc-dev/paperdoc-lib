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
};
use Paperdoc\Document\Style\TextStyle;
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

        $author = null;
        if ($document instanceof Document) {
            $author = $document->getProperties()?->getAuthor();
        }
        $this->engine->setCreator($author !== null && $author !== ''
            ? $author
            : ($document->getMetadata()['creator'] ?? 'Paperdoc'));

        $isFirst = true;

        foreach ($document->getSections() as $section) {
            if (! $isFirst) {
                $this->engine->newPage();
            }

            $this->writeSection($section, $document);
            $isFirst = false;
        }

        $this->engine->save($filename);
    }

    private function writeSection(Section $section, DocumentInterface $document): void
    {
        foreach ($section->getElements() as $element) {
            $this->writeBlock($element, $document, 0);
        }
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
            $element instanceof PageBreak  => $this->engine->newPage(),
            default                        => null,
        };
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

        if ($spaceBefore > 0) {
            $this->engine->moveCursorY(-$spaceBefore);
        }

        foreach ($paragraph->getRuns() as $run) {
            $runStyle = $run->getStyle();

            if ($headingFont !== null && $runStyle === null) {
                $headingStyle = TextStyle::make()
                    ->setFontSize($headingFont)
                    ->setBold();
                $styledRun = new TextRun($run->getText(), $headingStyle);
                $this->writeTextRun($styledRun, $document, $lineSpacing, $indent);
            } else {
                $this->writeTextRun($run, $document, $lineSpacing, $indent);
            }
        }

        if ($spaceAfter > 0) {
            $this->engine->moveCursorY(-$spaceAfter);
        }
    }

    private function writeTextRun(TextRun $run, DocumentInterface $document, float $lineSpacing, float $indent = 0): void
    {
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
        foreach ($paragraph->getRuns() as $run) {
            $style = $run->getStyle() ?? $document->getDefaultTextStyle();
            $quoteStyle = TextStyle::make()
                ->setFontFamily($style->getFontFamily())
                ->setFontSize($style->getFontSize())
                ->setItalic(true)
                ->setColor('#4B5563');

            $styled = new TextRun($run->getText(), $quoteStyle, $run->getLink());
            $this->writeTextRun($styled, $document, 1.15, $indent);
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
                $text  = $cell->getPlainText();
                $fontName = $defaultStyle->getPdfFontName();

                if ($row->isHeader() && ! str_contains($fontName, 'Bold')) {
                    $fontName = str_replace(
                        ['Helvetica', 'Times-Roman', 'Courier'],
                        ['Helvetica-Bold', 'Times-Bold', 'Courier-Bold'],
                        $fontName,
                    );
                }

                [$cr, $cg, $cb] = $defaultStyle->getColorRgb();

                $textX = $x + $cellPadding;
                $textY = $startY - $cellPadding - $fontSize;

                $lines = $this->engine->wrapText($text, $fontName, $fontSize, $cw - ($cellPadding * 2));

                foreach ($lines as $li => $line) {
                    $yPos = $textY - ($li * $fontSize * 1.15);
                    $this->engine->writeTextAt($line, $fontName, $fontSize, $textX, $yPos, $cr, $cg, $cb);
                }

                $x += $cw;
            }

            $this->engine->moveCursorY(-$rowHeight);
        }

        $this->engine->moveCursorY(-12);
    }

    /* =============================================================
     | Images
     |============================================================= */

    private function writeImage(Image $image): void
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
            }
        }

        if (! $src || ! file_exists($src)) {
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
}
