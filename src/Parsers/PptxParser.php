<?php

declare(strict_types=1);

namespace Paperdoc\Parsers;

use Paperdoc\Contracts\{DocumentInterface, ParserInterface};
use Paperdoc\Document\{Document, Image, Paragraph, Section, Table, TableCell, TableRow, TextRun};
use Paperdoc\Document\Style\{ParagraphStyle, TextStyle};

/**
 * Parser PPTX natif utilisant ZipArchive + XML.
 *
 * Les fichiers .pptx sont des archives ZIP contenant du XML
 * au format Office Open XML (PresentationML).
 * Chaque slide est stockée dans ppt/slides/slide{n}.xml.
 */
class PptxParser extends AbstractParser implements ParserInterface
{
    private const NS_MAIN = 'http://schemas.openxmlformats.org/presentationml/2006/main';
    private const NS_DRAWING = 'http://schemas.openxmlformats.org/drawingml/2006/main';
    private const NS_REL = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

    public function supports(string $extension): bool
    {
        return in_array(strtolower($extension), ['pptx'], true);
    }

    public function parse(string $filename): DocumentInterface
    {
        $this->assertFileReadable($filename);

        $zip = new \ZipArchive();

        if ($zip->open($filename) !== true) {
            throw new \RuntimeException("Impossible d'ouvrir le fichier PPTX : {$filename}");
        }

        $document = new Document('pptx');
        $document->setTitle(pathinfo($filename, PATHINFO_FILENAME));

        $this->extractMetadata($zip, $document);

        $slideOrder = $this->getSlideOrder($zip);

        foreach ($slideOrder as $index => $slidePath) {
            $section = $this->parseSlide($zip, $slidePath, $index + 1);

            if ($section !== null) {
                $document->addSection($section);
            }
        }

        $zip->close();

        return $document;
    }

    /* =============================================================
     | Slide Order (ppt/presentation.xml)
     |============================================================= */

    /**
     * @return string[] ordered slide paths
     */
    private function getSlideOrder(\ZipArchive $zip): array
    {
        $presXml = $zip->getFromName('ppt/presentation.xml');

        if ($presXml === false) {
            return $this->discoverSlides($zip);
        }

        $dom = new \DOMDocument();
        @$dom->loadXML($presXml);

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('p', self::NS_MAIN);
        $xpath->registerNamespace('r', self::NS_REL);

        $rels = $this->loadRelationships($zip, 'ppt/_rels/presentation.xml.rels');

        $slides = [];

        foreach ($this->queryElements($xpath, '//p:sldIdLst/p:sldId') as $node) {
            $rId = $node->getAttributeNS(self::NS_REL, 'id');

            if (isset($rels[$rId])) {
                $target = $rels[$rId];
                $slides[] = 'ppt/' . ltrim($target, '/');
            }
        }

        return ! empty($slides) ? $slides : $this->discoverSlides($zip);
    }

    /**
     * @return string[]
     */
    private function discoverSlides(\ZipArchive $zip): array
    {
        $slides = [];

        for ($i = 1; $i <= 200; $i++) {
            $path = "ppt/slides/slide{$i}.xml";

            if ($zip->getFromName($path) !== false) {
                $slides[] = $path;
            } else {
                break;
            }
        }

        return $slides;
    }

    /* =============================================================
     | Slide Parsing
     |============================================================= */

    private function parseSlide(\ZipArchive $zip, string $slidePath, int $slideNumber): ?Section
    {
        $xml = $zip->getFromName($slidePath);

        if ($xml === false) {
            return null;
        }

        $dom = new \DOMDocument();
        @$dom->loadXML($xml);

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('p', self::NS_MAIN);
        $xpath->registerNamespace('a', self::NS_DRAWING);
        $xpath->registerNamespace('r', self::NS_REL);

        $section = new Section("slide-{$slideNumber}");

        $this->extractShapeText($xpath, $section);
        $this->extractSlideTable($xpath, $section);
        $this->extractSlideImages($zip, $slidePath, $xpath, $section);

        $elements = $section->getElements();
        if (empty($elements)) {
            return null;
        }

        return $section;
    }

    /* =============================================================
     | Text Extraction (shapes, text bodies)
     |============================================================= */

    private function extractShapeText(\DOMXPath $xpath, Section $section): void
    {
        foreach ($this->queryElements($xpath, '//p:sp') as $sp) {
            $txBody = $this->queryElement($xpath, './/p:txBody', $sp);

            if ($txBody === null) {
                continue;
            }

            foreach ($this->queryElements($xpath, 'a:p', $txBody) as $pNode) {
                $this->parseParagraph($pNode, $section, $xpath);
            }
        }
    }

    private function parseParagraph(\DOMElement $pNode, Section $section, \DOMXPath $xpath): void
    {
        $paragraph = new Paragraph();
        $hasContent = false;

        foreach ($this->queryElements($xpath, 'a:r', $pNode) as $run) {
            $tNode = $this->queryElement($xpath, 'a:t', $run);

            if ($tNode === null) {
                continue;
            }

            $text = $tNode->textContent;

            if ($text === '') {
                continue;
            }

            $style = $this->extractRunStyle($run, $xpath);
            $paragraph->addRun(new TextRun($text, $style));
            $hasContent = true;
        }

        foreach ($this->queryElements($xpath, 'a:fld/a:t', $pNode) as $t) {
            $text = $t->textContent;
            if ($text !== '') {
                $paragraph->addRun(new TextRun($text));
                $hasContent = true;
            }
        }

        if ($hasContent) {
            $pPr = $this->queryElement($xpath, 'a:pPr', $pNode);
            $headingLevel = $this->detectHeadingLevel($pPr, $xpath);

            if ($headingLevel !== null) {
                $paraStyle = ParagraphStyle::make()->setHeadingLevel($headingLevel);
                $paragraph->setStyle($paraStyle);
            }

            $section->addElement($paragraph);
        }
    }

    private function detectHeadingLevel(?\DOMElement $pPr, \DOMXPath $xpath): ?int
    {
        if ($pPr === null) {
            return null;
        }

        $lvl = $pPr->getAttribute('lvl');

        if ($lvl !== '' && (int) $lvl === 0) {
            $defRPr = $this->queryElement($xpath, 'a:defRPr', $pPr);

            if ($defRPr !== null) {
                $sz = $defRPr->getAttribute('sz');

                if ($sz !== '' && (int) $sz >= 2400) {
                    return 1;
                }
                if ($sz !== '' && (int) $sz >= 2000) {
                    return 2;
                }
            }
        }

        return null;
    }

    private function extractRunStyle(\DOMElement $run, \DOMXPath $xpath): ?TextStyle
    {
        $rPr = $this->queryElement($xpath, 'a:rPr', $run);

        if ($rPr === null) {
            return null;
        }

        $style = TextStyle::make();
        $hasProps = false;

        if ($rPr->getAttribute('b') === '1') {
            $style->setBold();
            $hasProps = true;
        }

        if ($rPr->getAttribute('i') === '1') {
            $style->setItalic();
            $hasProps = true;
        }

        if ($rPr->getAttribute('u') !== '' && $rPr->getAttribute('u') !== 'none') {
            $style->setUnderline();
            $hasProps = true;
        }

        $sz = $rPr->getAttribute('sz');
        if ($sz !== '') {
            $style->setFontSize((float) $sz / 100.0);
            $hasProps = true;
        }

        $solidFill = $this->queryElement($xpath, 'a:solidFill/a:srgbClr', $rPr);
        if ($solidFill !== null) {
            $color = $solidFill->getAttribute('val');
            if ($color !== '') {
                $style->setColor('#' . $color);
                $hasProps = true;
            }
        }

        $latin = $this->queryElement($xpath, 'a:latin', $rPr);
        if ($latin !== null) {
            $typeface = $latin->getAttribute('typeface');
            if ($typeface !== '') {
                $style->setFontFamily($typeface);
                $hasProps = true;
            }
        }

        return $hasProps ? $style : null;
    }

    /* =============================================================
     | Table Extraction
     |============================================================= */

    private function extractSlideTable(\DOMXPath $xpath, Section $section): void
    {
        foreach ($this->queryElements($xpath, '//a:tbl') as $tblNode) {
            $table = new Table();
            $isFirstRow = true;

            foreach ($this->queryElements($xpath, 'a:tr', $tblNode) as $tr) {
                $row = new TableRow();

                if ($isFirstRow) {
                    $row->setHeader();
                    $isFirstRow = false;
                }

                foreach ($this->queryElements($xpath, 'a:tc', $tr) as $tc) {
                    $cell = new TableCell();
                    $text = $this->extractTextFromBody($tc, $xpath);
                    $cell->addElement((new Paragraph())->addRun(new TextRun($text)));

                    $gridSpan = $tc->getAttribute('gridSpan');
                    if ($gridSpan !== '' && (int) $gridSpan > 1) {
                        $cell->setColspan((int) $gridSpan);
                    }

                    $rowSpan = $tc->getAttribute('rowSpan');
                    if ($rowSpan !== '' && (int) $rowSpan > 1) {
                        $cell->setRowspan((int) $rowSpan);
                    }

                    $row->addCell($cell);
                }

                $table->addRow($row);
            }

            if (count($table->getRows()) > 0) {
                $section->addElement($table);
            }
        }
    }

    private function extractTextFromBody(\DOMElement $node, \DOMXPath $xpath): string
    {
        $parts = [];

        foreach ($this->queryElements($xpath, './/a:t', $node) as $t) {
            $parts[] = $t->textContent;
        }

        return implode(' ', $parts);
    }

    /* =============================================================
     | Image Extraction
     |============================================================= */

    private function extractSlideImages(
        \ZipArchive $zip,
        string $slidePath,
        \DOMXPath $xpath,
        Section $section,
    ): void {
        $slideDir = dirname($slidePath);
        $slideFile = basename($slidePath);
        $relsPath = $slideDir . '/_rels/' . $slideFile . '.rels';
        $rels = $this->loadRelationships($zip, $relsPath);

        foreach ($this->queryElements($xpath, '//a:blip') as $blip) {
            $rEmbed = $blip->getAttributeNS(self::NS_REL, 'embed');

            if ($rEmbed === '' || ! isset($rels[$rEmbed])) {
                continue;
            }

            $target = $rels[$rEmbed];
            $imagePath = $this->resolveRelPath($slideDir, $target);

            $data = $zip->getFromName($imagePath);

            if ($data === false) {
                continue;
            }

            $mimeType = $this->guessMimeType($imagePath);
            $image = Image::fromData($data, $mimeType);
            $image->setSrc($target);

            $section->addElement($image);
        }
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
        @$dom->loadXML($core);

        $titleNode = $dom->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'title')->item(0);

        if ($titleNode && trim($titleNode->textContent) !== '') {
            $document->setTitle(trim($titleNode->textContent));
        }

        $creatorNode = $dom->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'creator')->item(0);

        if ($creatorNode) {
            $document->setMetadata('author', trim($creatorNode->textContent));
        }
    }

    /* =============================================================
     | Helpers
     |============================================================= */

    /**
     * @return array<string, string> rId → target
     */
    private function loadRelationships(\ZipArchive $zip, string $relsPath): array
    {
        $rels = [];
        $xml = $zip->getFromName($relsPath);

        if ($xml === false) {
            return $rels;
        }

        $dom = new \DOMDocument();
        @$dom->loadXML($xml);

        foreach ($dom->getElementsByTagName('Relationship') as $rel) {
            /** @var \DOMElement $rel */
            $id = $rel->getAttribute('Id');
            $target = $rel->getAttribute('Target');

            if ($id && $target) {
                $rels[$id] = $target;
            }
        }

        return $rels;
    }

    private function resolveRelPath(string $baseDir, string $target): string
    {
        if (str_starts_with($target, '/')) {
            return ltrim($target, '/');
        }

        $parts = explode('/', $baseDir . '/' . $target);
        $resolved = [];

        foreach ($parts as $part) {
            if ($part === '..') {
                array_pop($resolved);
            } elseif ($part !== '.' && $part !== '') {
                $resolved[] = $part;
            }
        }

        return implode('/', $resolved);
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
            'svg'         => 'image/svg+xml',
            'emf'         => 'image/x-emf',
            'wmf'         => 'image/x-wmf',
            'tiff', 'tif' => 'image/tiff',
            default       => 'application/octet-stream',
        };
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
