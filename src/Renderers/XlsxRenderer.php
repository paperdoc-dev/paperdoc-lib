<?php

declare(strict_types=1);

namespace Paperdoc\Renderers;

use Paperdoc\Contracts\DocumentInterface;
use Paperdoc\Document\{Paragraph, Table, TableRow};

/**
 * Renderer XLSX natif utilisant ZipArchive + XML.
 *
 * Génère des fichiers Office Open XML (SpreadsheetML) valides
 * sans aucune dépendance tierce.
 */
class XlsxRenderer extends AbstractRenderer
{
    public function getFormat(): string { return 'xlsx'; }

    public function render(DocumentInterface $document): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'paperdoc_xlsx_');

        try {
            $this->buildXlsx($document, $tmp);

            return file_get_contents($tmp) ?: '';
        } finally {
            @unlink($tmp);
        }
    }

    public function save(DocumentInterface $document, string $filename): void
    {
        $this->ensureDirectoryWritable($filename);
        $this->buildXlsx($document, $filename);
    }

    private function buildXlsx(DocumentInterface $document, string $filename): void
    {
        $zip = new \ZipArchive();

        if ($zip->open($filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Impossible de créer le fichier XLSX : {$filename}");
        }

        $sheets = $this->collectSheets($document);

        $zip->addFromString('[Content_Types].xml', $this->buildContentTypes($sheets));
        $zip->addFromString('_rels/.rels', $this->buildRootRels());
        $zip->addFromString('xl/workbook.xml', $this->buildWorkbook($sheets));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->buildWorkbookRels($sheets));
        $zip->addFromString('xl/styles.xml', $this->buildStyles());

        $allStrings = [];
        $sheetXmls = [];

        foreach ($sheets as $i => $sheet) {
            $xml = $this->buildSheet($sheet['rows'], $allStrings);
            $sheetXmls[] = $xml;
        }

        $zip->addFromString('xl/sharedStrings.xml', $this->buildSharedStrings($allStrings));

        foreach ($sheetXmls as $i => $xml) {
            $zip->addFromString("xl/worksheets/sheet" . ($i + 1) . ".xml", $xml);
        }

        $zip->addFromString('docProps/core.xml', $this->buildCoreMeta($document));

        $zip->close();
    }

    /**
     * @return array<int, array{name: string, rows: array<int, string[]>}>
     */
    private function collectSheets(DocumentInterface $document): array
    {
        $sheets = [];

        foreach ($document->getSections() as $section) {
            $rows = [];

            foreach ($section->getElements() as $element) {
                if ($element instanceof Table) {
                    foreach ($element->getRows() as $row) {
                        $cells = [];
                        foreach ($row->getCells() as $cell) {
                            $cells[] = $cell->getPlainText();
                        }
                        $rows[] = $cells;
                    }
                } elseif ($element instanceof Paragraph) {
                    $text = $element->getPlainText();
                    if (trim($text) !== '') {
                        $rows[] = [$text];
                    }
                }
            }

            if (! empty($rows)) {
                $name = $section->getName() ?: 'Sheet' . (count($sheets) + 1);
                $name = substr(preg_replace('/[\\\\\/\?\*\[\]:]+/', '_', $name), 0, 31);
                $sheets[] = ['name' => $name, 'rows' => $rows];
            }
        }

        if (empty($sheets)) {
            $sheets[] = ['name' => 'Sheet1', 'rows' => [['']]];
        }

        return $sheets;
    }

    /**
     * @param array<int, string[]> $rows
     * @param string[] $allStrings
     */
    private function buildSheet(array $rows, array &$allStrings): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $xml .= '<sheetData>';

        foreach ($rows as $rowIdx => $cells) {
            $rowNum = $rowIdx + 1;
            $xml .= "<row r=\"{$rowNum}\">";

            foreach ($cells as $colIdx => $value) {
                $colLetter = $this->colIndexToLetter($colIdx);
                $ref = $colLetter . $rowNum;

                if (is_numeric($value) && $value !== '') {
                    $xml .= "<c r=\"{$ref}\"><v>" . $this->escapeXml($value) . "</v></c>";
                } else {
                    $stringIdx = array_search($value, $allStrings, true);
                    if ($stringIdx === false) {
                        $stringIdx = count($allStrings);
                        $allStrings[] = $value;
                    }
                    $xml .= "<c r=\"{$ref}\" t=\"s\"><v>{$stringIdx}</v></c>";
                }
            }

            $xml .= '</row>';
        }

        $xml .= '</sheetData></worksheet>';

        return $xml;
    }

    private function colIndexToLetter(int $index): string
    {
        $result = '';

        while ($index >= 0) {
            $result = chr(65 + ($index % 26)) . $result;
            $index = (int) ($index / 26) - 1;
        }

        return $result;
    }

    /**
     * @param string[] $strings
     */
    private function buildSharedStrings(array $strings): string
    {
        $count = count($strings);
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= "<sst xmlns=\"http://schemas.openxmlformats.org/spreadsheetml/2006/main\" count=\"{$count}\" uniqueCount=\"{$count}\">";

        foreach ($strings as $s) {
            $xml .= '<si><t>' . $this->escapeXml($s) . '</t></si>';
        }

        $xml .= '</sst>';

        return $xml;
    }

    /**
     * @param array<int, array{name: string, rows: array}> $sheets
     */
    private function buildContentTypes(array $sheets): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">';
        $xml .= '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>';
        $xml .= '<Default Extension="xml" ContentType="application/xml"/>';
        $xml .= '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>';
        $xml .= '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';
        $xml .= '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>';
        $xml .= '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>';

        foreach ($sheets as $i => $sheet) {
            $n = $i + 1;
            $xml .= "<Override PartName=\"/xl/worksheets/sheet{$n}.xml\" ContentType=\"application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml\"/>";
        }

        $xml .= '</Types>';

        return $xml;
    }

    private function buildRootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '</Relationships>';
    }

    /**
     * @param array<int, array{name: string, rows: array}> $sheets
     */
    private function buildWorkbook(array $sheets): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';
        $xml .= '<sheets>';

        foreach ($sheets as $i => $sheet) {
            $n = $i + 1;
            $name = $this->escapeXml($sheet['name']);
            $xml .= "<sheet name=\"{$name}\" sheetId=\"{$n}\" r:id=\"rId{$n}\"/>";
        }

        $xml .= '</sheets></workbook>';

        return $xml;
    }

    /**
     * @param array<int, array{name: string, rows: array}> $sheets
     */
    private function buildWorkbookRels(array $sheets): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';

        foreach ($sheets as $i => $sheet) {
            $n = $i + 1;
            $xml .= "<Relationship Id=\"rId{$n}\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet\" Target=\"worksheets/sheet{$n}.xml\"/>";
        }

        $nextId = count($sheets) + 1;
        $xml .= "<Relationship Id=\"rId{$nextId}\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles\" Target=\"styles.xml\"/>";
        $nextId++;
        $xml .= "<Relationship Id=\"rId{$nextId}\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings\" Target=\"sharedStrings.xml\"/>";

        $xml .= '</Relationships>';

        return $xml;
    }

    private function buildStyles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>'
            . '</styleSheet>';
    }

    private function buildCoreMeta(DocumentInterface $document): string
    {
        $title = $this->escapeXml($document->getTitle());
        $author = $this->escapeXml($document->getMetadata()['author'] ?? 'Paperdoc');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/">'
            . "<dc:title>{$title}</dc:title>"
            . "<dc:creator>{$author}</dc:creator>"
            . '</cp:coreProperties>';
    }

    private function escapeXml(string $text): string
    {
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
