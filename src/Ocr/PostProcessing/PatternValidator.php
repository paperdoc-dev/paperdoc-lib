<?php

declare(strict_types=1);

namespace Paperdoc\Ocr\PostProcessing;

/**
 * Layer 4 — Regex-based entity recognition, validation and normalisation.
 *
 * Detects structured patterns in OCR text (dates, phone numbers, emails,
 * amounts, document identifiers) and normalises them.
 *
 * Also exposes every found entity via $context['entities'].
 */
class PatternValidator implements PostProcessorInterface
{
    /**
     * Each rule: ['name' => string, 'pattern' => regex, 'normalizer' => ?callable, 'type' => string]
     *
     * @var array<int, array{name: string, pattern: string, normalizer: ?callable, type: string}>
     */
    private array $rules;

    /**
     * @param array<int, array{name: string, pattern: string, normalizer?: callable, type: string}> $customRules
     *        Additional rules merged after the built-in set
     */
    public function __construct(array $customRules = [])
    {
        $this->rules = array_merge(self::builtInRules(), $customRules);
    }

    public function getName(): string
    {
        return 'pattern_validator';
    }

    public function process(string $text, array &$context): string
    {
        $context['entities'] ??= [];

        foreach ($this->rules as $rule) {
            $pattern = $rule['pattern'];
            $normalizer = $rule['normalizer'] ?? null;
            $type = $rule['type'];

            $text = preg_replace_callback(
                $pattern,
                function (array $m) use (&$context, $type, $normalizer) {
                    $raw = $m[0];
                    $normalized = $normalizer !== null ? $normalizer($m) : $raw;

                    $context['entities'][] = [
                        'type'  => $type,
                        'raw'   => $raw,
                        'value' => $normalized,
                    ];

                    return $normalized;
                },
                $text,
            ) ?? $text;
        }

        return $text;
    }

    // ──────────────────────────────────────────────────────────────
    //  Built-in rules
    // ──────────────────────────────────────────────────────────────

    /** @return array<int, array{name: string, pattern: string, normalizer: ?callable, type: string}> */
    public static function builtInRules(): array
    {
        return [
            // ── Dates ────────────────────────────────────────────
            [
                'name'    => 'date_dmy_slash',
                'pattern' => '/\b(\d{1,2})\s*[\/\-\.]\s*(\d{1,2})\s*[\/\-\.]\s*(\d{2,4})\b/',
                'type'    => 'date',
                'normalizer' => fn (array $m) => sprintf(
                    '%02d/%02d/%s',
                    (int) $m[1],
                    (int) $m[2],
                    strlen($m[3]) === 2 ? '20' . $m[3] : $m[3],
                ),
            ],

            // ── Phone numbers ────────────────────────────────────
            [
                'name'    => 'phone_international',
                'pattern' => '/(?:\+\d{1,3}[\s\-]?)?\(?\d{2,4}\)?[\s\-]?\d{2,3}[\s\-]?\d{2,3}[\s\-]?\d{2,4}\b/',
                'type'    => 'phone',
                'normalizer' => fn (array $m) => preg_replace('/\s+/', ' ', $m[0]),
            ],

            // ── Email addresses ──────────────────────────────────
            [
                'name'    => 'email',
                'pattern' => '/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/',
                'type'    => 'email',
                'normalizer' => null,
            ],

            // ── URLs ─────────────────────────────────────────────
            [
                'name'    => 'url',
                'pattern' => '#(?:https?://|www\.)[^\s<>\]]+#i',
                'type'    => 'url',
                'normalizer' => null,
            ],

            // ── Monetary amounts ─────────────────────────────────
            [
                'name'    => 'amount',
                'pattern' => '/\b\d{1,3}(?:[\s\.,]\d{3})*(?:[,\.]\d{1,2})?\s*(?:€|EUR|DA|USD|\$|£|MAD|DZD|TND)\b/i',
                'type'    => 'amount',
                'normalizer' => null,
            ],

            // ── IBAN ─────────────────────────────────────────────
            [
                'name'    => 'iban',
                'pattern' => '/\b[A-Z]{2}\d{2}[\s\-]?[\dA-Z]{4}[\s\-]?[\dA-Z]{4}[\s\-]?[\dA-Z]{4}(?:[\s\-]?[\dA-Z]{1,4}){0,5}\b/',
                'type'    => 'iban',
                'normalizer' => fn (array $m) => strtoupper(preg_replace('/[\s\-]/', '', $m[0])),
            ],

            // ── SIRET / SIREN ────────────────────────────────────
            [
                'name'    => 'siret',
                'pattern' => '/\b\d{3}[\s\-]?\d{3}[\s\-]?\d{3}[\s\-]?\d{3}[\s\-]?\d{2}\b/',
                'type'    => 'siret',
                'normalizer' => fn (array $m) => preg_replace('/[\s\-]/', ' ', $m[0]),
            ],

            // ── Reference / document numbers ─────────────────────
            [
                'name'    => 'reference',
                'pattern' => '/\b(?:R[ée]f|N°|Ref|Dossier)[\s.:]*[\w\-\/]+/iu',
                'type'    => 'reference',
                'normalizer' => null,
            ],

            // ── OCR-broken spaces in long digit sequences (e.g. "691 000 000 DA") ─
            [
                'name'    => 'spaced_number',
                'pattern' => '/\b(\d{1,3}(?:\s\d{3}){1,4})\b/',
                'type'    => 'number',
                'normalizer' => fn (array $m) => str_replace(' ', ' ', $m[0]),
            ],
        ];
    }
}
