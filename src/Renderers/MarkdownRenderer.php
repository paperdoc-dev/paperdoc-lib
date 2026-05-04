<?php

declare(strict_types=1);

namespace Paperdoc\Renderers;

use Paperdoc\Contracts\{DocumentElementInterface, DocumentInterface};
use Paperdoc\Document\{
    Blockquote,
    Bookmark,
    CodeBlock,
    Heading,
    HorizontalRule,
    Image,
    ListBlock,
    ListItem,
    PageBreak,
    Paragraph,
    Section,
    Table,
    TableCell,
    TextRun,
};

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
            $md .= $this->renderBlock($element);
        }

        return $md;
    }

    private function renderBlock(DocumentElementInterface $element): string
    {
        return match (true) {
            $element instanceof Heading        => $this->renderHeading($element) . "\n\n",
            $element instanceof Paragraph      => $this->renderParagraph($element) . "\n\n",
            $element instanceof ListBlock      => $this->renderList($element, 0) . "\n",
            $element instanceof Blockquote     => $this->renderBlockquote($element) . "\n",
            $element instanceof CodeBlock      => $this->renderCodeBlock($element) . "\n\n",
            $element instanceof Bookmark       => $this->renderBookmark($element) . "\n\n",
            $element instanceof Table          => $this->renderTable($element) . "\n",
            $element instanceof Image          => $this->renderImage($element) . "\n\n",
            // Markdown's CommonMark thematic-break: a line of dashes
            // surrounded by blank lines. The visual width / colour
            // properties of HorizontalRule are lost in MD (the format
            // has no notion of those) — we keep the semantic break.
            $element instanceof HorizontalRule => "---\n\n",
            $element instanceof PageBreak      => "---\n\n",
            default                            => '',
        };
    }

    private function renderHeading(Heading $heading): string
    {
        $level = max(1, min(6, $heading->getLevel()));
        $content = $this->renderRuns($heading->getRuns(), true);

        if ($heading->hasId()) {
            $content = rtrim($content) . ' {#' . $heading->getId() . '}';
        }

        return str_repeat('#', $level) . ' ' . $content;
    }

    private function renderList(ListBlock $list, int $depth): string
    {
        $md = '';
        $indent = str_repeat('  ', $depth);
        $counter = $list->getStart();

        foreach ($list->getItems() as $item) {
            $marker = $list->isOrdered() ? ($counter . '. ') : '- ';
            $label = $this->renderRuns($item->getRuns());
            $md .= $indent . $marker . $label . "\n";

            foreach ($item->getBlocks() as $child) {
                if ($child instanceof ListBlock) {
                    $md .= $this->renderList($child, $depth + 1);
                } else {
                    foreach (explode("\n", rtrim($this->renderBlock($child))) as $line) {
                        if ($line === '') {
                            continue;
                        }
                        $md .= $indent . '  ' . $line . "\n";
                    }
                }
            }

            if ($list->isOrdered()) {
                $counter++;
            }
        }

        return $md;
    }

    private function renderBlockquote(Blockquote $quote): string
    {
        $inner = '';

        foreach ($quote->getElements() as $child) {
            if (! $child instanceof DocumentElementInterface) {
                continue;
            }
            $inner .= $this->renderBlock($child);
        }

        $inner = rtrim($inner);

        if ($inner === '') {
            return '';
        }

        $lines = explode("\n", $inner);
        $md = '';

        foreach ($lines as $line) {
            $md .= '> ' . $line . "\n";
        }

        return $md;
    }

    private function renderCodeBlock(CodeBlock $code): string
    {
        $lang = $code->getLanguage();
        $fence = "```" . $lang;

        return $fence . "\n" . $code->getCode() . "\n```";
    }

    private function renderBookmark(Bookmark $bookmark): string
    {
        return sprintf('<a id="%s"></a>', htmlspecialchars($bookmark->getId()));
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

            $link = $run->getLink();
            if ($link !== null) {
                $text = $this->formatMarkdownLink($text, $link->getHref(), $link->getTitle());
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

            $md .= $text;
        }

        return $md;
    }

    /**
     * Build a safe `[label](url "title")` inline link.
     * Escapes characters that would break markdown link syntax.
     */
    private function formatMarkdownLink(string $label, string $href, string $title = ''): string
    {
        $label = strtr($label, ['[' => '\\[', ']' => '\\]']);

        // URLs containing spaces or parentheses are wrapped with angle brackets.
        if ($href === '' || preg_match('/[\s()<>]/', $href) === 1) {
            $href = '<' . str_replace(['<', '>'], ['%3C', '%3E'], $href) . '>';
        }

        if ($title !== '') {
            $title = str_replace('"', '\\"', $title);
            return sprintf('[%s](%s "%s")', $label, $href, $title);
        }

        return sprintf('[%s](%s)', $label, $href);
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
            $piece = $this->cellElementToInline($el);
            if ($piece !== '') {
                $parts[] = $piece;
            }
        }

        // Pipe tables are single-line; collapse newlines that may have
        // slipped in from inline-rendered code or quoted blocks.
        $text = preg_replace('/\s*\n+\s*/', ' ', implode(' ', $parts)) ?? '';

        // Escape pipes so the cell never breaks the column separator.
        return str_replace('|', '\\|', $text);
    }

    /**
     * Render any block element so it remains valid inside a single
     * Markdown table cell. Multi-line blocks (lists, blockquotes,
     * code blocks) are flattened to a one-line representation.
     */
    private function cellElementToInline(DocumentElementInterface $element): string
    {
        if ($element instanceof Paragraph) {
            return $this->renderRuns($element->getRuns());
        }

        if ($element instanceof Heading) {
            return $this->renderRuns($element->getRuns());
        }

        if ($element instanceof Image) {
            return rtrim($this->renderImage($element));
        }

        if ($element instanceof CodeBlock) {
            $code = preg_replace('/\s+/', ' ', trim($element->getCode())) ?? '';
            return $code === '' ? '' : '`' . $code . '`';
        }

        if ($element instanceof ListBlock) {
            $items = [];
            foreach ($element->getItems() as $item) {
                $items[] = $this->renderRuns($item->getRuns());
            }
            return implode(', ', array_filter($items, fn (string $s) => $s !== ''));
        }

        if ($element instanceof Blockquote) {
            $parts = [];
            foreach ($element->getElements() as $child) {
                if ($child instanceof DocumentElementInterface) {
                    $parts[] = $this->cellElementToInline($child);
                }
            }
            return implode(' ', array_filter($parts, fn (string $s) => $s !== ''));
        }

        // Bookmarks and other invisible / structural elements have no
        // meaningful inline representation in a pipe table cell.
        return '';
    }

    private function renderImage(Image $image): string
    {
        $alt = $image->getAlt();
        $src = $image->hasData()
            ? ($image->getDataUri() ?? $image->getSrc())
            : $image->getSrc();

        return sprintf('![%s](%s)', $alt, $src);
    }
}
