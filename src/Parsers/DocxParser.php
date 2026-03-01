<?php

declare(strict_types=1);

namespace Paperdoc\Parsers;

use Paperdoc\Contracts\{DocumentInterface, ParserInterface};
use Paperdoc\Document\{Document, Image, PageBreak, Paragraph, Section, Table, TableCell, TableRow, TextRun};
use Paperdoc\Document\Style\{ParagraphStyle, TableStyle, TextStyle};
use Paperdoc\Enum\Alignment;

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

        $body = $xpath->query('//w:body')->item(0);

        if (! $body) {
            $zip->close();

            return $document;
        }

        $section = new Section('main');
        $this->parseBody($body, $section, $xpath, $zip);
        $document->addSection($section);

        $zip->close();
        $this->relationships = [];
        $this->styleMap = [];

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

        $styles = $xpath->query('//w:style[@w:type="paragraph"]');

        foreach ($styles as $style) {
            /** @var \DOMElement $style */
            $styleId = $style->getAttributeNS(self::NS_MAIN, 'styleId');

            if (! $styleId) {
                continue;
            }

            $nameNode = $xpath->query('w:name', $style)->item(0);
            $name = $nameNode ? $nameNode->getAttributeNS(self::NS_MAIN, 'val') : '';

            $basedOnNode = $xpath->query('w:basedOn', $style)->item(0);
            $basedOn = $basedOnNode ? $basedOnNode->getAttributeNS(self::NS_MAIN, 'val') : '';

            $this->styleMap[strtolower($styleId)] = strtolower($name ?: $basedOn);
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
                $this->parseParagraph($node, $section, $xpath, $zip);
            } elseif ($localName === 'tbl') {
                $this->parseTable($node, $section, $xpath, $zip);
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

    private function detectHeadingLevel(\DOMNode $node, \DOMXPath $xpath): ?int
    {
        $pStyleNode = $xpath->query('w:pPr/w:pStyle', $node)->item(0);

        if (! $pStyleNode instanceof \DOMElement) {
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

        $outlineLvl = $xpath->query('w:pPr/w:outlineLvl', $node)->item(0);
        if ($outlineLvl instanceof \DOMElement) {
            $lvl = (int) $outlineLvl->getAttributeNS(self::NS_MAIN, 'val');

            return min($lvl + 1, 4);
        }

        return null;
    }

    private function extractParagraphStyle(\DOMNode $node, \DOMXPath $xpath): ?ParagraphStyle
    {
        $pPr = $xpath->query('w:pPr', $node)->item(0);

        if (! $pPr) {
            return null;
        }

        $style = ParagraphStyle::make();
        $hasProps = false;

        $jcNode = $xpath->query('w:jc', $pPr)->item(0);
        if ($jcNode instanceof \DOMElement) {
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

        $spacingNode = $xpath->query('w:spacing', $pPr)->item(0);
        if ($spacingNode instanceof \DOMElement) {
            $before = $spacingNode->getAttributeNS(self::NS_MAIN, 'before');
            $after = $spacingNode->getAttributeNS(self::NS_MAIN, 'after');
            $line = $spacingNode->getAttributeNS(self::NS_MAIN, 'line');

            if ($before !== '') {
                $style->setSpaceBefore($this->twipsToPt((int) $before));
                $hasProps = true;
            }
            if ($after !== '') {
                $style->setSpaceAfter($this->twipsToPt((int) $after));
                $hasProps = true;
            }
            if ($line !== '' && (int) $line > 0) {
                $style->setLineSpacing((int) $line / 240.0);
                $hasProps = true;
            }
        }

        return $hasProps ? $style : null;
    }

    /* =============================================================
     | Runs (w:r)
     |============================================================= */

    private function parseRuns(\DOMNode $node, Paragraph $paragraph, \DOMXPath $xpath, \ZipArchive $zip, Section $section): void
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $localName = $child->localName;

            if ($localName === 'r') {
                $this->parseRun($child, $paragraph, $xpath, $zip, $section);
            } elseif ($localName === 'hyperlink') {
                $this->parseRuns($child, $paragraph, $xpath, $zip, $section);
            }
        }
    }

    private function parseRun(\DOMNode $run, Paragraph $paragraph, \DOMXPath $xpath, \ZipArchive $zip, Section $section): void
    {
        $drawing = $xpath->query('w:drawing', $run)->item(0);
        if ($drawing) {
            $this->parseDrawing($drawing, $section, $xpath, $zip);

            return;
        }

        $hasLineBreak = false;

        foreach ($xpath->query('w:br', $run) as $br) {
            /** @var \DOMElement $br */
            $type = $br->getAttributeNS(self::NS_MAIN, 'type');

            if ($type === 'page' || $type === 'column') {
                $section->addPageBreak();
            } else {
                $hasLineBreak = true;
            }
        }

        if ($xpath->query('w:lastRenderedPageBreak', $run)->length > 0) {
            $section->addPageBreak();
        }

        $textNodes = $xpath->query('w:t', $run);
        $text = '';

        foreach ($textNodes as $t) {
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
        $paragraph->addRun(new TextRun($text, $style));
    }

    private function extractRunStyle(\DOMNode $run, \DOMXPath $xpath): ?TextStyle
    {
        $rPr = $xpath->query('w:rPr', $run)->item(0);

        if (! $rPr) {
            return null;
        }

        $style = TextStyle::make();
        $hasProps = false;

        if ($xpath->query('w:b', $rPr)->length > 0) {
            $bNode = $xpath->query('w:b', $rPr)->item(0);
            $val = $bNode instanceof \DOMElement ? $bNode->getAttributeNS(self::NS_MAIN, 'val') : '';
            if ($val !== '0' && $val !== 'false') {
                $style->setBold();
                $hasProps = true;
            }
        }

        if ($xpath->query('w:i', $rPr)->length > 0) {
            $iNode = $xpath->query('w:i', $rPr)->item(0);
            $val = $iNode instanceof \DOMElement ? $iNode->getAttributeNS(self::NS_MAIN, 'val') : '';
            if ($val !== '0' && $val !== 'false') {
                $style->setItalic();
                $hasProps = true;
            }
        }

        if ($xpath->query('w:u', $rPr)->length > 0) {
            $uNode = $xpath->query('w:u', $rPr)->item(0);
            $val = $uNode instanceof \DOMElement ? $uNode->getAttributeNS(self::NS_MAIN, 'val') : '';
            if ($val !== 'none') {
                $style->setUnderline();
                $hasProps = true;
            }
        }

        $colorNode = $xpath->query('w:color', $rPr)->item(0);
        if ($colorNode instanceof \DOMElement) {
            $val = $colorNode->getAttributeNS(self::NS_MAIN, 'val');
            if ($val && $val !== 'auto') {
                $style->setColor('#' . ltrim($val, '#'));
                $hasProps = true;
            }
        }

        $szNode = $xpath->query('w:sz', $rPr)->item(0);
        if ($szNode instanceof \DOMElement) {
            $val = $szNode->getAttributeNS(self::NS_MAIN, 'val');
            if ($val !== '') {
                $style->setFontSize((float) $val / 2.0);
                $hasProps = true;
            }
        }

        $fontNode = $xpath->query('w:rFonts', $rPr)->item(0);
        if ($fontNode instanceof \DOMElement) {
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

        $trNodes = $xpath->query('w:tr', $tblNode);

        foreach ($trNodes as $tr) {
            $row = new TableRow();

            $tblHeader = $xpath->query('w:trPr/w:tblHeader', $tr);
            if ($tblHeader->length > 0 || $isFirstRow) {
                $row->setHeader();
            }

            $tcNodes = $xpath->query('w:tc', $tr);

            foreach ($tcNodes as $tc) {
                $cell = new TableCell();

                $gridSpan = $xpath->query('w:tcPr/w:gridSpan', $tc)->item(0);
                if ($gridSpan instanceof \DOMElement) {
                    $span = (int) $gridSpan->getAttributeNS(self::NS_MAIN, 'val');
                    if ($span > 1) {
                        $cell->setColspan($span);
                    }
                }

                $vMerge = $xpath->query('w:tcPr/w:vMerge', $tc)->item(0);
                if ($vMerge instanceof \DOMElement) {
                    $val = $vMerge->getAttributeNS(self::NS_MAIN, 'val');
                    if ($val === 'restart') {
                        $cell->setRowspan(2);
                    }
                }

                $pNodes = $xpath->query('w:p', $tc);
                $cellTexts = [];

                foreach ($pNodes as $p) {
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
        $tblPr = $xpath->query('w:tblPr', $tblNode)->item(0);

        if (! $tblPr) {
            return null;
        }

        $style = TableStyle::make();
        $hasProps = false;

        $jcNode = $xpath->query('w:jc', $tblPr)->item(0);
        if ($jcNode instanceof \DOMElement) {
            $val = $jcNode->getAttributeNS(self::NS_MAIN, 'val');
            $alignment = match ($val) {
                'center' => Alignment::CENTER,
                'right', 'end' => Alignment::RIGHT,
                default => Alignment::LEFT,
            };
            $style->setAlignment($alignment);
            $hasProps = true;
        }

        $bordersNode = $xpath->query('w:tblBorders', $tblPr)->item(0);
        if ($bordersNode) {
            $top = $xpath->query('w:top', $bordersNode)->item(0);

            if ($top instanceof \DOMElement) {
                $sz = $top->getAttributeNS(self::NS_MAIN, 'sz');
                $color = $top->getAttributeNS(self::NS_MAIN, 'color');

                if ($sz !== '') {
                    $style->setBorderWidth((int) $sz / 8.0);
                    $hasProps = true;
                }

                if ($color !== '' && $color !== 'auto') {
                    $style->setBorderColor('#' . $color);
                    $hasProps = true;
                }
            }
        }

        $cellMargin = $xpath->query('w:tblCellMar/w:top', $tblPr)->item(0)
            ?? $xpath->query('w:tblCellMar/w:left', $tblPr)->item(0);
        if ($cellMargin instanceof \DOMElement) {
            $w = $cellMargin->getAttributeNS(self::NS_MAIN, 'w');

            if ($w !== '') {
                $style->setCellPadding($this->twipsToPt((int) $w));
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
        $blipNodes = $xpath->query('.//a:blip', $drawing);

        if ($blipNodes->length === 0) {
            return;
        }

        $blip = $blipNodes->item(0);

        if (! $blip instanceof \DOMElement) {
            return;
        }

        $rId = $blip->getAttributeNS(self::NS_R, 'embed');

        if (! $rId || ! isset($this->relationships[$rId])) {
            return;
        }

        $target = $this->relationships[$rId];
        $zipPath = 'word/' . ltrim($target, '/');

        $width = 0;
        $height = 0;

        $extentNode = $xpath->query('.//wp:extent', $drawing)->item(0);
        if ($extentNode instanceof \DOMElement) {
            $cx = (int) $extentNode->getAttribute('cx');
            $cy = (int) $extentNode->getAttribute('cy');
            $width = (int) round($cx / 9525);
            $height = (int) round($cy / 9525);
        }

        $altText = '';
        $docPr = $xpath->query('.//wp:docPr', $drawing)->item(0);
        if ($docPr instanceof \DOMElement) {
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

    private function extractPlainText(\DOMNode $node, \DOMXPath $xpath): string
    {
        $textNodes = $xpath->query('.//w:t', $node);
        $text = '';

        foreach ($textNodes as $t) {
            $text .= $t->textContent;
        }

        return trim($text);
    }

    private function twipsToPt(int $twips): float
    {
        return $twips / 20.0;
    }
}
