<?php

declare(strict_types=1);

namespace Paperdoc\Renderers;

use Paperdoc\Contracts\{BlockElementInterface, DocumentElementInterface, DocumentInterface};
use Paperdoc\Document\{
    Blockquote,
    Bookmark,
    CodeBlock,
    Document,
    Heading,
    HorizontalRule,
    Image,
    ListBlock,
    ListItem,
    PageBreak,
    Paragraph,
    Section,
    Table,
    TextRun,
    TextZone,
};
use Paperdoc\Document\Style\{PageSetup, RunningElement};
use Paperdoc\Enum\{Alignment, VerticalAlignment};
use Paperdoc\Support\TextDirection;

class HtmlRenderer extends AbstractRenderer
{
    public function getFormat(): string { return 'html'; }

    private ?\Paperdoc\Support\TocResolver $tocResolver = null;
    private bool $autoHeadingIds = false;
    private ?\Paperdoc\Document\Style\Watermark $watermark = null;
    /** @var list<string> */
    private array $sectionFootnotes = [];

    /**
     * Maps the document's typed {@see \Paperdoc\Document\Metadata} to
     * standard <head> meta tags (author, description, keywords,
     * generator, dcterms dates). Returns '' when there is nothing to
     * emit so the head stays minimal.
     */
    private function buildHeadMetaTags(?\Paperdoc\Document\Metadata $properties): string
    {
        $tags = [];

        if ($properties !== null) {
            $pairs = [
                'author'      => $properties->getAuthor(),
                'description' => $properties->getDescription() !== ''
                    ? $properties->getDescription()
                    : $properties->getSubject(),
                'keywords'    => $properties->getKeywords(),
            ];

            foreach ($pairs as $name => $content) {
                if ($content !== '') {
                    $tags[] = sprintf('<meta name="%s" content="%s">', $name, htmlspecialchars($content));
                }
            }

            if ($properties->getCreatedAt() !== null) {
                $tags[] = sprintf(
                    '<meta name="dcterms.created" content="%s">',
                    $properties->getCreatedAt()->format('Y-m-d\TH:i:sP'),
                );
            }
            if ($properties->getModifiedAt() !== null) {
                $tags[] = sprintf(
                    '<meta name="dcterms.modified" content="%s">',
                    $properties->getModifiedAt()->format('Y-m-d\TH:i:sP'),
                );
            }
        }

        if ($tags === []) {
            return '';
        }

        return "\n    " . implode("\n    ", $tags);
    }

    public function render(DocumentInterface $document): string
    {
        $this->tocResolver = new \Paperdoc\Support\TocResolver($document);
        $this->autoHeadingIds = false;

        foreach ($document->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \Paperdoc\Document\TableOfContents) {
                    $this->autoHeadingIds = true;
                    break 2;
                }
            }
        }

        $defaultStyle = $document->getDefaultTextStyle();
        $bodyStyle = sprintf(
            'font-family:%s,sans-serif;font-size:%spt;color:%s;',
            htmlspecialchars($defaultStyle->getFontFamily()),
            $defaultStyle->getFontSize(),
            htmlspecialchars($defaultStyle->getColor()),
        );

        $documentHeader = $document instanceof Document ? $document->getHeader() : null;
        $documentFooter = $document instanceof Document ? $document->getFooter() : null;
        $this->watermark = $document instanceof Document ? $document->getWatermark() : null;

        $sections   = $document->getSections();
        $totalPages = max(1, count($sections));
        $title      = $document->getTitle();

        $body = '';

        foreach ($sections as $i => $section) {
            $pageNumber = $i + 1;

            // Per-section override / hide flags (since v0.8.0). The
            // section decides which (if any) running element to draw,
            // falling back to the document-level ones unless an
            // explicit hideHeader()/hideFooter() was set.
            $sectionHeader = $section->resolveHeader($documentHeader);
            $sectionFooter = $section->resolveFooter($documentFooter);

            $body .= $this->renderSection($section, $sectionHeader, $sectionFooter, $pageNumber, $totalPages, $title);
        }

        $title = htmlspecialchars($document->getTitle());
        $charset = 'UTF-8';

        // <html lang> and the <head> meta tags come from the document's
        // typed properties (v1.0.0). Fallback: English, no extra meta.
        $properties = $document instanceof Document ? $document->getProperties() : null;
        $language   = $properties !== null ? $properties->getLanguage() : '';
        $langAttr   = htmlspecialchars($language !== '' ? $language : 'en');
        $metaTags   = $this->buildHeadMetaTags($properties);

        // Déclarer le sens laisse le navigateur appliquer l'algorithme
        // bidirectionnel Unicode : ponctuation, chiffres et mots latins
        // insérés dans de l'arabe ou de l'hébreu se placent correctement.
        $dirAttr = TextDirection::detect(strip_tags($body));

        return <<<HTML
        <!DOCTYPE html>
        <html lang="{$langAttr}" dir="{$dirAttr}">
        <head>
            <meta charset="{$charset}">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">{$metaTags}
            <title>{$title}</title>
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body { line-height: 1.6; background: #f3f4f6; }
                table { border-collapse: collapse; width: 100%; margin: 1em 0; }
                th, td { border: 1px solid #d1d5db; padding: 8px 12px; text-align: left; }
                th { background: #f3f4f6; font-weight: 600; }
                tr:nth-child(even) { background: #f9fafb; }
                img { max-width: 100%; height: auto; }
                section.paperdoc-page {
                    background: #ffffff;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                    margin: 24px auto;
                    background-size: cover;
                    background-position: center center;
                    background-repeat: no-repeat;
                    position: relative;
                    overflow: hidden;
                }
                section.paperdoc-page.flow {
                    max-width: 800px;
                    padding: 40px 20px;
                }
                /*
                 * v0.8.0: Section::setVerticalAlignment() wraps the body
                 * in this element. The wrapping section uses flexbox to
                 * align it (justify-content: center / flex-end), so the
                 * body itself doesn't need to grow.
                 */
                .paperdoc-section-body { flex: 0 0 auto; }
                .paperdoc-text-zone {
                    position: absolute;
                    box-sizing: border-box;
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                    overflow: hidden;
                }
                .paperdoc-text-zone p,
                .paperdoc-text-zone p[style] { margin: 0 !important; }
                .paperdoc-text-zone p + p { margin-top: 0.4em !important; }
                .paperdoc-text-zone.visible { overflow: visible; }
                /*
                 * Inner clamp: pure max-height + overflow:hidden. This
                 * approach clips at an exact integer number of lines
                 * (max-height = N × line-height in pt) regardless of
                 * browser, with no reliance on -webkit-line-clamp which
                 * has been observed to leak content past N lines when
                 * the parent box has a fixed height in position:absolute.
                 */
                .paperdoc-clamp {
                    overflow: hidden;
                    max-height: var(--paperdoc-clamp-h, none);
                }
                .paperdoc-text-zone.ellipsis .paperdoc-clamp {
                    position: relative;
                }
                .paperdoc-text-zone.ellipsis .paperdoc-clamp::after {
                    content: "…";
                    position: absolute;
                    right: 0;
                    bottom: 0;
                    padding: 0 4pt 0 16pt;
                    background: var(--paperdoc-zone-bg, #ffffff);
                    line-height: inherit;
                    pointer-events: none;
                }
                .paperdoc-running {
                    position: absolute;
                    left: 0;
                    right: 0;
                    box-sizing: border-box;
                    display: flex;
                    align-items: center;
                    pointer-events: none;
                    color: #4b5563;
                    font-size: 10pt;
                    /*
                     * Bande semi-transparente : assure la lisibilité quand
                     * la page a une image de fond ET garantit visuellement
                     * une zone de respiration entre les TextZones et le
                     * header/footer (sinon ils peuvent paraître collés).
                     */
                    background: rgba(255, 255, 255, 0.85);
                    backdrop-filter: blur(2px);
                    min-height: 24pt;
                }
                .paperdoc-running.header { top: 0;    padding: 8pt 24pt; }
                .paperdoc-running.footer { bottom: 0; padding: 8pt 24pt; }
                .paperdoc-running.align-left   { justify-content: flex-start; }
                .paperdoc-running.align-center { justify-content: center; }
                .paperdoc-running.align-right  { justify-content: flex-end; }
                .page-break { page-break-after: always; border-top: 2px dashed #d1d5db; margin: 2em 0; }
                blockquote { border-left: 4px solid #d1d5db; padding: 0.25em 1em; margin: 1em 0; color: #4b5563; font-style: italic; }
                blockquote > :last-child { margin-bottom: 0; }
                pre { background: #f3f4f6; padding: 1em; overflow-x: auto; border-radius: 6px; margin: 1em 0; }
                pre code { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 0.9em; white-space: pre; }
                ul, ol { margin: 1em 0; padding-left: 2em; }
                li { margin: 0.25em 0; }
                @media print {
                    body { background: #ffffff; }
                    section.paperdoc-page { box-shadow: none; margin: 0; }
                }
            </style>
        </head>
        <body style="{$bodyStyle}">
        {$body}
        </body>
        </html>
        HTML;
    }

    private function renderSection(
        Section $section,
        ?RunningElement $header = null,
        ?RunningElement $footer = null,
        int $pageNumber = 1,
        int $totalPages = 1,
        string $title = '',
    ): string {
        $this->sectionFootnotes = [];
        $id    = htmlspecialchars($section->getName());
        $setup = $section->getPageSetup();

        $sectionStyle  = $this->buildSectionStyle($setup);
        $cssClass      = $setup !== null ? 'paperdoc-page' : 'paperdoc-page flow';
        $verticalAlign = $section->getVerticalAlignment();

        // Vertical alignment (v0.8.0). For TOP we keep the original DOM
        // (header / content / footer as siblings); for CENTER/BOTTOM we
        // wrap the content in a flex column whose justify-content
        // pushes the body to the centre or to the bottom of the
        // page-padding box. Header/footer stay absolutely positioned
        // (.paperdoc-running uses absolute), so they don't participate
        // in the flex layout.
        $extraStyle = '';
        if ($verticalAlign !== VerticalAlignment::TOP) {
            $justify = $verticalAlign === VerticalAlignment::CENTER ? 'center' : 'flex-end';
            $extraStyle = sprintf(';display:flex;flex-direction:column;justify-content:%s', $justify);
        }

        $styleAttr = ($sectionStyle . $extraStyle) !== ''
            ? sprintf(' style="%s%s"', $sectionStyle, $extraStyle)
            : '';

        $html = "<section id=\"{$id}\" class=\"{$cssClass}\"{$styleAttr}>\n";

        if ($this->watermark !== null) {
            $html .= sprintf(
                "<div class=\"paperdoc-watermark\" style=\"position:absolute;top:50%%;left:50%%;"
                . "transform:translate(-50%%,-50%%) rotate(%.1fdeg);font-size:%.1fpt;font-weight:bold;"
                . "font-family:%s,sans-serif;color:%s;opacity:%.2f;pointer-events:none;"
                . "white-space:nowrap;user-select:none;z-index:0;\">%s</div>\n",
                $this->watermark->getAngle(),
                $this->watermark->getFontSize(),
                htmlspecialchars($this->watermark->getFontFamily()),
                htmlspecialchars($this->watermark->getColor()),
                $this->watermark->getOpacity(),
                htmlspecialchars($this->watermark->getText()),
            );
        }

        if ($header !== null) {
            $html .= $this->renderRunningElement($header, RunningElement::TYPE_HEADER, $pageNumber, $totalPages, $title);
        }

        // When vertical alignment is non-TOP, wrap body content in a
        // div so flex-grow can balance it against an empty box at the
        // top/bottom (justify-content already handles that for us).
        $wrapBody = $verticalAlign !== VerticalAlignment::TOP;
        if ($wrapBody) {
            $html .= "<div class=\"paperdoc-section-body\">\n";
        }

        foreach ($section->getElements() as $element) {
            $html .= $this->renderBlock($element);
        }

        $html .= $this->renderFootnotesBlock();

        if ($wrapBody) {
            $html .= "</div>\n";
        }

        if ($footer !== null) {
            $html .= $this->renderRunningElement($footer, RunningElement::TYPE_FOOTER, $pageNumber, $totalPages, $title);
        }

        $html .= "</section>\n";

        return $html;
    }

    private function renderRunningElement(
        RunningElement $element,
        string $type,
        int $pageNumber,
        int $totalPages,
        string $title,
    ): string {
        $text = $element->resolve($pageNumber, $totalPages, $title);

        if ($text === '') {
            return '';
        }

        $alignClass = 'align-' . $element->getAlignment()->value;
        $cssClass   = sprintf('paperdoc-running %s %s', htmlspecialchars($type), htmlspecialchars($alignClass));

        $style    = $element->getStyle();
        $inlineParts = [];
        $inlineParts[] = sprintf('font-family:%s,sans-serif', htmlspecialchars($style->getFontFamily()));
        $inlineParts[] = sprintf('font-size:%spt', $style->getFontSize());
        $inlineParts[] = sprintf('color:%s', htmlspecialchars($style->getColor()));

        if ($style->isBold()) {
            $inlineParts[] = 'font-weight:bold';
        }
        if ($style->isItalic()) {
            $inlineParts[] = 'font-style:italic';
        }

        $inlineParts[] = sprintf('height:%.2fpt', $element->getHeight());

        $inlineStyle = implode(';', $inlineParts);

        return sprintf(
            "<div class=\"%s\" style=\"%s\"><span>%s</span></div>\n",
            $cssClass,
            $inlineStyle,
            htmlspecialchars($text),
        );
    }

    private function buildSectionStyle(?PageSetup $setup): string
    {
        if ($setup === null) {
            return '';
        }

        $parts = [];
        $parts[] = sprintf('width:%.2fpt', $setup->getWidth());
        $parts[] = sprintf('height:%.2fpt', $setup->getHeight());
        $parts[] = sprintf(
            'padding:%.2fpt %.2fpt %.2fpt %.2fpt',
            $setup->getPaddingTop(),
            $setup->getPaddingRight(),
            $setup->getPaddingBottom(),
            $setup->getPaddingLeft(),
        );

        if ($setup->getBackgroundColor() !== null) {
            $parts[] = sprintf('background-color:%s', htmlspecialchars($setup->getBackgroundColor()));
        }

        if ($setup->getColumnCount() > 1) {
            $parts[] = sprintf('column-count:%d', $setup->getColumnCount());
            $parts[] = sprintf('column-gap:%.2fpt', $setup->getColumnGap());
        }

        $bgImage = $setup->getBackgroundImage();
        if ($bgImage !== null) {
            $url = $this->resolveImageUrl($bgImage);

            if ($url !== '') {
                $parts[] = sprintf("background-image:url('%s')", $url);
                // Per-section overrides for sizing (cover/contain/auto/...),
                // position and repeat. Sane defaults (cover/center/no-repeat)
                // also live in the global stylesheet so any section without
                // a PageSetup keeps its previous look.
                $parts[] = sprintf('background-size:%s',     htmlspecialchars($this->cssBackgroundSize($setup->getBackgroundSize())));
                $parts[] = sprintf('background-position:%s', htmlspecialchars($setup->getBackgroundPosition()));
                $parts[] = sprintf('background-repeat:%s',   htmlspecialchars($setup->getBackgroundRepeat()));
            }
        }

        return implode(';', $parts);
    }

    /**
     * Normalises a Paperdoc background-size value to a CSS-valid one.
     * The library accepts the convenience alias `'stretch'` (clearer
     * intent than `'100% 100%'`), but browsers don't recognise it — so
     * we expand it for HTML output. All other values are passed through
     * unchanged so users can supply any CSS-valid string (`50% auto`,
     * `300pt 200pt`, etc.).
     */
    private function cssBackgroundSize(string $value): string
    {
        return $value === 'stretch' ? '100% 100%' : $value;
    }

    /**
     * Résout une URL utilisable directement dans un attribut `url()` ou
     * `src=""`. Les URLs absolues (http(s)://, data:, //) sont conservées
     * telles quelles ; les chemins locaux sont encodés en data URI pour
     * que le HTML reste portable et autonome.
     */
    private function resolveImageUrl(Image $image): string
    {
        if ($image->hasData()) {
            $uri = $image->getDataUri();
            if ($uri !== null) {
                return $uri;
            }
        }

        $src = $image->getSrc();

        if ($src === '') {
            return '';
        }

        if (preg_match('#^(?:https?:|data:|//)#i', $src) === 1) {
            return htmlspecialchars($src);
        }

        if (is_readable($src)) {
            $data = @file_get_contents($src);
            if ($data !== false) {
                $info = @getimagesizefromstring($data);
                $mime = is_array($info) ? $info['mime'] : 'application/octet-stream';

                return 'data:' . $mime . ';base64,' . base64_encode($data);
            }
        }

        return htmlspecialchars($src);
    }

    private function renderBlock(DocumentElementInterface $element): string
    {
        return match (true) {
            $element instanceof Heading        => $this->renderHeading($element),
            $element instanceof Paragraph      => $this->renderParagraph($element),
            $element instanceof ListBlock      => $this->renderList($element),
            $element instanceof Blockquote     => $this->renderBlockquote($element),
            $element instanceof CodeBlock      => $this->renderCodeBlock($element),
            $element instanceof Bookmark       => $this->renderBookmark($element),
            $element instanceof Table          => $this->renderTable($element),
            $element instanceof Image          => $this->renderImage($element),
            $element instanceof TextZone       => $this->renderTextZone($element),
            $element instanceof HorizontalRule => $this->renderHorizontalRule($element),
            $element instanceof \Paperdoc\Document\TableOfContents => $this->renderTableOfContents($element),
            $element instanceof PageBreak      => "<div class=\"page-break\"></div>\n",
            default                            => '',
        };
    }

    private function renderTableOfContents(\Paperdoc\Document\TableOfContents $toc): string
    {
        $entries = $this->tocResolver?->entries($toc->getMaxLevel()) ?? [];

        if ($entries === []) {
            return '';
        }

        $html = "<nav class=\"paperdoc-toc\">\n";

        if ($toc->getTitle() !== '') {
            $html .= sprintf("<p class=\"paperdoc-toc-title\">%s</p>\n", htmlspecialchars($toc->getTitle()));
        }

        $html .= "<ul>\n";
        foreach ($entries as $entry) {
            $html .= sprintf(
                "<li class=\"toc-level-%d\" style=\"margin-left:%dpx\"><a href=\"#%s\">%s</a></li>\n",
                $entry['level'],
                ($entry['level'] - 1) * 16,
                htmlspecialchars($entry['anchor']),
                htmlspecialchars($entry['text']),
            );
        }

        return $html . "</ul>\n</nav>\n";
    }

    /**
     * Renders a {@see HorizontalRule} as a CSS-styled `<hr>` element.
     * The `width`, alignment, thickness, colour and margins are all
     * honoured; an `<hr>` carries no semantic role for screen readers
     * other than "thematic break", which matches our intent exactly.
     */
    private function renderHorizontalRule(HorizontalRule $rule): string
    {
        $widthCss = is_string($rule->getWidth()) ? $rule->getWidth() : sprintf('%.2fpt', (float) $rule->getWidth());

        $marginLeftRight = match ($rule->getAlignment()) {
            Alignment::LEFT   => 'margin-left:0;margin-right:auto',
            Alignment::RIGHT  => 'margin-left:auto;margin-right:0',
            Alignment::CENTER => 'margin-left:auto;margin-right:auto',
            default           => 'margin-left:auto;margin-right:auto',
        };

        $style = sprintf(
            'border:none;border-top:%.2fpt solid %s;width:%s;margin-top:%.2fpt;margin-bottom:%.2fpt;%s',
            $rule->getThickness(),
            htmlspecialchars($rule->getColor()),
            htmlspecialchars($widthCss),
            $rule->getMarginTop(),
            $rule->getMarginBottom(),
            $marginLeftRight,
        );

        return sprintf("<hr style=\"%s\">\n", $style);
    }

    private function renderTextZone(TextZone $zone): string
    {
        $parts = [];
        $parts[] = sprintf('left:%.2fpt', $zone->getX());
        $parts[] = sprintf('top:%.2fpt', $zone->getY());
        $parts[] = sprintf('width:%.2fpt', $zone->getWidth());
        $parts[] = sprintf('height:%.2fpt', $zone->getHeight());

        if ($zone->getPadding() > 0) {
            $parts[] = sprintf('padding:%.2fpt', $zone->getPadding());
        }

        if ($zone->getBackgroundColor() !== null) {
            $parts[] = sprintf('background:%s', htmlspecialchars($zone->getBackgroundColor()));
            // Re-exposed as a CSS variable so the ellipsis pseudo-element
            // can mask the clipped text behind a matching background.
            $parts[] = sprintf('--paperdoc-zone-bg:%s', htmlspecialchars($zone->getBackgroundColor()));
        }

        if ($zone->getBorderColor() !== null && $zone->getBorderWidth() > 0) {
            $parts[] = sprintf(
                'border:%.2fpt solid %s',
                $zone->getBorderWidth(),
                htmlspecialchars($zone->getBorderColor()),
            );
        }

        $overflow  = $zone->getOverflow();
        $cssClass  = 'paperdoc-text-zone';
        $isClamped = false;
        $clampLines  = 1;
        $lineHeightPt = 12.0;

        switch ($overflow) {
            case TextZone::OVERFLOW_VISIBLE:
                $cssClass .= ' visible';
                break;
            case TextZone::OVERFLOW_ELLIPSIS:
                $cssClass .= ' ellipsis';
                $isClamped = true;
                break;
            case TextZone::OVERFLOW_CLIP:
            default:
                $cssClass .= ' clip';
                $isClamped = true;
                break;
        }

        // En mode clamp, le wrapper porte font-size/line-height en
        // valeurs ABSOLUES (pt) cohérentes avec celles du contenu :
        //  - la même font-size évite tout décalage entre le clamp et les
        //    spans enfants ;
        //  - line-height en pt absolu garantit que le calcul du
        //    `max-height` côté serveur correspond pile-poil au rendu CSS.
        //
        // L'alignement (left / center / right / justify) est en revanche
        // porté PAR PARAGRAPHE (chaque paragraphe est un <div> dans le
        // clamp), pour qu'on puisse mélanger plusieurs alignements dans
        // une même zone. Cf. renderTextZoneInline().
        if ($isClamped) {
            $paraStyle    = $this->firstParagraphStyle($zone);
            $runStyle     = $this->firstRunStyle($zone);
            $lineSpacing  = $paraStyle?->getLineSpacing() ?? 1.15;
            $fontSize     = $runStyle?->getFontSize() ?? 12.0;
            $lineHeightPt = $fontSize * $lineSpacing;
            $clampLines   = $this->estimateLineClamp($zone);

            $parts[] = sprintf('font-size:%.2fpt', $fontSize);
            $parts[] = sprintf('line-height:%.2fpt', $lineHeightPt);

            if ($runStyle !== null) {
                $parts[] = sprintf('font-family:%s,sans-serif', htmlspecialchars($runStyle->getFontFamily()));
                $parts[] = sprintf('color:%s', htmlspecialchars($runStyle->getColor()));
                if ($runStyle->isBold()) {
                    $parts[] = 'font-weight:bold';
                }
                if ($runStyle->isItalic()) {
                    $parts[] = 'font-style:italic';
                }
            }
        }

        $style = implode(';', $parts);

        if ($isClamped) {
            $maxHeightPt  = $clampLines * $lineHeightPt;
            $clampStyle   = sprintf('max-height:%.2fpt;--paperdoc-clamp-h:%.2fpt', $maxHeightPt, $maxHeightPt);
            $innerContent = $this->renderTextZoneInline($zone);

            return sprintf(
                "<div class=\"%s\" style=\"%s\"><div class=\"paperdoc-clamp\" style=\"%s\">%s</div></div>\n",
                $cssClass,
                $style,
                $clampStyle,
                $innerContent,
            );
        }

        return "<div class=\"{$cssClass}\" style=\"{$style}\">" . $this->renderTextZoneBlocks($zone) . "</div>\n";
    }

    /**
     * Renders zone content as block paragraphs (used when overflow is
     * "visible"). Each paragraph keeps its own styling.
     */
    private function renderTextZoneBlocks(TextZone $zone): string
    {
        $inner = '';
        foreach ($zone->getParagraphs() as $paragraph) {
            $inner .= $this->renderParagraph($paragraph);
        }

        return $inner;
    }

    /**
     * Renders zone content inside the inner clamp container. Each
     * paragraph becomes its own `<div>` so it can carry its individual
     * `text-align` (left / center / right / justify). Block-level divs
     * also produce natural line breaks between paragraphs without
     * breaking the `max-height` line-clamp calculation, since they
     * inherit the wrapper's `line-height`.
     */
    private function renderTextZoneInline(TextZone $zone): string
    {
        $out = '';

        foreach ($zone->getParagraphs() as $paragraph) {
            $runs = '';
            foreach ($paragraph->getRuns() as $run) {
                $runs .= $this->renderTextRun($run);
            }

            if ($runs === '') {
                continue;
            }

            $style    = $paragraph->getStyle();
            $align    = $style?->getAlignment()->value ?? 'left';
            $cssParts = ['margin:0', 'text-align:' . $align];

            if ($style !== null && abs($style->getFirstLineIndent()) > 1e-4) {
                $cssParts[] = sprintf('text-indent:%.2fpt', $style->getFirstLineIndent());
            }

            $out .= sprintf(
                '<div class="paperdoc-text-zone-line" style="%s">%s</div>',
                implode(';', $cssParts),
                $runs,
            );
        }

        return $out;
    }

    private function firstParagraphStyle(TextZone $zone): ?\Paperdoc\Document\Style\ParagraphStyle
    {
        foreach ($zone->getParagraphs() as $paragraph) {
            $style = $paragraph->getStyle();
            if ($style !== null) {
                return $style;
            }
        }

        return null;
    }

    private function firstRunStyle(TextZone $zone): ?\Paperdoc\Document\Style\TextStyle
    {
        foreach ($zone->getParagraphs() as $paragraph) {
            foreach ($paragraph->getRuns() as $run) {
                $style = $run->getStyle();
                if ($style !== null) {
                    return $style;
                }
            }
        }

        return null;
    }

    /**
     * Calcule le nombre de lignes de texte qui tiennent dans la hauteur
     * visible de la zone. On utilise la première paire (font-size,
     * line-spacing) qu'on rencontre comme représentative ; les zones
     * mixant plusieurs tailles sont rares en pratique.
     */
    private function estimateLineClamp(TextZone $zone): int
    {
        $available = $zone->getHeight() - 2 * $zone->getPadding();

        if ($available <= 0) {
            return 1;
        }

        $fontSize    = 12.0;
        $lineSpacing = 1.15;
        $found       = false;

        foreach ($zone->getParagraphs() as $paragraph) {
            $paraStyle = $paragraph->getStyle();
            if ($paraStyle !== null) {
                $lineSpacing = $paraStyle->getLineSpacing() ?: $lineSpacing;
            }

            foreach ($paragraph->getRuns() as $run) {
                $runStyle = $run->getStyle();
                if ($runStyle !== null && $runStyle->getFontSize() > 0) {
                    $fontSize = $runStyle->getFontSize();
                    $found = true;
                    break 2;
                }
            }
        }

        $lineHeight = $fontSize * $lineSpacing;

        if ($lineHeight <= 0) {
            return 1;
        }

        return max(1, (int) floor($available / $lineHeight));
    }

    private function renderHeading(Heading $heading): string
    {
        $level = max(1, min(6, $heading->getLevel()));
        $id = $heading->getId() !== ''
            ? $heading->getId()
            : ($this->autoHeadingIds ? ($this->tocResolver?->anchorFor($heading) ?? '') : '');
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
            $inner .= $this->renderBlock($child);
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

            // First-line indent (CSS text-indent). Negative values are
            // supported and produce a hanging indent. Skip when zero
            // so we don't pollute every paragraph's style attribute.
            if (abs($style->getFirstLineIndent()) > 1e-4) {
                $parts[] = sprintf('text-indent:%.2fpt', $style->getFirstLineIndent());
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
        $footnoteMarker = '';

        if ($run->getFootnote() !== null) {
            $footnoteMarker = $this->registerFootnoteMarker($run->getFootnote()->getText());
        }

        if ($style === null && $link === null) {
            return $text . $footnoteMarker;
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

            $decorations = array_filter([
                $style->isUnderline() ? 'underline' : null,
                $style->isStrikethrough() ? 'line-through' : null,
            ]);
            if ($decorations !== []) {
                $parts[] = 'text-decoration:' . implode(' ', $decorations);
            }

            if ($style->getHighlight() !== null) {
                $parts[] = sprintf('background-color:%s', htmlspecialchars($style->getHighlight()));
            }

            // letter-spacing (v0.8.0). Same units as the rest of the
            // library (points). Skip when zero to avoid noisy markup.
            if (abs($style->getLetterSpacing()) > 1e-4) {
                $parts[] = sprintf('letter-spacing:%.2fpt', $style->getLetterSpacing());
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

            return "<a {$attrs}>{$text}</a>" . $footnoteMarker;
        }

        return "<span style=\"{$css}\">{$text}</span>" . $footnoteMarker;
    }

    /**
     * @phpstan-impure
     */
    private function registerFootnoteMarker(string $text): string
    {
        $this->sectionFootnotes[] = $text;
        $number = count($this->sectionFootnotes);

        return sprintf('<sup class="paperdoc-footnote-ref">[%d]</sup>', $number);
    }

    private function renderFootnotesBlock(): string
    {
        $footnotes = $this->sectionFootnotes;
        if ($footnotes === []) {
            return '';
        }

        $html = "<section class=\"paperdoc-footnotes\">\n<hr>\n<ol>\n";

        foreach ($footnotes as $text) {
            $html .= sprintf("<li>%s</li>\n", htmlspecialchars($text));
        }

        return $html . "</ol>\n</section>\n";
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
                    // Paragraphs render inline so single-line cells stay
                    // visually compact (no <p> wrapper inside <td>).
                    if ($el instanceof Paragraph) {
                        $content .= $this->renderParagraphInline($el);
                        continue;
                    }

                    if ($el instanceof BlockElementInterface) {
                        $content .= $this->renderBlock($el);
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

        // No <figure> wrapper — keeps the element valid inside <td>/<li>
        // and lets the host page wrap it in its own semantic container
        // when needed.
        return "<img {$attrs}>\n";
    }
}
