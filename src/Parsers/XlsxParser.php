<?php

declare(strict_types=1);

namespace Paperdoc\Parsers;

use Paperdoc\Contracts\{DocumentInterface, ParserInterface};
use Paperdoc\Document\{Document, Image, Paragraph, Section, Table, TableCell, TableRow, TextRun};
use Paperdoc\Document\Style\TextStyle;

/**
 * Parser XLSX natif utilisant ZipArchive + XML.
 *
 * Les fichiers .xlsx sont des archives ZIP contenant du XML
 * au format Office Open XML (SpreadsheetML).
 * Chaque feuille est stockée dans xl/worksheets/sheet{n}.xml.
 */
class XlsxParser extends AbstractParser implements ParserInterface
{
    private const NS_MAIN = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
    private const NS_REL  = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    private const NS_DRAWING_REL = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing';
    private const NS_IMAGE_REL = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image';

    /** @var string[] Shared strings table */
    private array $sharedStrings = [];

    /** @var array<int, array{name: string, path: string}> */
    private array $sheets = [];

    public function supports(string $extension): bool
    {
        return in_array(strtolower($extension), ['xlsx'], true);
    }

    public function parse(string $filename): DocumentInterface
    {
        $this->assertFileReadable($filename);

        $zip = new \ZipArchive();

        if ($zip->open($filename) !== true) {
            throw new \RuntimeException("Impossible d'ouvrir le fichier XLSX : {$filename}");
        }

        $document = new Document('xlsx');
        $document->setTitle(pathinfo($filename, PATHINFO_FILENAME));

        $this->loadSharedStrings($zip);
        $this->loadWorkbook($zip);
        $this->extractMetadata($zip, $document);

        foreach ($this->sheets as $sheet) {
            $section = $this->parseSheet($zip, $sheet['name'], $sheet['path']);

            if ($section !== null) {
                $document->addSection($section);
            }
        }

        $this->extractImages($zip, $document);

        $zip->close();
        $this->sharedStrings = [];
        $this->sheets = [];

        return $document;
    }

    /* =============================================================
     | Shared Strings (xl/sharedStrings.xml)
     |============================================================= */

    private function loadSharedStrings(\ZipArchive $zip): void
    {
        $this->sharedStrings = [];
        $xml = $zip->getFromName('xl/sharedStrings.xml');

        if ($xml === false) {
            return;
        }

        $dom = new \DOMDocument();
        @$dom->loadXML($xml);

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('s', self::NS_MAIN);

        $siNodes = $xpath->query('//s:si');

        foreach ($siNodes as $si) {
            $text = '';
            $tNodes = $xpath->query('.//s:t', $si);

            foreach ($tNodes as $t) {
                $text .= $t->textContent;
            }

            $this->sharedStrings[] = $text;
        }
    }

    /* =============================================================
     | Workbook (xl/workbook.xml) — discover sheets
     |============================================================= */

    private function loadWorkbook(\ZipArchive $zip): void
    {
        $this->sheets = [];
        $xml = $zip->getFromName('xl/workbook.xml');

        if ($xml === false) {
            return;
        }

        $dom = new \DOMDocument();
        @$dom->loadXML($xml);

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('s', self::NS_MAIN);
        $xpath->registerNamespace('r', self::NS_REL);

        $sheetNodes = $xpath->query('//s:sheets/s:sheet');
        $rels = $this->loadRelationships($zip, 'xl/_rels/workbook.xml.rels');

        foreach ($sheetNodes as $i => $node) {
            /** @var \DOMElement $node */
            $name = $node->getAttribute('name') ?: 'Sheet' . ($i + 1);
            $rId = $node->getAttributeNS(self::NS_REL, 'id');

            $target = $rels[$rId] ?? "worksheets/sheet" . ($i + 1) . ".xml";
            $path = 'xl/' . ltrim($target, '/');

            $this->sheets[] = ['name' => $name, 'path' => $path];
        }

        if (empty($this->sheets)) {
            for ($i = 1; $i <= 10; $i++) {
                $path = "xl/worksheets/sheet{$i}.xml";
                if ($zip->getFromName($path) !== false) {
                    $this->sheets[] = ['name' => "Sheet{$i}", 'path' => $path];
                }
            }
        }
    }

    /* =============================================================
     | Sheet Parsing
     |============================================================= */

    private const MAX_SHEET_SIZE = 50 * 1024 * 1024;

    private function parseSheet(\ZipArchive $zip, string $sheetName, string $sheetPath): ?Section
    {
        $stat = $zip->statName($sheetPath);

        if ($stat !== false && $stat['size'] > self::MAX_SHEET_SIZE) {
            return null;
        }

        $xml = $zip->getFromName($sheetPath);

        if ($xml === false) {
            return null;
        }

        $dom = new \DOMDocument();
        @$dom->loadXML($xml);

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('s', self::NS_MAIN);

        $rows = $xpath->query('//s:sheetData/s:row');

        if ($rows->length === 0) {
            return null;
        }

        $section = new Section($sheetName);
        $table = new Table();
        $isFirstRow = true;
        $maxCol = 0;

        foreach ($rows as $rowNode) {
            /** @var \DOMElement $rowNode */
            $cells = $xpath->query('s:c', $rowNode);
            $rowData = [];

            foreach ($cells as $cellNode) {
                /** @var \DOMElement $cellNode */
                $ref = $cellNode->getAttribute('r');
                $colIndex = $this->colRefToIndex($ref);
                $value = $this->getCellValue($cellNode, $xpath);
                $rowData[$colIndex] = $value;

                if ($colIndex > $maxCol) {
                    $maxCol = $colIndex;
                }
            }

            $row = new TableRow();

            if ($isFirstRow) {
                $row->setHeader();
                $isFirstRow = false;
            }

            for ($col = 0; $col <= $maxCol; $col++) {
                $cell = new TableCell();
                $text = $rowData[$col] ?? '';
                $cell->addElement((new Paragraph())->addRun(new TextRun($text)));
                $row->addCell($cell);
            }

            $table->addRow($row);
        }

        if (count($table->getRows()) > 0) {
            $section->addElement($table);
        }

        return $section;
    }

    private function getCellValue(\DOMElement $cell, \DOMXPath $xpath): string
    {
        $type = $cell->getAttribute('t');
        $vNode = $xpath->query('s:v', $cell)->item(0);
        $isNode = $xpath->query('s:is/s:t', $cell)->item(0);

        if ($isNode !== null) {
            return $isNode->textContent;
        }

        if ($vNode === null) {
            return '';
        }

        $value = $vNode->textContent;

        if ($type === 's' && isset($this->sharedStrings[(int) $value])) {
            return $this->sharedStrings[(int) $value];
        }

        if ($type === 'b') {
            return $value === '1' ? 'TRUE' : 'FALSE';
        }

        return $value;
    }

    /**
     * Convert cell reference like "C5" to a 0-based column index.
     */
    private function colRefToIndex(string $ref): int
    {
        $letters = preg_replace('/[^A-Z]/i', '', strtoupper($ref));

        if ($letters === '') {
            return 0;
        }

        $index = 0;
        $len = strlen($letters);

        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($letters[$i]) - ord('A') + 1);
        }

        return $index - 1;
    }

    /* =============================================================
     | Images
     |============================================================= */

    private function extractImages(\ZipArchive $zip, Document $document): void
    {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if ($name === false || ! preg_match('#^xl/media/#i', $name)) {
                continue;
            }

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'emf', 'wmf'], true)) {
                continue;
            }

            $data = $zip->getFromName($name);

            if ($data === false) {
                continue;
            }

            $mimeType = $this->guessMimeType($name);
            $image = Image::fromData($data, $mimeType);

            $sections = $document->getSections();
            $target = ! empty($sections) ? $sections[0] : null;

            if ($target === null) {
                $target = new Section('images');
                $document->addSection($target);
            }

            $target->addElement($image);
        }
    }

    /* =============================================================
     | Metadata (docProps/core.xml)
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

    private function guessMimeType(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'gif'         => 'image/gif',
            'bmp'         => 'image/bmp',
            'webp'        => 'image/webp',
            'emf'         => 'image/x-emf',
            'wmf'         => 'image/x-wmf',
            default       => 'application/octet-stream',
        };
    }
}
