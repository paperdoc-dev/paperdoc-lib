<?php

declare(strict_types=1);

namespace Paperdoc\Parsers;

use Paperdoc\Contracts\{DocumentInterface, ParserInterface};
use Paperdoc\Document\{Document, Paragraph, Section, Table, TableCell, TableRow, TextRun};
use Paperdoc\Support\Ole2\Ole2Reader;

/**
 * Parser pour les fichiers .xls (Excel 97-2003, format BIFF8 binaire).
 *
 * Stratégie :
 * 1. Ouvrir le fichier OLE2 via le flux « Workbook » (ou « Book »)
 * 2. Lire les enregistrements BIFF8 séquentiellement
 * 3. Collecter les SST (Shared String Table)
 * 4. Collecter les cellules (LABELSST, LABEL, NUMBER, RK, MULRK, FORMULA)
 * 5. Organiser par feuille (BOUNDSHEET) et par ligne/colonne
 *
 * Référence : [MS-XLS] — Excel (.xls) Binary File Format
 */
class XlsParser extends AbstractParser implements ParserInterface
{
    private const RECORD_BOF        = 0x0809;
    private const RECORD_EOF        = 0x000A;
    private const RECORD_BOUNDSHEET = 0x0085;
    private const RECORD_SST        = 0x00FC;
    private const RECORD_CONTINUE   = 0x003C;
    private const RECORD_LABELSST   = 0x00FD;
    private const RECORD_LABEL      = 0x0204;
    private const RECORD_NUMBER     = 0x0203;
    private const RECORD_RK         = 0x027E;
    private const RECORD_MULRK      = 0x00BD;
    private const RECORD_FORMULA    = 0x0006;
    private const RECORD_STRING     = 0x0207;
    private const RECORD_BOOLERR    = 0x0205;
    private const RECORD_SHEET      = 0x0085;

    /** @var string[] */
    private array $sst = [];

    /** @var array<int, array<int, array<int, string>>> sheetIndex → row → col → value */
    private array $cellData = [];

    /** @var string[] Sheet names */
    private array $sheetNames = [];

    public function supports(string $extension): bool
    {
        return strtolower($extension) === 'xls';
    }

    public function parse(string $filename): DocumentInterface
    {
        $this->assertFileReadable($filename);

        $ole = Ole2Reader::fromFile($filename);
        $document = new Document('xls');
        $document->setTitle(pathinfo($filename, PATHINFO_FILENAME));

        $streamName = $ole->hasStream('Workbook') ? 'Workbook' : 'Book';

        if (! $ole->hasStream($streamName)) {
            return $document;
        }

        $stream = $ole->getStream($streamName);
        $this->sst = [];
        $this->cellData = [];
        $this->sheetNames = [];

        $this->parseRecords($stream);
        $this->extractSummaryInfo($ole, $document);

        foreach ($this->cellData as $sheetIdx => $rows) {
            $sheetName = $this->sheetNames[$sheetIdx] ?? 'Sheet' . ($sheetIdx + 1);
            $section = new Section($sheetName);
            $table = $this->buildTable($rows);

            if ($table !== null) {
                $section->addElement($table);
                $document->addSection($section);
            }
        }

        $this->sst = [];
        $this->cellData = [];
        $this->sheetNames = [];

        return $document;
    }

    /* =============================================================
     | BIFF8 Record Parsing
     |============================================================= */

    private function parseRecords(string $stream): void
    {
        $pos = 0;
        $len = strlen($stream);
        $currentSheet = -1;
        $bofCount = 0;
        $lastFormulaRow = -1;
        $lastFormulaCol = -1;
        $lastFormulaSheet = -1;

        while ($pos + 4 <= $len) {
            $recordType = $this->readUint16($stream, $pos);
            $recordLen = $this->readUint16($stream, $pos + 2);
            $pos += 4;

            if ($pos + $recordLen > $len) {
                break;
            }

            $data = substr($stream, $pos, $recordLen);

            switch ($recordType) {
                case self::RECORD_BOF:
                    $bofCount++;
                    if ($bofCount > 1) {
                        $currentSheet++;
                    }
                    break;

                case self::RECORD_BOUNDSHEET:
                    if ($recordLen >= 8) {
                        $name = $this->readBiffString($data, 6);
                        $this->sheetNames[] = $name;
                    }
                    break;

                case self::RECORD_SST:
                    $this->parseSst($data, $stream, $pos + $recordLen);
                    break;

                case self::RECORD_LABELSST:
                    if ($recordLen >= 10 && $currentSheet >= 0) {
                        $row = $this->readUint16($data, 0);
                        $col = $this->readUint16($data, 2);
                        $sstIdx = $this->readUint32($data, 6);
                        $this->cellData[$currentSheet][$row][$col] = $this->sst[$sstIdx] ?? '';
                    }
                    break;

                case self::RECORD_LABEL:
                    if ($recordLen >= 8 && $currentSheet >= 0) {
                        $row = $this->readUint16($data, 0);
                        $col = $this->readUint16($data, 2);
                        $text = $this->readBiffString($data, 6);
                        $this->cellData[$currentSheet][$row][$col] = $text;
                    }
                    break;

                case self::RECORD_NUMBER:
                    if ($recordLen >= 14 && $currentSheet >= 0) {
                        $row = $this->readUint16($data, 0);
                        $col = $this->readUint16($data, 2);
                        $val = unpack('d', substr($data, 6, 8))[1];
                        $this->cellData[$currentSheet][$row][$col] = $this->formatNumber($val);
                    }
                    break;

                case self::RECORD_RK:
                    if ($recordLen >= 10 && $currentSheet >= 0) {
                        $row = $this->readUint16($data, 0);
                        $col = $this->readUint16($data, 2);
                        $val = $this->decodeRk($this->readUint32($data, 6));
                        $this->cellData[$currentSheet][$row][$col] = $this->formatNumber($val);
                    }
                    break;

                case self::RECORD_MULRK:
                    if ($recordLen >= 6 && $currentSheet >= 0) {
                        $row = $this->readUint16($data, 0);
                        $firstCol = $this->readUint16($data, 2);
                        $numRks = (int) (($recordLen - 6) / 6);

                        for ($i = 0; $i < $numRks; $i++) {
                            $rkOffset = 4 + $i * 6 + 2;
                            if ($rkOffset + 4 <= $recordLen) {
                                $val = $this->decodeRk($this->readUint32($data, $rkOffset));
                                $this->cellData[$currentSheet][$row][$firstCol + $i] = $this->formatNumber($val);
                            }
                        }
                    }
                    break;

                case self::RECORD_FORMULA:
                    if ($recordLen >= 20 && $currentSheet >= 0) {
                        $row = $this->readUint16($data, 0);
                        $col = $this->readUint16($data, 2);

                        $byte6 = ord($data[12]);
                        $byte7 = ord($data[13]);

                        if ($byte6 === 0xFF && $byte7 === 0xFF) {
                            $resultType = ord($data[6]);
                            if ($resultType === 0) {
                                $lastFormulaRow = $row;
                                $lastFormulaCol = $col;
                                $lastFormulaSheet = $currentSheet;
                            } elseif ($resultType === 1) {
                                $boolVal = ord($data[8]);
                                $this->cellData[$currentSheet][$row][$col] = $boolVal ? 'TRUE' : 'FALSE';
                            }
                        } else {
                            $val = unpack('d', substr($data, 6, 8))[1];
                            $this->cellData[$currentSheet][$row][$col] = $this->formatNumber($val);
                        }
                    }
                    break;

                case self::RECORD_STRING:
                    if ($lastFormulaSheet >= 0) {
                        $text = $this->readBiffString($data, 0);
                        $this->cellData[$lastFormulaSheet][$lastFormulaRow][$lastFormulaCol] = $text;
                        $lastFormulaSheet = -1;
                    }
                    break;

                case self::RECORD_BOOLERR:
                    if ($recordLen >= 8 && $currentSheet >= 0) {
                        $row = $this->readUint16($data, 0);
                        $col = $this->readUint16($data, 2);
                        $isError = ord($data[7]);

                        if ($isError === 0) {
                            $this->cellData[$currentSheet][$row][$col] = ord($data[6]) ? 'TRUE' : 'FALSE';
                        }
                    }
                    break;
            }

            $pos += $recordLen;
        }
    }

    /* =============================================================
     | SST (Shared String Table)
     |============================================================= */

    private function parseSst(string $data, string $stream, int $nextPos): void
    {
        if (strlen($data) < 8) {
            return;
        }

        $totalStrings = $this->readUint32($data, 4);
        $pos = 8;
        $dataLen = strlen($data);

        for ($i = 0; $i < $totalStrings; $i++) {
            if ($pos >= $dataLen) {
                $continued = $this->readContinue($stream, $nextPos);

                if ($continued === null) {
                    break;
                }

                $data = $continued['data'];
                $dataLen = strlen($data);
                $nextPos = $continued['nextPos'];
                $pos = 0;
            }

            if ($pos + 3 > $dataLen) {
                break;
            }

            $charCount = $this->readUint16($data, $pos);
            $flags = ord($data[$pos + 2]);
            $pos += 3;

            $isUnicode = ($flags & 0x01) !== 0;
            $hasExtRst = ($flags & 0x04) !== 0;
            $hasRichText = ($flags & 0x08) !== 0;

            $richTextRuns = 0;
            $extRstSize = 0;

            if ($hasRichText && $pos + 2 <= $dataLen) {
                $richTextRuns = $this->readUint16($data, $pos);
                $pos += 2;
            }

            if ($hasExtRst && $pos + 4 <= $dataLen) {
                $extRstSize = $this->readUint32($data, $pos);
                $pos += 4;
            }

            $byteCount = $isUnicode ? $charCount * 2 : $charCount;

            if ($pos + $byteCount > $dataLen) {
                $partial = substr($data, $pos);
                $remaining = $byteCount - strlen($partial);

                $continued = $this->readContinue($stream, $nextPos);

                if ($continued !== null) {
                    $partial .= substr($continued['data'], 0, $remaining);
                    $data = $continued['data'];
                    $dataLen = strlen($data);
                    $nextPos = $continued['nextPos'];
                    $pos = $remaining;
                }

                if ($isUnicode) {
                    $this->sst[] = mb_convert_encoding($partial, 'UTF-8', 'UTF-16LE');
                } else {
                    $this->sst[] = mb_convert_encoding($partial, 'UTF-8', 'Windows-1252');
                }
            } else {
                $raw = substr($data, $pos, $byteCount);
                $pos += $byteCount;

                if ($isUnicode) {
                    $this->sst[] = mb_convert_encoding($raw, 'UTF-8', 'UTF-16LE');
                } else {
                    $this->sst[] = mb_convert_encoding($raw, 'UTF-8', 'Windows-1252');
                }
            }

            $pos += $richTextRuns * 4;
            $pos += $extRstSize;
        }
    }

    /**
     * @return array{data: string, nextPos: int}|null
     */
    private function readContinue(string $stream, int $pos): ?array
    {
        if ($pos + 4 > strlen($stream)) {
            return null;
        }

        $type = $this->readUint16($stream, $pos);
        $len = $this->readUint16($stream, $pos + 2);

        if ($type !== self::RECORD_CONTINUE || $pos + 4 + $len > strlen($stream)) {
            return null;
        }

        return [
            'data' => substr($stream, $pos + 4, $len),
            'nextPos' => $pos + 4 + $len,
        ];
    }

    /* =============================================================
     | Build Table from cell data
     |============================================================= */

    private function buildTable(array $rows): ?Table
    {
        if (empty($rows)) {
            return null;
        }

        ksort($rows);

        $maxCol = 0;
        foreach ($rows as $cols) {
            $maxCol = max($maxCol, max(array_keys($cols)));
        }

        $table = new Table();
        $isFirst = true;

        foreach ($rows as $rowIdx => $cols) {
            $row = new TableRow();

            if ($isFirst) {
                $row->setHeader();
                $isFirst = false;
            }

            for ($c = 0; $c <= $maxCol; $c++) {
                $cell = new TableCell();
                $text = $cols[$c] ?? '';
                $cell->addElement((new Paragraph())->addRun(new TextRun($text)));
                $row->addCell($cell);
            }

            $table->addRow($row);
        }

        return $table;
    }

    /* =============================================================
     | Metadata
     |============================================================= */

    private function extractSummaryInfo(Ole2Reader $ole, Document $document): void
    {
        if (! $ole->hasStream("\x05SummaryInformation")) {
            return;
        }

        try {
            $data = $ole->getStream("\x05SummaryInformation");
            $this->parseSummaryInfo($data, $document);
        } catch (\Throwable) {
            // Non-critical
        }
    }

    private function parseSummaryInfo(string $data, Document $document): void
    {
        if (strlen($data) < 48) {
            return;
        }

        $offset = $this->readUint32($data, 44);

        if ($offset + 8 > strlen($data)) {
            return;
        }

        $numProps = $this->readUint32($data, $offset + 4);
        $propBase = $offset + 8;

        for ($i = 0; $i < $numProps && $propBase + $i * 8 + 8 <= strlen($data); $i++) {
            $propId = $this->readUint32($data, $propBase + $i * 8);
            $propOffset = $this->readUint32($data, $propBase + $i * 8 + 4);
            $absOffset = $offset + $propOffset;

            if ($absOffset + 8 > strlen($data)) {
                continue;
            }

            $propType = $this->readUint32($data, $absOffset);

            if ($propType !== 30 && $propType !== 31) {
                continue;
            }

            $strLen = $this->readUint32($data, $absOffset + 4);

            if ($strLen <= 0 || $absOffset + 8 + $strLen > strlen($data)) {
                continue;
            }

            $str = rtrim(substr($data, $absOffset + 8, $strLen), "\x00");

            if ($propType === 31) {
                $str = mb_convert_encoding($str, 'UTF-8', 'UTF-16LE');
            } elseif (! mb_check_encoding($str, 'UTF-8')) {
                $str = mb_convert_encoding($str, 'UTF-8', 'Windows-1252');
            }

            $str = trim($str);

            if ($str === '') {
                continue;
            }

            match ($propId) {
                2 => $document->setTitle($str),
                4 => $document->setMetadata('author', $str),
                default => null,
            };
        }
    }

    /* =============================================================
     | Helpers
     |============================================================= */

    private function readBiffString(string $data, int $offset): string
    {
        if ($offset + 3 > strlen($data)) {
            return '';
        }

        $charCount = $this->readUint16($data, $offset);
        $flags = ord($data[$offset + 2]);
        $start = $offset + 3;

        $isUnicode = ($flags & 0x01) !== 0;
        $byteCount = $isUnicode ? $charCount * 2 : $charCount;

        if ($start + $byteCount > strlen($data)) {
            $byteCount = strlen($data) - $start;
        }

        $raw = substr($data, $start, $byteCount);

        if ($isUnicode) {
            return mb_convert_encoding($raw, 'UTF-8', 'UTF-16LE');
        }

        return mb_convert_encoding($raw, 'UTF-8', 'Windows-1252');
    }

    private function decodeRk(int $rk): float
    {
        $isDiv100 = ($rk & 0x01) !== 0;
        $isInt = ($rk & 0x02) !== 0;

        if ($isInt) {
            $val = (float) ($rk >> 2);

            if ($rk & 0x80000000) {
                $val -= 1073741824.0;
            }
        } else {
            $packed = pack('VV', 0, $rk & 0xFFFFFFFC);
            $val = unpack('d', $packed)[1];
        }

        return $isDiv100 ? $val / 100.0 : $val;
    }

    private function formatNumber(float $val): string
    {
        if ($val == (int) $val && abs($val) < 1e15) {
            return (string) (int) $val;
        }

        return rtrim(rtrim(sprintf('%.10f', $val), '0'), '.');
    }

    private function readUint16(string $data, int $offset): int
    {
        if ($offset + 2 > strlen($data)) {
            return 0;
        }

        return unpack('v', substr($data, $offset, 2))[1];
    }

    private function readUint32(string $data, int $offset): int
    {
        if ($offset + 4 > strlen($data)) {
            return 0;
        }

        return unpack('V', substr($data, $offset, 4))[1];
    }
}
