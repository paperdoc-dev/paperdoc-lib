<?php

declare(strict_types=1);

namespace Paperdoc\Parsers;

use Paperdoc\Contracts\{DocumentInterface, ParserInterface};
use Paperdoc\Document\{Document, Image, Paragraph, Section, Table, TableCell, TableRow, TextRun};
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Document\Style\{ParagraphStyle, TextStyle};
use Paperdoc\Enum\Alignment;
use Paperdoc\Support\Cast;

/**
 * Parser Markdown natif — aucune dépendance tierce.
 *
 * Supporte :
 *  - Headings (# à ######, et underline === / ---)
 *  - Paragraphes avec inline : **bold**, *italic*, ~~strikethrough~~, `code`
 *  - Images : ![alt](src)
 *  - Tableaux GFM : | col | col |
 *  - Listes à puces (-, *, +) et numérotées (1.)
 *  - Blockquotes (>)
 *  - Liens : [text](url)
 *  - Règles horizontales (---, ***, ___)
 *  - Front-matter YAML (title, author)
 */
class MarkdownParser extends AbstractParser implements ParserInterface
{
    public function supports(string $extension): bool
    {
        return in_array(strtolower($extension), ['md', 'markdown', 'mkd', 'mdown'], true);
    }

    public function parse(string $filename): DocumentInterface
    {
        $this->assertFileReadable($filename);

        $raw = Cast::asString(file_get_contents($filename));
        $document = new Document('md');
        $document->setTitle(pathinfo($filename, PATHINFO_FILENAME));

        $content = $this->extractFrontMatter($raw, $document);

        $lines = preg_split('/\r?\n/', $content);
        $section = new Section('main');

        $this->parseLines(is_array($lines) ? $lines : [], $section);

        $document->addSection($section);

        return $document;
    }

    /* =============================================================
     | Front-matter YAML
     |============================================================= */

    private function extractFrontMatter(string $content, Document $document): string
    {
        if (! str_starts_with(ltrim($content), '---')) {
            return $content;
        }

        $content = ltrim($content);

        if (! preg_match('/\A---\s*\n(.*?)\n---\s*\n/s', $content, $m)) {
            return $content;
        }

        $yaml = $m[1];

        foreach (explode("\n", $yaml) as $line) {
            $line = trim($line);

            if ($line === '' || ! str_contains($line, ':')) {
                continue;
            }

            [$key, $value] = explode(':', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\"'");

            match (strtolower($key)) {
                'title', 'titre' => $document->setTitle($value),
                'author', 'auteur' => $document->setMetadata('author', $value),
                'date' => $document->setMetadata('date', $value),
                'description' => $document->setMetadata('description', $value),
                default => $document->setMetadata($key, $value),
            };
        }

        return substr($content, strlen($m[0]));
    }

    /* =============================================================
     | Line-by-line Parsing
     |============================================================= */

    /**
     * @param string[] $lines
     */
    private function parseLines(array $lines, Section $section): void
    {
        $count = count($lines);
        $i = 0;

        while ($i < $count) {
            $line = $lines[$i];
            $trimmed = trim($line);

            if ($trimmed === '') {
                $i++;

                continue;
            }

            if (preg_match('/^(#{1,6})\s+(.+)$/', $trimmed, $m)) {
                $level = min(strlen($m[1]), 4);
                $text = $this->stripInlineFormatting($m[2]);
                $section->addHeading($text, $level);
                $i++;

                continue;
            }

            if ($i + 1 < $count) {
                $nextLine = trim($lines[$i + 1]);

                if (preg_match('/^={3,}\s*$/', $nextLine)) {
                    $section->addHeading($this->stripInlineFormatting($trimmed), 1);
                    $i += 2;

                    continue;
                }

                if (preg_match('/^-{3,}\s*$/', $nextLine) && ! preg_match('/^[-*_]{3,}\s*$/', $trimmed)) {
                    $section->addHeading($this->stripInlineFormatting($trimmed), 2);
                    $i += 2;

                    continue;
                }
            }

            if (preg_match('/^[-*_]{3,}\s*$/', $trimmed)) {
                $section->addElement(new \Paperdoc\Document\PageBreak());
                $i++;

                continue;
            }

            if (preg_match('/^!\[([^\]]*)\]\(([^)\s]+)(?:\s+"([^"]*)")?\)/', $trimmed, $m)) {
                $alt = $m[1];
                $src = $m[2];
                $section->addElement(new Image($src, 0, 0, $alt));
                $i++;

                continue;
            }

            if (str_starts_with($trimmed, '|') && str_contains($trimmed, '|')) {
                $i = $this->parseTable($lines, $i, $count, $section);

                continue;
            }

            if (preg_match('/^(\d+\.|-|\*|\+)\s+/', $trimmed)) {
                $i = $this->parseList($lines, $i, $count, $section);

                continue;
            }

            if (str_starts_with($trimmed, '>')) {
                $i = $this->parseBlockquote($lines, $i, $count, $section);

                continue;
            }

            if (str_starts_with($trimmed, '```') || str_starts_with($trimmed, '~~~')) {
                $i = $this->parseCodeBlock($lines, $i, $count, $section);

                continue;
            }

            $i = $this->parseParagraph($lines, $i, $count, $section);
        }
    }

    /* =============================================================
     | Paragraph (multi-line)
     |============================================================= */

    /**
     * @param string[] $lines
     */
    private function parseParagraph(array $lines, int $start, int $count, Section $section): int
    {
        $buffer = '';
        $i = $start;

        while ($i < $count) {
            $trimmed = trim($lines[$i]);

            if ($trimmed === '') {
                break;
            }

            if (preg_match('/^(#{1,6})\s+/', $trimmed)) {
                break;
            }
            if (str_starts_with($trimmed, '|') && str_contains($trimmed, '|')) {
                break;
            }
            if (preg_match('/^(\d+\.|-|\*|\+)\s+/', $trimmed)) {
                break;
            }
            if (str_starts_with($trimmed, '>')) {
                break;
            }
            if (str_starts_with($trimmed, '```') || str_starts_with($trimmed, '~~~')) {
                break;
            }
            if (preg_match('/^[-*_]{3,}\s*$/', $trimmed) && $i > $start) {
                break;
            }

            $buffer .= ($buffer !== '' ? ' ' : '') . $trimmed;
            $i++;
        }

        if ($buffer !== '') {
            $paragraph = new Paragraph();
            $this->parseInlineContent($buffer, $paragraph);

            if (count($paragraph->getRuns()) > 0) {
                $section->addElement($paragraph);
            }
        }

        return $i;
    }

    /* =============================================================
     | Table (GFM)
     |============================================================= */

    /**
     * @param string[] $lines
     */
    private function parseTable(array $lines, int $start, int $count, Section $section): int
    {
        $table = new Table();
        $i = $start;
        $isFirstRow = true;
        $hasSeparator = false;
        $alignment = [];

        while ($i < $count) {
            $trimmed = trim($lines[$i]);

            if ($trimmed === '' || (! str_contains($trimmed, '|') && $i > $start)) {
                break;
            }

            if (preg_match('/^\|?\s*[-:]+[-|\s:]*\|?\s*$/', $trimmed)) {
                $alignment = $this->parseTableAlignment($trimmed);
                $hasSeparator = true;
                $i++;

                continue;
            }

            $cells = $this->parseTableRow($trimmed);

            if (empty($cells)) {
                break;
            }

            $row = new TableRow();

            if ($isFirstRow) {
                $row->setHeader();
            }

            foreach ($cells as $cellText) {
                $cell = new TableCell();
                $p = new Paragraph();
                $this->parseInlineContent(trim($cellText), $p);

                if (count($p->getRuns()) > 0) {
                    $cell->addElement($p);
                } else {
                    $cell->addElement((new Paragraph())->addRun(new TextRun('')));
                }

                $row->addCell($cell);
            }

            $table->addRow($row);
            $isFirstRow = false;
            $i++;
        }

        if (count($table->getRows()) > 0) {
            $section->addElement($table);
        }

        return $i;
    }

    /**
     * @return string[]
     */
    private function parseTableRow(string $line): array
    {
        $line = trim($line);

        if (str_starts_with($line, '|')) {
            $line = substr($line, 1);
        }

        if (str_ends_with($line, '|')) {
            $line = substr($line, 0, -1);
        }

        if ($line === '') {
            return [];
        }

        return explode('|', $line);
    }

    /**
     * @return Alignment[]
     */
    private function parseTableAlignment(string $line): array
    {
        $cols = $this->parseTableRow($line);
        $alignments = [];

        foreach ($cols as $col) {
            $col = trim($col);

            if (str_starts_with($col, ':') && str_ends_with($col, ':')) {
                $alignments[] = Alignment::CENTER;
            } elseif (str_ends_with($col, ':')) {
                $alignments[] = Alignment::RIGHT;
            } else {
                $alignments[] = Alignment::LEFT;
            }
        }

        return $alignments;
    }

    /* =============================================================
     | List (bulleted & numbered)
     |============================================================= */

    /**
     * @param string[] $lines
     */
    private function parseList(array $lines, int $start, int $count, Section $section): int
    {
        $i = $start;

        while ($i < $count) {
            $trimmed = trim($lines[$i]);

            if ($trimmed === '') {
                break;
            }

            if (preg_match('/^(\d+)\.\s+(.+)$/', $trimmed, $m)) {
                $text = $m[1] . '. ' . $m[2];
                $p = new Paragraph(ParagraphStyle::make()->setSpaceBefore(2)->setSpaceAfter(2));
                $this->parseInlineContent($text, $p);
                $section->addElement($p);
                $i++;
            } elseif (preg_match('/^[-*+]\s+(.+)$/', $trimmed, $m)) {
                $text = '• ' . $m[1];
                $p = new Paragraph(ParagraphStyle::make()->setSpaceBefore(2)->setSpaceAfter(2));
                $this->parseInlineContent($text, $p);
                $section->addElement($p);
                $i++;
            } else {
                break;
            }
        }

        return $i;
    }

    /* =============================================================
     | Blockquote
     |============================================================= */

    /**
     * @param string[] $lines
     */
    private function parseBlockquote(array $lines, int $start, int $count, Section $section): int
    {
        $buffer = '';
        $i = $start;

        while ($i < $count) {
            $trimmed = trim($lines[$i]);

            if (! str_starts_with($trimmed, '>') && $trimmed !== '') {
                break;
            }

            if ($trimmed === '') {
                break;
            }

            $text = preg_replace('/^>\s?/', '', $trimmed);
            $buffer .= ($buffer !== '' ? ' ' : '') . $text;
            $i++;
        }

        if ($buffer !== '') {
            $p = new Paragraph(ParagraphStyle::make()->setAlignment(Alignment::LEFT)->setSpaceBefore(6)->setSpaceAfter(6));
            $style = TextStyle::make()->setItalic()->setColor('#6B7280');
            $p->addRun(new TextRun('« ' . $this->stripInlineFormatting($buffer) . ' »', $style));
            $section->addElement($p);
        }

        return $i;
    }

    /* =============================================================
     | Code Block (fenced)
     |============================================================= */

    /**
     * @param string[] $lines
     */
    private function parseCodeBlock(array $lines, int $start, int $count, Section $section): int
    {
        $fence = str_starts_with(trim($lines[$start]), '```') ? '```' : '~~~';
        $i = $start + 1;
        $buffer = '';

        while ($i < $count) {
            if (str_starts_with(trim($lines[$i]), $fence)) {
                $i++;

                break;
            }

            $buffer .= ($buffer !== '' ? "\n" : '') . $lines[$i];
            $i++;
        }

        if ($buffer !== '') {
            $p = new Paragraph(ParagraphStyle::make()->setSpaceBefore(6)->setSpaceAfter(6));
            $style = TextStyle::make()->setFontFamily('Courier')->setFontSize(10)->setColor('#1F2937');
            $p->addRun(new TextRun($buffer, $style));
            $section->addElement($p);
        }

        return $i;
    }

    /* =============================================================
     | Inline Content Parsing
     |============================================================= */

    private function parseInlineContent(string $text, Paragraph $paragraph): void
    {
        $pattern = '/(\*\*\*(.+?)\*\*\*|\*\*(.+?)\*\*|__(.+?)__|(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)|_(?!_)(.+?)(?<!_)_(?!_)|~~(.+?)~~|`(.+?)`|\[([^\]]+)\]\(([^)]+)\)|!\[([^\]]*)\]\(([^)\s]+)\))/s';

        $lastPos = 0;

        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            foreach ($matches as $match) {
                $matchStart = $match[0][1];
                $fullMatch = $match[0][0];
                $matchLen = strlen($fullMatch);

                if ($matchStart > $lastPos) {
                    $before = substr($text, $lastPos, $matchStart - $lastPos);
                    if ($before !== '') {
                        $paragraph->addRun(new TextRun($before));
                    }
                }

                $boldItalic = $this->offsetCapture($match, 2);
                $bold = $this->offsetCapture($match, 3);
                $boldAlt = $this->offsetCapture($match, 4);
                $italic = $this->offsetCapture($match, 5);
                $italicAlt = $this->offsetCapture($match, 6);
                $strike = $this->offsetCapture($match, 7);
                $code = $this->offsetCapture($match, 8);
                $linkText = $this->offsetCapture($match, 9);
                $linkHref = $this->offsetCapture($match, 10);

                if ($linkHref) {
                    $link = TextLink::make($linkHref, '', $linkText);
                } else {
                    $link = null;
                }

                if ($boldItalic !== '') {
                    $style = TextStyle::make()->setBold()->setItalic();
                    $paragraph->addRun(new TextRun($boldItalic, $style, $link));
                } elseif ($bold !== '') {
                    $style = TextStyle::make()->setBold();
                    $paragraph->addRun(new TextRun($bold, $style, $link));
                } elseif ($boldAlt !== '') {
                    $style = TextStyle::make()->setBold();
                    $paragraph->addRun(new TextRun($boldAlt, $style, $link));
                } elseif ($italic !== '') {
                    $style = TextStyle::make()->setItalic();
                    $paragraph->addRun(new TextRun($italic, $style, $link));
                } elseif ($italicAlt !== '') {
                    $style = TextStyle::make()->setItalic();
                    $paragraph->addRun(new TextRun($italicAlt, $style, $link));
                } elseif ($strike !== '') {
                    $style = TextStyle::make()->setItalic();
                    $paragraph->addRun(new TextRun($strike, $style, $link));
                } elseif ($code !== '') {
                    $style = TextStyle::make()->setFontFamily('Courier')->setColor('#BE185D');
                    $paragraph->addRun(new TextRun($code, $style, $link));
                } elseif ($linkText !== '') {
                    $style = TextStyle::make()->setUnderline()->setColor('#2563EB');
                    $paragraph->addRun(new TextRun($linkText, $style, $link));
                }

                $lastPos = $matchStart + $matchLen;
            }
        }

        if ($lastPos < strlen($text)) {
            $remaining = substr($text, $lastPos);
            if ($remaining !== '') {
                $paragraph->addRun(new TextRun($remaining));
            }
        }
    }

    /**
     * @param array<int|string, mixed> $match
     */
    private function offsetCapture(array $match, int $index): string
    {
        $part = $match[$index] ?? null;

        if (! is_array($part)) {
            return '';
        }

        return Cast::asString($part[0] ?? null);
    }

    private function stripInlineFormatting(string $text): string
    {
        $text = Cast::asString(preg_replace('/\*\*\*(.+?)\*\*\*/', '$1', $text), $text);
        $text = Cast::asString(preg_replace('/\*\*(.+?)\*\*/', '$1', $text), $text);
        $text = Cast::asString(preg_replace('/__(.+?)__/', '$1', $text), $text);
        $text = Cast::asString(preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/', '$1', $text), $text);
        $text = Cast::asString(preg_replace('/(?<!_)_(?!_)(.+?)(?<!_)_(?!_)/', '$1', $text), $text);
        $text = Cast::asString(preg_replace('/~~(.+?)~~/', '$1', $text), $text);
        $text = Cast::asString(preg_replace('/`(.+?)`/', '$1', $text), $text);
        $text = Cast::asString(preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $text), $text);
        $text = Cast::asString(preg_replace('/!\[([^\]]*)\]\([^)]+\)/', '$1', $text), $text);

        return trim($text);
    }
}
