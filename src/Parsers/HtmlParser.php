<?php

declare(strict_types=1);

namespace Paperdoc\Parsers;

use Paperdoc\Contracts\{DocumentInterface, ParserInterface};
use Paperdoc\Document\{Document, Image, ListBlock, ListItem, PageBreak, Paragraph, Section, Table, TableCell, TableRow, TextRun};
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Document\Style\{ParagraphStyle, TableStyle, TextStyle};
use Paperdoc\Enum\Alignment;

/**
 * Parser HTML natif utilisant l'extension DOM de PHP.
 *
 * Convertit un fichier HTML en modèle Document unifié.
 */
class HtmlParser extends AbstractParser implements ParserInterface
{
    /**
     * Tags parseNode() turns into document elements. Their presence inside an
     * unhandled wrapper means the wrapper has to be descended into, not flattened.
     */
    private const BLOCK_ELEMENTS = [
        'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'table',
        'img', 'figure', 'hr', 'div', 'section', 'article', 'main',
        'header', 'footer', 'nav',
    ];

    public function supports(string $extension): bool
    {
        return in_array(strtolower($extension), ['html', 'htm'], true);
    }

    public function parse(string $filename): DocumentInterface
    {
        $this->assertFileReadable($filename);

        $html = file_get_contents($filename);

        if ($html === false) {
            throw new \RuntimeException("Impossible de lire le fichier HTML : {$filename}");
        }

        $document = new Document('html');

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);

        if (! $this->declaresCharset($html)) {
            $html = '<?xml encoding="UTF-8">' . $html;
        }

        $dom->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        $title = $dom->getElementsByTagName('title')->item(0);

        if ($title) {
            $document->setTitle($title->textContent);
        }

        $body = $dom->getElementsByTagName('body')->item(0);

        if (! $body) {
            return $document;
        }

        $sections = [];
        $loose = new Section('main');

        $this->collectSections($body, $loose, $sections);

        if ($sections === [] || count($loose->getElements()) > 0) {
            $sections[] = $loose;
        }

        foreach ($sections as $section) {
            $document->addSection($section);
        }

        return $document;
    }

    /**
     * Walks the body in document order, opening a new Section on each <section>.
     * Content sitting outside any <section> lands in $loose so it is never
     * dropped — a <table> before the first <section> used to vanish entirely.
     *
     * @param array<int, Section> $sections
     */
    private function collectSections(\DOMNode $parent, Section &$loose, array &$sections): void
    {
        foreach ($parent->childNodes as $node) {
            if (! $node instanceof \DOMElement) {
                $this->parseNode($node, $loose);

                continue;
            }

            if (strtolower($node->nodeName) === 'section') {
                if (count($loose->getElements()) > 0) {
                    $sections[] = $loose;
                    $loose = new Section('main');
                }

                $section = new Section($node->getAttribute('id'));
                $this->parseChildNodes($node, $section);
                $sections[] = $section;

                continue;
            }

            // A wrapper may hold <section> children; descend so their
            // boundaries are honoured wherever they sit in the tree.
            if ($node->getElementsByTagName('section')->length > 0) {
                $this->collectSections($node, $loose, $sections);

                continue;
            }

            $this->parseNode($node, $loose);
        }
    }

    /**
     * Un document qui déclare son encodage ne doit pas se voir imposer
     * UTF-8. La déclaration ne peut vivre que dans le prologue : chercher
     * « encoding » dans tout le fichier faisait passer pour déclaré
     * n'importe quel texte employant le mot, et libxml retombait alors
     * sur ISO-8859-1 — mojibake sur tout contenu non ASCII.
     */
    private function declaresCharset(string $html): bool
    {
        $bodyStart = stripos($html, '<body');
        $prologue  = substr($html, 0, $bodyStart === false ? 4096 : $bodyStart);

        return (bool) preg_match(
            '/<\?xml[^>]*\bencoding\s*=|<meta[^>]*\bcharset\s*=/i',
            $prologue,
        );
    }

    private function parseChildNodes(\DOMNode $parent, Section $section): void
    {
        foreach ($parent->childNodes as $node) {
            $this->parseNode($node, $section);
        }
    }

    private function parseNode(\DOMNode $node, Section $section): void
    {
        if ($node->nodeType !== XML_ELEMENT_NODE) {
            if ($node->nodeType === XML_TEXT_NODE && trim($node->textContent) !== '') {
                $section->addText($node->textContent);
            }

            return;
        }

        /** @var \DOMElement $node */
        match (strtolower($node->nodeName)) {
            'p'          => $this->parseParagraph($node, $section),
            'h1'         => $section->addHeading($node->textContent, 1),
            'h2'         => $section->addHeading($node->textContent, 2),
            'h3'         => $section->addHeading($node->textContent, 3),
            'h4'         => $section->addHeading($node->textContent, 4),
            'h5'         => $section->addHeading($node->textContent, 5),
            'h6'         => $section->addHeading($node->textContent, 6),
            'ul', 'ol'   => $this->parseList($node, $section),
            'table'      => $this->parseTable($node, $section),
            'img'        => $this->parseImage($node, $section),
            'figure'     => $this->parseFigure($node, $section),
            'hr'         => $section->addPageBreak(),
            'div', 'article', 'main', 'header', 'footer', 'nav', 'section'
                         => $this->parseChildNodes($node, $section),
            default      => $this->parseFallbackElement($node, $section),
        };
    }

    private function parseParagraph(\DOMElement $node, Section $section): void
    {
        $paragraph = new Paragraph();

        $style = $this->extractParagraphStyle($node);
        if ($style) {
            $paragraph->setStyle($style);
        }

        $this->parseInlineContent($node, $paragraph);

        if (count($paragraph->getRuns()) > 0) {
            $section->addElement($paragraph);
        }
    }

    /**
     * $link is inherited from an enclosing <a>, so every run produced below
     * an anchor keeps the hyperlink — including nested <strong>, <em>…
     */
    private function parseInlineContent(\DOMNode $parent, Paragraph $paragraph, ?TextLink $link = null): void
    {
        foreach ($parent->childNodes as $child) {
            $this->parseInlineNode($child, $paragraph, $link);
        }
    }

    private function parseInlineNode(\DOMNode $node, Paragraph $paragraph, ?TextLink $link = null): void
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            $text = $node->textContent;

            if (trim($text) !== '') {
                $paragraph->addRun(new TextRun($text, null, $link));
            }

            return;
        }

        if ($node->nodeType !== XML_ELEMENT_NODE) {
            return;
        }

        /** @var \DOMElement $node */
        $tag = strtolower($node->nodeName);

        $textStyle = $this->extractTextStyle($node);

        match ($tag) {
            'strong', 'b' => $this->addStyledRun($node, $paragraph, (clone ($textStyle ?? TextStyle::make()))->setBold(), $link),
            'em', 'i'    => $this->addStyledRun($node, $paragraph, (clone ($textStyle ?? TextStyle::make()))->setItalic(), $link),
            'u'          => $this->addStyledRun($node, $paragraph, (clone ($textStyle ?? TextStyle::make()))->setUnderline(), $link),
            'span'       => $this->addStyledRun($node, $paragraph, $textStyle, $link),
            'a'          => $this->addStyledRun($node, $paragraph, $textStyle, $this->extractLink($node) ?? $link),
            'br'         => $paragraph->addRun(new TextRun("\n", null, $link)),
            default      => $paragraph->addRun(new TextRun($node->textContent, $textStyle, $link)),
        };
    }

    private function addStyledRun(\DOMElement $node, Paragraph $paragraph, ?TextStyle $style, ?TextLink $link = null): void
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                $paragraph->addRun(new TextRun($child->textContent, $style, $link));
            } elseif ($child->nodeType === XML_ELEMENT_NODE) {
                $this->parseInlineContent($node, $paragraph, $link);

                return;
            }
        }
    }

    /**
     * A bare fragment href ("#intro") becomes an internal anchor, anything
     * else an external URL — TextLink::getHref() recomposes both.
     */
    private function extractLink(\DOMElement $node): ?TextLink
    {
        $href = trim($node->getAttribute('href'));

        if ($href === '') {
            return null;
        }

        $title = $node->getAttribute('title');

        if (str_starts_with($href, '#')) {
            return TextLink::make('', ltrim($href, '#'), $title);
        }

        return TextLink::make($href, '', $title);
    }

    private function parseList(\DOMElement $node, Section $section): void
    {
        $list = $this->buildList($node);

        if (! $list->isEmpty()) {
            $section->addElement($list);
        }
    }

    /**
     * Build a ListBlock from a <ul>/<ol> node. Nested lists are attached
     * to their parent ListItem, so the recursion mirrors the markup.
     */
    private function buildList(\DOMElement $node): ListBlock
    {
        $isOrdered = strtolower($node->nodeName) === 'ol';

        $start = $isOrdered ? filter_var($node->getAttribute('start'), FILTER_VALIDATE_INT) : false;

        $list = new ListBlock(
            $isOrdered ? ListBlock::STYLE_ORDERED : ListBlock::STYLE_BULLET,
            $start === false ? 1 : $start,
        );

        foreach ($node->childNodes as $child) {
            if (! $child instanceof \DOMElement) {
                continue;
            }

            $tag = strtolower($child->nodeName);

            if ($tag === 'li') {
                $list->addItem($this->parseListItem($child));

                continue;
            }

            // Some editors and Word exports place a nested list as a sibling
            // of the <li> it belongs to rather than inside it.
            if ($tag === 'ul' || $tag === 'ol') {
                $items = $list->getItems();
                $parent = $items === [] ? $list->addText('') : end($items);
                $parent->addList($this->buildList($child));
            }
        }

        return $list;
    }

    private function parseListItem(\DOMElement $node): ListItem
    {
        $item = new ListItem();

        // Runs are collected through a scratch Paragraph so <li> labels get
        // the same inline handling (bold, italic, styled spans) as elsewhere.
        $label = new Paragraph();

        foreach ($node->childNodes as $child) {
            $tag = $child instanceof \DOMElement ? strtolower($child->nodeName) : '';

            match ($tag) {
                'ul', 'ol' => $item->addList($this->buildList($child)),
                // Block wrappers around the label — <li><p>text</p></li> is
                // what rich-text editors emit.
                'p', 'div' => $this->parseInlineContent($child, $label),
                default    => $this->parseInlineNode($child, $label),
            };
        }

        foreach ($label->getRuns() as $run) {
            $item->addRun($run);
        }

        return $item;
    }

    private function parseTable(\DOMElement $node, Section $section): void
    {
        $table = new Table();

        $tableStyle = $this->extractTableStyle($node);
        if ($tableStyle) {
            $table->setStyle($tableStyle);
        }

        $rows = [];
        foreach ($node->getElementsByTagName('tr') as $tr) {
            $rows[] = $tr;
        }

        $isFirstRow = true;

        foreach ($rows as $tr) {
            $row = new TableRow();
            $isHeader = false;

            foreach ($tr->childNodes as $td) {
                if (! $td instanceof \DOMElement) {
                    continue;
                }

                $tag = strtolower($td->nodeName);

                if ($tag !== 'td' && $tag !== 'th') {
                    continue;
                }

                if ($tag === 'th') {
                    $isHeader = true;
                }

                $cell = new TableCell();

                $colspanAttr = $td->getAttribute('colspan');
                if ($colspanAttr) {
                    $cell->setColspan((int) $colspanAttr);
                }

                $rowspanAttr = $td->getAttribute('rowspan');
                if ($rowspanAttr) {
                    $cell->setRowspan((int) $rowspanAttr);
                }

                $p = new Paragraph();
                $this->parseInlineContent($td, $p);

                if (count($p->getRuns()) > 0) {
                    $cell->addElement($p);
                } else {
                    $cell->addElement((new Paragraph())->addRun(new TextRun($td->textContent)));
                }

                $row->addCell($cell);
            }

            if ($isHeader || ($isFirstRow && $tr->parentNode && strtolower($tr->parentNode->nodeName) === 'thead')) {
                $row->setHeader();
            }

            $table->addRow($row);
            $isFirstRow = false;
        }

        $section->addElement($table);
    }

    private function parseImage(\DOMElement $node, Section $section): void
    {
        $src = $node->getAttribute('src');
        $alt = $node->getAttribute('alt');
        $width = (int) ($node->getAttribute('width') ?: 0);
        $height = (int) ($node->getAttribute('height') ?: 0);

        if ($src !== '') {
            $section->addElement(new Image($src, $width, $height, $alt));
        }
    }

    private function parseFigure(\DOMElement $node, Section $section): void
    {
        $img = $node->getElementsByTagName('img')->item(0);

        if ($img instanceof \DOMElement) {
            $this->parseImage($img, $section);
        }
    }

    private function parseFallbackElement(\DOMElement $node, Section $section): void
    {
        // Unhandled wrappers (<aside>, <blockquote>, <form>, <details>…) must
        // not swallow the blocks they carry: flattening to textContent turns a
        // nested <table> into a single text run.
        if ($this->containsBlockElement($node)) {
            $this->parseChildNodes($node, $section);

            return;
        }

        $text = trim($node->textContent);

        if ($text !== '') {
            $section->addText($text);
        }
    }

    private function containsBlockElement(\DOMElement $node): bool
    {
        foreach ($node->childNodes as $child) {
            if (! $child instanceof \DOMElement) {
                continue;
            }

            if (in_array(strtolower($child->nodeName), self::BLOCK_ELEMENTS, true)) {
                return true;
            }

            if ($this->containsBlockElement($child)) {
                return true;
            }
        }

        return false;
    }

    /* -------------------------------------------------------------
     | Style Extraction
     |------------------------------------------------------------- */

    private function extractTableStyle(\DOMElement $node): ?TableStyle
    {
        $css = $node->getAttribute('style');

        if (! $css) {
            return null;
        }

        $props = $this->parseCssProperties($css);
        $style = TableStyle::make();
        $hasProps = false;

        if (isset($props['border-color'])) {
            $style->setBorderColor($props['border-color']);
            $hasProps = true;
        }

        if (isset($props['border-width'])) {
            $style->setBorderWidth($this->parsePtValue($props['border-width']));
            $hasProps = true;
        }

        if (isset($props['border-style'])) {
            $bs = \Paperdoc\Enum\BorderStyle::tryFrom($props['border-style']);

            if ($bs) {
                $style->setBorderStyle($bs);
                $hasProps = true;
            }
        }

        return $hasProps ? $style : null;
    }

    private function extractParagraphStyle(\DOMElement $node): ?ParagraphStyle
    {
        $cssStyle = $node->getAttribute('style');

        if (! $cssStyle) {
            return null;
        }

        $style = ParagraphStyle::make();
        $props = $this->parseCssProperties($cssStyle);

        if (isset($props['text-align'])) {
            $alignment = Alignment::tryFrom($props['text-align']);

            if ($alignment) {
                $style->setAlignment($alignment);
            }
        }

        if (isset($props['margin-top'])) {
            $style->setSpaceBefore($this->parsePtValue($props['margin-top']));
        }

        if (isset($props['margin-bottom'])) {
            $style->setSpaceAfter($this->parsePtValue($props['margin-bottom']));
        }

        if (isset($props['line-height'])) {
            $style->setLineSpacing((float) $props['line-height']);
        }

        return $style;
    }

    private function extractTextStyle(\DOMElement $node): ?TextStyle
    {
        $cssStyle = $node->getAttribute('style');

        if (! $cssStyle) {
            return null;
        }

        $style = TextStyle::make();
        $props = $this->parseCssProperties($cssStyle);

        if (isset($props['font-family'])) {
            $family = explode(',', $props['font-family'])[0];
            $style->setFontFamily(trim($family, " '\""));
        }

        if (isset($props['font-size'])) {
            $style->setFontSize($this->parsePtValue($props['font-size']));
        }

        if (isset($props['color'])) {
            $style->setColor($props['color']);
        }

        if (isset($props['font-weight']) && $props['font-weight'] === 'bold') {
            $style->setBold();
        }

        if (isset($props['font-style']) && $props['font-style'] === 'italic') {
            $style->setItalic();
        }

        if (isset($props['text-decoration']) && str_contains($props['text-decoration'], 'underline')) {
            $style->setUnderline();
        }

        return $style;
    }

    /**
     * @return array<string, string>
     */
    private function parseCssProperties(string $css): array
    {
        $props = [];
        $pairs = explode(';', $css);

        foreach ($pairs as $pair) {
            $pair = trim($pair);

            if ($pair === '') {
                continue;
            }

            $parts = explode(':', $pair, 2);

            if (count($parts) === 2) {
                $props[trim($parts[0])] = trim($parts[1]);
            }
        }

        return $props;
    }

    private function parsePtValue(string $value): float
    {
        $value = trim($value);

        if (str_ends_with($value, 'pt')) {
            return (float) rtrim($value, 'pt');
        }

        if (str_ends_with($value, 'px')) {
            return (float) rtrim($value, 'px') * 0.75;
        }

        if (str_ends_with($value, 'em')) {
            return (float) rtrim($value, 'em') * 12;
        }

        return (float) $value;
    }
}
