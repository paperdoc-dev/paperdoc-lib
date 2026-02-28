<?php

declare(strict_types=1);

namespace Pagina\Parsers;

use Pagina\Contracts\{DocumentInterface, ParserInterface};
use Pagina\Document\{Document, Image, Paragraph, Section, Table, TableCell, TableRow, TextRun};
use Pagina\Document\Style\{ParagraphStyle, TextStyle};
use Pagina\Enum\Alignment;

/**
 * Parser HTML natif utilisant l'extension DOM de PHP.
 *
 * Convertit un fichier HTML en modèle Document unifié.
 */
class HtmlParser extends AbstractParser implements ParserInterface
{
    public function supports(string $extension): bool
    {
        return in_array(strtolower($extension), ['html', 'htm'], true);
    }

    public function parse(string $filename): DocumentInterface
    {
        $this->assertFileReadable($filename);

        $html = file_get_contents($filename);
        $document = new Document('html');

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOWARNING);
        libxml_clear_errors();

        $title = $dom->getElementsByTagName('title')->item(0);

        if ($title) {
            $document->setTitle($title->textContent);
        }

        $body = $dom->getElementsByTagName('body')->item(0);

        if (! $body) {
            return $document;
        }

        $sections = $dom->getElementsByTagName('section');

        if ($sections->length > 0) {
            foreach ($sections as $sectionNode) {
                $section = new Section($sectionNode->getAttribute('id') ?? '');
                $this->parseChildNodes($sectionNode, $section);
                $document->addSection($section);
            }
        } else {
            $section = new Section('main');
            $this->parseChildNodes($body, $section);
            $document->addSection($section);
        }

        return $document;
    }

    private function parseChildNodes(\DOMNode $parent, Section $section): void
    {
        foreach ($parent->childNodes as $node) {
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                if ($node->nodeType === XML_TEXT_NODE && trim($node->textContent) !== '') {
                    $section->addText($node->textContent);
                }

                continue;
            }

            /** @var \DOMElement $node */
            match (strtolower($node->nodeName)) {
                'p'          => $this->parseParagraph($node, $section),
                'h1'         => $section->addHeading($node->textContent, 1),
                'h2'         => $section->addHeading($node->textContent, 2),
                'h3'         => $section->addHeading($node->textContent, 3),
                'h4', 'h5', 'h6' => $section->addHeading($node->textContent, 4),
                'table'      => $this->parseTable($node, $section),
                'img'        => $this->parseImage($node, $section),
                'figure'     => $this->parseFigure($node, $section),
                'div', 'article', 'main', 'header', 'footer', 'nav'
                             => $this->parseChildNodes($node, $section),
                default      => $this->parseFallbackElement($node, $section),
            };
        }
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

    private function parseInlineContent(\DOMNode $parent, Paragraph $paragraph): void
    {
        foreach ($parent->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                $text = $child->textContent;

                if ($text !== '') {
                    $paragraph->addRun(new TextRun($text));
                }

                continue;
            }

            if ($child->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            /** @var \DOMElement $child */
            $tag = strtolower($child->nodeName);

            $textStyle = $this->extractTextStyle($child);

            match ($tag) {
                'strong', 'b' => $this->addStyledRun($child, $paragraph, (clone ($textStyle ?? TextStyle::make()))->setBold()),
                'em', 'i'    => $this->addStyledRun($child, $paragraph, (clone ($textStyle ?? TextStyle::make()))->setItalic()),
                'u'          => $this->addStyledRun($child, $paragraph, (clone ($textStyle ?? TextStyle::make()))->setUnderline()),
                'span'       => $this->addStyledRun($child, $paragraph, $textStyle),
                'br'         => $paragraph->addRun(new TextRun("\n")),
                default      => $paragraph->addRun(new TextRun($child->textContent, $textStyle)),
            };
        }
    }

    private function addStyledRun(\DOMElement $node, Paragraph $paragraph, ?TextStyle $style): void
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                $paragraph->addRun(new TextRun($child->textContent, $style));
            } elseif ($child->nodeType === XML_ELEMENT_NODE) {
                $this->parseInlineContent($node, $paragraph);

                return;
            }
        }
    }

    private function parseTable(\DOMElement $node, Section $section): void
    {
        $table = new Table();

        $rows = [];
        foreach ($node->getElementsByTagName('tr') as $tr) {
            $rows[] = $tr;
        }

        $isFirstRow = true;

        foreach ($rows as $tr) {
            $row = new TableRow();
            $isHeader = false;

            foreach ($tr->childNodes as $td) {
                if ($td->nodeType !== XML_ELEMENT_NODE) {
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
        $src = $node->getAttribute('src') ?? '';
        $alt = $node->getAttribute('alt') ?? '';
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
        $text = trim($node->textContent);

        if ($text !== '') {
            $section->addText($text);
        }
    }

    /* -------------------------------------------------------------
     | Style Extraction
     |------------------------------------------------------------- */

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
