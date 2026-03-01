<?php

declare(strict_types=1);

namespace Paperdoc\Renderers;

use Paperdoc\Contracts\DocumentInterface;
use Paperdoc\Document\{Paragraph, Table};
use Paperdoc\Support\Ole2\Ole2Writer;

/**
 * Renderer XLS natif (Excel 97-2003 BIFF8).
 *
 * Produit des fichiers .xls valides conformes à [MS-XLS]
 * en utilisant des enregistrements BIFF8 dans un flux OLE2 « Workbook ».
 * Pas de dépendance tierce.
 */
class XlsRenderer extends AbstractRenderer
{
    private const RECORD_BOF        = 0x0809;
    private const RECORD_EOF        = 0x000A;
    private const RECORD_CODEPAGE   = 0x0042;
    private const RECORD_WINDOW1    = 0x003D;
    private const RECORD_FONT       = 0x0031;
    private const RECORD_XF         = 0x00E0;
    private const RECORD_STYLE      = 0x0293;
    private const RECORD_BOUNDSHEET = 0x0085;
    private const RECORD_DIMENSION  = 0x0200;
    private const RECORD_SST        = 0x00FC;
    private const RECORD_LABELSST   = 0x00FD;
    private const RECORD_NUMBER     = 0x0203;

    public function getFormat(): string { return 'xls'; }

    public function render(DocumentInterface $document): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'paperdoc_xls_');

        try {
            $this->buildXls($document, $tmp);

            return file_get_contents($tmp) ?: '';
        } finally {
            @unlink($tmp);
        }
    }

    public function save(DocumentInterface $document, string $filename): void
    {
        $this->ensureDirectoryWritable($filename);
        $this->buildXls($document, $filename);
    }

    /* =============================================================
     | Builder
     |============================================================= */

    private function buildXls(DocumentInterface $document, string $filename): void
    {
        $sheets = $this->collectSheets($document);
        $sst    = $this->buildSst($sheets);

        $workbook = $this->buildWorkbookGlobals($sheets, $sst);

        foreach ($sheets as $sheet) {
            $workbook .= $this->buildSheet($sheet, $sst);
        }

        $writer = new Ole2Writer();
        $writer->addStream('Workbook', $workbook);

        file_put_contents($filename, $writer->build());
    }

    /* =============================================================
     | Sheet Collection
     |============================================================= */

    /**
     * @return array<int, array{name: string, rows: array<int, array<int, string>>}>
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

    /* =============================================================
     | SST (Shared String Table)
     |============================================================= */

    /**
     * Build SST index map: unique string → SST index.
     *
     * @return array{strings: string[], index: array<string, int>}
     */
    private function buildSst(array $sheets): array
    {
        $strings = [];
        $index   = [];

        foreach ($sheets as $sheet) {
            foreach ($sheet['rows'] as $row) {
                foreach ($row as $value) {
                    if (! is_numeric($value) && ! isset($index[$value])) {
                        $index[$value] = count($strings);
                        $strings[] = $value;
                    }
                }
            }
        }

        return ['strings' => $strings, 'index' => $index];
    }

    /* =============================================================
     | Workbook Globals
     |============================================================= */

    private function buildWorkbookGlobals(array $sheets, array $sst): string
    {
        $data = '';

        // BOF — workbook globals
        $data .= $this->biffRecord(self::RECORD_BOF,
            pack('v', 0x0600) . pack('v', 0x0005) . pack('v', 0x0DBB) . pack('v', 0x07CC)
            . str_repeat("\x00", 6)
        );

        // CODEPAGE — UTF-16
        $data .= $this->biffRecord(self::RECORD_CODEPAGE, pack('v', 0x04B0));

        // WINDOW1 (minimal)
        $data .= $this->biffRecord(self::RECORD_WINDOW1,
            pack('v', 0) . pack('v', 0) . pack('v', 0x3A98) . pack('v', 0x1B58)
            . pack('v', 0x0038) . pack('v', 0) . pack('v', 0) . pack('v', 1)
        );

        // 5 default FONTs
        for ($i = 0; $i < 5; $i++) {
            $fontName = 'Calibri';
            $data .= $this->biffRecord(self::RECORD_FONT,
                pack('v', 200)          // dyHeight (10pt * 20)
                . pack('v', 0)          // grbit
                . pack('v', 0x7FFF)     // icv (default)
                . pack('v', 400)        // bls (normal weight)
                . pack('v', 0)          // sss
                . pack('C', 0)          // uls
                . pack('C', 0)          // bFamily
                . pack('C', 0)          // bCharSet
                . pack('C', 0)          // reserved
                . $this->biffString($fontName)
            );
        }

        // 21 default XF records (minimum for Excel to open cleanly)
        for ($i = 0; $i < 21; $i++) {
            $data .= $this->biffRecord(self::RECORD_XF,
                pack('v', 0)            // ifnt
                . pack('v', 0)          // ifmt
                . pack('v', ($i < 16) ? 0xFFF5 : 0x0001) // fLocked/hidden/style
                . pack('v', 0x20)       // alignment
                . pack('v', 0)          // rotation
                . pack('v', 0)          // text properties
                . pack('V', 0)          // borders
                . pack('V', 0)          // more borders
                . pack('V', 0x20C0)     // pattern/colours
            );
        }

        // STYLE for default (index 0)
        $data .= $this->biffRecord(self::RECORD_STYLE,
            pack('v', 0x8000) . pack('C', 0) . pack('C', 0xFF)
        );

        // BOUNDSHEET records (offsets patched after building)
        $boundsheetPositions = [];
        foreach ($sheets as $sheet) {
            $boundsheetPositions[] = strlen($data);
            $data .= $this->biffRecord(self::RECORD_BOUNDSHEET,
                pack('V', 0)            // lbPlyPos — placeholder, patched later
                . pack('C', 0)          // visibility
                . pack('C', 0)          // sheet type (worksheet)
                . $this->biffString($sheet['name'])
            );
        }

        // SST record
        $totalRefs = 0;
        foreach ($sheets as $sheet) {
            foreach ($sheet['rows'] as $row) {
                foreach ($row as $value) {
                    if (! is_numeric($value)) {
                        $totalRefs++;
                    }
                }
            }
        }

        $sstPayload  = pack('V', $totalRefs);
        $sstPayload .= pack('V', count($sst['strings']));

        foreach ($sst['strings'] as $str) {
            $sstPayload .= $this->biffUnicodeString($str);
        }

        $data .= $this->biffRecord(self::RECORD_SST, $sstPayload);

        // EOF
        $data .= $this->biffRecord(self::RECORD_EOF, '');

        $globalsLen = strlen($data);

        // Patch BOUNDSHEET offsets
        $sheetOffset = $globalsLen;
        foreach ($sheets as $i => $sheet) {
            $pos = $boundsheetPositions[$i] + 4; // skip record header
            $data = substr_replace($data, pack('V', $sheetOffset), $pos, 4);
            $sheetOffset += strlen($this->buildSheet($sheet, $sst));
        }

        return $data;
    }

    /* =============================================================
     | Sheet Data
     |============================================================= */

    private function buildSheet(array $sheet, array $sst): string
    {
        $data = '';

        // BOF — worksheet
        $data .= $this->biffRecord(self::RECORD_BOF,
            pack('v', 0x0600) . pack('v', 0x0010) . pack('v', 0x0DBB) . pack('v', 0x07CC)
            . str_repeat("\x00", 6)
        );

        // DIMENSION
        $rows = $sheet['rows'];
        $maxCol = 0;
        foreach ($rows as $row) {
            $maxCol = max($maxCol, count($row));
        }

        $data .= $this->biffRecord(self::RECORD_DIMENSION,
            pack('V', 0)                    // first row
            . pack('V', count($rows))       // last row + 1
            . pack('v', 0)                  // first col
            . pack('v', $maxCol)            // last col + 1
            . pack('v', 0)                  // reserved
        );

        // Cell records
        foreach ($rows as $rowIdx => $cells) {
            foreach ($cells as $colIdx => $value) {
                if (is_numeric($value) && $value !== '') {
                    // NUMBER record
                    $data .= $this->biffRecord(self::RECORD_NUMBER,
                        pack('v', $rowIdx)
                        . pack('v', $colIdx)
                        . pack('v', 0)      // XF index
                        . pack('d', (float) $value)
                    );
                } else {
                    // LABELSST record
                    $sstIdx = $sst['index'][$value] ?? 0;
                    $data .= $this->biffRecord(self::RECORD_LABELSST,
                        pack('v', $rowIdx)
                        . pack('v', $colIdx)
                        . pack('v', 0)      // XF index
                        . pack('V', $sstIdx)
                    );
                }
            }
        }

        // EOF
        $data .= $this->biffRecord(self::RECORD_EOF, '');

        return $data;
    }

    /* =============================================================
     | BIFF8 Helpers
     |============================================================= */

    private function biffRecord(int $type, string $data): string
    {
        return pack('v', $type) . pack('v', strlen($data)) . $data;
    }

    /**
     * BIFF8 byte string (used for sheet names, font names).
     * Format: charCount(1 byte) + flags(1 byte) + chars
     */
    private function biffString(string $str): string
    {
        $len = strlen($str);

        if ($len <= 255 && mb_check_encoding($str, 'ASCII')) {
            return pack('C', $len) . pack('C', 0x00) . $str;
        }

        $utf16 = mb_convert_encoding($str, 'UTF-16LE', 'UTF-8');

        return pack('C', mb_strlen($str)) . pack('C', 0x01) . $utf16;
    }

    /**
     * BIFF8 Unicode string for SST entries.
     * Format: charCount(2 bytes) + flags(1 byte) + chars
     */
    private function biffUnicodeString(string $str): string
    {
        $mbLen = mb_strlen($str, 'UTF-8');

        if (mb_check_encoding($str, 'ASCII')) {
            return pack('v', $mbLen) . pack('C', 0x00) . $str;
        }

        $utf16 = mb_convert_encoding($str, 'UTF-16LE', 'UTF-8');

        return pack('v', $mbLen) . pack('C', 0x01) . $utf16;
    }
}
