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

    private ?\Paperdoc\Support\TocResolver $tocResolver = null;
    /** @var list<string> */
    private array $sectionFootnotes = [];

    public function render(DocumentInterface $document): string
    {
        $this->tocResolver = new \Paperdoc\Support\TocResolver($document);
        $parts = [];

        $frontMatter = $this->buildFrontMatterData($document);

        if ($frontMatter !== []) {
            $parts[] = $this->renderFrontMatter($frontMatter);
        }

        foreach ($document->getSections() as $section) {
            $parts[] = $this->renderSection($section);
        }

        return implode("\n", $parts);
    }

    /* ------------------------------------------------------------- */

    /**
     * Merges the typed document properties (v1.0.0) and the loose
     * metadata bag into a single frontmatter map. Typed keys win on
     * collision; empty typed fields are omitted entirely.
     *
     * @return array<string, mixed>
     */
    private function buildFrontMatterData(DocumentInterface $document): array
    {
        $data = [];

        $properties = $document instanceof \Paperdoc\Document\Document
            ? $document->getProperties()
            : null;

        if ($properties !== null) {
            if ($document->getTitle() !== '') {
                $data['title'] = $document->getTitle();
            }

            foreach ([
                'author'      => $properties->getAuthor(),
                'subject'     => $properties->getSubject(),
                'description' => $properties->getDescription(),
                'keywords'    => $properties->getKeywords(),
                'language'    => $properties->getLanguage(),
            ] as $key => $value) {
                if ($value !== '') {
                    $data[$key] = $value;
                }
            }

            if ($properties->getCreatedAt() !== null) {
                $data['created'] = $properties->getCreatedAt()->format('Y-m-d\TH:i:sP');
            }
            if ($properties->getModifiedAt() !== null) {
                $data['modified'] = $properties->getModifiedAt()->format('Y-m-d\TH:i:sP');
            }
        }

        foreach ($document->getMetadata() as $key => $value) {
            $data[$key] ??= $value;
        }

        return $data;
    }

    /** @param array<string, mixed> $metadata */
    private function renderFrontMatter(array $metadata): string
    {
        $yaml = "---\n";

        foreach ($metadata as $key => $value) {
            $yaml .= sprintf("%s: %s\n", $key, $this->yamlScalar($value));
        }

        $yaml .= "---\n";

        return $yaml;
    }

    /**
     * Serialises a frontmatter value as a YAML scalar. Plain strings
     * without YAML-sensitive characters are emitted as-is; anything
     * else (colons, quotes, leading/trailing spaces, non-strings) is
     * JSON-encoded — JSON scalars are valid YAML.
     */
    private function yamlScalar(mixed $value): string
    {
        if (! is_string($value)) {
            return (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $isPlainSafe = $value !== ''
            && ! preg_match('/[:#\[\]{}&*!|>\'"%@`\r\n\t]/', $value)
            && $value === trim($value);

        if ($isPlainSafe) {
            return $value;
        }

        return (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function renderSection(Section $section): string
    {
        $this->sectionFootnotes = [];
        $md = '';

        foreach ($section->getElements() as $element) {
            $md .= $this->renderBlock($element);
        }

        $md .= $this->renderFootnotesBlock();

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
            $element instanceof \Paperdoc\Document\TableOfContents => $this->renderTableOfContents($element),
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
                if ($run->getFootnote() !== null) {
                    $text .= $this->registerFootnoteMarker($run->getFootnote()->getText());
                }
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

            if ($style->isStrikethrough()) {
                $text = '~~' . $text . '~~';
            }

            if ($run->getFootnote() !== null) {
                $text .= $this->registerFootnoteMarker($run->getFootnote()->getText());
            }

            $md .= $text;
        }

        return $md;
    }

    /**
     * @phpstan-impure
     */
    private function registerFootnoteMarker(string $text): string
    {
        $this->sectionFootnotes[] = $text;

        return '[^' . count($this->sectionFootnotes) . ']';
    }

    private function renderFootnotesBlock(): string
    {
        $footnotes = $this->sectionFootnotes;
        if ($footnotes === []) {
            return '';
        }

        $md = '';

        foreach ($footnotes as $i => $text) {
            $md .= sprintf("[^%d]: %s\n", $i + 1, $text);
        }

        return $md . "\n";
    }

    private function renderTableOfContents(\Paperdoc\Document\TableOfContents $toc): string
    {
        $entries = $this->tocResolver?->entries($toc->getMaxLevel()) ?? [];

        if ($entries === []) {
            return '';
        }

        $md = $toc->getTitle() !== '' ? '## ' . $toc->getTitle() . "\n\n" : '';

        foreach ($entries as $entry) {
            $anchor = $entry['generated'] ? $this->githubSlug($entry['text']) : $entry['anchor'];
            $md .= str_repeat('  ', $entry['level'] - 1)
                . '- ' . $this->formatMarkdownLink($entry['text'], '#' . $anchor) . "\n";
        }

        return $md . "\n";
    }

    private function githubSlug(string $text): string
    {
        $slug = mb_strtolower(trim($text));
        $slug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $slug) ?? '';

        return preg_replace('/\s+/', '-', $slug) ?? '';
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
                $parts[] = $this->cellElementToInline($child);
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
