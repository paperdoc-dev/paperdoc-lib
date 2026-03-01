<?php

declare(strict_types=1);

namespace Paperdoc\Renderers;

use Paperdoc\Contracts\DocumentInterface;
use Paperdoc\Document\{Image, PageBreak, Paragraph, Section, Table, TableCell, TextRun};

class MarkdownRenderer extends AbstractRenderer
{
    public function getFormat(): string
    {
        return 'md';
    }

    public function render(DocumentInterface $document): string
    {
        $parts = [];

        if ($document->getMetadata() !== []) {
            $parts[] = $this->renderFrontMatter($document->getMetadata());
        }

        foreach ($document->getSections() as $section) {
            $parts[] = $this->renderSection($section);
        }

        return implode("\n", $parts);
    }

    /* ------------------------------------------------------------- */

    /** @param array<string, mixed> $metadata */
    private function renderFrontMatter(array $metadata): string
    {
        $yaml = "---\n";

        foreach ($metadata as $key => $value) {
            $yaml .= sprintf("%s: %s\n", $key, is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE));
        }

        $yaml .= "---\n";

        return $yaml;
    }

    private function renderSection(Section $section): string
    {
        $md = '';

        foreach ($section->getElements() as $element) {
            $md .= match (true) {
                $element instanceof Paragraph => $this->renderParagraph($element) . "\n\n",
                $element instanceof Table     => $this->renderTable($element) . "\n",
                $element instanceof Image     => $this->renderImage($element) . "\n\n",
                $element instanceof PageBreak => "---\n\n",
                default                       => '',
            };
        }

        return $md;
    }

    private function renderParagraph(Paragraph $paragraph): string
    {
        $headingLevel = $paragraph->getStyle()?->getHeadingLevel();
        $isHeading = $headingLevel !== null && $headingLevel >= 1 && $headingLevel <= 6;
        $content = $this->renderRuns($paragraph->getRuns(), $isHeading);

        if ($isHeading) {
            return str_repeat('#', $headingLevel) . ' ' . $content;
        }

        return $content;
    }

    /** @param TextRun[] $runs */
    private function renderRuns(array $runs, bool $stripBold = false): string
    {
        $md = '';

        foreach ($runs as $run) {
            $text = $run->getText();

            if ($text === '') {
                continue;
            }

            $style = $run->getStyle();

            if ($style === null) {
                $md .= $text;
                continue;
            }

            $isBold = $style->isBold() && ! $stripBold;

            if ($isBold && $style->isItalic()) {
                $text = '***' . $text . '***';
            } elseif ($isBold) {
                $text = '**' . $text . '**';
            } elseif ($style->isItalic()) {
                $text = '*' . $text . '*';
            }

            if ($style->isUnderline()) {
                $text = '<u>' . $text . '</u>';
            }

            $md .= $text;
        }

        return $md;
    }

    private function renderTable(Table $table): string
    {
        $rows = $table->getRows();

        if ($rows === []) {
            return '';
        }

        $colCount = $table->getColumnCount();
        $md = '';
        $headerRendered = false;

        foreach ($rows as $row) {
            $cells = $row->getCells();
            $line = '|';

            for ($c = 0; $c < $colCount; $c++) {
                $cellText = isset($cells[$c]) ? $this->cellToText($cells[$c]) : '';
                $line .= ' ' . $cellText . ' |';
            }

            $md .= $line . "\n";

            if (! $headerRendered) {
                $md .= '|' . str_repeat(' --- |', $colCount) . "\n";
                $headerRendered = true;
            }
        }

        return $md;
    }

    private function cellToText(TableCell $cell): string
    {
        $parts = [];

        foreach ($cell->getElements() as $el) {
            if ($el instanceof Paragraph) {
                $parts[] = $this->renderRuns($el->getRuns());
            }
        }

        $text = implode(' ', $parts);

        return str_replace('|', '\\|', $text);
    }

    private function renderImage(Image $image): string
    {
        $alt = $image->getAlt();
        $src = $image->getSrc();

        if ($src === '' && $image->hasData()) {
            $src = $image->getDataUri() ?? '';
        }

        return sprintf('![%s](%s)', $alt, $src);
    }
}
