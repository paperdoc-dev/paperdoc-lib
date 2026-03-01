<?php

declare(strict_types=1);

namespace Paperdoc\Ocr\PostProcessing;

/**
 * Layer 3 — N-gram language model for contextual coherence.
 *
 * Train the model on your own corpus, save/load it,
 * and use it to score OCR words in context and pick better alternatives.
 *
 * Usage:
 *   $scorer = new NgramScorer();
 *   $scorer->train(file_get_contents('corpus.txt'));
 *   $scorer->saveModel('/path/to/model.json');
 *
 * Later:
 *   $scorer = NgramScorer::loadModel('/path/to/model.json');
 *   $pipeline->addLayer($scorer);
 */
class NgramScorer implements PostProcessorInterface
{
    /** @var array<string, int>  bigram "w1 w2" → count */
    private array $bigrams = [];

    /** @var array<string, int>  unigram "w" → count */
    private array $unigrams = [];

    private int $totalBigrams = 0;

    private int $totalUnigrams = 0;

    /** Minimum ratio current→candidate to accept a replacement */
    private float $minScoreRatio;

    /** Maximum Levenshtein distance for candidate generation */
    private int $maxEditDistance;

    /** Absolute minimum score a candidate must reach (prevents noise) */
    private float $minAbsoluteScore;

    /** Words that must never be replaced (e.g. from spell dictionary) */
    private array $protectedWords = [];

    public function __construct(
        float $minScoreRatio = 3.0,
        int $maxEditDistance = 1,
        float $minAbsoluteScore = 0.0,
    ) {
        $this->minScoreRatio = $minScoreRatio;
        $this->maxEditDistance = $maxEditDistance;
        $this->minAbsoluteScore = $minAbsoluteScore;
    }

    public function setMinScoreRatio(float $ratio): self
    {
        $this->minScoreRatio = $ratio;

        return $this;
    }

    public function setMaxEditDistance(int $distance): self
    {
        $this->maxEditDistance = $distance;

        return $this;
    }

    /**
     * Mark words as protected — they will never be replaced.
     *
     * @param array<string, mixed> $words  Keys = lowercase words
     */
    public function setProtectedWords(array $words): self
    {
        $this->protectedWords = $words;

        return $this;
    }

    public function getName(): string
    {
        return 'ngram_scorer';
    }

    /** @return array{unique_unigrams: int, unique_bigrams: int, total_unigrams: int, total_bigrams: int} */
    public function getStats(): array
    {
        return [
            'unique_unigrams' => count($this->unigrams),
            'unique_bigrams'  => count($this->bigrams),
            'total_unigrams'  => $this->totalUnigrams,
            'total_bigrams'   => $this->totalBigrams,
        ];
    }

    // ──────────────────────────────────────────────────────────────
    //  Training
    // ──────────────────────────────────────────────────────────────

    public function train(string $text): void
    {
        $words = $this->tokenize($text);

        foreach ($words as $w) {
            $this->unigrams[$w] = ($this->unigrams[$w] ?? 0) + 1;
            $this->totalUnigrams++;
        }

        for ($i = 0, $len = count($words) - 1; $i < $len; $i++) {
            $key = $words[$i] . ' ' . $words[$i + 1];
            $this->bigrams[$key] = ($this->bigrams[$key] ?? 0) + 1;
            $this->totalBigrams++;
        }
    }

    public function saveModel(string $path): void
    {
        $data = [
            'unigrams'       => $this->unigrams,
            'bigrams'        => $this->bigrams,
            'totalUnigrams'  => $this->totalUnigrams,
            'totalBigrams'   => $this->totalBigrams,
        ];

        file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public static function loadModel(string $path): self
    {
        if (! file_exists($path)) {
            throw new \RuntimeException("N-gram model not found: {$path}");
        }

        $data = json_decode(file_get_contents($path), true);
        $instance = new self;
        $instance->unigrams = $data['unigrams'] ?? [];
        $instance->bigrams = $data['bigrams'] ?? [];
        $instance->totalUnigrams = $data['totalUnigrams'] ?? 0;
        $instance->totalBigrams = $data['totalBigrams'] ?? 0;

        return $instance;
    }

    // ──────────────────────────────────────────────────────────────
    //  Scoring
    // ──────────────────────────────────────────────────────────────

    /**
     * Score a bigram. Higher = more likely to appear together.
     */
    public function scoreBigram(string $w1, string $w2): float
    {
        $key = mb_strtolower($w1) . ' ' . mb_strtolower($w2);
        $bigramCount = $this->bigrams[$key] ?? 0;
        $unigramCount = $this->unigrams[mb_strtolower($w1)] ?? 0;

        if ($unigramCount === 0) {
            return 0.0;
        }

        return $bigramCount / $unigramCount;
    }

    /**
     * Score a word in isolation (unigram probability).
     */
    public function scoreWord(string $word): float
    {
        if ($this->totalUnigrams === 0) {
            return 0.0;
        }

        return ($this->unigrams[mb_strtolower($word)] ?? 0) / $this->totalUnigrams;
    }

    public function isKnownWord(string $word): bool
    {
        return isset($this->unigrams[mb_strtolower($word)]);
    }

    // ──────────────────────────────────────────────────────────────
    //  Processing
    // ──────────────────────────────────────────────────────────────

    public function process(string $text, array &$context): string
    {
        if ($this->totalUnigrams === 0) {
            return $text;
        }

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
        $wordTokens = [];
        $positions = [];

        // Identify word tokens
        foreach ($tokens as $i => $tok) {
            if (preg_match('/\\p{L}/u', $tok)) {
                $wordTokens[] = preg_replace('/[^\\p{L}]/u', '', $tok);
                $positions[] = $i;
            }
        }

        if (count($wordTokens) < 2) {
            return $line;
        }

        // Try to improve unknown words using bigram context
        for ($j = 0, $len = count($wordTokens); $j < $len; $j++) {
            $word = $wordTokens[$j];
            $lower = mb_strtolower($word);

            if (mb_strlen($word) < 4 || $this->isKnownWord($word)) {
                continue;
            }

            if (isset($this->protectedWords[$lower])) {
                continue;
            }

            if (self::looksLikeProperName($word, $wordTokens, $j)) {
                continue;
            }

            $prevWord = $j > 0 ? $wordTokens[$j - 1] : null;
            $nextWord = $j < $len - 1 ? $wordTokens[$j + 1] : null;

            $best = $this->findBestCandidate($word, $prevWord, $nextWord);

            if ($best !== null && $best !== mb_strtolower($word)) {
                $original = $tokens[$positions[$j]];
                $tokens[$positions[$j]] = self::matchCase($original, $best);
                $context['ngram_corrections'][] = ['from' => $word, 'to' => $best];
            }
        }

        return implode('', $tokens);
    }

    /**
     * Generate candidates within edit distance and score them in context.
     *
     * Only replaces when the candidate has BOTH a high enough absolute score
     * AND a score significantly better than the current word.
     */
    private function findBestCandidate(string $word, ?string $prev, ?string $next): ?string
    {
        $lower = mb_strtolower($word);
        $currentScore = $this->contextScore($lower, $prev, $next);

        $minAbsolute = $this->minAbsoluteScore;
        if ($minAbsolute <= 0.0 && $this->totalUnigrams > 0) {
            $minAbsolute = 5.0 / $this->totalUnigrams;
        }

        $threshold = max($currentScore * $this->minScoreRatio, $minAbsolute);

        $bestCandidate = null;
        $bestScore = $threshold;

        foreach ($this->unigrams as $candidate => $freq) {
            if (abs(mb_strlen((string) $candidate) - mb_strlen($lower)) > $this->maxEditDistance) {
                continue;
            }

            $dist = levenshtein($lower, (string) $candidate);
            if ($dist < 1 || $dist > $this->maxEditDistance) {
                continue;
            }

            $candidateScore = $this->contextScore((string) $candidate, $prev, $next);

            if ($candidateScore > $bestScore) {
                $bestScore = $candidateScore;
                $bestCandidate = (string) $candidate;
            }
        }

        return $bestCandidate;
    }

    private function contextScore(string $word, ?string $prev, ?string $next): float
    {
        $score = $this->scoreWord($word);

        if ($prev !== null) {
            $score += $this->scoreBigram($prev, $word) * 2;
        }
        if ($next !== null) {
            $score += $this->scoreBigram($word, $next) * 2;
        }

        return $score;
    }

    // ──────────────────────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────────────────────

    /**
     * Detect probable proper names: capitalized word near other capitalized words.
     */
    private static function looksLikeProperName(string $word, array $tokens, int $index): bool
    {
        if (! preg_match('/^\p{Lu}/u', $word)) {
            return false;
        }

        if (mb_strtoupper($word) === $word && mb_strlen($word) > 3) {
            return false;
        }

        $prev = $index > 0 ? $tokens[$index - 1] : null;
        $next = $index < count($tokens) - 1 ? $tokens[$index + 1] : null;

        if ($prev !== null && preg_match('/^\p{Lu}/u', $prev)) {
            return true;
        }
        if ($next !== null && preg_match('/^\p{Lu}/u', $next)) {
            return true;
        }

        return false;
    }

    /** @return string[] */
    private function tokenize(string $text): array
    {
        $words = preg_split('/[^\\p{L}\']+/u', mb_strtolower($text));

        return array_values(array_filter($words, fn (string $w) => mb_strlen($w) >= 2));
    }

    private static function matchCase(string $original, string $suggestion): string
    {
        $letters = preg_replace('/[^\\p{L}]/u', '', $original);

        if (mb_strtoupper($letters) === $letters) {
            return mb_strtoupper($suggestion);
        }

        if (preg_match('/^\\p{Lu}/u', $letters)) {
            return mb_strtoupper(mb_substr($suggestion, 0, 1)) . mb_substr($suggestion, 1);
        }

        return $suggestion;
    }
}
