<?php

declare(strict_types=1);

namespace Paperdoc\Ocr\PostProcessing;

/**
 * Layer 2 — Dictionary-based spell correction using Levenshtein distance.
 *
 * Loads one or more dictionaries (plain text, one word per line) and
 * corrects OCR words that are within a configurable edit distance.
 *
 * The dictionary is language-specific and must be supplied by the caller.
 * Generate one from your own corpus or download a word-frequency list.
 */
class SpellCorrector implements PostProcessorInterface
{
    /** @var array<string, int> word → frequency (lower = rarer) */
    private array $dictionary = [];

    private int $maxDistance;

    private int $minWordLength;

    /** Words that must never be "corrected" */
    private array $ignore = [];

    /** Minimum frequency a candidate must have to be accepted */
    private int $minFrequency;

    /**
     * @param array<string, int>|string|null $dictionary     Associative array or path
     * @param int                            $maxDistance     Maximum Levenshtein distance
     * @param int                            $minWordLength   Minimum word length to check
     * @param int                            $minFrequency    Minimum candidate frequency
     */
    public function __construct(
        array|string|null $dictionary = null,
        int $maxDistance = 1,
        int $minWordLength = 5,
        int $minFrequency = 100,
    ) {
        $this->maxDistance = $maxDistance;
        $this->minWordLength = $minWordLength;
        $this->minFrequency = $minFrequency;

        if (is_string($dictionary)) {
            $this->loadFile($dictionary);
        } elseif (is_array($dictionary)) {
            $this->dictionary = $dictionary;
        }
    }

    public function getName(): string
    {
        return 'spell_correction';
    }

    // ──────────────────────────────────────────────────────────────
    //  Dictionary management
    // ──────────────────────────────────────────────────────────────

    /**
     * Load a dictionary from a text file (one word per line, optional frequency after tab).
     *
     * Format: "word\tfrequency" or just "word" (frequency defaults to 1).
     */
    public function loadFile(string $path): void
    {
        if (! file_exists($path) || ! is_readable($path)) {
            throw new \RuntimeException("Dictionary file not readable: {$path}");
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return;
        }

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }

            $parts = explode("\t", $line, 2);
            $word = mb_strtolower(trim($parts[0]));
            $freq = isset($parts[1]) ? (int) trim($parts[1]) : 1;

            $this->dictionary[$word] = $freq;
        }

        fclose($handle);
    }

    /**
     * Build a dictionary from a corpus text.
     */
    public function trainFromText(string $text): void
    {
        $words = preg_split('/[^\\p{L}]+/u', mb_strtolower($text));

        foreach ($words as $word) {
            if (mb_strlen($word) < 2) {
                continue;
            }
            $this->dictionary[$word] = ($this->dictionary[$word] ?? 0) + 1;
        }
    }

    /**
     * Save the current dictionary to a file.
     */
    public function saveDictionary(string $path): void
    {
        arsort($this->dictionary);
        $lines = [];
        foreach ($this->dictionary as $word => $freq) {
            $lines[] = "{$word}\t{$freq}";
        }
        file_put_contents($path, implode("\n", $lines));
    }

    public function addWord(string $word, int $frequency = 1): void
    {
        $this->dictionary[mb_strtolower($word)] = $frequency;
    }

    /** @param string[] $words */
    public function addIgnoreList(array $words): void
    {
        foreach ($words as $w) {
            $this->ignore[mb_strtolower($w)] = true;
        }
    }

    public function getDictionarySize(): int
    {
        return count($this->dictionary);
    }

    /** @return array<string, int> */
    public function getDictionary(): array
    {
        return $this->dictionary;
    }

    /**
     * Remove entries below minimum frequency or word length.
     */
    public function filterByFrequency(int $minFreq = 2, int $minLength = 2): void
    {
        $this->dictionary = array_filter(
            $this->dictionary,
            fn (int $freq, string $word) => $freq >= $minFreq && mb_strlen($word) >= $minLength,
            ARRAY_FILTER_USE_BOTH,
        );
    }

    // ──────────────────────────────────────────────────────────────
    //  Processing
    // ──────────────────────────────────────────────────────────────

    public function process(string $text, array &$context): string
    {
        if (empty($this->dictionary)) {
            return $text;
        }

        $context['corrections'] ??= [];

        $lines = explode("\n", $text);
        $result = [];

        foreach ($lines as $line) {
            $result[] = $this->processLine($line, $context);
        }

        return implode("\n", $result);
    }

    private function processLine(string $line, array &$context): string
    {
        $tokens = preg_split('/(\s+)/u', $line, -1, PREG_SPLIT_DELIM_CAPTURE);

        $wordIndices = [];
        foreach ($tokens as $i => $tok) {
            if (preg_match('/\p{L}/u', $tok)) {
                $wordIndices[] = $i;
            }
        }

        foreach ($wordIndices as $pos => $i) {
            $word = preg_replace('/[^\p{L}]/u', '', $tokens[$i]);

            if (mb_strlen($word) < $this->minWordLength) {
                continue;
            }

            $lower = mb_strtolower($word);

            if (isset($this->ignore[$lower]) || isset($this->dictionary[$lower])) {
                continue;
            }

            if ($this->looksLikeProperName($word, $tokens, $wordIndices, $pos)) {
                continue;
            }

            $suggestion = $this->findClosest($lower);

            if ($suggestion === null) {
                continue;
            }

            $corrected = self::matchCase($word, $suggestion);
            $context['corrections'][] = ['from' => $word, 'to' => $corrected];

            $tokens[$i] = str_replace($word, $corrected, $tokens[$i]);
        }

        return implode('', $tokens);
    }

    private function looksLikeProperName(string $word, array $tokens, array $wordIndices, int $pos): bool
    {
        if (! preg_match('/^\p{Lu}\p{Ll}/u', $word)) {
            return false;
        }

        $prevIdx = $pos > 0 ? $wordIndices[$pos - 1] : null;
        $nextIdx = $pos < count($wordIndices) - 1 ? $wordIndices[$pos + 1] : null;

        if ($prevIdx !== null && preg_match('/^\p{Lu}/u', $tokens[$prevIdx])) {
            return true;
        }

        if ($nextIdx !== null && preg_match('/^\p{Lu}\p{Ll}/u', $tokens[$nextIdx])) {
            return true;
        }

        return false;
    }

    // ──────────────────────────────────────────────────────────────
    //  Internals
    // ──────────────────────────────────────────────────────────────

    /**
     * Find the closest dictionary word within max edit distance.
     *
     * Only returns a candidate if it has a high enough frequency
     * to avoid replacing valid domain words with common but wrong ones.
     */
    public function findClosest(string $word): ?string
    {
        $bestWord = null;
        $bestDist = $this->maxDistance + 1;
        $bestFreq = -1;
        $wordLen = mb_strlen($word);

        foreach ($this->dictionary as $candidate => $freq) {
            $candidate = (string) $candidate;
            $candLen = mb_strlen($candidate);

            if (abs($candLen - $wordLen) > $this->maxDistance) {
                continue;
            }

            $dist = levenshtein($word, $candidate);

            if ($dist < $bestDist || ($dist === $bestDist && $freq > $bestFreq)) {
                $bestDist = $dist;
                $bestWord = $candidate;
                $bestFreq = $freq;
            }
        }

        if ($bestDist > $this->maxDistance) {
            return null;
        }

        if ($bestFreq < $this->minFrequency) {
            return null;
        }

        return $bestWord;
    }

    /**
     * Reproduce the original casing on the suggestion.
     */
    private static function matchCase(string $original, string $suggestion): string
    {
        if (mb_strtoupper($original) === $original) {
            return mb_strtoupper($suggestion);
        }

        if (preg_match('/^\\p{Lu}/u', $original)) {
            return mb_strtoupper(mb_substr($suggestion, 0, 1)) . mb_substr($suggestion, 1);
        }

        return $suggestion;
    }
}
