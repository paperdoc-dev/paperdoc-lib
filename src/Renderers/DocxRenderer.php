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
    Image,
    ListBlock,
    ListItem,
    Metadata,
    PageBreak,
    Paragraph,
    Section,
    Table,
    TableCell,
    TableRow,
    TextRun,
};
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Document\Style\{ParagraphStyle, TextStyle};
use Paperdoc\Enum\Alignment;

/**
 * Native DOCX renderer producing a valid Office Open XML
 * (WordprocessingML) package via ZipArchive — no third-party deps.
 *
 * Supports the full v0.4.0 model core: paragraphs (run-level styling),
 * typed headings (Heading1..Heading6), ordered/bullet lists with
 * arbitrary nesting, blockquotes, code blocks, bookmarks (as internal
 * link targets), hyperlinks (external + anchor, with preserved run
 * styling), tables with colspan, embedded images and page breaks.
 */
class DocxRenderer extends AbstractRenderer
{
    private const NS_W = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    private const NS_R = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

    /* -------------------------------------------------------------
     | Per-render state (reset in buildDocx)
     |------------------------------------------------------------- */

    private int $bookmarkSeq = 0;
    private int $relSeq = 1;

    /** @var array<int, array{type: string, target: string, targetMode?: string}> */
    private array $relationships = [];

    /** @var array<string, array{id: int, mimeType: string, data: string, extension: string}> */
    private array $images = [];

    /** @var array<int, array{type: string, start: int}> one "num" entry per list in the document */
    private array $lists = [];

    public function getFormat(): string { return 'docx'; }

    public function render(DocumentInterface $document): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'paperdoc_docx_');

        try {
            $this->buildDocx($document, $tmp);

            return file_get_contents($tmp) ?: '';
        } finally {
            @unlink($tmp);
        }
    }

    public function save(DocumentInterface $document, string $filename): void
    {
        $this->ensureDirectoryWritable($filename);
        $this->buildDocx($document, $filename);
    }

    private function buildDocx(DocumentInterface $document, string $filename): void
    {
        if (file_exists($filename)) {
            @unlink($filename);
        }

        $this->bookmarkSeq  = 0;
        $this->relSeq       = 1;
        $this->relationships = [];
        $this->images       = [];
        $this->lists        = [];

        $zip = new \ZipArchive();

        if ($zip->open($filename, \ZipArchive::CREATE) !== true) {
            throw new \RuntimeException("Unable to create DOCX file: {$filename}");
        }

        $bodyXml = $this->buildDocumentBody($document);

        $zip->addFromString('word/document.xml', $this->wrapDocument($bodyXml));
        $zip->addFromString('word/styles.xml', $this->buildStyles());
        $zip->addFromString('word/numbering.xml', $this->buildNumbering());
        $zip->addFromString('word/_rels/document.xml.rels', $this->buildDocumentRels());
        $zip->addFromString('_rels/.rels', $this->buildRootRels());
        $zip->addFromString('[Content_Types].xml', $this->buildContentTypes());
        $zip->addFromString('docProps/core.xml', $this->buildCoreProps($document));
        $zip->addFromString('docProps/app.xml', $this->buildAppProps());

        foreach ($this->images as $image) {
            $zip->addFromString('word/media/image' . $image['id'] . '.' . $image['extension'], $image['data']);
        }

        if ($zip->close() !== true) {
            throw new \RuntimeException("Unable to finalize DOCX file: {$filename}");
        }
    }

    /* =============================================================
     | word/document.xml
     |============================================================= */

    private function wrapDocument(string $body): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<w:document xmlns:w="' . self::NS_W . '" xmlns:r="' . self::NS_R . '"';
        $xml .= ' xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"';
        $xml .= ' xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture"';
        $xml .= ' xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing">';
        $xml .= '<w:body>';
        $xml .= $body;
        $xml .= $this->buildSectPr();
        $xml .= '</w:body></w:document>';

        return $xml;
    }

    private function buildDocumentBody(DocumentInterface $document): string
    {
        $sections = $document->getSections();

        if (empty($sections)) {
            return '<w:p/>';
        }

        $xml = '';

        foreach ($sections as $section) {
            $xml .= $this->renderSection($section);
        }

        return $xml;
    }

    private function renderSection(Section $section): string
    {
        $xml = '';

        foreach ($section->getElements() as $element) {
            $xml .= $this->renderBlock($element);
        }

        return $xml;
    }

    private function renderBlock(DocumentElementInterface $element, int $indentTwips = 0): string
    {
        return match (true) {
            $element instanceof Heading    => $this->renderHeading($element, $indentTwips),
            $element instanceof Paragraph  => $this->renderParagraph($element, $indentTwips),
            $element instanceof ListBlock  => $this->renderList($element, 0, $indentTwips),
            $element instanceof Blockquote => $this->renderBlockquote($element, $indentTwips),
            $element instanceof CodeBlock  => $this->renderCodeBlock($element, $indentTwips),
            $element instanceof Bookmark   => $this->renderBookmark($element),
            $element instanceof Table      => $this->renderTable($element),
            $element instanceof Image      => $this->renderImageBlock($element),
            $element instanceof PageBreak  => '<w:p><w:r><w:br w:type="page"/></w:r></w:p>',
            default                        => '',
        };
    }

    /* =============================================================
     | Paragraph / Heading
     |============================================================= */

    private function renderParagraph(Paragraph $paragraph, int $indentTwips = 0): string
    {
        $xml = '<w:p>';
        $xml .= $this->renderParagraphProperties($paragraph->getStyle(), indent: $indentTwips);

        foreach ($paragraph->getRuns() as $run) {
            $xml .= $this->renderRun($run);
        }

        $xml .= '</w:p>';

        return $xml;
    }

    private function renderHeading(Heading $heading, int $indentTwips = 0): string
    {
        $level = max(1, min(6, $heading->getLevel()));

        $bookmarkOpen  = '';
        $bookmarkClose = '';

        if ($heading->hasId()) {
            [$bookmarkOpen, $bookmarkClose] = $this->wrapBookmark($heading->getId());
        }

        $xml = '<w:p><w:pPr>';
        $xml .= '<w:pStyle w:val="Heading' . $level . '"/>';

        if ($indentTwips > 0) {
            $xml .= '<w:ind w:left="' . $indentTwips . '"/>';
        }

        $xml .= '</w:pPr>';
        $xml .= $bookmarkOpen;

        foreach ($heading->getRuns() as $run) {
            $xml .= $this->renderRun($run);
        }

        $xml .= $bookmarkClose;
        $xml .= '</w:p>';

        return $xml;
    }

    private function renderParagraphProperties(
        ?ParagraphStyle $style,
        ?string $extraStyleId = null,
        int $indent = 0,
        bool $preserveSpaces = false,
        bool $indentFromStyleOnly = false,
    ): string {
        $parts = '';

        if ($extraStyleId !== null) {
            $parts .= '<w:pStyle w:val="' . $extraStyleId . '"/>';
        }

        if ($style !== null) {
            $heading = $style->getHeadingLevel();

            if ($extraStyleId === null && $heading !== null && $heading >= 1 && $heading <= 6) {
                $parts .= '<w:pStyle w:val="Heading' . $heading . '"/>';
            }

            $alignmentVal = match ($style->getAlignment()) {
                Alignment::CENTER  => 'center',
                Alignment::RIGHT   => 'right',
                Alignment::JUSTIFY => 'both',
                default            => null,
            };

            if ($alignmentVal !== null) {
                $parts .= '<w:jc w:val="' . $alignmentVal . '"/>';
            }

            $spacing = '';
            if ($style->getSpaceBefore() > 0) {
                $spacing .= ' w:before="' . (int) round($style->getSpaceBefore() * 20) . '"';
            }
            if ($style->getSpaceAfter() > 0) {
                $spacing .= ' w:after="' . (int) round($style->getSpaceAfter() * 20) . '"';
            }
            if ($style->getLineSpacing() > 0 && abs($style->getLineSpacing() - 1.0) > 0.001) {
                $spacing .= ' w:line="' . (int) round($style->getLineSpacing() * 240) . '" w:lineRule="auto"';
            }
            if ($spacing !== '') {
                $parts .= '<w:spacing' . $spacing . '/>';
            }
        }

        if ($indent > 0 && ! $indentFromStyleOnly) {
            $parts .= '<w:ind w:left="' . $indent . '"/>';
        }

        if ($preserveSpaces) {
            $parts = '<w:rPr><w:noProof/></w:rPr>' . $parts;
        }

        return $parts === '' ? '' : '<w:pPr>' . $parts . '</w:pPr>';
    }

    /* =============================================================
     | Lists
     |============================================================= */

    private function renderList(ListBlock $list, int $depth, int $baseIndent = 0): string
    {
        $numId = $this->registerList($list);
        $xml = '';

        foreach ($list->getItems() as $item) {
            $xml .= $this->renderListItem($item, $numId, $depth, $baseIndent);
        }

        return $xml;
    }

    private function renderListItem(ListItem $item, int $numId, int $depth, int $baseIndent): string
    {
        $xml = '<w:p><w:pPr>';
        $xml .= '<w:pStyle w:val="ListParagraph"/>';
        $xml .= '<w:numPr>';
        $xml .= '<w:ilvl w:val="' . min(8, $depth) . '"/>';
        $xml .= '<w:numId w:val="' . $numId . '"/>';
        $xml .= '</w:numPr>';

        if ($baseIndent > 0) {
            $xml .= '<w:ind w:left="' . ($baseIndent + (($depth + 1) * 360)) . '"/>';
        }

        $xml .= '</w:pPr>';

        foreach ($item->getRuns() as $run) {
            $xml .= $this->renderRun($run);
        }

        $xml .= '</w:p>';

        foreach ($item->getBlocks() as $block) {
            if ($block instanceof ListBlock) {
                $xml .= $this->renderList($block, $depth + 1, $baseIndent);
                continue;
            }

            $xml .= $this->renderBlock($block, $baseIndent + (($depth + 1) * 720));
        }

        return $xml;
    }

    private function registerList(ListBlock $list): int
    {
        $this->lists[] = [
            'type'  => $list->isOrdered() ? ListBlock::STYLE_ORDERED : ListBlock::STYLE_BULLET,
            'start' => $list->getStart(),
        ];

        return count($this->lists);
    }

    /* =============================================================
     | Blockquote + CodeBlock + Bookmark + Image
     |============================================================= */

    private function renderBlockquote(Blockquote $quote, int $indent = 0): string
    {
        $quoteIndent = $indent + 720;
        $xml = '';

        foreach ($quote->getElements() as $element) {
            if ($element instanceof Paragraph) {
                $xml .= $this->renderQuotedParagraph($element, $quoteIndent);
                continue;
            }

            if ($element instanceof DocumentElementInterface) {
                $xml .= $this->renderBlock($element, $quoteIndent);
            }
        }

        return $xml;
    }

    private function renderQuotedParagraph(Paragraph $paragraph, int $indent): string
    {
        $xml = '<w:p><w:pPr>';
        $xml .= '<w:pStyle w:val="Quote"/>';
        $xml .= '<w:ind w:left="' . $indent . '"/>';
        $xml .= '</w:pPr>';

        foreach ($paragraph->getRuns() as $run) {
            $xml .= $this->renderRun($run);
        }

        $xml .= '</w:p>';

        return $xml;
    }

    private function renderCodeBlock(CodeBlock $code, int $indent = 0): string
    {
        $xml = '<w:p><w:pPr>';
        $xml .= '<w:pStyle w:val="Code"/>';

        if ($indent > 0) {
            $xml .= '<w:ind w:left="' . $indent . '"/>';
        }

        $xml .= '</w:pPr>';

        $lines = $code->getLines();

        if ($lines === []) {
            $xml .= '<w:r><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/></w:rPr><w:t xml:space="preserve"></w:t></w:r>';
            $xml .= '</w:p>';
            return $xml;
        }

        $xml .= '<w:r><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/></w:rPr>';

        $first = true;
        foreach ($lines as $line) {
            if (! $first) {
                $xml .= '<w:br/>';
            }
            $first = false;

            $xml .= '<w:t xml:space="preserve">' . $this->escapeXml($line) . '</w:t>';
        }

        $xml .= '</w:r></w:p>';

        return $xml;
    }

    private function renderBookmark(Bookmark $bookmark): string
    {
        [$open, $close] = $this->wrapBookmark($bookmark->getId());

        return '<w:p>' . $open . $close . '</w:p>';
    }

    /** @return array{0: string, 1: string} */
    private function wrapBookmark(string $id): array
    {
        $bookmarkId = $this->bookmarkSeq++;
        $name = $this->sanitiseBookmarkName($id);

        return [
            '<w:bookmarkStart w:id="' . $bookmarkId . '" w:name="' . $this->escapeXml($name) . '"/>',
            '<w:bookmarkEnd w:id="' . $bookmarkId . '"/>',
        ];
    }

    private function sanitiseBookmarkName(string $id): string
    {
        $clean = preg_replace('/[^A-Za-z0-9_]/', '_', $id) ?? $id;

        if ($clean === '' || ! preg_match('/^[A-Za-z_]/', $clean)) {
            $clean = '_' . $clean;
        }

        return substr($clean, 0, 40);
    }

    private function renderImageBlock(Image $image): string
    {
        $data = $image->getData();
        $mime = $image->getMimeType();

        if ($data === null || $mime === null) {
            if ($image->getSrc() === '' || ! @file_exists($image->getSrc())) {
                return '';
            }

            $loaded = @file_get_contents($image->getSrc());
            if ($loaded === false) {
                return '';
            }
            $data = $loaded;
            $mime = $this->detectMime($image->getSrc()) ?? 'image/png';
        }

        $ext = $this->extensionForMime($mime);
        $imgId = count($this->images) + 1;
        $key = 'image' . $imgId;
        $this->images[$key] = [
            'id' => $imgId,
            'mimeType' => $mime,
            'data' => $data,
            'extension' => $ext,
        ];

        $relId = $this->registerRelationship(
            'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image',
            'media/image' . $imgId . '.' . $ext,
        );

        $widthPx  = $image->getWidth()  > 0 ? $image->getWidth()  : 400;
        $heightPx = $image->getHeight() > 0 ? $image->getHeight() : 300;
        $cx = $widthPx * 9525;
        $cy = $heightPx * 9525;
        $alt = $this->escapeXml($image->getAlt());
        $docPrId = $imgId;

        $xml = '<w:p><w:r><w:drawing>';
        $xml .= '<wp:inline distT="0" distB="0" distL="0" distR="0">';
        $xml .= '<wp:extent cx="' . $cx . '" cy="' . $cy . '"/>';
        $xml .= '<wp:effectExtent l="0" t="0" r="0" b="0"/>';
        $xml .= '<wp:docPr id="' . $docPrId . '" name="Picture ' . $docPrId . '" descr="' . $alt . '"/>';
        $xml .= '<wp:cNvGraphicFramePr><a:graphicFrameLocks xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" noChangeAspect="1"/></wp:cNvGraphicFramePr>';
        $xml .= '<a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">';
        $xml .= '<a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">';
        $xml .= '<pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">';
        $xml .= '<pic:nvPicPr>';
        $xml .= '<pic:cNvPr id="' . $docPrId . '" name="picture' . $imgId . '.' . $ext . '"/>';
        $xml .= '<pic:cNvPicPr/>';
        $xml .= '</pic:nvPicPr>';
        $rNs = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
        $xml .= '<pic:blipFill><a:blip xmlns:r="' . $rNs . '" r:embed="' . $relId . '"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill>';
        $xml .= '<pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="' . $cx . '" cy="' . $cy . '"/></a:xfrm>';
        $xml .= '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr>';
        $xml .= '</pic:pic>';
        $xml .= '</a:graphicData></a:graphic>';
        $xml .= '</wp:inline>';
        $xml .= '</w:drawing></w:r></w:p>';

        return $xml;
    }

    /* =============================================================
     | Runs / Hyperlinks
     |============================================================= */

    private function renderRun(TextRun $run): string
    {
        $link = $run->getLink();

        if ($link !== null) {
            return $this->renderHyperlinkRun($run, $link);
        }

        return $this->renderPlainRun($run);
    }

    private function renderPlainRun(TextRun $run): string
    {
        $text = $run->getText();

        if ($text === '') {
            return '';
        }

        $xml = '<w:r>';
        $rPr = $this->renderRunProperties($run->getStyle());
        if ($rPr !== '') {
            $xml .= $rPr;
        }

        $parts = preg_split('/(\r\n|\r|\n)/', $text) ?: [$text];
        $first = true;
        foreach ($parts as $chunk) {
            if (! $first) {
                $xml .= '<w:br/>';
            }
            $first = false;

            if ($chunk === '') {
                continue;
            }

            $needsPreserve = $chunk !== trim($chunk, " \t");
            $space = $needsPreserve ? ' xml:space="preserve"' : '';
            $xml .= '<w:t' . $space . '>' . $this->escapeXml($chunk) . '</w:t>';
        }

        $xml .= '</w:r>';

        return $xml;
    }

    private function renderHyperlinkRun(TextRun $run, TextLink $link): string
    {
        $style = $this->hyperlinkStyle($run->getStyle());
        $innerRun = new TextRun($run->getText(), $style);
        $inner = $this->renderPlainRun($innerRun);

        if ($inner === '') {
            return '';
        }

        $attrs = '';

        if ($link->getUrl() !== '') {
            $relId = $this->registerRelationship(
                'http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink',
                $link->getUrl(),
                'External',
            );
            $attrs .= ' r:id="' . $relId . '"';

            if ($link->getAnchor() !== '') {
                $attrs .= ' w:anchor="' . $this->escapeXml($this->sanitiseBookmarkName($link->getAnchor())) . '"';
            }
        } elseif ($link->getAnchor() !== '') {
            $attrs .= ' w:anchor="' . $this->escapeXml($this->sanitiseBookmarkName($link->getAnchor())) . '"';
        }

        if ($link->getTitle() !== '') {
            $attrs .= ' w:tooltip="' . $this->escapeXml($link->getTitle()) . '"';
        }

        return '<w:hyperlink' . $attrs . ' w:history="1">' . $inner . '</w:hyperlink>';
    }

    private function hyperlinkStyle(?TextStyle $original): TextStyle
    {
        $style = $original !== null
            ? TextStyle::make()
                ->setFontFamily($original->getFontFamily())
                ->setFontSize($original->getFontSize())
                ->setBold($original->isBold())
                ->setItalic($original->isItalic())
                ->setColor($original->getColor() === '#000000' ? '#0563C1' : $original->getColor())
                ->setUnderline(true)
            : TextStyle::make()->setColor('#0563C1')->setUnderline(true);

        return $style;
    }

    private function renderRunProperties(?TextStyle $style): string
    {
        if ($style === null) {
            return '';
        }

        $parts = '';

        $font = $style->getFontFamily();
        if ($font !== '' && $font !== 'Helvetica') {
            $escaped = $this->escapeXml($font);
            $parts .= '<w:rFonts w:ascii="' . $escaped . '" w:hAnsi="' . $escaped . '" w:cs="' . $escaped . '"/>';
        }

        if ($style->isBold()) {
            $parts .= '<w:b/><w:bCs/>';
        }

        if ($style->isItalic()) {
            $parts .= '<w:i/><w:iCs/>';
        }

        if ($style->isUnderline()) {
            $parts .= '<w:u w:val="single"/>';
        }

        $color = ltrim($style->getColor(), '#');
        if ($color !== '' && strtolower($color) !== '000000') {
            $parts .= '<w:color w:val="' . $this->escapeXml($color) . '"/>';
        }

        $size = $style->getFontSize();
        if ($size > 0 && abs($size - 12.0) > 0.001) {
            $halfPoints = (int) round($size * 2);
            $parts .= '<w:sz w:val="' . $halfPoints . '"/><w:szCs w:val="' . $halfPoints . '"/>';
        }

        return $parts === '' ? '' : '<w:rPr>' . $parts . '</w:rPr>';
    }

    /* =============================================================
     | Tables
     |============================================================= */

    private function renderTable(Table $table): string
    {
        $xml = '<w:tbl>';
        $xml .= '<w:tblPr>';
        $xml .= '<w:tblW w:w="5000" w:type="pct"/>';
        $xml .= '<w:tblBorders>';
        foreach (['top', 'left', 'bottom', 'right', 'insideH', 'insideV'] as $side) {
            $xml .= '<w:' . $side . ' w:val="single" w:sz="4" w:space="0" w:color="auto"/>';
        }
        $xml .= '</w:tblBorders>';
        $xml .= '</w:tblPr>';

        foreach ($table->getRows() as $row) {
            $xml .= $this->renderTableRow($row);
        }

        $xml .= '</w:tbl>';

        return $xml;
    }

    private function renderTableRow(TableRow $row): string
    {
        $xml = '<w:tr>';

        if ($row->isHeader()) {
            $xml .= '<w:trPr><w:tblHeader/></w:trPr>';
        }

        foreach ($row->getCells() as $cell) {
            $xml .= $this->renderTableCell($cell, $row->isHeader());
        }

        $xml .= '</w:tr>';

        return $xml;
    }

    private function renderTableCell(TableCell $cell, bool $headerRow): string
    {
        $xml = '<w:tc><w:tcPr>';

        if ($cell->getColspan() > 1) {
            $xml .= '<w:gridSpan w:val="' . $cell->getColspan() . '"/>';
        }

        $xml .= '</w:tcPr>';

        $hasContent = false;

        foreach ($cell->getElements() as $element) {
            if ($element instanceof Paragraph) {
                if ($headerRow) {
                    foreach ($element->getRuns() as $run) {
                        $runStyle = $run->getStyle() ?? TextStyle::make();
                        if (! $runStyle->isBold()) {
                            $run->setStyle(TextStyle::make()
                                ->setFontFamily($runStyle->getFontFamily())
                                ->setFontSize($runStyle->getFontSize())
                                ->setColor($runStyle->getColor())
                                ->setBold()
                                ->setItalic($runStyle->isItalic())
                                ->setUnderline($runStyle->isUnderline()));
                        }
                    }
                }
                $xml .= $this->renderParagraph($element);
                $hasContent = true;
            }
        }

        if (! $hasContent) {
            $xml .= '<w:p/>';
        }

        $xml .= '</w:tc>';

        return $xml;
    }

    /* =============================================================
     | Section Properties, Styles, Numbering, Relationships
     |============================================================= */

    private function buildSectPr(): string
    {
        return '<w:sectPr>'
            . '<w:pgSz w:w="12240" w:h="15840"/>'
            . '<w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="720" w:footer="720" w:gutter="0"/>'
            . '</w:sectPr>';
    }

    private function buildStyles(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<w:styles xmlns:w="' . self::NS_W . '">';
        $xml .= '<w:docDefaults>'
              . '<w:rPrDefault><w:rPr>'
              . '<w:rFonts w:ascii="Calibri" w:hAnsi="Calibri" w:cs="Calibri"/>'
              . '<w:sz w:val="22"/><w:szCs w:val="22"/>'
              . '</w:rPr></w:rPrDefault>'
              . '<w:pPrDefault><w:pPr><w:spacing w:after="160" w:line="259" w:lineRule="auto"/></w:pPr></w:pPrDefault>'
              . '</w:docDefaults>';

        $xml .= '<w:style w:type="paragraph" w:default="1" w:styleId="Normal">'
              . '<w:name w:val="Normal"/><w:qFormat/>'
              . '</w:style>';

        $headings = [
            1 => ['size' => 32, 'color' => '2F5496'],
            2 => ['size' => 26, 'color' => '2F5496'],
            3 => ['size' => 24, 'color' => '1F3763'],
            4 => ['size' => 22, 'color' => '2F5496'],
            5 => ['size' => 22, 'color' => '2F5496'],
            6 => ['size' => 22, 'color' => '1F3763'],
        ];

        foreach ($headings as $level => $props) {
            $xml .= '<w:style w:type="paragraph" w:styleId="Heading' . $level . '">'
                  . '<w:name w:val="heading ' . $level . '"/>'
                  . '<w:basedOn w:val="Normal"/>'
                  . '<w:next w:val="Normal"/>'
                  . '<w:qFormat/>'
                  . '<w:pPr>'
                  . '<w:keepNext/><w:keepLines/>'
                  . '<w:spacing w:before="240" w:after="220"/>'
                  . '<w:outlineLvl w:val="' . ($level - 1) . '"/>'
                  . '</w:pPr>'
                  . '<w:rPr>'
                  . '<w:b/><w:bCs/>'
                  . '<w:color w:val="' . $props['color'] . '"/>'
                  . '<w:sz w:val="' . $props['size'] . '"/><w:szCs w:val="' . $props['size'] . '"/>'
                  . '</w:rPr>'
                  . '</w:style>';
        }

        $xml .= '<w:style w:type="paragraph" w:styleId="ListParagraph">'
              . '<w:name w:val="List Paragraph"/><w:basedOn w:val="Normal"/><w:qFormat/>'
              . '<w:pPr><w:ind w:left="720"/><w:contextualSpacing/></w:pPr>'
              . '</w:style>';

        $xml .= '<w:style w:type="paragraph" w:styleId="Quote">'
              . '<w:name w:val="Quote"/><w:basedOn w:val="Normal"/><w:qFormat/>'
              . '<w:pPr><w:spacing w:before="120" w:after="120"/><w:ind w:left="720"/></w:pPr>'
              . '<w:rPr><w:i/><w:iCs/><w:color w:val="404040"/></w:rPr>'
              . '</w:style>';

        $xml .= '<w:style w:type="paragraph" w:styleId="Code">'
              . '<w:name w:val="Code"/><w:basedOn w:val="Normal"/><w:qFormat/>'
              . '<w:pPr><w:spacing w:before="0" w:after="0"/><w:shd w:val="clear" w:color="auto" w:fill="F3F4F6"/></w:pPr>'
              . '<w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:sz w:val="20"/></w:rPr>'
              . '</w:style>';

        $xml .= '<w:style w:type="character" w:styleId="Hyperlink">'
              . '<w:name w:val="Hyperlink"/>'
              . '<w:rPr><w:color w:val="0563C1"/><w:u w:val="single"/></w:rPr>'
              . '</w:style>';

        $xml .= '</w:styles>';

        return $xml;
    }

    private function buildNumbering(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<w:numbering xmlns:w="' . self::NS_W . '">';

        $xml .= $this->buildAbstractNum(1, 'bullet');
        $xml .= $this->buildAbstractNum(2, 'decimal');

        foreach ($this->lists as $i => $list) {
            $numId = $i + 1;
            $abstractId = $list['type'] === ListBlock::STYLE_ORDERED ? 2 : 1;
            $xml .= '<w:num w:numId="' . $numId . '">';
            $xml .= '<w:abstractNumId w:val="' . $abstractId . '"/>';

            if ($list['type'] === ListBlock::STYLE_ORDERED && $list['start'] !== 1) {
                $xml .= '<w:lvlOverride w:ilvl="0"><w:startOverride w:val="' . $list['start'] . '"/></w:lvlOverride>';
            }

            $xml .= '</w:num>';
        }

        $xml .= '</w:numbering>';

        return $xml;
    }

    private function buildAbstractNum(int $id, string $style): string
    {
        $xml = '<w:abstractNum w:abstractNumId="' . $id . '">';

        for ($level = 0; $level < 9; $level++) {
            $xml .= '<w:lvl w:ilvl="' . $level . '">';
            $xml .= '<w:start w:val="1"/>';

            if ($style === 'bullet') {
                $symbol = match ($level % 3) {
                    0 => '&#8226;',
                    1 => 'o',
                    default => '&#9642;',
                };
                $xml .= '<w:numFmt w:val="bullet"/>';
                $xml .= '<w:lvlText w:val="' . $symbol . '"/>';
            } else {
                $xml .= '<w:numFmt w:val="decimal"/>';
                $xml .= '<w:lvlText w:val="%' . ($level + 1) . '."/>';
            }

            $xml .= '<w:lvlJc w:val="left"/>';
            $xml .= '<w:pPr><w:ind w:left="' . (720 + $level * 360) . '" w:hanging="360"/></w:pPr>';
            $xml .= '</w:lvl>';
        }

        $xml .= '</w:abstractNum>';

        return $xml;
    }

    private function registerRelationship(string $type, string $target, ?string $targetMode = null): string
    {
        $relId = 'rId' . (++$this->relSeq);
        $entry = ['type' => $type, 'target' => $target];

        if ($targetMode !== null) {
            $entry['targetMode'] = $targetMode;
        }

        $this->relationships[$relId] = $entry;

        return $relId;
    }

    private function buildDocumentRels(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
        $xml .= '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';
        $xml .= '<Relationship Id="rIdNumbering" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/numbering" Target="numbering.xml"/>';

        foreach ($this->relationships as $id => $rel) {
            $mode = isset($rel['targetMode'])
                ? ' TargetMode="' . $rel['targetMode'] . '"'
                : '';
            $xml .= '<Relationship Id="' . $id . '"'
                . ' Type="' . $rel['type'] . '"'
                . ' Target="' . $this->escapeXml($rel['target']) . '"'
                . $mode
                . '/>';
        }

        $xml .= '</Relationships>';

        return $xml;
    }

    /* =============================================================
     | Package metadata (Content Types, root rels, core / app props)
     |============================================================= */

    private function buildContentTypes(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">';
        $xml .= '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>';
        $xml .= '<Default Extension="xml" ContentType="application/xml"/>';

        $defaults = [
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
        ];
        $seen = [];

        foreach ($this->images as $image) {
            $ext = strtolower($image['extension']);
            if (isset($seen[$ext])) {
                continue;
            }
            $seen[$ext] = true;
            $mime = $defaults[$ext] ?? $image['mimeType'];
            $xml .= '<Default Extension="' . $ext . '" ContentType="' . $mime . '"/>';
        }

        $xml .= '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>';
        $xml .= '<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>';
        $xml .= '<Override PartName="/word/numbering.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.numbering+xml"/>';
        $xml .= '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>';
        $xml .= '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>';
        $xml .= '</Types>';

        return $xml;
    }

    private function buildRootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';
    }

    private function buildCoreProps(DocumentInterface $document): string
    {
        $properties = $document instanceof Document ? $document->getProperties() : null;

        $title       = $this->escapeXml($document->getTitle());
        $author      = $this->escapeXml($properties instanceof Metadata && $properties->getAuthor() !== ''
            ? $properties->getAuthor()
            : ($document->getMetadata()['author'] ?? 'Paperdoc'));
        $subject     = $this->escapeXml($properties?->getSubject() ?? '');
        $description = $this->escapeXml($properties?->getDescription() ?? '');
        $keywords    = $this->escapeXml($properties?->getKeywords() ?? '');
        $language    = $this->escapeXml($properties?->getLanguage() ?? '');

        $created  = ($properties?->getCreatedAt() ?? new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
            ->format('Y-m-d\TH:i:s\Z');
        $modified = ($properties?->getModifiedAt() ?? new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
            ->format('Y-m-d\TH:i:s\Z');

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties"'
            . ' xmlns:dc="http://purl.org/dc/elements/1.1/"'
            . ' xmlns:dcterms="http://purl.org/dc/terms/"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:title>' . $title . '</dc:title>'
            . '<dc:creator>' . $author . '</dc:creator>'
            . '<cp:lastModifiedBy>' . $author . '</cp:lastModifiedBy>';

        if ($subject !== '') {
            $xml .= '<dc:subject>' . $subject . '</dc:subject>';
        }
        if ($description !== '') {
            $xml .= '<dc:description>' . $description . '</dc:description>';
        }
        if ($keywords !== '') {
            $xml .= '<cp:keywords>' . $keywords . '</cp:keywords>';
        }
        if ($language !== '') {
            $xml .= '<dc:language>' . $language . '</dc:language>';
        }

        $xml .= '<dcterms:created xsi:type="dcterms:W3CDTF">' . $created . '</dcterms:created>'
            . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . $modified . '</dcterms:modified>'
            . '</cp:coreProperties>';

        return $xml;
    }

    private function buildAppProps(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"'
            . ' xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            . '<Application>Paperdoc</Application>'
            . '</Properties>';
    }

    /* =============================================================
     | Helpers
     |============================================================= */

    private function extensionForMime(string $mime): string
    {
        return match (strtolower($mime)) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            'image/bmp'  => 'bmp',
            'image/tiff' => 'tiff',
            'image/svg+xml' => 'svg',
            default      => 'png',
        };
    }

    private function detectMime(string $path): ?string
    {
        if (function_exists('mime_content_type')) {
            $mime = @mime_content_type($path);
            if ($mime !== false && $mime !== null) {
                return $mime;
            }
        }

        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'png'  => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'bmp'  => 'image/bmp',
            'tiff' => 'image/tiff',
            'svg'  => 'image/svg+xml',
            default => null,
        };
    }

    private function escapeXml(string $text): string
    {
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
