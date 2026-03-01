<?php

declare(strict_types=1);

namespace Paperdoc\Parsers;

use Paperdoc\Contracts\{DocumentInterface, ParserInterface};
use Paperdoc\Document\{Document, Paragraph, Section, TextRun};
use Paperdoc\Support\Ole2\Ole2Reader;

/**
 * Parser pour les fichiers .ppt (PowerPoint 97-2003, format binaire).
 *
 * Stratégie :
 * 1. Ouvrir le fichier OLE2 via le flux « PowerPoint Document »
 * 2. Lire les enregistrements binaires séquentiellement
 * 3. Extraire le texte des records TextCharsAtom (UTF-16LE)
 *    et TextBytesAtom (CP1252)
 * 4. Organiser par slide
 *
 * Référence : [MS-PPT] — PowerPoint (.ppt) Binary File Format
 */
class PptParser extends AbstractParser implements ParserInterface
{
    private const RECORD_SLIDE_BEGIN          = 0x03EE;
    private const RECORD_SLIDE_PERSIST_ATOM   = 0x03F3;
    private const RECORD_TEXT_CHARS_ATOM      = 0x0FA0;
    private const RECORD_TEXT_BYTES_ATOM      = 0x0FA8;
    private const RECORD_CSTRING              = 0x0FBA;
    private const RECORD_SLIDE_LIST_WITH_TEXT = 0x0FF0;

    public function supports(string $extension): bool
    {
        return strtolower($extension) === 'ppt';
    }

    public function parse(string $filename): DocumentInterface
    {
        $this->assertFileReadable($filename);

        $ole = Ole2Reader::fromFile($filename);
        $document = new Document('ppt');
        $document->setTitle(pathinfo($filename, PATHINFO_FILENAME));

        $this->extractSummaryInfo($ole, $document);

        $streamName = $ole->hasStream('PowerPoint Document') ? 'PowerPoint Document' : null;

        if ($streamName === null) {
            foreach ($ole->getStreamNames() as $name) {
                if (stripos($name, 'powerpoint') !== false) {
                    $streamName = $name;
                    break;
                }
            }
        }

        if ($streamName === null) {
            return $document;
        }

        $stream = $ole->getStream($streamName);
        $slides = $this->extractSlideTexts($stream);

        foreach ($slides as $i => $texts) {
            $section = new Section('slide-' . ($i + 1));

            foreach ($texts as $text) {
                $text = trim($text);
                if ($text !== '') {
                    $section->addText($text);
                }
            }

            if (! empty($section->getElements())) {
                $document->addSection($section);
            }
        }

        return $document;
    }

    /* =============================================================
     | Text Extraction
     |============================================================= */

    /**
     * @return array<int, string[]> slide index → texts
     */
    private function extractSlideTexts(string $stream): array
    {
        $slides = [];
        $currentSlide = 0;
        $pos = 0;
        $len = strlen($stream);
        $allTexts = [];

        while ($pos + 8 <= $len) {
            $recVer = $this->readUint16($stream, $pos);
            $recType = $this->readUint16($stream, $pos + 2);
            $recLen = $this->readUint32($stream, $pos + 4);
            $pos += 8;

            $isContainer = ($recVer & 0x0F) === 0x0F;

            if ($isContainer) {
                continue;
            }

            if ($pos + $recLen > $len) {
                break;
            }

            switch ($recType) {
                case self::RECORD_TEXT_CHARS_ATOM:
                    $raw = substr($stream, $pos, $recLen);
                    $text = mb_convert_encoding($raw, 'UTF-8', 'UTF-16LE');
                    $text = $this->cleanText($text);
                    if ($text !== '') {
                        $allTexts[] = $text;
                    }
                    break;

                case self::RECORD_TEXT_BYTES_ATOM:
                    $raw = substr($stream, $pos, $recLen);
                    $text = mb_convert_encoding($raw, 'UTF-8', 'Windows-1252');
                    $text = $this->cleanText($text);
                    if ($text !== '') {
                        $allTexts[] = $text;
                    }
                    break;

                case self::RECORD_CSTRING:
                    $raw = substr($stream, $pos, $recLen);
                    $text = mb_convert_encoding($raw, 'UTF-8', 'UTF-16LE');
                    $text = $this->cleanText($text);
                    if ($text !== '') {
                        $allTexts[] = $text;
                    }
                    break;
            }

            $pos += $recLen;
        }

        if (empty($allTexts)) {
            return [];
        }

        return $this->groupTextsIntoSlides($allTexts);
    }

    /**
     * Group texts into slides heuristically.
     * Consecutive text blocks are grouped until a short title-like block
     * appears after longer content, indicating a new slide.
     *
     * @param string[] $texts
     * @return array<int, string[]>
     */
    private function groupTextsIntoSlides(array $texts): array
    {
        if (count($texts) <= 3) {
            return [0 => $texts];
        }

        $slides = [];
        $current = [];
        $prevLen = 0;

        foreach ($texts as $text) {
            $textLen = mb_strlen($text);

            if (! empty($current) && $textLen < 80 && $prevLen > 100) {
                $slides[] = $current;
                $current = [];
            }

            $current[] = $text;
            $prevLen = $textLen;
        }

        if (! empty($current)) {
            $slides[] = $current;
        }

        return $slides;
    }

    private function cleanText(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $text);

        return trim($text);
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
        } catch (\Throwable) {
            // Non-critical
        }
    }

    /* =============================================================
     | Helpers
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
}
