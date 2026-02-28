<?php

declare(strict_types=1);

namespace Pagina\Writers;

use Pagina\Contracts\DocumentInterface;
use Pagina\Document\{Image, PageBreak, Paragraph, Section, Table, TextRun};

/**
 * Writer HTML pur (aucune dépendance externe).
 */
class HtmlWriter extends AbstractWriter
{
    public function getFormat(): string { return 'html'; }

    public function write(DocumentInterface $document, string $filename): void
    {
        $this->ensureDirectoryWritable($filename);

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

        $html = <<<HTML
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
            </style>
        </head>
        <body style="{$bodyStyle}">
        {$body}
        </body>
        </html>
        HTML;

        file_put_contents($filename, $html);
    }

    private function renderSection(Section $section): string
    {
        $id = htmlspecialchars($section->getName());
        $html = "<section id=\"{$id}\">\n";

        foreach ($section->getElements() as $element) {
            $html .= match (true) {
                $element instanceof Paragraph => $this->renderParagraph($element),
                $element instanceof Table     => $this->renderTable($element),
                $element instanceof Image     => $this->renderImage($element),
                $element instanceof PageBreak => "<div class=\"page-break\"></div>\n",
                default                       => '',
            };
        }

        $html .= "</section>\n";

        return $html;
    }

    private function renderParagraph(Paragraph $paragraph): string
    {
        $style = $paragraph->getStyle();
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

        return "<p{$css}>{$content}</p>\n";
    }

    private function renderTextRun(TextRun $run): string
    {
        $text = htmlspecialchars($run->getText());
        $style = $run->getStyle();

        if ($style === null) {
            return $text;
        }

        $parts = [];
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

        $css = implode(';', $parts);

        return "<span style=\"{$css}\">{$text}</span>";
    }

    private function renderTable(Table $table): string
    {
        $style = $table->getStyle();
        $css = '';

        if ($style) {
            $parts = [];
            $parts[] = sprintf('border-color:%s', htmlspecialchars($style->getBorderColor()));

            if ($style->getBorderWidth() > 0) {
                $parts[] = sprintf('border-width:%spx', $style->getBorderWidth());
            }

            $css = ' style="' . implode(';', $parts) . '"';
        }

        $html = "<table{$css}>\n";
        $headerDone = false;

        foreach ($table->getRows() as $row) {
            if ($row->isHeader() && ! $headerDone) {
                $html .= "<thead>\n";
            }

            $html .= '<tr>';
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
        $src = htmlspecialchars($image->getSrc());
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
