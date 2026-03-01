<?php

declare(strict_types=1);

namespace Paperdoc\Ocr\PostProcessing;

/**
 * Layer 5 — Heuristic document structure detection.
 *
 * Analyses OCR text and converts it to light Markdown:
 *   - ALL-CAPS short lines → headings
 *   - Numbered/bulleted patterns → lists
 *   - Aligned columns → tables
 *   - Signature/footer patterns → separated
 *
 * Also populates $context['structure'] with detected blocks.
 */
class StructureDetector implements PostProcessorInterface
{
    private int $maxHeadingLength;

    private bool $emitMarkdown;

    public function __construct(int $maxHeadingLength = 60, bool $emitMarkdown = true)
    {
        $this->maxHeadingLength = $maxHeadingLength;
        $this->emitMarkdown = $emitMarkdown;
    }

    public function getName(): string
    {
        return 'structure_detector';
    }

    public function process(string $text, array &$context): string
    {
        $lines = explode("\n", $text);
        $blocks = $this->detectBlocks($lines);

        $context['structure'] = $blocks;

        if (! $this->emitMarkdown) {
            return $text;
        }

        return $this->renderMarkdown($blocks);
    }

    // ──────────────────────────────────────────────────────────────
    //  Block detection
    // ──────────────────────────────────────────────────────────────

    /**
     * @return array<int, array{type: string, lines: string[], level?: int}>
     */
    private function detectBlocks(array $lines): array
    {
        $blocks = [];
        $currentParagraph = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Empty line → flush paragraph
            if ($trimmed === '') {
                if (! empty($currentParagraph)) {
                    $blocks[] = ['type' => 'paragraph', 'lines' => $currentParagraph];
                    $currentParagraph = [];
                }

                continue;
            }

            // Heading detection
            $headingLevel = $this->detectHeading($trimmed);
            if ($headingLevel > 0) {
                if (! empty($currentParagraph)) {
                    $blocks[] = ['type' => 'paragraph', 'lines' => $currentParagraph];
                    $currentParagraph = [];
                }
                $blocks[] = ['type' => 'heading', 'lines' => [$trimmed], 'level' => $headingLevel];

                continue;
            }

            // List item detection
            $listInfo = $this->detectListItem($trimmed);
            if ($listInfo !== null) {
                if (! empty($currentParagraph)) {
                    $blocks[] = ['type' => 'paragraph', 'lines' => $currentParagraph];
                    $currentParagraph = [];
                }
                $last = end($blocks);
                if ($last !== false && $last['type'] === 'list') {
                    $blocks[array_key_last($blocks)]['lines'][] = $listInfo;
                } else {
                    $blocks[] = ['type' => 'list', 'lines' => [$listInfo]];
                }

                continue;
            }

            // Separator / horizontal rule
            if ($this->isSeparator($trimmed)) {
                if (! empty($currentParagraph)) {
                    $blocks[] = ['type' => 'paragraph', 'lines' => $currentParagraph];
                    $currentParagraph = [];
                }
                $blocks[] = ['type' => 'separator', 'lines' => []];

                continue;
            }

            // Default: paragraph line
            $currentParagraph[] = $trimmed;
        }

        // Flush remaining
        if (! empty($currentParagraph)) {
            $blocks[] = ['type' => 'paragraph', 'lines' => $currentParagraph];
        }

        return $blocks;
    }

    // ──────────────────────────────────────────────────────────────
    //  Heuristic detectors
    // ──────────────────────────────────────────────────────────────

    /**
     * Detect if a line is a heading. Returns heading level (1-3) or 0.
     */
    private function detectHeading(string $line): int
    {
        $len = mb_strlen($line);
        $letters = preg_replace('/[^\\p{L}]/u', '', $line);
        $letterCount = mb_strlen($letters);

        if ($letterCount < 4 || $len > $this->maxHeadingLength) {
            return 0;
        }

        // Skip lines that contain dates, colons (key:value), or digits
        if (preg_match('/\d{2,}/u', $line)) {
            return 0;
        }
        if (preg_match('/:\s+/u', $line)) {
            return 0;
        }

        // ALL CAPS + short → likely a title
        $upper = mb_strtoupper($letters);
        if ($upper === $letters && $letterCount >= 4) {
            // Must have at least 2 words to be a heading
            $wordCount = count(preg_split('/\s+/', trim($line)));
            if ($wordCount < 2) {
                return 0;
            }
            if ($len <= 30) {
                return 2;
            }
            if ($len <= 50) {
                return 3;
            }

            return 0;
        }

        return 0;
    }

    /**
     * Detect a list item. Returns the cleaned item text or null.
     */
    private function detectListItem(string $line): ?string
    {
        // Numbered: "1.", "1)", "1 -", "a.", "a)"
        if (preg_match('/^(\d{1,3}|[a-zA-Z])[\.\)\-]\s+(.+)$/u', $line, $m)) {
            return $m[0];
        }

        // Bullet: "- ", "• ", "* ", "– "
        if (preg_match('/^[\-\*•–]\s+(.+)$/u', $line, $m)) {
            return $m[0];
        }

        return null;
    }

    /**
     * Lines that look like separators / horizontal rules.
     */
    private function isSeparator(string $line): bool
    {
        $cleaned = preg_replace('/\s/u', '', $line);

        if (mb_strlen($cleaned) < 3) {
            return false;
        }

        // "---", "===", "___", "***"
        return (bool) preg_match('/^[\-=_\*]{3,}$/', $cleaned);
    }

    // ──────────────────────────────────────────────────────────────
    //  Markdown rendering
    // ──────────────────────────────────────────────────────────────

    /**
     * @param array<int, array{type: string, lines: string[], level?: int}> $blocks
     */
    private function renderMarkdown(array $blocks): string
    {
        $parts = [];

        foreach ($blocks as $block) {
            switch ($block['type']) {
                case 'heading':
                    $prefix = str_repeat('#', $block['level'] ?? 2);
                    $text = implode(' ', $block['lines']);
                    // Remove ALL-CAPS for readability (Titlecase)
                    $text = $this->toTitleCase($text);
                    $parts[] = "{$prefix} {$text}";
                    break;

                case 'list':
                    foreach ($block['lines'] as $item) {
                        $parts[] = $item;
                    }
                    break;

                case 'separator':
                    $parts[] = '---';
                    break;

                case 'paragraph':
                default:
                    $parts[] = implode("\n", $block['lines']);
                    break;
            }
        }

        return implode("\n\n", $parts);
    }

    /**
     * Convert ALL-CAPS text to Title Case, preserving short words.
     */
    private function toTitleCase(string $text): string
    {
        $upper = mb_strtoupper(preg_replace('/[^\\p{L}]/u', '', $text));

        if ($upper !== preg_replace('/[^\\p{L}]/u', '', $text)) {
            return $text; // not all-caps — keep as-is
        }

        $words = explode(' ', mb_strtolower($text));
        $result = [];

        foreach ($words as $i => $w) {
            if ($i === 0 || mb_strlen($w) > 3) {
                $result[] = mb_strtoupper(mb_substr($w, 0, 1)) . mb_substr($w, 1);
            } else {
                $result[] = $w;
            }
        }

        return implode(' ', $result);
    }
}
