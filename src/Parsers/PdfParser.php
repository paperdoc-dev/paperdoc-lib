<?php

declare(strict_types=1);

namespace Paperdoc\Parsers;

use Paperdoc\Contracts\{DocumentInterface, ParserInterface};
use Paperdoc\Document\{Document, Image, Paragraph, Section, Table, TableCell, TableRow, TextRun};

/**
 * Parser PDF natif — extraction de texte sans dépendance tierce.
 *
 * Stratégie :
 * 1. Localiser les objets PDF via scan séquentiel
 * 2. Trouver les content streams des pages
 * 3. Suivre les Form XObjects (opérateur Do)
 * 4. Décompresser (FlateDecode via gzuncompress/gzinflate)
 * 5. Extraire le texte via les opérateurs Tj, TJ, ', "
 * 6. Nettoyer les textes CID (espacement inter-caractères)
 * 7. Regrouper en paragraphes par position Y
 *
 * Limitations connues :
 * - Les PDF chiffrés ne sont pas supportés
 * - Les encodages ToUnicode/CMap complexes sont partiellement gérés
 * - Seules les images JPEG (DCTDecode) et PNG (FlateDecode) sont extraites
 */
class PdfParser extends AbstractParser implements ParserInterface
{
    /** @var array<int, string> objNum → raw object data */
    private array $objects = [];

    private string $rawContent = '';

    /** @var array<int, array<string, string>> font obj num → glyph→unicode map */
    private array $fontCMaps = [];

    public function supports(string $extension): bool
    {
        return strtolower($extension) === 'pdf';
    }

    public function parse(string $filename): DocumentInterface
    {
        $this->assertFileReadable($filename);

        $this->rawContent = file_get_contents($filename);
        $document = new Document('pdf');
        $document->setTitle(pathinfo($filename, PATHINFO_FILENAME));

        if (! str_starts_with($this->rawContent, '%PDF-')) {
            throw new \RuntimeException("Le fichier n'est pas un PDF valide : {$filename}");
        }

        if (preg_match('/\/Encrypt\s/', $this->rawContent)) {
            throw new \RuntimeException("Les PDF chiffrés ne sont pas supportés : {$filename}");
        }

        $this->parseObjects();
        $this->buildFontCMaps();
        $this->extractMetadata($document);

        $pages = $this->findPages();
        $sectionIndex = 0;

        foreach ($pages as $pageObjNum) {
            $sectionIndex++;
            $section = new Section("page-{$sectionIndex}");

            $fontMap = $this->resolvePageFontMap($pageObjNum);
            $streams = $this->getAllPageStreams($pageObjNum);
            $allLines = [];

            foreach ($streams as $streamData) {
                $lines = $this->extractTextLines($streamData, $fontMap);
                array_push($allLines, ...$lines);
            }

            if (! empty($allLines)) {
                $this->sortAndBuildElements($allLines, $section);
            }

            $this->extractPageImages($pageObjNum, $section);

            $document->addSection($section);
        }

        $this->objects = [];
        $this->rawContent = '';
        $this->fontCMaps = [];

        return $document;
    }

    /* =============================================================
     | Metadata
     |============================================================= */

    private function extractMetadata(Document $document): void
    {
        if (preg_match('/\/Info\s+(\d+)\s+\d+\s+R/', $this->rawContent, $m)) {
            $infoObj = $this->getRawObject((int) $m[1]);

            if ($infoObj !== null) {
                if (preg_match('/\/Title\s*\(([^)]*)\)/', $infoObj, $tm)) {
                    $title = $this->decodePdfString($tm[1]);
                    if ($title !== '') {
                        $document->setTitle($title);
                    }
                }

                if (preg_match('/\/Author\s*\(([^)]*)\)/', $infoObj, $am)) {
                    $document->setMetadata('author', $this->decodePdfString($am[1]));
                }

                if (preg_match('/\/Creator\s*\(([^)]*)\)/', $infoObj, $cm)) {
                    $document->setMetadata('creator', $this->decodePdfString($cm[1]));
                }
            }
        }
    }

    /* =============================================================
     | Object Parsing
     |============================================================= */

    private function parseObjects(): void
    {
        $this->objects = [];

        preg_match_all('/(\d+)\s+\d+\s+obj\b/', $this->rawContent, $matches, PREG_OFFSET_CAPTURE);

        foreach ($matches[1] as $i => $m) {
            $objNum = (int) $m[0];
            $startOffset = (int) $matches[0][$i][1];

            $endPos = strpos($this->rawContent, 'endobj', $startOffset);
            if ($endPos === false) {
                continue;
            }

            $this->objects[$objNum] = substr(
                $this->rawContent,
                $startOffset,
                $endPos + 6 - $startOffset
            );
        }

        $this->unpackObjectStreams();
    }

    /**
     * Unpacks objects stored inside ObjStm (Object Streams, PDF 1.5+).
     *
     * ObjStm format: /N <count> /First <byteOffset>
     * Header (before /First): pairs of "objNum offset" separated by whitespace.
     * Body (from /First): concatenated object dictionaries.
     */
    private function unpackObjectStreams(): void
    {
        $objStmKeys = [];

        foreach ($this->objects as $objNum => $data) {
            if (preg_match('/\/Type\s*\/ObjStm\b/', $data)) {
                $objStmKeys[] = $objNum;
            }
        }

        foreach ($objStmKeys as $stmObjNum) {
            $data = $this->objects[$stmObjNum];

            if (! preg_match('/\/N\s+(\d+)/', $data, $nm) || ! preg_match('/\/First\s+(\d+)/', $data, $fm)) {
                continue;
            }

            $n = (int) $nm[1];
            $first = (int) $fm[1];

            $decoded = $this->extractStreamFromObject($data);

            if ($decoded === '' || $first > strlen($decoded)) {
                continue;
            }

            $header = substr($decoded, 0, $first);
            $body = substr($decoded, $first);
            $pairs = preg_split('/\s+/', trim($header));

            for ($i = 0; $i < $n && ($i * 2 + 1) < count($pairs); $i++) {
                $embeddedObjNum = (int) $pairs[$i * 2];
                $offset = (int) $pairs[$i * 2 + 1];

                if (isset($this->objects[$embeddedObjNum])) {
                    continue;
                }

                $nextOffset = ($i + 1 < $n && ($i * 2 + 3) < count($pairs))
                    ? (int) $pairs[($i + 1) * 2 + 1]
                    : strlen($body);

                $objBody = substr($body, $offset, $nextOffset - $offset);
                $objBody = trim($objBody);

                $this->objects[$embeddedObjNum] = "{$embeddedObjNum} 0 obj\n{$objBody}\nendobj";
            }
        }
    }

    private function getRawObject(int $objNum): ?string
    {
        return $this->objects[$objNum] ?? null;
    }

    /* =============================================================
     | Font / CMap Parsing
     |============================================================= */

    private function buildFontCMaps(): void
    {
        $this->fontCMaps = [];

        foreach ($this->objects as $objNum => $data) {
            if (! preg_match('/\/ToUnicode\s+(\d+)\s+\d+\s+R/', $data, $tm)) {
                continue;
            }

            $cmapStream = $this->extractStreamFromObjNum((int) $tm[1]);

            if ($cmapStream === '') {
                continue;
            }

            $map = $this->parseCMapStream($cmapStream);

            if (! empty($map)) {
                $this->fontCMaps[$objNum] = $map;
            }
        }
    }

    /**
     * @return array<string, string> hex glyph code (uppercase) → UTF-8 character(s)
     */
    private function parseCMapStream(string $stream): array
    {
        $map = [];

        if (preg_match_all('/beginbfchar\s*\n(.*?)endbfchar/s', $stream, $charBlocks)) {
            foreach ($charBlocks[1] as $block) {
                preg_match_all('/<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>/', $block, $entries);

                foreach ($entries[1] as $i => $src) {
                    $map[strtoupper($src)] = $this->hexToUtf8($entries[2][$i]);
                }
            }
        }

        if (preg_match_all('/beginbfrange\s*\n(.*?)endbfrange/s', $stream, $rangeBlocks)) {
            foreach ($rangeBlocks[1] as $block) {
                preg_match_all('/<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>\s*(?:<([0-9A-Fa-f]+)>|\[([^\]]+)\])/', $block, $entries);

                foreach ($entries[1] as $i => $lo) {
                    $hi = $entries[2][$i];
                    $loInt = hexdec($lo);
                    $hiInt = hexdec($hi);
                    $srcLen = strlen($lo);

                    if ($entries[3][$i] !== '') {
                        $dstInt = hexdec($entries[3][$i]);
                        $dstLen = strlen($entries[3][$i]);

                        for ($code = $loInt; $code <= $hiInt; $code++) {
                            $srcHex = strtoupper(str_pad(dechex($code), $srcLen, '0', STR_PAD_LEFT));
                            $dstHex = str_pad(dechex($dstInt + ($code - $loInt)), $dstLen, '0', STR_PAD_LEFT);
                            $map[$srcHex] = $this->hexToUtf8($dstHex);
                        }
                    } elseif ($entries[4][$i] !== '') {
                        preg_match_all('/<([0-9A-Fa-f]+)>/', $entries[4][$i], $arr);

                        for ($j = 0, $code = $loInt; $code <= $hiInt && $j < count($arr[1]); $code++, $j++) {
                            $srcHex = strtoupper(str_pad(dechex($code), $srcLen, '0', STR_PAD_LEFT));
                            $map[$srcHex] = $this->hexToUtf8($arr[1][$j]);
                        }
                    }
                }
            }
        }

        return $map;
    }

    private function hexToUtf8(string $hex): string
    {
        if (strlen($hex) <= 4) {
            $cp = hexdec($hex);

            return $cp > 0 ? mb_chr($cp, 'UTF-8') : '';
        }

        $bytes = hex2bin($hex);

        return $bytes !== false ? mb_convert_encoding($bytes, 'UTF-8', 'UTF-16BE') : '';
    }

    /**
     * @return array<string, array<string, string>> fontName → CMap
     */
    private function resolvePageFontMap(int $pageObjNum): array
    {
        $obj = $this->objects[$pageObjNum] ?? '';
        $fontMap = [];
        $fontDicts = [];

        if (preg_match('/\/Font\s*<<([^>]+)>>/s', $obj, $m)) {
            $fontDicts[] = $m[1];
        }

        if (preg_match('/\/Resources\s+(\d+)\s+\d+\s+R/', $obj, $resRef)) {
            $resObj = $this->getRawObject((int) $resRef[1]) ?? '';

            if (preg_match('/\/Font\s*<<([^>]+)>>/s', $resObj, $m)) {
                $fontDicts[] = $m[1];
            }
        }

        foreach ($fontDicts as $dict) {
            preg_match_all('/\/(\w+)\s+(\d+)\s+\d+\s+R/', $dict, $refs);

            foreach ($refs[1] as $i => $name) {
                $objNum = (int) $refs[2][$i];

                if (isset($this->fontCMaps[$objNum])) {
                    $fontMap[$name] = $this->fontCMaps[$objNum];
                }
            }
        }

        return $fontMap;
    }

    /**
     * @param array<string, string> $cmap glyph hex → UTF-8 character
     */
    private function decodeHexViaCMap(string $hex, array $cmap): string
    {
        $hex = strtoupper(trim($hex));

        if (empty($cmap)) {
            $bytes = hex2bin($hex);

            return $bytes !== false ? $this->decodePdfString($bytes) : '';
        }

        $charLen = strlen(array_key_first($cmap));

        if ($charLen < 2) {
            $charLen = 2;
        }

        $result = '';
        $len = strlen($hex);

        for ($i = 0; $i < $len; $i += $charLen) {
            $code = substr($hex, $i, $charLen);

            if (isset($cmap[$code])) {
                $result .= $cmap[$code];
            } else {
                $cp = hexdec($code);

                if ($cp >= 0x20) {
                    $result .= mb_chr($cp, 'UTF-8');
                }
            }
        }

        return $result;
    }

    /* =============================================================
     | Page Discovery
     |============================================================= */

    /** @return int[] */
    private function findPages(): array
    {
        $pages = [];

        foreach ($this->objects as $objNum => $data) {
            if (preg_match('/\/Type\s*\/Page\b(?!s)/', $data)) {
                $pages[$objNum] = $objNum;
            }
        }

        foreach ($this->objects as $data) {
            if (! preg_match('/\/Type\s*\/Pages\b/', $data)) {
                continue;
            }

            if (preg_match('/\/Kids\s*\[([^\]]+)\]/', $data, $m)) {
                preg_match_all('/(\d+)\s+\d+\s+R/', $m[1], $refs);
                $ordered = [];

                foreach ($refs[1] as $refNum) {
                    $num = (int) $refNum;
                    if (isset($pages[$num])) {
                        $ordered[] = $num;
                    }
                }

                if (count($ordered) === count($pages)) {
                    return $ordered;
                }
            }
        }

        return array_values($pages);
    }

    /* =============================================================
     | Stream Extraction — includes XObject traversal
     |============================================================= */

    /**
     * Collecte tous les streams de contenu d'une page, y compris les XObjects.
     *
     * @return string[]
     */
    private function getAllPageStreams(int $pageObjNum, int $depth = 0): array
    {
        if ($depth > 10) {
            return [];
        }

        $obj = $this->objects[$pageObjNum] ?? null;
        if ($obj === null) {
            return [];
        }

        $streams = [];

        if (preg_match('/\/Contents\s+(\d+)\s+\d+\s+R/', $obj, $m)) {
            $s = $this->extractStreamFromObjNum((int) $m[1]);
            if ($s !== '') {
                $streams[] = $s;
            }
        } elseif (preg_match('/\/Contents\s*\[([^\]]+)\]/', $obj, $m)) {
            preg_match_all('/(\d+)\s+\d+\s+R/', $m[1], $refs);
            foreach ($refs[1] as $ref) {
                $s = $this->extractStreamFromObjNum((int) $ref);
                if ($s !== '') {
                    $streams[] = $s;
                }
            }
        } else {
            $s = $this->extractStreamFromObject($obj);
            if ($s !== '') {
                $streams[] = $s;
            }
        }

        $xobjRefs = $this->findXObjectRefs($obj);

        foreach ($xobjRefs as $xObjNum) {
            $xobjStreams = $this->getAllPageStreams($xObjNum, $depth + 1);
            array_push($streams, ...$xobjStreams);
        }

        return $streams;
    }

    /**
     * @return int[] Object numbers of referenced XObjects
     */
    private function findXObjectRefs(string $obj): array
    {
        $refs = [];

        if (preg_match_all('/\/XObject\s*<<([^>]+)>>/s', $obj, $xobjDefs)) {
            foreach ($xobjDefs[1] as $def) {
                preg_match_all('/\/\w+\s+(\d+)\s+\d+\s+R/', $def, $r);
                foreach ($r[1] as $refNum) {
                    $refs[] = (int) $refNum;
                }
            }
        }

        if (preg_match('/\/Resources\s+(\d+)\s+\d+\s+R/', $obj, $resRef)) {
            $resObj = $this->getRawObject((int) $resRef[1]);
            if ($resObj !== null && preg_match_all('/\/XObject\s*<<([^>]+)>>/s', $resObj, $xobjDefs)) {
                foreach ($xobjDefs[1] as $def) {
                    preg_match_all('/\/\w+\s+(\d+)\s+\d+\s+R/', $def, $r);
                    foreach ($r[1] as $refNum) {
                        $refs[] = (int) $refNum;
                    }
                }
            }
        }

        return array_unique($refs);
    }

    private function extractStreamFromObjNum(int $objNum): string
    {
        $obj = $this->objects[$objNum] ?? null;

        if ($obj === null) {
            return '';
        }

        return $this->extractStreamFromObject($obj);
    }

    private function extractStreamFromObject(string $obj): string
    {
        $streamStart = strpos($obj, 'stream');

        if ($streamStart === false) {
            return '';
        }

        $streamStart += 6;
        if (isset($obj[$streamStart]) && $obj[$streamStart] === "\r") {
            $streamStart++;
        }
        if (isset($obj[$streamStart]) && $obj[$streamStart] === "\n") {
            $streamStart++;
        }

        $streamEnd = strrpos($obj, 'endstream');

        if ($streamEnd === false || $streamEnd <= $streamStart) {
            return '';
        }

        $streamData = substr($obj, $streamStart, $streamEnd - $streamStart);

        if (str_contains($obj, '/FlateDecode')) {
            $decoded = @gzuncompress($streamData);

            if ($decoded === false) {
                $decoded = @gzinflate($streamData);
            }

            if ($decoded !== false) {
                return $decoded;
            }
        }

        if ($this->looksLikeTextStream($streamData)) {
            return $streamData;
        }

        return '';
    }

    private function looksLikeTextStream(string $data): bool
    {
        $sample = substr($data, 0, min(200, strlen($data)));

        return str_contains($sample, 'BT') || str_contains($sample, 'Tj') || str_contains($sample, 'TJ')
            || preg_match('/[\x20-\x7E]{20,}/', $sample) === 1;
    }

    /* =============================================================
     | Text Extraction from Content Streams
     |============================================================= */

    /** @var array{a: float, b: float, c: float, d: float, e: float, f: float} */
    private const CTM_IDENTITY = ['a' => 1.0, 'b' => 0.0, 'c' => 0.0, 'd' => 1.0, 'e' => 0.0, 'f' => 0.0];

    /**
     * @param  array<string, array<string, string>> $fontMap font name → CMap
     * @return array<int, array{text: string, x: float, y: float}>
     */
    private function extractTextLines(string $stream, array $fontMap = []): array
    {
        $lines = [];
        $ctmStack = [self::CTM_IDENTITY];
        $streamOps = preg_split('/\r?\n/', $stream);
        $inBT = false;
        $btContent = '';

        foreach ($streamOps as $opLine) {
            $opLine = trim($opLine);

            if ($opLine === '') {
                continue;
            }

            if (! $inBT) {
                $this->processGraphicsOps($opLine, $ctmStack);

                if (preg_match('/\bBT\b(.*?)\bET\b/s', $opLine, $singleLine)) {
                    $this->parseTextBlockWithCtm($singleLine[1], end($ctmStack), $lines, $fontMap);
                } elseif (preg_match('/\bBT\b(.*)$/s', $opLine, $btMatch)) {
                    $inBT = true;
                    $btContent = $btMatch[1] . "\n";
                }
            } else {
                if (preg_match('/^(.*?)\bET\b/', $opLine, $etMatch)) {
                    $btContent .= $etMatch[1] . "\n";
                    $inBT = false;
                    $this->parseTextBlockWithCtm($btContent, end($ctmStack), $lines, $fontMap);
                } else {
                    $btContent .= $opLine . "\n";
                }
            }
        }

        return $lines;
    }

    /**
     * Process graphics state operators (q, Q, cm) from a line.
     *
     * @param array<int, array{a: float, b: float, c: float, d: float, e: float, f: float}> $ctmStack
     */
    private function processGraphicsOps(string $opLine, array &$ctmStack): void
    {
        if (str_contains($opLine, 'q') && preg_match('/(?:^|\s)q(?:\s|$)/', $opLine)) {
            $ctmStack[] = end($ctmStack);
        }

        if (str_contains($opLine, 'Q') && preg_match('/(?:^|\s)Q(?:\s|$)/', $opLine)) {
            if (count($ctmStack) > 1) {
                array_pop($ctmStack);
            }
        }

        if (preg_match('/([\d.\-]+)\s+([\d.\-]+)\s+([\d.\-]+)\s+([\d.\-]+)\s+([\d.\-]+)\s+([\d.\-]+)\s+cm\b/', $opLine, $m)) {
            $newMatrix = [
                'a' => (float) $m[1], 'b' => (float) $m[2],
                'c' => (float) $m[3], 'd' => (float) $m[4],
                'e' => (float) $m[5], 'f' => (float) $m[6],
            ];
            $key = array_key_last($ctmStack);
            $ctmStack[$key] = $this->multiplyCtm($newMatrix, $ctmStack[$key]);
        }
    }

    /**
     * PDF matrix multiplication: Result = A × B
     *
     * @param  array{a: float, b: float, c: float, d: float, e: float, f: float} $a
     * @param  array{a: float, b: float, c: float, d: float, e: float, f: float} $b
     * @return array{a: float, b: float, c: float, d: float, e: float, f: float}
     */
    private function multiplyCtm(array $a, array $b): array
    {
        return [
            'a' => $a['a'] * $b['a'] + $a['b'] * $b['c'],
            'b' => $a['a'] * $b['b'] + $a['b'] * $b['d'],
            'c' => $a['c'] * $b['a'] + $a['d'] * $b['c'],
            'd' => $a['c'] * $b['b'] + $a['d'] * $b['d'],
            'e' => $a['e'] * $b['a'] + $a['f'] * $b['c'] + $b['e'],
            'f' => $a['e'] * $b['b'] + $a['f'] * $b['d'] + $b['f'],
        ];
    }

    /**
     * Transforms a local text position through the CTM to page coordinates.
     *
     * @param  array{a: float, b: float, c: float, d: float, e: float, f: float} $ctm
     * @return array{0: float, 1: float}
     */
    private function transformPoint(float $x, float $y, array $ctm): array
    {
        return [
            $ctm['a'] * $x + $ctm['c'] * $y + $ctm['e'],
            $ctm['b'] * $x + $ctm['d'] * $y + $ctm['f'],
        ];
    }

    /**
     * @param array{a: float, b: float, c: float, d: float, e: float, f: float} $ctm
     * @param array<int, array{text: string, x: float, y: float}>               $lines
     * @param array<string, array<string, string>>                               $fontMap
     */
    private function parseTextBlockWithCtm(string $block, array $ctm, array &$lines, array $fontMap = []): void
    {
        $localX = 0.0;
        $localY = 0.0;
        $currentFont = '';

        $blockLines = preg_split('/\r?\n/', $block);

        foreach ($blockLines as $bLine) {
            $bLine = trim($bLine);

            if ($bLine === '') {
                continue;
            }

            if (preg_match('/\/(\w+)\s+[\d.]+\s+Tf\b/', $bLine, $fm)) {
                $currentFont = $fm[1];
            }

            if (preg_match('/([\d.\-]+)\s+([\d.\-]+)\s+([\d.\-]+)\s+([\d.\-]+)\s+([\d.\-]+)\s+([\d.\-]+)\s+Tm\b/', $bLine, $m)) {
                $localX = (float) $m[5];
                $localY = (float) $m[6];
            }

            if (preg_match('/([\d.\-]+)\s+([\d.\-]+)\s+Td\b/', $bLine, $m)) {
                $localX += (float) $m[1];
                $localY += (float) $m[2];
            }

            [$pageX, $pageY] = $this->transformPoint($localX, $localY, $ctm);
            $cmap = $fontMap[$currentFont] ?? [];

            if (preg_match_all('/\[([^\]]*)\]\s*TJ\b/s', $bLine, $tjMatches)) {
                foreach ($tjMatches[1] as $tjContent) {
                    $text = $this->extractTJText($tjContent, $cmap);

                    if ($text !== '') {
                        $lines[] = ['text' => $text, 'x' => round($pageX, 1), 'y' => round($pageY, 1)];
                    }
                }
            }

            if (preg_match_all('/\(([^)]*)\)\s*Tj\b/s', $bLine, $tjSimple)) {
                foreach ($tjSimple[1] as $str) {
                    $text = $this->decodePdfString($str);

                    if ($text !== '') {
                        $lines[] = ['text' => $text, 'x' => round($pageX, 1), 'y' => round($pageY, 1)];
                    }
                }
            }

            if (preg_match_all('/<([0-9A-Fa-f]+)>\s*Tj\b/', $bLine, $hexTj)) {
                foreach ($hexTj[1] as $hex) {
                    $text = $this->decodeHexViaCMap($hex, $cmap);

                    if ($text !== '') {
                        $lines[] = ['text' => $text, 'x' => round($pageX, 1), 'y' => round($pageY, 1)];
                    }
                }
            }

            if (preg_match_all("/\(([^)]*)\)\s*'/s", $bLine, $quoteMatches)) {
                foreach ($quoteMatches[1] as $str) {
                    $text = $this->decodePdfString($str);

                    if ($text !== '') {
                        $lines[] = ['text' => $text, 'x' => round($pageX, 1), 'y' => round($pageY, 1)];
                    }
                }
            }
        }
    }

    /**
     * @param array<int, array{text: string, x: float, y: float}> $lines
     */
    private function sortAndBuildElements(array $lines, Section $section): void
    {
        usort($lines, function ($a, $b) {
            $yDiff = $b['y'] - $a['y'];

            if (abs($yDiff) > 2) {
                return $yDiff > 0 ? 1 : -1;
            }

            return $a['x'] <=> $b['x'];
        });

        $groups = $this->groupLinesByY($lines);
        $count = count($groups);
        $i = 0;

        while ($i < $count) {
            $group = $groups[$i];
            $clusters = $this->clusterSegmentsByX($group);

            if (count($clusters) >= 3 && $this->looksLikeTableClusters($clusters)) {
                $result = $this->collectTableFromGroups($groups, $i, $count);

                if ($result !== null) {
                    [$table, $nextI] = $result;
                    $section->addElement($table);
                    $i = $nextI;

                    continue;
                }
            }

            $text = trim(implode(' ', array_column($group, 'text')));
            $text = $this->collapseCidSpacing($text);

            if ($text !== '' && ! $this->isGarbageText($text)) {
                $section->addText($text);
            }

            $i++;
        }
    }

    /* =============================================================
     | Table Detection
     |============================================================= */

    /**
     * Groups segments on the same Y-line into X-position clusters.
     * Segments separated by more than $gap points start a new column.
     *
     * @param array<int, array{text: string, x: float, y: float}> $segments
     * @return array<int, array<int, array{text: string, x: float, y: float}>>
     */
    private function clusterSegmentsByX(array $segments, float $gap = 40.0): array
    {
        if (empty($segments)) {
            return [];
        }

        $sorted = $segments;
        usort($sorted, fn ($a, $b) => $a['x'] <=> $b['x']);

        $clusters = [[$sorted[0]]];

        for ($i = 1, $n = count($sorted); $i < $n; $i++) {
            $lastCluster = $clusters[array_key_last($clusters)];
            $lastX = end($lastCluster)['x'];

            if ($sorted[$i]['x'] - $lastX > $gap) {
                $clusters[] = [$sorted[$i]];
            } else {
                $clusters[array_key_last($clusters)][] = $sorted[$i];
            }
        }

        return $clusters;
    }

    /**
     * Validates that clusters form a plausible table row —
     * no single gap between adjacent columns exceeds 250pt.
     *
     * @param array<int, array<int, array{text: string, x: float, y: float}>> $clusters
     */
    private function looksLikeTableClusters(array $clusters): bool
    {
        if (count($clusters) < 3) {
            return false;
        }

        $xPositions = array_map(fn ($c) => $c[0]['x'], $clusters);
        sort($xPositions);

        for ($i = 1, $n = count($xPositions); $i < $n; $i++) {
            if ($xPositions[$i] - $xPositions[$i - 1] > 300) {
                return false;
            }
        }

        return true;
    }

    /**
     * Starting from $startIndex, collects consecutive Y-groups that
     * belong to the same table and builds a Table element.
     *
     * Uses two heuristics:
     *  - Column matching: segments must align with the table's column X-positions
     *  - Row gap: Y-distance >= 15pt means a new logical row;
     *    smaller gaps are merged (multi-line cell content)
     *
     * @param array<int, array<int, array{text: string, x: float, y: float}>> $groups
     * @return array{0: Table, 1: int}|null
     */
    private function collectTableFromGroups(array $groups, int $startIndex, int $totalGroups): ?array
    {
        $firstGroup = $groups[$startIndex];
        $clusters = $this->clusterSegmentsByX($firstGroup);
        $colPositions = array_map(fn ($c) => $c[0]['x'], $clusters);
        sort($colPositions);
        $numCols = count($colPositions);

        $tableRowCells = [];
        $currentRow = $this->assignSegmentsToColumns($firstGroup, $colPositions);
        $prevAvgY = array_sum(array_column($firstGroup, 'y')) / count($firstGroup);

        $i = $startIndex + 1;

        while ($i < $totalGroups) {
            $group = $groups[$i];
            $avgY = array_sum(array_column($group, 'y')) / count($group);
            $yGap = $prevAvgY - $avgY;

            $groupClusters = $this->clusterSegmentsByX($group);
            $groupXPositions = array_map(fn ($c) => $c[0]['x'], $groupClusters);
            $numGroupClusters = count($groupXPositions);

            $matchCount = $this->countColumnMatches($groupXPositions, $colPositions);

            $minRequired = $numGroupClusters === 1
                ? 1
                : max(2, (int) ceil($numGroupClusters * 0.6));

            if ($matchCount < $minRequired) {
                break;
            }

            $assigned = $this->assignSegmentsToColumns($group, $colPositions);

            if ($yGap >= 15.0) {
                $tableRowCells[] = $currentRow;
                $currentRow = $assigned;
            } else {
                for ($c = 0; $c < $numCols; $c++) {
                    if ($assigned[$c] !== '') {
                        $currentRow[$c] = $currentRow[$c] !== ''
                            ? $currentRow[$c] . ' ' . $assigned[$c]
                            : $assigned[$c];
                    }
                }
            }

            $prevAvgY = $avgY;
            $i++;
        }

        $tableRowCells[] = $currentRow;

        if (count($tableRowCells) < 2) {
            return null;
        }

        $table = new Table();
        $isFirst = true;

        foreach ($tableRowCells as $cells) {
            $row = new TableRow();

            if ($isFirst) {
                $row->setHeader();
            }

            foreach ($cells as $cellText) {
                $cell = new TableCell();
                $cell->addElement(
                    (new Paragraph())->addRun(new TextRun(trim($cellText)))
                );
                $row->addCell($cell);
            }

            $table->addRow($row);
            $isFirst = false;
        }

        return [$table, $i];
    }

    /**
     * Counts how many of $groupXPositions align with $colPositions (within 15pt tolerance).
     *
     * @param float[] $groupXPositions
     * @param float[] $colPositions
     */
    private function countColumnMatches(array $groupXPositions, array $colPositions, float $tolerance = 15.0): int
    {
        $matches = 0;

        foreach ($groupXPositions as $gx) {
            foreach ($colPositions as $cx) {
                if (abs($gx - $cx) < $tolerance) {
                    $matches++;

                    break;
                }
            }
        }

        return $matches;
    }

    /**
     * Maps each segment to the nearest column position.
     *
     * @param array<int, array{text: string, x: float, y: float}> $segments
     * @param float[] $colPositions
     * @return string[]
     */
    private function assignSegmentsToColumns(array $segments, array $colPositions): array
    {
        $numCols = count($colPositions);
        $cells = array_fill(0, $numCols, '');

        foreach ($segments as $seg) {
            $bestCol = 0;
            $bestDist = PHP_FLOAT_MAX;

            for ($c = 0; $c < $numCols; $c++) {
                $dist = abs($seg['x'] - $colPositions[$c]);

                if ($dist < $bestDist) {
                    $bestDist = $dist;
                    $bestCol = $c;
                }
            }

            $cells[$bestCol] = $cells[$bestCol] !== ''
                ? $cells[$bestCol] . ' ' . $seg['text']
                : $seg['text'];
        }

        return $cells;
    }

    /* =============================================================
     | Y-line Grouping
     |============================================================= */

    /**
     * @param array<int, array{text: string, x: float, y: float}> $lines
     * @return array<int, array<int, array{text: string, x: float, y: float}>>
     */
    private function groupLinesByY(array $lines): array
    {
        if (empty($lines)) {
            return [];
        }

        $groups = [];
        $currentGroup = [$lines[0]];
        $lastY = $lines[0]['y'];

        for ($i = 1, $count = count($lines); $i < $count; $i++) {
            if (abs($lines[$i]['y'] - $lastY) < 2.0) {
                $currentGroup[] = $lines[$i];
            } else {
                $groups[] = $currentGroup;
                $currentGroup = [$lines[$i]];
                $lastY = $lines[$i]['y'];
            }
        }

        $groups[] = $currentGroup;

        return $groups;
    }

    /* =============================================================
     | Image Extraction
     |============================================================= */

    private function extractPageImages(int $pageObjNum, Section $section): void
    {
        $obj = $this->objects[$pageObjNum] ?? null;
        if ($obj === null) {
            return;
        }

        $imageRefs = $this->findImageXObjectRefs($obj);

        foreach ($imageRefs as $objNum) {
            $image = $this->extractImageFromObject($objNum);
            if ($image !== null) {
                $section->addElement($image);
            }
        }
    }

    /**
     * @return int[]
     */
    private function findImageXObjectRefs(string $obj, int $depth = 0): array
    {
        if ($depth > 5) {
            return [];
        }

        $refs = [];

        $xobjectDefs = [];
        if (preg_match_all('/\/XObject\s*<<([^>]+)>>/s', $obj, $matches)) {
            $xobjectDefs = $matches[1];
        }

        if (preg_match('/\/Resources\s+(\d+)\s+\d+\s+R/', $obj, $resRef)) {
            $resObj = $this->getRawObject((int) $resRef[1]);
            if ($resObj !== null && preg_match_all('/\/XObject\s*<<([^>]+)>>/s', $resObj, $resMatches)) {
                array_push($xobjectDefs, ...$resMatches[1]);
            }
        }

        foreach ($xobjectDefs as $def) {
            preg_match_all('/\/\w+\s+(\d+)\s+\d+\s+R/', $def, $r);
            foreach ($r[1] as $refNum) {
                $num = (int) $refNum;
                $xObj = $this->objects[$num] ?? null;

                if ($xObj === null) {
                    continue;
                }

                if (preg_match('/\/Subtype\s*\/Image\b/', $xObj)) {
                    $refs[] = $num;
                } elseif (preg_match('/\/Subtype\s*\/Form\b/', $xObj)) {
                    $nested = $this->findImageXObjectRefs($xObj, $depth + 1);
                    array_push($refs, ...$nested);
                }
            }
        }

        return array_unique($refs);
    }

    private function extractImageFromObject(int $objNum): ?Image
    {
        $obj = $this->objects[$objNum] ?? null;
        if ($obj === null) {
            return null;
        }

        $width = 0;
        $height = 0;
        if (preg_match('/\/Width\s+(\d+)/', $obj, $m)) {
            $width = (int) $m[1];
        }
        if (preg_match('/\/Height\s+(\d+)/', $obj, $m)) {
            $height = (int) $m[1];
        }

        $streamStart = strpos($obj, 'stream');
        if ($streamStart === false) {
            return null;
        }

        $streamStart += 6;
        if (isset($obj[$streamStart]) && $obj[$streamStart] === "\r") {
            $streamStart++;
        }
        if (isset($obj[$streamStart]) && $obj[$streamStart] === "\n") {
            $streamStart++;
        }

        $streamEnd = strrpos($obj, 'endstream');
        if ($streamEnd === false || $streamEnd <= $streamStart) {
            return null;
        }

        $rawData = substr($obj, $streamStart, $streamEnd - $streamStart);

        if (str_contains($obj, '/DCTDecode')) {
            return Image::fromData($rawData, 'image/jpeg', $width, $height);
        }

        if (str_contains($obj, '/JPXDecode')) {
            return Image::fromData($rawData, 'image/jp2', $width, $height);
        }

        if (str_contains($obj, '/FlateDecode') && ! str_contains($obj, '/Subtype /Form')) {
            $decoded = @gzuncompress($rawData);
            if ($decoded === false) {
                $decoded = @gzinflate($rawData);
            }

            if ($decoded !== false && $width > 0 && $height > 0) {
                $bpc = 8;
                if (preg_match('/\/BitsPerComponent\s+(\d+)/', $obj, $m)) {
                    $bpc = (int) $m[1];
                }

                $colorSpace = 'DeviceRGB';
                if (preg_match('/\/ColorSpace\s*\/(\w+)/', $obj, $m)) {
                    $colorSpace = $m[1];
                }

                $png = $this->rawToPng($decoded, $width, $height, $colorSpace, $bpc);
                if ($png !== null) {
                    return Image::fromData($png, 'image/png', $width, $height);
                }
            }
        }

        return null;
    }

    /**
     * Reconstruit une image PNG à partir de pixels bruts (décompressés FlateDecode).
     */
    private function rawToPng(string $data, int $width, int $height, string $colorSpace, int $bpc): ?string
    {
        $channels = match ($colorSpace) {
            'DeviceRGB'  => 3,
            'DeviceGray' => 1,
            'DeviceCMYK' => 4,
            default      => 3,
        };

        if ($channels === 4) {
            return null;
        }

        $expectedSize = $width * $height * $channels * ($bpc / 8);

        if (abs(strlen($data) - $expectedSize) > $width) {
            return null;
        }

        $colorType = $channels === 1 ? 0 : 2;

        ob_start();

        echo "\x89PNG\r\n\x1a\n";

        $ihdr = pack('Nnn', $width, $height, 0)
              . chr($bpc) . chr($colorType) . chr(0) . chr(0) . chr(0);
        $ihdr = pack('N', 13) . 'IHDR' . $ihdr;
        $ihdr .= pack('N', crc32('IHDR' . substr($ihdr, 8)));
        echo $ihdr;

        $rawRows = '';
        $stride = $width * $channels * ((int) ceil($bpc / 8));
        for ($y = 0; $y < $height; $y++) {
            $rawRows .= "\x00";
            $rawRows .= substr($data, $y * $stride, $stride);
        }

        $compressed = gzcompress($rawRows);
        if ($compressed === false) {
            ob_end_clean();

            return null;
        }

        $idat = pack('N', strlen($compressed)) . 'IDAT' . $compressed;
        $idat .= pack('N', crc32('IDAT' . $compressed));
        echo $idat;

        $iend = pack('N', 0) . 'IEND';
        $iend .= pack('N', crc32('IEND'));
        echo $iend;

        return ob_get_clean() ?: null;
    }

    /**
     * Rejects text that is mostly non-printable / binary data,
     * typical of misidentified image streams or garbled OCR.
     *
     * Uses ASCII letter sequences (2+ chars) as the primary signal —
     * Unicode letter matching is too lenient because random binary
     * bytes in the Latin-1 Supplement range (0x80–0xFF) are valid
     * Unicode letters but not readable text.
     */
    private function isGarbageText(string $text): bool
    {
        $totalChars = mb_strlen($text, 'UTF-8');

        if ($totalChars < 5) {
            return false;
        }

        $readableChars = 0;

        if (preg_match_all('/[a-zA-Z]{2,}/', $text, $words)) {
            foreach ($words[0] as $w) {
                $readableChars += strlen($w);
            }
        }

        if (preg_match_all('/\d{2,}/', $text, $nums)) {
            foreach ($nums[0] as $n) {
                $readableChars += strlen($n);
            }
        }

        return ($readableChars / max(1, $totalChars)) < 0.15;
    }

    /* =============================================================
     | String Decoding
     |============================================================= */

    private function decodePdfString(string $str): string
    {
        $str = preg_replace_callback('/\\\\(\d{3})/', fn ($m) => chr((int) octdec($m[1])), $str);

        $str = str_replace(
            ['\\n', '\\r', '\\t', '\\(', '\\)', '\\\\'],
            ["\n", "\r", "\t", '(', ')', '\\'],
            $str
        );

        if (str_starts_with($str, "\xFE\xFF")) {
            return trim(mb_convert_encoding(substr($str, 2), 'UTF-8', 'UTF-16BE'));
        }

        if ($this->looksLikeUtf16BE($str)) {
            return trim(mb_convert_encoding($str, 'UTF-8', 'UTF-16BE'));
        }

        if (! mb_check_encoding($str, 'UTF-8') && preg_match('/[\x80-\xFF]/', $str)) {
            $str = mb_convert_encoding($str, 'UTF-8', 'ISO-8859-1');
        }

        $str = $this->collapseCidSpacing($str);

        return trim($str);
    }

    /**
     * Détecte UTF-16BE sans BOM : alternance régulière d'octets nuls
     * et de caractères imprimables, typique des polices CID.
     */
    private function looksLikeUtf16BE(string $str): bool
    {
        $len = strlen($str);

        if ($len < 4 || $len % 2 !== 0) {
            return false;
        }

        $nullHighBytes = 0;

        for ($i = 0; $i < $len; $i += 2) {
            if ($str[$i] === "\x00") {
                $nullHighBytes++;
            }
        }

        return $nullHighBytes >= ($len / 2) * 0.7;
    }

    /**
     * @param array<string, string> $cmap
     */
    private function extractTJText(string $content, array $cmap = []): string
    {
        $text = '';

        preg_match_all('/\(([^)]*)\)|<([0-9A-Fa-f]+)>|(-?\d+)/', $content, $parts, PREG_SET_ORDER);

        foreach ($parts as $part) {
            if (isset($part[1]) && $part[1] !== '') {
                $text .= $this->decodePdfString($part[1]);
            } elseif (isset($part[2]) && $part[2] !== '') {
                $text .= $this->decodeHexViaCMap($part[2], $cmap);
            } elseif (isset($part[3])) {
                $kern = (int) $part[3];
                if ($kern < -100) {
                    $text .= ' ';
                }
            }
        }

        return trim($text);
    }

    /**
     * Détecte et corrige le motif CID : "D e v i s   N °" → "Devis N°"
     *
     * Heuristique : si le texte contient un motif récurrent
     * «char espace char espace …», c'est de l'espacement CID.
     */
    private function collapseCidSpacing(string $text): string
    {
        $mbLen = mb_strlen($text, 'UTF-8');

        if ($mbLen < 5) {
            return $text;
        }

        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $spaceCount = 0;
        $nonSpaceCount = 0;

        foreach ($chars as $ch) {
            if ($ch === ' ') {
                $spaceCount++;
            } else {
                $nonSpaceCount++;
            }
        }

        if ($nonSpaceCount === 0) {
            return $text;
        }

        $ratio = $spaceCount / $nonSpaceCount;

        if ($ratio < 0.6) {
            return $text;
        }

        if (! preg_match('/^\s?\S\s\S\s\S/u', $text)) {
            return $text;
        }

        $result = preg_replace('/(\S) (?=\S)/u', '$1', $text);
        $result = preg_replace('/\s{2,}/u', ' ', $result);

        return trim($result);
    }
}
