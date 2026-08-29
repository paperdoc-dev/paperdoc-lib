<?php

declare(strict_types=1);

namespace Paperdoc\Parsers;

use Paperdoc\Contracts\{DocumentInterface, ParserInterface};
use Paperdoc\Document\{Document, Image, Paragraph, Section, Table, TableCell, TableRow, TextRun};
use Paperdoc\Document\Style\TextStyle;

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

        $raw = file_get_contents($filename);

        if ($raw === false) {
            throw new \RuntimeException("Impossible de lire le fichier PDF : {$filename}");
        }

        $this->rawContent = $raw;
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
            $highlightRects = $this->extractHighlightAnnotations($pageObjNum);

            foreach ($streams as $streamData) {
                $lines = $this->extractTextLines($streamData, $fontMap, $highlightRects);
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
            $pairs = preg_split('/\s+/', trim($header)) ?: [];

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
                            $srcHex = strtoupper(str_pad(dechex((int) $code), $srcLen, '0', STR_PAD_LEFT));
                            $dstHex = str_pad(dechex((int) ($dstInt + ($code - $loInt))), $dstLen, '0', STR_PAD_LEFT);
                            $map[$srcHex] = $this->hexToUtf8($dstHex);
                        }
                    } elseif ($entries[4][$i] !== '') {
                        preg_match_all('/<([0-9A-Fa-f]+)>/', $entries[4][$i], $arr);

                        for ($j = 0, $code = $loInt; $code <= $hiInt && $j < count($arr[1]); $code++, $j++) {
                            $srcHex = strtoupper(str_pad(dechex((int) $code), $srcLen, '0', STR_PAD_LEFT));
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
            $cp = (int) hexdec($hex);

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
    private function decodeHexViaCMap(string $hex, array $cmap, bool $trim = true): string
    {
        $hex = strtoupper(trim($hex));

        if (empty($cmap)) {
            $bytes = hex2bin($hex);

            return $bytes !== false ? $this->decodePdfString($bytes, $trim) : '';
        }

        $charLen = strlen((string) array_key_first($cmap));

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
                $cp = (int) hexdec($code);

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
     * @param  array<int, array{xMin: float, xMax: float, yMin: float, yMax: float, color: string}> $highlightRects
     * @return array<int, array{text: string, x: float, y: float, highlight?: string}>
     */
    private function extractTextLines(string $stream, array $fontMap = [], array &$highlightRects = []): array
    {
        $lines = [];
        $ctmStack = [self::CTM_IDENTITY];
        $streamOps = preg_split('/\r?\n/', $stream) ?: [];
        $inBT = false;
        $btContent = '';
        $fillColor = null;
        $pendingRect = null;

        foreach ($streamOps as $opLine) {
            $opLine = trim($opLine);

            if ($opLine === '') {
                continue;
            }

            if (! $inBT) {
                $this->processGraphicsOps($opLine, $ctmStack);
                $this->processHighlightOps(
                    $opLine,
                    $ctmStack,
                    $fillColor,
                    $pendingRect,
                    $highlightRects,
                );

                $ctm = $ctmStack[count($ctmStack) - 1];

                if (preg_match('/\bBT\b(.*?)\bET\b/s', $opLine, $singleLine)) {
                    $this->parseTextBlockWithCtm($singleLine[1], $ctm, $lines, $fontMap, $highlightRects);
                } elseif (preg_match('/\bBT\b(.*)$/s', $opLine, $btMatch)) {
                    $inBT = true;
                    $btContent = $btMatch[1] . "\n";
                }
            } else {
                if (preg_match('/^(.*?)\bET\b/', $opLine, $etMatch)) {
                    $btContent .= $etMatch[1] . "\n";
                    $inBT = false;
                    $ctm = $ctmStack[count($ctmStack) - 1];
                    $this->parseTextBlockWithCtm($btContent, $ctm, $lines, $fontMap, $highlightRects);
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
            $ctmStack[] = $ctmStack[count($ctmStack) - 1];
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
            $key = count($ctmStack) - 1;
            $ctmStack[$key] = $this->multiplyCtm($newMatrix, $ctmStack[$key]);
        }
    }

    /**
     * Tracks fill colour and filled rectangles that act as text highlights.
     *
     * @param array<int, array{a: float, b: float, c: float, d: float, e: float, f: float}> $ctmStack
     * @param array{x: float, y: float, w: float, h: float}|null $pendingRect
     * @param array<int, array{xMin: float, xMax: float, yMin: float, yMax: float, color: string}> $highlightRects
     */
    private function processHighlightOps(
        string $opLine,
        array $ctmStack,
        ?string &$fillColor,
        ?array &$pendingRect,
        array &$highlightRects,
    ): void {
        if (preg_match_all('/([\d.\-]+)\s+([\d.\-]+)\s+([\d.\-]+)\s+rg\b/', $opLine, $rgMatches, PREG_SET_ORDER)) {
            foreach ($rgMatches as $m) {
                $fillColor = $this->rgbComponentsToHex((float) $m[1], (float) $m[2], (float) $m[3]);
            }
        }

        if (preg_match_all('/([\d.\-]+)\s+([\d.\-]+)\s+([\d.\-]+)\s+([\d.\-]+)\s+re\b/', $opLine, $reMatches, PREG_SET_ORDER)) {
            foreach ($reMatches as $m) {
                $pendingRect = [
                    'x' => (float) $m[1],
                    'y' => (float) $m[2],
                    'w' => (float) $m[3],
                    'h' => (float) $m[4],
                ];
            }
        }

        if ($pendingRect !== null && preg_match('/(?:^|\s)(f\*|f|F|B\*|B|b\*|b)(?:\s|$)/', $opLine)) {
            $ctm = $ctmStack[count($ctmStack) - 1];
            [$x1, $y1] = $this->transformPoint($pendingRect['x'], $pendingRect['y'], $ctm);
            [$x2, $y2] = $this->transformPoint(
                $pendingRect['x'] + $pendingRect['w'],
                $pendingRect['y'] + $pendingRect['h'],
                $ctm,
            );

            if ($fillColor !== null) {
                $highlightRects[] = [
                    'xMin' => min($x1, $x2),
                    'xMax' => max($x1, $x2),
                    'yMin' => min($y1, $y2),
                    'yMax' => max($y1, $y2),
                    'color' => $fillColor,
                ];
            }

            $pendingRect = null;
        }
    }

    /**
     * @param array<int, array{xMin: float, xMax: float, yMin: float, yMax: float, color: string}> $highlightRects
     */
    private function findHighlightAt(float $x, float $y, array $highlightRects): ?string
    {
        foreach ($highlightRects as $rect) {
            if ($x >= $rect['xMin'] - 3.0
                && $x <= $rect['xMax'] + 3.0
                && $y >= $rect['yMin'] - 3.0
                && $y <= $rect['yMax'] + 3.0) {
                return $rect['color'];
            }
        }

        return null;
    }

    /**
     * @return array<int, array{xMin: float, xMax: float, yMin: float, yMax: float, color: string}>
     */
    private function extractHighlightAnnotations(int $pageObjNum): array
    {
        $obj = $this->objects[$pageObjNum] ?? null;

        if ($obj === null || ! preg_match('/\/Annots\s*\[([^\]]+)\]/', $obj, $m)) {
            return [];
        }

        preg_match_all('/(\d+)\s+\d+\s+R/', $m[1], $refs);
        $rects = [];

        foreach ($refs[1] as $refNum) {
            $annot = $this->getRawObject((int) $refNum);

            if ($annot === null || ! preg_match('/\/Subtype\s*\/Highlight\b/', $annot)) {
                continue;
            }

            $color = '#FFFF00';

            if (preg_match('/\/C\s*\[([\d.\s]+)\]/', $annot, $cm)) {
                $parts = preg_split('/\s+/', trim($cm[1])) ?: [];

                if (count($parts) >= 3) {
                    $color = $this->rgbComponentsToHex((float) $parts[0], (float) $parts[1], (float) $parts[2]);
                }
            }

            if (preg_match('/\/QuadPoints\s*\[([^\]]+)\]/', $annot, $qm)) {
                $points = array_map('floatval', preg_split('/\s+/', trim($qm[1])) ?: []);

                for ($i = 0; $i + 7 < count($points); $i += 8) {
                    $xs = [$points[$i], $points[$i + 2], $points[$i + 4], $points[$i + 6]];
                    $ys = [$points[$i + 1], $points[$i + 3], $points[$i + 5], $points[$i + 7]];
                    $rects[] = [
                        'xMin' => min($xs),
                        'xMax' => max($xs),
                        'yMin' => min($ys),
                        'yMax' => max($ys),
                        'color' => $color,
                    ];
                }

                continue;
            }

            if (preg_match('/\/Rect\s*\[([\d.\s\-]+)\]/', $annot, $rm)) {
                $parts = array_map('floatval', preg_split('/\s+/', trim($rm[1])) ?: []);

                if (count($parts) >= 4) {
                    $rects[] = [
                        'xMin' => min($parts[0], $parts[2]),
                        'xMax' => max($parts[0], $parts[2]),
                        'yMin' => min($parts[1], $parts[3]),
                        'yMax' => max($parts[1], $parts[3]),
                        'color' => $color,
                    ];
                }
            }
        }

        return $rects;
    }

    private function rgbComponentsToHex(float $r, float $g, float $b): string
    {
        return sprintf(
            '#%02X%02X%02X',
            (int) round(max(0.0, min(1.0, $r)) * 255),
            (int) round(max(0.0, min(1.0, $g)) * 255),
            (int) round(max(0.0, min(1.0, $b)) * 255),
        );
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
     * @param array<int, array{text: string, x: float, y: float, highlight?: string}> $lines
     * @param array<string, array<string, string>> $fontMap
     * @param array<int, array{xMin: float, xMax: float, yMin: float, yMax: float, color: string}> $highlightRects
     */
    private function parseTextBlockWithCtm(
        string $block,
        array $ctm,
        array &$lines,
        array $fontMap = [],
        array $highlightRects = [],
    ): void {
        $localX = 0.0;
        $localY = 0.0;
        $currentFont = '';

        $blockLines = preg_split('/\r?\n/', $block) ?: [];

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
            $highlight = $this->findHighlightAt($pageX, $pageY, $highlightRects);

            $appendSegment = function (string $text) use (&$lines, $pageX, $pageY, $highlight): void {
                if ($text === '') {
                    return;
                }

                $segment = [
                    'text' => $text,
                    'x' => round($pageX, 1),
                    'y' => round($pageY, 1),
                ];

                if ($highlight !== null) {
                    $segment['highlight'] = $highlight;
                }

                $lines[] = $segment;
            };

            if (preg_match_all('/\[([^\]]*)\]\s*TJ\b/s', $bLine, $tjMatches)) {
                foreach ($tjMatches[1] as $tjContent) {
                    $appendSegment($this->extractTJText($tjContent, $cmap));
                }
            }

            if (preg_match_all('/\(([^)]*)\)\s*Tj\b/s', $bLine, $tjSimple)) {
                foreach ($tjSimple[1] as $str) {
                    $appendSegment($this->decodePdfString($str));
                }
            }

            if (preg_match_all('/<([0-9A-Fa-f]+)>\s*Tj\b/', $bLine, $hexTj)) {
                foreach ($hexTj[1] as $hex) {
                    $appendSegment($this->decodeHexViaCMap($hex, $cmap));
                }
            }

            if (preg_match_all("/\(([^)]*)\)\s*'/s", $bLine, $quoteMatches)) {
                foreach ($quoteMatches[1] as $str) {
                    $appendSegment($this->decodePdfString($str));
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
                $this->addTextGroupAsParagraph($group, $section);
            }

            $i++;
        }
    }

    /**
     * @param array<int, array{text: string, x: float, y: float, highlight?: string}> $group
     */
    private function addTextGroupAsParagraph(array $group, Section $section): void
    {
        usort($group, fn ($a, $b) => $a['x'] <=> $b['x']);

        /** @var array<int, array{0: string, 1: ?TextStyle}> $runs */
        $runs = [];
        $currentText = '';
        $currentStyle = null;

        foreach ($group as $seg) {
            $text = trim($this->collapseCidSpacing($seg['text']));

            if ($text === '' || $this->isGarbageText($text)) {
                continue;
            }

            $style = isset($seg['highlight'])
                ? TextStyle::make()->setHighlight($seg['highlight'])
                : null;

            if ($currentText !== '' && $this->textStylesEquivalent($currentStyle, $style)) {
                $currentText .= ' ' . $text;
            } else {
                if ($currentText !== '') {
                    $runs[] = [$currentText, $currentStyle];
                }

                $currentText = $text;
                $currentStyle = $style;
            }
        }

        if ($currentText !== '') {
            $runs[] = [$currentText, $currentStyle];
        }

        if ($runs === []) {
            return;
        }

        if (count($runs) === 1 && $runs[0][1] === null) {
            $section->addText($runs[0][0]);

            return;
        }

        $paragraph = new Paragraph();

        foreach ($runs as [$text, $style]) {
            $paragraph->addRun(new TextRun($text, $style));
        }

        $section->addElement($paragraph);
    }

    private function textStylesEquivalent(?TextStyle $a, ?TextStyle $b): bool
    {
        if ($a === null && $b === null) {
            return true;
        }

        if ($a === null || $b === null) {
            return false;
        }

        return $a->getHighlight() === $b->getHighlight();
    }

    /* =============================================================
     | Table Detection
     |============================================================= */

    /**
     * Groups segments on the same Y-line into X-position clusters.
     * Segments separated by more than $gap points start a new column.
     *
     * @param array<int, array{text: string, x: float, y: float, highlight?: string}> $segments
     * @return array<int, array<int, array{text: string, x: float, y: float, highlight?: string}>>
     */
    private function clusterSegmentsByX(array $segments, float $gap = 65.0): array
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
     * Validates that clusters form a plausible table row.
     *
     * @param array<int, array<int, array{text: string, x: float, y: float, highlight?: string}>> $clusters
     */
    private function looksLikeTableClusters(array $clusters): bool
    {
        if (count($clusters) < 3) {
            return false;
        }

        if ($this->clustersLookLikeProseFragments($clusters) || $this->looksLikeTocRow($clusters)) {
            return false;
        }

        $xPositions = array_map(fn ($c) => $c[0]['x'], $clusters);
        sort($xPositions);

        for ($i = 1, $n = count($xPositions); $i < $n; $i++) {
            if ($xPositions[$i] - $xPositions[$i - 1] > 300) {
                return false;
            }
        }

        $cellTexts = array_map(
            fn ($c) => trim(implode(' ', array_column($c, 'text'))),
            $clusters,
        );

        $shortLabels = array_filter($cellTexts, fn ($t) => $t !== '' && mb_strlen($t) <= 20);

        return count($shortLabels) >= (int) ceil(count($cellTexts) * 0.6);
    }

    /**
     * @param array<int, array<int, array{text: string, x: float, y: float, highlight?: string}>> $clusters
     */
    private function clustersLookLikeProseFragments(array $clusters): bool
    {
        $texts = array_map(
            fn ($c) => trim(implode(' ', array_column($c, 'text'))),
            $clusters,
        );

        for ($i = 0, $n = count($texts) - 1; $i < $n; $i++) {
            if ($texts[$i] === '' || $texts[$i + 1] === '') {
                continue;
            }

            $lastChar = mb_substr($texts[$i], -1);
            $firstChar = mb_substr($texts[$i + 1], 0, 1);

            if (preg_match('/\p{Ll}/u', $lastChar) && preg_match('/\p{Ll}/u', $firstChar)) {
                return true;
            }
        }

        $shortFragments = array_filter(
            $texts,
            fn ($t) => $t !== '' && mb_strlen($t) <= 4 && preg_match('/\p{L}/u', $t) === 1,
        );

        if (count($shortFragments) >= 2) {
            return true;
        }

        $joined = implode(' ', $texts);

        if (preg_match('/[.!?;:]/u', $joined) && preg_match('/\s+\p{L}{4,}\s+/u', $joined) === 1) {
            return true;
        }

        $wordCount = preg_match_all('/\p{L}{3,}/u', $joined);

        return $wordCount >= 6 && count($clusters) <= 4;
    }

    /**
     * @param array<int, array<int, array{text: string, x: float, y: float, highlight?: string}>> $clusters
     */
    private function looksLikeTocRow(array $clusters): bool
    {
        $full = implode(' ', array_map(
            fn ($c) => trim(implode(' ', array_column($c, 'text'))),
            $clusters,
        ));

        return preg_match('/\.{3,}/', $full) === 1
            || preg_match('/\d+\s*$/', trim($full)) === 1;
    }

    /**
     * @param string[][] $rows
     */
    private function isPlausibleTableData(array $rows): bool
    {
        if (count($rows) < 2) {
            return false;
        }

        $numCols = count($rows[0]);
        $numericOrShort = 0;
        $total = 0;

        foreach ($rows as $row) {
            if (count($row) !== $numCols) {
                return false;
            }

            foreach ($row as $cell) {
                $cell = trim($cell);

                if ($cell === '') {
                    continue;
                }

                $total++;

                if (preg_match('/^[\d.,€$£%\s+\-]+$/u', $cell) === 1 || mb_strlen($cell) <= 18) {
                    $numericOrShort++;
                }
            }
        }

        if ($total > 0 && ($numericOrShort / $total) >= 0.45) {
            return true;
        }

        foreach ($rows[0] as $header) {
            $header = trim($header);

            if ($header === '') {
                continue;
            }

            if (mb_strlen($header) > 28 || preg_match('/[.!?;:]/u', $header) === 1) {
                return false;
            }
        }

        return count($rows) >= 3;
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

            $minRequired = max(2, (int) ceil($numGroupClusters * 0.8));

            if ($matchCount < $minRequired || $numGroupClusters === 1) {
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

        if (count($tableRowCells) < 2 || ! $this->isPlausibleTableData($tableRowCells)) {
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
              . chr($bpc & 0xFF) . chr($colorType & 0xFF) . chr(0) . chr(0) . chr(0);
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

        // \p{L} et non [a-zA-Z] : sinon toute écriture non latine —
        // cyrillique, grec, arabe, hébreu, japonais, thaï… — compte pour
        // zéro et la page entière part à la poubelle. mb_strlen et non
        // strlen : $totalChars est en caractères, pas en octets.
        if (preg_match_all('/\p{L}{2,}/u', $text, $words)) {
            foreach ($words[0] as $w) {
                $readableChars += mb_strlen($w, 'UTF-8');
            }
        }

        if (preg_match_all('/\p{N}{2,}/u', $text, $nums)) {
            foreach ($nums[0] as $n) {
                $readableChars += mb_strlen($n, 'UTF-8');
            }
        }

        return ($readableChars / max(1, $totalChars)) < 0.15;
    }

    /* =============================================================
     | String Decoding
     |============================================================= */

    private function decodePdfString(string $str, bool $trim = true): string
    {
        $str = preg_replace_callback('/\\\\(\d{3})/', fn ($m) => chr(((int) octdec($m[1])) & 0xFF), $str) ?? $str;

        $str = str_replace(
            ['\\n', '\\r', '\\t', '\\(', '\\)', '\\\\'],
            ["\n", "\r", "\t", '(', ')', '\\'],
            $str
        );

        if (str_starts_with($str, "\xFE\xFF")) {
            $decoded = mb_convert_encoding(substr($str, 2), 'UTF-8', 'UTF-16BE');

            return $trim ? trim($decoded) : $decoded;
        }

        if ($this->looksLikeUtf16BE($str)) {
            $decoded = mb_convert_encoding($str, 'UTF-8', 'UTF-16BE');

            return $trim ? trim($decoded) : $decoded;
        }

        if (! mb_check_encoding($str, 'UTF-8') && preg_match('/[\x80-\xFF]/', $str)) {
            // WinAnsiEncoding, pas Latin-1 : c'est l'encodage des polices
            // Type 1 simples (et celui que PdfEngine écrit). En ISO-8859-1
            // la plage 0x80–0x9F est constituée de contrôles C1, ce qui
            // détruit — ' " € • … œ Ž ™ et les 19 autres caractères.
            $str = mb_convert_encoding($str, 'UTF-8', 'Windows-1252');
        }

        $str = $this->collapseCidSpacing($str);

        return $trim ? trim($str) : $str;
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

        $highBytes = [];
        $lowBytes  = [];

        for ($i = 0; $i < $len; $i += 2) {
            $highBytes[] = $str[$i];
            $lowBytes[]  = $str[$i + 1];
        }

        $units = count($highBytes);

        // Un texte UTF-16BE d'une seule écriture répète le même octet de
        // poids fort : 0x00 en ASCII, mais aussi 0x04 en cyrillique, 0x03
        // en grec, 0x05 en hébreu, 0x06 en arabe, 0x0E en thaï. Ne tester
        // que 0x00 revenait à ne reconnaître que le latin.
        $counts = array_count_values(array_map('ord', $highBytes));
        arsort($counts);
        $dominant = (int) array_key_first($counts);

        if ($counts[$dominant] < $units * 0.7) {
            return false;
        }

        // Les octets de poids faible d'un vrai texte varient. Sans ce
        // garde-fou, une suite d'octets répétés serait prise pour de
        // l'UTF-16 alors que c'est du simple octet.
        if (count(array_unique($lowBytes)) < min(3, $units)) {
            return false;
        }

        // Le plan multilingue de base au-delà de 0x0FFF est occupé par les
        // écritures indiennes, le CJK, etc. — un octet de poids fort élevé
        // et constant est bien plus probablement du texte simple octet
        // dont les lettres se ressemblent que de l'UTF-16.
        return $dominant <= 0x0F;
    }

    /**
     * @param array<string, string> $cmap
     */
    private function extractTJText(string $content, array $cmap = []): string
    {
        $text = '';

        preg_match_all('/\(([^)]*)\)|<([0-9A-Fa-f]+)>|(-?\d+)/', $content, $parts, PREG_SET_ORDER);

        foreach ($parts as $part) {
            $literal = $part[1] ?? '';
            $hex = $part[2] ?? '';
            $kernToken = $part[3] ?? null;

            if ($literal !== '') {
                $text .= $this->decodePdfString($literal, false);
            } elseif ($hex !== '') {
                $text .= $this->decodeHexViaCMap($hex, $cmap, false);
            } elseif ($kernToken !== null) {
                $kern = (int) $kernToken;
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

        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
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

        $result = preg_replace('/(\S) (?=\S)/u', '$1', $text) ?? $text;
        $result = preg_replace('/\s{2,}/u', ' ', $result) ?? $result;

        return trim($result);
    }
}
