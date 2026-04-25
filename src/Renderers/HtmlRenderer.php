<?php

declare(strict_types=1);

namespace Paperdoc\Renderers;

use Paperdoc\Contracts\{BlockElementInterface, DocumentElementInterface, DocumentInterface};
use Paperdoc\Document\{
    Blockquote,
    Bookmark,
    CodeBlock,
    Heading,
    Image,
    ListBlock,
    ListItem,
    PageBreak,
    Paragraph,
    Section,
    Table,
    TextRun,
};

class HtmlRenderer extends AbstractRenderer
{
    public function getFormat(): string { return 'html'; }

    public function render(DocumentInterface $document): string
    {
        $defaultStyle = $document->getDefaultTextStyle();
        $bodyStyle = sprintf(
            'font-family:%s,sans-serif;font-size:%spt;color:%s;max-width:800px;margin:0 auto;padding:40px 20px;',
            htmlspecialchars($defaultStyle->getFontFamily()),
            $defaultStyle->getFontSize(),
            htmlspecialchars($defaultStyle->getColor()),
        );

        $body = '';

        foreach ($document->getSections() as $section) {
            $body .= $this->renderSection($section);
        }

        $title = htmlspecialchars($document->getTitle());
        $charset = 'UTF-8';
        $lang = 'fr';

        return <<<HTML
        <!DOCTYPE html>
        <html lang="{$lang}">
        <head>
            <meta charset="{$charset}">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>{$title}</title>
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body { line-height: 1.6; }
                table { border-collapse: collapse; width: 100%; margin: 1em 0; }
                th, td { border: 1px solid #d1d5db; padding: 8px 12px; text-align: left; }
                th { background: #f3f4f6; font-weight: 600; }
                tr:nth-child(even) { background: #f9fafb; }
                img { max-width: 100%; height: auto; }
                section { margin-bottom: 2em; }
                .page-break { page-break-after: always; border-top: 2px dashed #d1d5db; margin: 2em 0; }
                blockquote { border-left: 4px solid #d1d5db; padding: 0.25em 1em; margin: 1em 0; color: #4b5563; font-style: italic; }
                blockquote > :last-child { margin-bottom: 0; }
                pre { background: #f3f4f6; padding: 1em; overflow-x: auto; border-radius: 6px; margin: 1em 0; }
                pre code { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 0.9em; white-space: pre; }
                ul, ol { margin: 1em 0; padding-left: 2em; }
                li { margin: 0.25em 0; }
            </style>
        </head>
        <body style="{$bodyStyle}">
        {$body}
        </body>
        </html>
        HTML;
    }

    private function renderSection(Section $section): string
    {
        $id = htmlspecialchars($section->getName());
        $html = "<section id=\"{$id}\">\n";

        foreach ($section->getElements() as $element) {
            $html .= $this->renderBlock($element);
        }

        $html .= "</section>\n";

        return $html;
    }

    private function renderBlock(DocumentElementInterface $element): string
    {
        return match (true) {
            $element instanceof Heading    => $this->renderHeading($element),
            $element instanceof Paragraph  => $this->renderParagraph($element),
            $element instanceof ListBlock  => $this->renderList($element),
            $element instanceof Blockquote => $this->renderBlockquote($element),
            $element instanceof CodeBlock  => $this->renderCodeBlock($element),
            $element instanceof Bookmark   => $this->renderBookmark($element),
            $element instanceof Table      => $this->renderTable($element),
            $element instanceof Image      => $this->renderImage($element),
            $element instanceof PageBreak  => "<div class=\"page-break\"></div>\n",
            default                        => '',
        };
    }

    private function renderHeading(Heading $heading): string
    {
        $level = max(1, min(6, $heading->getLevel()));
        $id = $heading->getId();
        $idAttr = $id !== '' ? sprintf(' id="%s"', htmlspecialchars($id)) : '';

        $content = '';
        foreach ($heading->getRuns() as $run) {
            $content .= $this->renderTextRun($run);
        }

        return "<h{$level}{$idAttr}>{$content}</h{$level}>\n";
    }

    private function renderList(ListBlock $list): string
    {
        $tag = $list->isOrdered() ? 'ol' : 'ul';
        $startAttr = '';

        if ($list->isOrdered() && $list->getStart() !== 1) {
            $startAttr = sprintf(' start="%d"', $list->getStart());
        }

        $html = "<{$tag}{$startAttr}>\n";

        foreach ($list->getItems() as $item) {
            $html .= $this->renderListItem($item);
        }

        $html .= "</{$tag}>\n";

        return $html;
    }

    private function renderListItem(ListItem $item): string
    {
        $content = '';
        foreach ($item->getRuns() as $run) {
            $content .= $this->renderTextRun($run);
        }

        $childHtml = '';
        foreach ($item->getBlocks() as $child) {
            $childHtml .= $this->renderBlock($child);
        }

        return "<li>{$content}{$childHtml}</li>\n";
    }

    private function renderBlockquote(Blockquote $quote): string
    {
        $inner = '';
        foreach ($quote->getElements() as $child) {
            if ($child instanceof BlockElementInterface) {
                $inner .= $this->renderBlock($child);
                continue;
            }

            if ($child instanceof DocumentElementInterface) {
                $inner .= $this->renderBlock($child);
            }
        }

        return "<blockquote>\n{$inner}</blockquote>\n";
    }

    private function renderCodeBlock(CodeBlock $code): string
    {
        $lang = $code->getLanguage();
        $class = $lang !== ''
            ? sprintf(' class="language-%s"', htmlspecialchars($lang))
            : '';

        $escaped = htmlspecialchars($code->getCode());

        return "<pre><code{$class}>{$escaped}</code></pre>\n";
    }

    private function renderBookmark(Bookmark $bookmark): string
    {
        return sprintf(
            "<a id=\"%s\" class=\"paperdoc-bookmark\"></a>\n",
            htmlspecialchars($bookmark->getId()),
        );
    }

    private function renderParagraph(Paragraph $paragraph): string
    {
        $style = $paragraph->getStyle();
        $headingLevel = $style?->getHeadingLevel();
        $css = '';

        if ($style) {
            $parts = [];
            $parts[] = 'text-align:' . $style->getAlignment()->value;

            if ($style->getSpaceBefore() > 0) {
                $parts[] = sprintf('margin-top:%spt', $style->getSpaceBefore());
            }

            if ($style->getSpaceAfter() > 0) {
                $parts[] = sprintf('margin-bottom:%spt', $style->getSpaceAfter());
            }

            if ($style->getLineSpacing() !== 1.0) {
                $parts[] = sprintf('line-height:%.2f', $style->getLineSpacing());
            }

            $css = ' style="' . implode(';', $parts) . '"';
        }

        $content = '';

        foreach ($paragraph->getRuns() as $run) {
            $content .= $this->renderTextRun($run);
        }

        if ($headingLevel !== null && $headingLevel >= 1 && $headingLevel <= 6) {
            $tag = 'h' . $headingLevel;

            return "<{$tag}{$css}>{$content}</{$tag}>\n";
        }

        return "<p{$css}>{$content}</p>\n";
    }

    private function renderTextRun(TextRun $run): string
    {
        $text = htmlspecialchars($run->getText());
        $style = $run->getStyle();
        $link = $run->getLink();

        if ($style === null && $link === null) {
            return $text;
        }

        $parts = [];
        if ($style) {
            $parts[] = sprintf('font-family:%s,sans-serif', htmlspecialchars($style->getFontFamily()));
            $parts[] = sprintf('font-size:%spt', $style->getFontSize());
            $parts[] = sprintf('color:%s', htmlspecialchars($style->getColor()));

            if ($style->isBold()) {
                $parts[] = 'font-weight:bold';
            }

            if ($style->isItalic()) {
                $parts[] = 'font-style:italic';
            }

            if ($style->isUnderline()) {
                $parts[] = 'text-decoration:underline';
            }
        }

        $css = implode(';', $parts);

        if ($link !== null) {
            $href = htmlspecialchars($link->getHref());
            $attrs = "href=\"{$href}\"";

            if ($css !== '') {
                $attrs .= " style=\"{$css}\"";
            }

            if ($link->getTitle() !== '') {
                $attrs .= sprintf(' title="%s"', htmlspecialchars($link->getTitle()));
            }

            if ($link->isExternal()) {
                $attrs .= ' target="_blank" rel="noopener noreferrer"';
            }

            return "<a {$attrs}>{$text}</a>";
        }

        return "<span style=\"{$css}\">{$text}</span>";
    }

    private function renderTable(Table $table): string
    {
        $style = $table->getStyle();
        $css = '';

        $headerBg = null;
        $stripedBg = null;

        if ($style) {
            $parts = [];
            $parts[] = sprintf('border-color:%s', htmlspecialchars($style->getBorderColor()));

            if ($style->getBorderWidth() > 0) {
                $parts[] = sprintf('border-width:%spx', $style->getBorderWidth());
            }

            $parts[] = sprintf('border-style:%s', $style->getBorderStyle()->value);

            if ($style->getCellPadding() > 0) {
                $parts[] = sprintf('--cell-padding:%spt', $style->getCellPadding());
            }

            $css = ' style="' . implode(';', $parts) . '"';
            $headerBg = $style->getHeaderBg();
            $stripedBg = $style->getStripedBg();
        }

        $html = "<table{$css}>\n";
        $headerDone = false;
        $dataRowIndex = 0;

        foreach ($table->getRows() as $row) {
            if ($row->isHeader() && ! $headerDone) {
                $html .= "<thead>\n";
            }

            $rowStyle = '';

            if ($row->isHeader() && $headerBg !== null) {
                $rowStyle = sprintf(' style="background:%s"', htmlspecialchars($headerBg));
            } elseif (! $row->isHeader() && $stripedBg !== null && $dataRowIndex % 2 === 1) {
                $rowStyle = sprintf(' style="background:%s"', htmlspecialchars($stripedBg));
            }

            $html .= "<tr{$rowStyle}>";
            $tag = $row->isHeader() ? 'th' : 'td';

            foreach ($row->getCells() as $cell) {
                $attrs = '';

                if ($cell->getColspan() > 1) {
                    $attrs .= sprintf(' colspan="%d"', $cell->getColspan());
                }

                if ($cell->getRowspan() > 1) {
                    $attrs .= sprintf(' rowspan="%d"', $cell->getRowspan());
                }

                $content = '';

                foreach ($cell->getElements() as $el) {
                    if ($el instanceof Paragraph) {
                        $content .= $this->renderParagraphInline($el);
                    }
                }

                $html .= "<{$tag}{$attrs}>{$content}</{$tag}>";
            }

            $html .= "</tr>\n";

            if ($row->isHeader()) {
                $html .= "</thead>\n<tbody>\n";
                $headerDone = true;
            } else {
                $dataRowIndex++;
            }
        }

        if ($headerDone) {
            $html .= "</tbody>\n";
        }

        $html .= "</table>\n";

        return $html;
    }

    private function renderParagraphInline(Paragraph $paragraph): string
    {
        $content = '';

        foreach ($paragraph->getRuns() as $run) {
            $content .= $this->renderTextRun($run);
        }

        return $content;
    }

    private function renderImage(Image $image): string
    {
        $src = $image->hasData()
            ? ($image->getDataUri() ?? $image->getSrc())
            : $image->getSrc();

        $src = htmlspecialchars($src);
        $alt = htmlspecialchars($image->getAlt());

        $attrs = "src=\"{$src}\" alt=\"{$alt}\"";

        if ($image->getWidth() > 0) {
            $attrs .= sprintf(' width="%d"', $image->getWidth());
        }

        if ($image->getHeight() > 0) {
            $attrs .= sprintf(' height="%d"', $image->getHeight());
        }

        return "<figure><img {$attrs}></figure>\n";
    }
}
