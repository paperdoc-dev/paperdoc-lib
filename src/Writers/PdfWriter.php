<?php

declare(strict_types=1);

namespace Pagina\Writers;

use Pagina\Contracts\DocumentInterface;
use Pagina\Document\{Image, PageBreak, Paragraph, Section, Table, TextRun};
use Pagina\Support\Pdf\PdfEngine;

/**
 * Writer PDF natif sans aucune dépendance tierce.
 *
 * Utilise le PdfEngine interne pour générer des fichiers
 * PDF 1.4 valides avec texte, tableaux et images.
 */
class PdfWriter extends AbstractWriter
{
    private PdfEngine $engine;

    public function getFormat(): string { return 'pdf'; }

    public function write(DocumentInterface $document, string $filename): void
    {
        $this->ensureDirectoryWritable($filename);

        $this->engine = new PdfEngine();
        $this->engine->setTitle($document->getTitle());
        $this->engine->setCreator($document->getMetadata()['creator'] ?? 'Pagina');

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
            match (true) {
                $element instanceof Paragraph => $this->writeParagraph($element, $document),
                $element instanceof Table     => $this->writeTable($element, $document),
                $element instanceof Image     => $this->writeImage($element),
                $element instanceof PageBreak => $this->engine->newPage(),
                default                       => null,
            };
        }
    }

    private function writeParagraph(Paragraph $paragraph, DocumentInterface $document): void
    {
        $paraStyle = $paragraph->getStyle();
        $lineSpacing = $paraStyle?->getLineSpacing() ?? 1.15;
        $spaceBefore = $paraStyle?->getSpaceBefore() ?? 0;
        $spaceAfter  = $paraStyle?->getSpaceAfter() ?? 6;

        if ($spaceBefore > 0) {
            $this->engine->moveCursorY(-$spaceBefore);
        }

        foreach ($paragraph->getRuns() as $run) {
            $this->writeTextRun($run, $document, $lineSpacing);
        }

        if ($spaceAfter > 0) {
            $this->engine->moveCursorY(-$spaceAfter);
        }
    }

    private function writeTextRun(TextRun $run, DocumentInterface $document, float $lineSpacing): void
    {
        $style = $run->getStyle() ?? $document->getDefaultTextStyle();

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
        );
    }

    private function writeTable(Table $table, DocumentInterface $document): void
    {
        $tableStyle   = $table->getStyle();
        $contentWidth = $this->engine->getContentWidth();
        $colCount     = $table->getColumnCount();

        if ($colCount === 0) {
            return;
        }

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

        $this->engine->moveCursorY(-6);
    }

    private function writeImage(Image $image): void
    {
        $src = $image->getSrc();

        if (! file_exists($src)) {
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
        $this->engine->moveCursorY(-($pdfH + 6));
    }
}
