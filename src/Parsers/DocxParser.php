<?php

declare(strict_types=1);

namespace Paperdoc\Parsers;

use Paperdoc\Contracts\{DocumentInterface, ParserInterface};
use Paperdoc\Document\{Document, Image, ListBlock, ListItem, Paragraph, Section, Table, TableCell, TableRow, TextRun};
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Document\Style\{ParagraphStyle, TableStyle, TextStyle};
use Paperdoc\Enum\Alignment;
use Paperdoc\Support\Cast;

/**
 * Parser DOCX natif utilisant ZipArchive + DOMDocument.
 *
 * Les fichiers .docx sont des archives ZIP contenant du XML
 * au format Office Open XML (OOXML). Le contenu principal se
 * trouve dans word/document.xml.
 */
class DocxParser extends AbstractParser implements ParserInterface
{
    private const NS_MAIN = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    private const NS_REL  = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    private const NS_DRAWING = 'http://schemas.openxmlformats.org/drawingml/2006/main';
    private const NS_WP   = 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing';
    private const NS_PIC  = 'http://schemas.openxmlformats.org/drawingml/2006/picture';
    private const NS_R    = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

    private const HEADING_STYLES = [
        'heading1' => 1, 'heading2' => 2, 'heading3' => 3, 'heading4' => 4,
        'heading5' => 4, 'heading6' => 4,
        'titre'    => 1, 'titre1' => 1, 'titre2' => 2, 'titre3' => 3, 'titre4' => 4,
        'title'    => 1,
    ];

    /** @var array<string, string> rId → target path */
    private array $relationships = [];

    /** @var array<string, string> styleId → baseOn/name mapping for heading detection */
    private array $styleMap = [];

    /** @var array<int, array{style: string, start: int}> */
    private array $numbering = [];

    /** @var array<int, string> */
    private array $abstractNumFormats = [];

    /** @var array<int, array{list: ListBlock, numId: int, last_item: ?ListItem}> */
    private array $listStack = [];

    public function supports(string $extension): bool
    {
        return in_array(strtolower($extension), ['docx'], true);
    }

    public function parse(string $filename): DocumentInterface
    {
        $this->assertFileReadable($filename);

        $zip = new \ZipArchive();

        if ($zip->open($filename) !== true) {
            throw new \RuntimeException("Impossible d'ouvrir le fichier DOCX : {$filename}");
        }

        $document = new Document('docx');

        $this->loadRelationships($zip);
        $this->loadStyles($zip);
        $this->loadNumbering($zip);
        $this->extractMetadata($zip, $document);



        $xml = $zip->getFromName('word/document.xml');

        if ($xml === false) {
            $zip->close();
            throw new \RuntimeException('Fichier word/document.xml introuvable dans le DOCX');
        }

        $dom = new \DOMDocument();
        $dom->loadXML($xml);

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', self::NS_MAIN);
        $xpath->registerNamespace('r', self::NS_R);
        $xpath->registerNamespace('wp', self::NS_WP);
        $xpath->registerNamespace('a', self::NS_DRAWING);
        $xpath->registerNamespace('pic', self::NS_PIC);

        $body = $this->queryElement($xpath, '//w:body');

        if ($body === null) {
            $zip->close();

            return $document;
        }

        $section = new Section('main');
        $this->listStack = [];
        $this->parseBody($body, $section, $xpath, $zip);
        $document->addSection($section);

        $zip->close();
        $this->relationships = [];
        $this->styleMap = [];
        $this->numbering = [];
        $this->abstractNumFormats = [];
        $this->listStack = [];

        return $document;
    }

    /* =============================================================
     | Metadata
     |============================================================= */

    private function extractMetadata(\ZipArchive $zip, Document $document): void
    {
        $core = $zip->getFromName('docProps/core.xml');

        if ($core === false) {
            return;
        }

        $dom = new \DOMDocument();
        $dom->loadXML($core);

        $titleNode = $dom->getElementsByTagNameNS(
            'http://purl.org/dc/elements/1.1/',
            'title'
        )->item(0);

        if ($titleNode && trim($titleNode->textContent) !== '') {
            $document->setTitle(trim($titleNode->textContent));
        }

        $creatorNode = $dom->getElementsByTagNameNS(
            'http://purl.org/dc/elements/1.1/',
            'creator'
        )->item(0);

        if ($creatorNode) {
            $document->setMetadata('author', trim($creatorNode->textContent));
        }
    }

    /* =============================================================
     | Relationships (word/_rels/document.xml.rels)
     |============================================================= */

    private function loadRelationships(\ZipArchive $zip): void
    {
        $this->relationships = [];
        $rels = $zip->getFromName('word/_rels/document.xml.rels');

        if ($rels === false) {
            return;
        }

        $dom = new \DOMDocument();
        $dom->loadXML($rels);

        foreach ($dom->getElementsByTagName('Relationship') as $rel) {
            /** @var \DOMElement $rel */
            $id = $rel->getAttribute('Id');
            $target = $rel->getAttribute('Target');

            if ($id && $target) {
                $this->relationships[$id] = $target;
            }
        }
    }

    /* =============================================================
     | Styles (word/styles.xml)
     |============================================================= */

    private function loadStyles(\ZipArchive $zip): void
    {
        $this->styleMap = [];
        $xml = $zip->getFromName('word/styles.xml');

        if ($xml === false) {
            return;
        }

        $dom = new \DOMDocument();
        $dom->loadXML($xml);

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', self::NS_MAIN);

        foreach ($this->queryElements($xpath, '//w:style[@w:type="paragraph"]') as $style) {
            $styleId = $style->getAttributeNS(self::NS_MAIN, 'styleId');

            if (! $styleId) {
                continue;
            }

            $nameNode = $this->queryElement($xpath, 'w:name', $style);
            $name = $nameNode !== null ? $nameNode->getAttributeNS(self::NS_MAIN, 'val') : '';

            $basedOnNode = $this->queryElement($xpath, 'w:basedOn', $style);
            $basedOn = $basedOnNode !== null ? $basedOnNode->getAttributeNS(self::NS_MAIN, 'val') : '';

            $this->styleMap[strtolower($styleId)] = strtolower($name ?: $basedOn);
        }
    }

    private function loadNumbering(\ZipArchive $zip): void
    {
        $this->numbering = [];
        $this->abstractNumFormats = [];

        $xml = $zip->getFromName('word/numbering.xml');

        if ($xml === false) {
            return;
        }

        $dom = new \DOMDocument();
        $dom->loadXML($xml);

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', self::NS_MAIN);

        foreach ($this->queryElements($xpath, '//w:abstractNum') as $abstract) {
            $abstractId = Cast::asInt($this->getAttributeValue($abstract, 'abstractNumId'));
            $fmtNode = $this->queryElement($xpath, 'w:lvl[@w:ilvl="0"]/w:numFmt', $abstract);
            $fmtVal = $fmtNode !== null ? $this->getAttributeValue($fmtNode, 'val') : '';

            $this->abstractNumFormats[$abstractId] = $this->numFmtToStyle($fmtVal);
        }

        foreach ($this->queryElements($xpath, '//w:num') as $num) {
            $numId = Cast::asInt($this->getAttributeValue($num, 'numId'));
            $abstractNode = $this->queryElement($xpath, 'w:abstractNumId', $num);
            if ($abstractNode === null) {
                continue;
            }

            $abstractId = Cast::asInt($this->getAttributeValue($abstractNode, 'val'));
            $style = $this->abstractNumFormats[$abstractId] ?? ListBlock::STYLE_BULLET;

            $startNode = $this->queryElement($xpath, 'w:lvlOverride/w:startOverride', $num);
            $start = $startNode !== null
                ? max(1, Cast::asInt($this->getAttributeValue($startNode, 'val')))
                : 1;

            $this->numbering[$numId] = [
                'style' => $style,
                'start' => $start,
            ];
        }
    }

    /* =============================================================
     | Body Parsing
     |============================================================= */

    private function parseBody(\DOMNode $body, Section $section, \DOMXPath $xpath, \ZipArchive $zip): void
    {
        foreach ($body->childNodes as $node) {
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $localName = $node->localName;

            if ($localName === 'p') {
                $numInfo = $this->extractNumberingInfo($node, $xpath);

                if ($numInfo !== null) {
                    $this->handleListParagraph($node, $section, $xpath, $zip, $numInfo);
                    continue;
                }

                $this->resetListState();
                $this->parseParagraph($node, $section, $xpath, $zip);
            } elseif ($localName === 'tbl') {
                $this->resetListState();
                $this->parseTable($node, $section, $xpath, $zip);
            } else {
                $this->resetListState();
            }
        }
    }

    /* =============================================================
     | Paragraphs
     |============================================================= */

    private function parseParagraph(\DOMNode $node, Section $section, \DOMXPath $xpath, \ZipArchive $zip): void
    {
        $headingLevel = $this->detectHeadingLevel($node, $xpath);

        if ($headingLevel !== null) {
            $text = $this->extractPlainText($node, $xpath);

            if ($text !== '') {
                $section->addHeading($text, $headingLevel);
            }

            return;
        }

        $paragraph = new Paragraph();

        $pStyle = $this->extractParagraphStyle($node, $xpath);
        if ($pStyle) {
            $paragraph->setStyle($pStyle);
        }

        $this->parseRuns($node, $paragraph, $xpath, $zip, $section);

        if (count($paragraph->getRuns()) > 0) {
            $section->addElement($paragraph);
        }
    }

    /**
     * @return array{numId: int, ilvl: int, style: string, start: int}|null
     */
    private function extractNumberingInfo(\DOMNode $node, \DOMXPath $xpath): ?array
    {
        $numPr = $this->queryElement($xpath, 'w:pPr/w:numPr', $node);

        if ($numPr === null) {
            return null;
        }

        $numIdNode = $this->queryElement($xpath, 'w:numId', $numPr);
        if ($numIdNode === null) {
            return null;
        }

        $numId = Cast::asInt($this->getAttributeValue($numIdNode, 'val'));
        $ilvlNode = $this->queryElement($xpath, 'w:ilvl', $numPr);
        $ilvl = $ilvlNode !== null ? Cast::asInt($this->getAttributeValue($ilvlNode, 'val')) : 0;

        return [
            'numId' => $numId,
            'ilvl' => max(0, $ilvl),
            'style' => $this->numbering[$numId]['style'] ?? ListBlock::STYLE_BULLET,
            'start' => $this->numbering[$numId]['start'] ?? 1,
        ];
    }

    /**
     * @param array{numId: int, ilvl: int, style: string, start: int} $numInfo
     */
    private function handleListParagraph(\DOMNode $node, Section $section, \DOMXPath $xpath, \ZipArchive $zip, array $numInfo): void
    {
        $list = $this->ensureListForNumbering($section, $numInfo);

        if ($list === null) {
            return;
        }

        $paragraph = new Paragraph();
        $this->parseRuns($node, $paragraph, $xpath, $zip, $section);

        $runs = $paragraph->getRuns();

        if ($runs === []) {
            return;
        }

        $item = new ListItem();

        foreach ($runs as $run) {
            $item->addRun(new TextRun($run->getText(), $run->getStyle(), $run->getLink()));
        }

        $list->addItem($item);
        $this->listStack[$numInfo['ilvl']]['last_item'] = $item;
    }

    /**
     * @param array{numId: int, ilvl: int, style: string, start: int} $numInfo
     */
    private function ensureListForNumbering(Section $section, array $numInfo): ?ListBlock
    {
        $level = $numInfo['ilvl'];
        $numId = $numInfo['numId'];

        if ($level === 0 && isset($this->listStack[0]) && $this->listStack[0]['numId'] !== $numId) {
            $this->listStack = [];
        }

        $this->pruneListStack($level);

        if (isset($this->listStack[$level]) && $this->listStack[$level]['numId'] === $numId) {
            return $this->listStack[$level]['list'];
        }

        if ($level > 0 && (! isset($this->listStack[$level - 1]) || $this->listStack[$level - 1]['last_item'] === null)) {
            return null;
        }

        $list = ListBlock::make($numInfo['style'], $numInfo['start']);

        if ($level === 0) {
            $section->addElement($list);
        } else {
            $parentItem = $this->listStack[$level - 1]['last_item'];

            if ($parentItem === null) {
                return null;
            }

            $parentItem->addList($list);
        }

        $this->listStack[$level] = [
            'list' => $list,
            'numId' => $numId,
            'last_item' => null,
        ];

        return $list;
    }

    private function pruneListStack(int $maxLevel): void
    {
        foreach (array_keys($this->listStack) as $level) {
            if ($level > $maxLevel) {
                unset($this->listStack[$level]);
            }
        }
    }

    private function resetListState(): void
    {
        $this->listStack = [];
    }

    private function detectHeadingLevel(\DOMNode $node, \DOMXPath $xpath): ?int
    {
        $pStyleNode = $this->queryElement($xpath, 'w:pPr/w:pStyle', $node);

        if ($pStyleNode === null) {
            return null;
        }

        $styleId = strtolower($pStyleNode->getAttributeNS(self::NS_MAIN, 'val'));

        if (isset(self::HEADING_STYLES[$styleId])) {
            return self::HEADING_STYLES[$styleId];
        }

        if (isset($this->styleMap[$styleId])) {
            $resolvedName = $this->styleMap[$styleId];

            foreach (self::HEADING_STYLES as $key => $level) {
                if (str_contains($resolvedName, $key)) {
                    return $level;
                }
            }
        }

        $outlineLvl = $this->queryElement($xpath, 'w:pPr/w:outlineLvl', $node);
        if ($outlineLvl !== null) {
            $lvl = Cast::asInt($outlineLvl->getAttributeNS(self::NS_MAIN, 'val'));

            return min($lvl + 1, 4);
        }

        return null;
    }

    private function extractParagraphStyle(\DOMNode $node, \DOMXPath $xpath): ?ParagraphStyle
    {
        $pPr = $this->queryElement($xpath, 'w:pPr', $node);

        if ($pPr === null) {
            return null;
        }

        $style = ParagraphStyle::make();
        $hasProps = false;

        $jcNode = $this->queryElement($xpath, 'w:jc', $pPr);
        if ($jcNode !== null) {
            $val = $jcNode->getAttributeNS(self::NS_MAIN, 'val');
            $alignment = match ($val) {
                'center' => Alignment::CENTER,
                'right', 'end' => Alignment::RIGHT,
                'both', 'distribute' => Alignment::JUSTIFY,
                default => Alignment::LEFT,
            };
            $style->setAlignment($alignment);
            $hasProps = true;
        }

        $spacingNode = $this->queryElement($xpath, 'w:spacing', $pPr);
        if ($spacingNode !== null) {
            $before = $spacingNode->getAttributeNS(self::NS_MAIN, 'before');
            $after = $spacingNode->getAttributeNS(self::NS_MAIN, 'after');
            $line = $spacingNode->getAttributeNS(self::NS_MAIN, 'line');

            if ($before !== '') {
                $style->setSpaceBefore($this->twipsToPt(Cast::asInt($before)));
                $hasProps = true;
            }
            if ($after !== '') {
                $style->setSpaceAfter($this->twipsToPt(Cast::asInt($after)));
                $hasProps = true;
            }
            if ($line !== '' && Cast::asInt($line) > 0) {
                $style->setLineSpacing(Cast::asInt($line) / 240.0);
                $hasProps = true;
            }
        }

        return $hasProps ? $style : null;
    }

    /* =============================================================
     | Runs (w:r)
     |============================================================= */

    private function parseRuns(\DOMNode $node, Paragraph $paragraph, \DOMXPath $xpath, \ZipArchive $zip, Section $section, ?TextLink $link = null): void
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE || ! $child instanceof \DOMElement) {
                continue;
            }

            $localName = $child->localName;

            if ($localName === 'r') {
                $this->parseRun($child, $paragraph, $xpath, $zip, $section, $link);
            } elseif ($localName === 'hyperlink') {
                $this->parseRunHyperlink($child, $paragraph, $xpath, $zip, $section);
            }
        }
    }

    private function parseRun(\DOMNode $run, Paragraph $paragraph, \DOMXPath $xpath, \ZipArchive $zip, Section $section, ?TextLink $link = null): void
    {
        $drawing = $this->queryElement($xpath, 'w:drawing', $run);
        if ($drawing !== null) {
            $this->parseDrawing($drawing, $section, $xpath, $zip);

            return;
        }

        $hasLineBreak = false;

        foreach ($this->queryElements($xpath, 'w:br', $run) as $br) {
            $type = $br->getAttributeNS(self::NS_MAIN, 'type');

            if ($type === 'page' || $type === 'column') {
                $section->addPageBreak();
            } else {
                $hasLineBreak = true;
            }
        }

        if ($this->queryElement($xpath, 'w:lastRenderedPageBreak', $run) !== null) {
            $section->addPageBreak();
        }

        $text = '';

        foreach ($this->queryElements($xpath, 'w:t', $run) as $t) {
            $text .= $t->textContent;
        }

        if ($text === '' && $hasLineBreak) {
            $paragraph->addRun(new TextRun("\n"));

            return;
        }

        if ($text === '') {
            return;
        }

        $style = $this->extractRunStyle($run, $xpath);
        $paragraph->addRun(new TextRun($text, $style, $link));
    }

    private function parseRunHyperlink(\DOMElement $run, Paragraph $paragraph, \DOMXPath $xpath, \ZipArchive $zip, Section $section): void
    {
        $rId    = $run->getAttributeNS(self::NS_REL, 'id');
        $anchor = $run->getAttributeNS(self::NS_MAIN, 'anchor');
        $tooltip = $run->getAttributeNS(self::NS_MAIN, 'tooltip');

        $url = '';
        if ($rId !== '' && isset($this->relationships[$rId])) {
            $url = $this->relationships[$rId];
        }

        $link = null;
        if ($url !== '' || $anchor !== '') {
            $link = TextLink::make($url, $anchor, $tooltip);
        }

        $this->parseRuns($run, $paragraph, $xpath, $zip, $section, $link);
    }

    private function extractRunStyle(\DOMNode $run, \DOMXPath $xpath): ?TextStyle
    {
        $rPr = $this->queryElement($xpath, 'w:rPr', $run);

        if ($rPr === null) {
            return null;
        }

        $style = TextStyle::make();
        $hasProps = false;

        $bNode = $this->queryElement($xpath, 'w:b', $rPr);
        if ($bNode !== null) {
            $val = $bNode->getAttributeNS(self::NS_MAIN, 'val');
            if ($val !== '0' && $val !== 'false') {
                $style->setBold();
                $hasProps = true;
            }
        }

        $iNode = $this->queryElement($xpath, 'w:i', $rPr);
        if ($iNode !== null) {
            $val = $iNode->getAttributeNS(self::NS_MAIN, 'val');
            if ($val !== '0' && $val !== 'false') {
                $style->setItalic();
                $hasProps = true;
            }
        }

        $uNode = $this->queryElement($xpath, 'w:u', $rPr);
        if ($uNode !== null) {
            $val = $uNode->getAttributeNS(self::NS_MAIN, 'val');
            if ($val !== 'none') {
                $style->setUnderline();
                $hasProps = true;
            }
        }

        $colorNode = $this->queryElement($xpath, 'w:color', $rPr);
        if ($colorNode !== null) {
            $val = $colorNode->getAttributeNS(self::NS_MAIN, 'val');
            if ($val && $val !== 'auto') {
                $style->setColor('#' . ltrim($val, '#'));
                $hasProps = true;
            }
        }

        $szNode = $this->queryElement($xpath, 'w:sz', $rPr);
        if ($szNode !== null) {
            $val = $szNode->getAttributeNS(self::NS_MAIN, 'val');
            if ($val !== '') {
                $style->setFontSize(Cast::asFloat($val) / 2.0);
                $hasProps = true;
            }
        }

        $fontNode = $this->queryElement($xpath, 'w:rFonts', $rPr);
        if ($fontNode !== null) {
            $ascii = $fontNode->getAttributeNS(self::NS_MAIN, 'ascii');
            $hAnsi = $fontNode->getAttributeNS(self::NS_MAIN, 'hAnsi');
            $font = $ascii ?: $hAnsi;
            if ($font) {
                $style->setFontFamily($font);
                $hasProps = true;
            }
        }

        return $hasProps ? $style : null;
    }

    /* =============================================================
     | Tables (w:tbl)
     |============================================================= */

    private function parseTable(\DOMNode $tblNode, Section $section, \DOMXPath $xpath, \ZipArchive $zip): void
    {
        $table = new Table();
        $tableStyle = $this->extractTableStyle($tblNode, $xpath);

        if ($tableStyle) {
            $table->setStyle($tableStyle);
        }

        $isFirstRow = true;

        foreach ($this->queryElements($xpath, 'w:tr', $tblNode) as $tr) {
            $row = new TableRow();

            if ($this->queryElement($xpath, 'w:trPr/w:tblHeader', $tr) !== null || $isFirstRow) {
                $row->setHeader();
            }

            foreach ($this->queryElements($xpath, 'w:tc', $tr) as $tc) {
                $cell = new TableCell();

                $gridSpan = $this->queryElement($xpath, 'w:tcPr/w:gridSpan', $tc);
                if ($gridSpan !== null) {
                    $span = Cast::asInt($gridSpan->getAttributeNS(self::NS_MAIN, 'val'));
                    if ($span > 1) {
                        $cell->setColspan($span);
                    }
                }

                $vMerge = $this->queryElement($xpath, 'w:tcPr/w:vMerge', $tc);
                if ($vMerge !== null) {
                    $val = $vMerge->getAttributeNS(self::NS_MAIN, 'val');
                    if ($val === 'restart') {
                        $cell->setRowspan(2);
                    }
                }

                $cellTexts = [];

                foreach ($this->queryElements($xpath, 'w:p', $tc) as $p) {
                    $text = $this->extractPlainText($p, $xpath);
                    if ($text !== '') {
                        $cellTexts[] = $text;
                    }
                }

                $cellContent = implode(' ', $cellTexts);
                $cell->addElement((new Paragraph())->addRun(new TextRun($cellContent ?: '')));
                $row->addCell($cell);
            }

            $table->addRow($row);
            $isFirstRow = false;
        }

        if (count($table->getRows()) > 0) {
            $section->addElement($table);
        }
    }

    private function extractTableStyle(\DOMNode $tblNode, \DOMXPath $xpath): ?TableStyle
    {
        $tblPr = $this->queryElement($xpath, 'w:tblPr', $tblNode);

        if ($tblPr === null) {
            return null;
        }

        $style = TableStyle::make();
        $hasProps = false;

        $jcNode = $this->queryElement($xpath, 'w:jc', $tblPr);
        if ($jcNode !== null) {
            $val = $jcNode->getAttributeNS(self::NS_MAIN, 'val');
            $alignment = match ($val) {
                'center' => Alignment::CENTER,
                'right', 'end' => Alignment::RIGHT,
                default => Alignment::LEFT,
            };
            $style->setAlignment($alignment);
            $hasProps = true;
        }

        $bordersNode = $this->queryElement($xpath, 'w:tblBorders', $tblPr);
        if ($bordersNode !== null) {
            $top = $this->queryElement($xpath, 'w:top', $bordersNode);

            if ($top !== null) {
                $sz = $top->getAttributeNS(self::NS_MAIN, 'sz');
                $color = $top->getAttributeNS(self::NS_MAIN, 'color');

                if ($sz !== '') {
                    $style->setBorderWidth(Cast::asInt($sz) / 8.0);
                    $hasProps = true;
                }

                if ($color !== '' && $color !== 'auto') {
                    $style->setBorderColor('#' . $color);
                    $hasProps = true;
                }
            }
        }

        $cellMargin = $this->queryElement($xpath, 'w:tblCellMar/w:top', $tblPr)
            ?? $this->queryElement($xpath, 'w:tblCellMar/w:left', $tblPr);
        if ($cellMargin !== null) {
            $w = $cellMargin->getAttributeNS(self::NS_MAIN, 'w');

            if ($w !== '') {
                $style->setCellPadding($this->twipsToPt(Cast::asInt($w)));
                $hasProps = true;
            }
        }

        return $hasProps ? $style : null;
    }

    /* =============================================================
     | Drawings / Images
     |============================================================= */

    private function parseDrawing(\DOMNode $drawing, Section $section, \DOMXPath $xpath, \ZipArchive $zip): void
    {
        $blipNodes = $this->queryElements($xpath, './/a:blip', $drawing);

        if ($blipNodes === []) {
            return;
        }

        $blip = $blipNodes[0];
        $rId = $blip->getAttributeNS(self::NS_R, 'embed');

        if (! $rId || ! isset($this->relationships[$rId])) {
            return;
        }

        $target = $this->relationships[$rId];
        $zipPath = 'word/' . ltrim($target, '/');

        $width = 0;
        $height = 0;

        $extentNode = $this->queryElement($xpath, './/wp:extent', $drawing);
        if ($extentNode !== null) {
            $cx = Cast::asInt($extentNode->getAttribute('cx'));
            $cy = Cast::asInt($extentNode->getAttribute('cy'));
            $width = (int) round($cx / 9525);
            $height = (int) round($cy / 9525);
        }

        $altText = '';
        $docPr = $this->queryElement($xpath, './/wp:docPr', $drawing);
        if ($docPr !== null) {
            $altText = $docPr->getAttribute('descr') ?: $docPr->getAttribute('name') ?: '';
        }

        $data = $zip->getFromName($zipPath);

        if ($data === false) {
            return;
        }

        $mimeType = $this->guessMimeType($zipPath);
        $image = Image::fromData($data, $mimeType, $width, $height, $altText);
        $image->setSrc($target);

        $section->addElement($image);
    }

    private function guessMimeType(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'gif'         => 'image/gif',
            'bmp'         => 'image/bmp',
            'webp'        => 'image/webp',
            'tiff', 'tif' => 'image/tiff',
            'svg'         => 'image/svg+xml',
            'emf'         => 'image/x-emf',
            'wmf'         => 'image/x-wmf',
            default       => 'application/octet-stream',
        };
    }

    /* =============================================================
     | Helpers
     |============================================================= */

    private function numFmtToStyle(string $fmt): string
    {
        return $fmt === 'bullet' ? ListBlock::STYLE_BULLET : ListBlock::STYLE_ORDERED;
    }

    private function getAttributeValue(\DOMElement $element, string $name): string
    {
        $value = $element->getAttributeNS(self::NS_MAIN, $name);
        if ($value !== '') {
            return $value;
        }

        $value = $element->getAttribute($name);
        if ($value !== '') {
            return $value;
        }

        return $element->getAttribute('w:' . $name);
    }

    private function extractPlainText(\DOMNode $node, \DOMXPath $xpath): string
    {
        $text = '';

        foreach ($this->queryElements($xpath, './/w:t', $node) as $t) {
            $text .= $t->textContent;
        }

        return trim($text);
    }

    private function twipsToPt(int $twips): float
    {
        return $twips / 20.0;
    }

    /**
     * @return list<\DOMElement>
     */
    private function queryElements(\DOMXPath $xpath, string $expression, ?\DOMNode $contextNode = null): array
    {
        $list = $xpath->query($expression, $contextNode);
        if ($list === false) {
            return [];
        }

        $elements = [];
        foreach ($list as $node) {
            if ($node instanceof \DOMElement) {
                $elements[] = $node;
            }
        }

        return $elements;
    }

    private function queryElement(\DOMXPath $xpath, string $expression, ?\DOMNode $contextNode = null): ?\DOMElement
    {
        $elements = $this->queryElements($xpath, $expression, $contextNode);

        return $elements[0] ?? null;
    }
}
