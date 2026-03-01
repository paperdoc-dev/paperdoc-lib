<?php

declare(strict_types=1);

namespace Paperdoc\Parsers;

use Paperdoc\Contracts\{DocumentInterface, ParserInterface};
use Paperdoc\Document\{Document, Paragraph, Section, Table, TableCell, TableRow, TextRun};
use Paperdoc\Document\Style\{ParagraphStyle, TextStyle};
use Paperdoc\Enum\Alignment;
use Paperdoc\Support\Ole2\Ole2Reader;

/**
 * Parser pour les fichiers .doc (Word 97-2003, format binaire).
 *
 * Stratégie :
 * 1. Ouvrir le fichier OLE2 Compound Document
 * 2. Lire le flux « WordDocument » pour le FIB (File Information Block)
 * 3. Lire le flux « 0Table » ou « 1Table » pour la Piece Table (CLX)
 * 4. Assembler le texte à partir des morceaux (pieces)
 * 5. Découper en paragraphes sur les marqueurs 0x0D
 * 6. Extraire les metadata depuis le flux « \005SummaryInformation »
 *
 * Référence : [MS-DOC] — Word (.doc) Binary File Format
 */
class DocParser extends AbstractParser implements ParserInterface
{
    public function supports(string $extension): bool
    {
        return strtolower($extension) === 'doc';
    }

    public function parse(string $filename): DocumentInterface
    {
        $this->assertFileReadable($filename);

        $ole = Ole2Reader::fromFile($filename);

        $document = new Document('doc');
        $document->setTitle(pathinfo($filename, PATHINFO_FILENAME));

        $this->extractSummaryInfo($ole, $document);

        $text = $this->extractText($ole);

        $section = new Section('main');
        $this->buildElements($text, $section);
        $document->addSection($section);

        return $document;
    }

    /* =============================================================
     | Text Extraction via FIB + Piece Table
     |============================================================= */

    private function extractText(Ole2Reader $ole): string
    {
        if (! $ole->hasStream('WordDocument')) {
            return '';
        }

        $wordDoc = $ole->getStream('WordDocument');

        if (strlen($wordDoc) < 68) {
            return '';
        }

        $fib = $this->parseFib($wordDoc);

        if ($fib['fcClx'] > 0 && $fib['lcbClx'] > 0) {
            $tableStream = $this->getTableStream($ole, $fib);

            if ($tableStream !== null) {
                $text = $this->extractTextFromPieceTable($wordDoc, $tableStream, $fib);

                if ($text !== '') {
                    return $text;
                }
            }
        }

        if ($fib['ccpText'] > 0) {
            $text = $this->extractInlineText($wordDoc, $fib);

            if ($text !== '') {
                return $text;
            }
        }

        return $this->fallbackTextExtraction($wordDoc);
    }

    /**
     * Extrait le texte directement depuis WordDocument quand il n'y a pas de CLX.
     * Scan depuis après le FIB pour trouver un bloc UTF-16LE ou CP1252.
     */
    private function extractInlineText(string $wordDoc, array $fib): string
    {
        $ccpText = $fib['ccpText'];
        $searchStart = $fib['fibEndOffset'];
        $streamLen = strlen($wordDoc);

        $startOffset = $this->findUtf16LETextStart($wordDoc, $searchStart, $ccpText);

        if ($startOffset !== null) {
            $byteLen = $ccpText * 2;

            if ($startOffset + $byteLen <= $streamLen) {
                $raw = substr($wordDoc, $startOffset, $byteLen);

                return mb_convert_encoding($raw, 'UTF-8', 'UTF-16LE');
            }
        }

        $startOffset = $this->findCp1252TextStart($wordDoc, $searchStart, $ccpText);

        if ($startOffset !== null && $startOffset + $ccpText <= $streamLen) {
            $raw = substr($wordDoc, $startOffset, $ccpText);

            return mb_convert_encoding($raw, 'UTF-8', 'Windows-1252');
        }

        return '';
    }

    /**
     * Cherche le debut d'un bloc UTF-16LE (pattern: octet, \x00, octet, \x00)
     * dans les positions candidates alignees a 512 octets.
     */
    private function findUtf16LETextStart(string $data, int $searchFrom, int $ccpText): ?int
    {
        $candidates = [];

        $aligned = (int) (ceil($searchFrom / 512) * 512);

        for ($offset = $aligned; $offset <= $aligned + 4096 && $offset + 20 <= strlen($data); $offset += 512) {
            $candidates[] = $offset;
        }

        foreach ($candidates as $offset) {
            if ($this->looksLikeUtf16LE($data, $offset) && $offset + $ccpText * 2 <= strlen($data)) {
                return $offset;
            }
        }

        return null;
    }

    private function looksLikeUtf16LE(string $data, int $offset): bool
    {
        $sampleLen = min(40, strlen($data) - $offset);

        if ($sampleLen < 10) {
            return false;
        }

        $nullCount = 0;
        $printable = 0;

        for ($i = 0; $i < $sampleLen; $i += 2) {
            $lo = ord($data[$offset + $i]);
            $hi = ord($data[$offset + $i + 1]);

            if ($hi === 0 && ($lo >= 0x20 || $lo === 0x0D || $lo === 0x0A || $lo === 0x09)) {
                $nullCount++;
                $printable++;
            }
        }

        return $printable >= ($sampleLen / 2) * 0.6;
    }

    private function findCp1252TextStart(string $data, int $searchFrom, int $ccpText): ?int
    {
        $aligned = (int) (ceil($searchFrom / 512) * 512);

        for ($offset = $aligned; $offset <= $aligned + 4096 && $offset + 20 <= strlen($data); $offset += 512) {
            $readable = 0;

            for ($i = 0; $i < min(40, strlen($data) - $offset); $i++) {
                $b = ord($data[$offset + $i]);

                if (($b >= 0x20 && $b <= 0x7E) || $b >= 0x80 || $b === 0x0D || $b === 0x0A) {
                    $readable++;
                }
            }

            if ($readable >= 30 && $offset + $ccpText <= strlen($data)) {
                return $offset;
            }
        }

        return null;
    }

    /**
     * @return array{ccpText: int, fcClx: int, lcbClx: int, tableName: string, fibEndOffset: int}
     */
    private function parseFib(string $wordDoc): array
    {
        $flags = $this->readUint16($wordDoc, 10);
        $whichTable = ($flags >> 9) & 1;

        $pos = 32;
        $csw = $this->readUint16($wordDoc, $pos);
        $pos += 2 + $csw * 2;

        $cslw = $this->readUint16($wordDoc, $pos);
        $pos += 2;

        $fibRgLwStart = $pos;
        $pos += $cslw * 4;

        $ccpText = ($cslw > 3) ? $this->readInt32($wordDoc, $fibRgLwStart + 3 * 4) : 0;

        $cbRgFcLcb = $this->readUint16($wordDoc, $pos);
        $pos += 2;
        $rgFcLcbStart = $pos;
        $pos += $cbRgFcLcb * 8;

        if ($pos + 2 <= strlen($wordDoc)) {
            $cswNew = $this->readUint16($wordDoc, $pos);
            $pos += 2 + $cswNew * 2;
        }

        $fcClx  = 0;
        $lcbClx = 0;

        $clxIndex = 66;
        if ($clxIndex < $cbRgFcLcb) {
            $fcClx  = $this->readUint32($wordDoc, $rgFcLcbStart + $clxIndex * 8);
            $lcbClx = $this->readUint32($wordDoc, $rgFcLcbStart + $clxIndex * 8 + 4);
        }

        return [
            'ccpText'       => max($ccpText, 0),
            'fcClx'         => (int) $fcClx,
            'lcbClx'        => (int) $lcbClx,
            'tableName'     => $whichTable === 1 ? '1Table' : '0Table',
            'fibEndOffset'  => $pos,
        ];
    }

    private function getTableStream(Ole2Reader $ole, array $fib): ?string
    {
        $name = $fib['tableName'];

        if ($ole->hasStream($name)) {
            return $ole->getStream($name);
        }

        $alt = $name === '1Table' ? '0Table' : '1Table';

        if ($ole->hasStream($alt)) {
            return $ole->getStream($alt);
        }

        return null;
    }

    private function extractTextFromPieceTable(string $wordDoc, string $tableStream, array $fib): string
    {
        $fcClx  = $fib['fcClx'];
        $lcbClx = $fib['lcbClx'];

        if ($fcClx <= 0 || $lcbClx <= 0 || $fcClx + $lcbClx > strlen($tableStream)) {
            return '';
        }

        $clxData = substr($tableStream, $fcClx, $lcbClx);

        return $this->parseClx($clxData, $wordDoc, $fib['ccpText']);
    }

    /**
     * Parse la structure CLX pour extraire le texte via les Piece Descriptors.
     */
    private function parseClx(string $clxData, string $wordDoc, int $ccpText): string
    {
        $pos = 0;
        $clxLen = strlen($clxData);

        while ($pos < $clxLen) {
            $type = ord($clxData[$pos]);

            if ($type === 1) {
                $cbGrpprl = $this->readUint16($clxData, $pos + 1);
                $pos += 3 + $cbGrpprl;
            } elseif ($type === 2) {
                $pos++;

                break;
            } else {
                $pos++;
            }
        }

        if ($pos >= $clxLen) {
            return '';
        }

        $pcdt_lcb = $this->readUint32($clxData, $pos);
        $pos += 4;

        $pcdSize = 8;
        $n = (int) (($pcdt_lcb - 4) / (4 + $pcdSize));

        if ($n <= 0 || $pos + ($n + 1) * 4 + $n * $pcdSize > $clxLen) {
            return '';
        }

        $cpArr = [];
        for ($i = 0; $i <= $n; $i++) {
            $cpArr[] = $this->readUint32($clxData, $pos + $i * 4);
        }
        $pos += ($n + 1) * 4;

        $text = '';
        $totalCp = 0;

        for ($i = 0; $i < $n; $i++) {
            $cpStart = $cpArr[$i];
            $cpEnd   = $cpArr[$i + 1];
            $charCount = $cpEnd - $cpStart;

            if ($charCount <= 0) {
                continue;
            }

            $pcdOffset = $pos + $i * $pcdSize;

            $fc = $this->readUint32($clxData, $pcdOffset + 2);

            $isCompressed = ($fc & 0x40000000) !== 0;
            $fcRaw = $fc & 0x3FFFFFFF;

            if ($isCompressed) {
                $fileOffset = (int) ($fcRaw / 2);
                $byteCount = $charCount;

                if ($fileOffset + $byteCount <= strlen($wordDoc)) {
                    $raw = substr($wordDoc, $fileOffset, $byteCount);
                    $text .= $this->convertCompressedText($raw);
                }
            } else {
                $fileOffset = $fcRaw;
                $byteCount = $charCount * 2;

                if ($fileOffset + $byteCount <= strlen($wordDoc)) {
                    $raw = substr($wordDoc, $fileOffset, $byteCount);
                    $text .= mb_convert_encoding($raw, 'UTF-8', 'UTF-16LE');
                }
            }

            $totalCp += $charCount;

            if ($ccpText > 0 && $totalCp >= $ccpText) {
                $text = mb_substr($text, 0, $ccpText);

                break;
            }
        }

        return $text;
    }

    /**
     * Convertit le texte compressé (CP1252 / Windows-1252) en UTF-8.
     */
    private function convertCompressedText(string $raw): string
    {
        return mb_convert_encoding($raw, 'UTF-8', 'Windows-1252');
    }

    /**
     * Extraction de secours : cherche les blocs de texte lisibles
     * directement dans le flux WordDocument.
     */
    private function fallbackTextExtraction(string $wordDoc): string
    {
        $text = '';
        $len = strlen($wordDoc);

        $start = min(2048, $len);

        $buffer = '';

        for ($i = $start; $i < $len; $i++) {
            $byte = ord($wordDoc[$i]);

            if ($byte >= 0x20 && $byte <= 0x7E || $byte === 0x0D || $byte === 0x0A || $byte === 0x09) {
                $buffer .= chr($byte);
            } else {
                if (mb_strlen($buffer) >= 3) {
                    $text .= $buffer;
                }
                $buffer = '';
            }
        }

        if (mb_strlen($buffer) >= 3) {
            $text .= $buffer;
        }

        return $text;
    }

    /* =============================================================
     | Summary Information (metadata)
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
            // Metadata non critique
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
            $propId     = $this->readUint32($data, $propBase + $i * 8);
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

            $str = substr($data, $absOffset + 8, $strLen);
            $str = rtrim($str, "\x00");

            if ($propType === 31) {
                $str = mb_convert_encoding($str, 'UTF-8', 'UTF-16LE');
            } else {
                if (! mb_check_encoding($str, 'UTF-8')) {
                    $str = mb_convert_encoding($str, 'UTF-8', 'Windows-1252');
                }
            }

            $str = trim($str);

            if ($str === '') {
                continue;
            }

            match ($propId) {
                2 => $document->setTitle($str),
                4 => $document->setMetadata('author', $str),
                6 => $document->setMetadata('comments', $str),
                default => null,
            };
        }
    }

    /* =============================================================
     | Build Document Elements
     |============================================================= */

    private function buildElements(string $text, Section $section): void
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $text);

        $paragraphs = explode("\n", $text);

        foreach ($paragraphs as $para) {
            $para = trim($para);

            if ($para === '') {
                continue;
            }

            $section->addText($para);
        }
    }

    /* =============================================================
     | Binary Reading Helpers
     |============================================================= */

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

    private function readInt32(string $data, int $offset): int
    {
        if ($offset + 4 > strlen($data)) {
            return 0;
        }

        $val = unpack('V', substr($data, $offset, 4))[1];

        if ($val >= 0x80000000) {
            return (int) ($val - 0x100000000);
        }

        return (int) $val;
    }
}
