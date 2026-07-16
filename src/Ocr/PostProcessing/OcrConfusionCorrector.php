<?php

declare(strict_types=1);

namespace Paperdoc\Ocr\PostProcessing;

use Paperdoc\Support\Cast;

/**
 * Layer 1 — Context-aware OCR character substitution.
 *
 * Fixes the most common OCR confusions using a confusion table
 * and context analysis (is the character inside a word or a number?).
 */
class OcrConfusionCorrector implements PostProcessorInterface
{
    /**
     * Substitutions applied inside letter-only tokens (word context).
     * Key = wrong sequence, Value = replacement.
     *
     * @var array<int|string, string>
     */
    private array $wordSubstitutions;

    /**
     * Substitutions applied inside digit-heavy tokens (number context).
     *
     * @var array<int|string, string>
     */
    private array $digitSubstitutions;

    /**
     * Whole-text regex substitutions (ligatures, spacing, etc.).
     *
     * @var array<string, string>
     */
    private array $globalPatterns;

    /**
     * @param array<int|string, string> $wordSubstitutions
     * @param array<int|string, string> $digitSubstitutions
     * @param array<string, string>     $globalPatterns
     */
    public function __construct(
        array $wordSubstitutions = [],
        array $digitSubstitutions = [],
        array $globalPatterns = [],
    ) {
        $this->wordSubstitutions = $wordSubstitutions !== [] ? $wordSubstitutions : self::defaultWordSubstitutions();
        $this->digitSubstitutions = $digitSubstitutions !== [] ? $digitSubstitutions : self::defaultDigitSubstitutions();
        $this->globalPatterns = $globalPatterns !== [] ? $globalPatterns : self::defaultGlobalPatterns();
    }

    public function getName(): string
    {
        return 'ocr_confusion';
    }

    /**
     * @param array<string, mixed> $context
     */
    public function process(string $text, array &$context): string
    {
        // Global regex patterns (ligatures, spacing, etc.)
        foreach ($this->globalPatterns as $pattern => $replacement) {
            $text = Cast::asString(preg_replace($pattern, $replacement, $text), $text);
        }

        // Token-level context-aware substitutions
        $text = Cast::asString(
            preg_replace_callback(
                '/\S+/u',
                fn (array $m): string => $this->correctToken(Cast::asString($m[0])),
                $text,
            ),
            $text,
        );

        return $text;
    }

    private function correctToken(string $token): string
    {
        $letters = Cast::asString(preg_replace('/[^\\p{L}]/u', '', $token));
        $digits = Cast::asString(preg_replace('/[^0-9]/', '', $token));

        $letterCount = mb_strlen($letters);
        $digitCount = strlen($digits);

        if ($letterCount === 0 && $digitCount === 0) {
            return $token;
        }

        if ($digitCount > $letterCount) {
            foreach ($this->digitSubstitutions as $from => $to) {
                $token = str_replace((string) $from, $to, $token);
            }
        } elseif ($letterCount > $digitCount && $digitCount > 0) {
            foreach ($this->wordSubstitutions as $from => $to) {
                $from = (string) $from;
                if (preg_match('/\d/', $from) === 1) {
                    $token = str_replace($from, $to, $token);
                }
            }
        }

        if ($letterCount >= 3) {
            foreach ($this->wordSubstitutions as $from => $to) {
                $from = (string) $from;
                if (preg_match('/\d/', $from) !== 1) {
                    $token = str_replace($from, $to, $token);
                }
            }
        }

        return $token;
    }

    // ──────────────────────────────────────────────────────────────
    //  Default confusion tables
    // ──────────────────────────────────────────────────────────────

    /** @return array<int|string, string> */
    public static function defaultWordSubstitutions(): array
    {
        return [
            // Digit→letter (when surrounded by letters)
            '0' => 'O',
            '1' => 'l',
            // Common character-pair confusions
            'rn' => 'm',
            'vv' => 'w',
            'cI' => 'd',
            'Iooking' => 'looking',
            'tbe' => 'the',
        ];
    }

    /** @return array<int|string, string> */
    public static function defaultDigitSubstitutions(): array
    {
        return [
            'O' => '0',
            'o' => '0',
            'l' => '1',
            'I' => '1',
            'S' => '5',
            'B' => '8',
            'G' => '6',
        ];
    }

    /** @return array<string, string> */
    public static function defaultGlobalPatterns(): array
    {
        return [
            // Broken ligatures
            '/\bﬁ/u'  => 'fi',
            '/\bﬂ/u'  => 'fl',
            '/\bﬀ/u'  => 'ff',
            '/\bﬃ/u'  => 'ffi',
            '/\bﬄ/u'  => 'ffl',
            // Stray pipe used as l or I inside words
            '/(?<=\\p{L})\|(?=\\p{L})/u' => 'l',
        ];
    }
}
