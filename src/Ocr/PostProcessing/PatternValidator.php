<?php

declare(strict_types=1);

namespace Paperdoc\Ocr\PostProcessing;

use Paperdoc\Support\Cast;

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
     * @var list<array{name: string, pattern: string, normalizer: (callable(array<int|string, string>): string)|null, type: string}>
     */
    private array $rules;

    /**
     * @param list<array{name: string, pattern: string, normalizer?: callable(array<int|string, string>): string, type: string}> $customRules
     *        Additional rules merged after the built-in set
     */
    public function __construct(array $customRules = [])
    {
        $merged = array_merge(self::builtInRules(), $customRules);
        $this->rules = [];
        foreach ($merged as $rule) {
            $this->rules[] = [
                'name'       => $rule['name'],
                'pattern'    => $rule['pattern'],
                'type'       => $rule['type'],
                'normalizer' => $rule['normalizer'] ?? null,
            ];
        }
    }

    public function getName(): string
    {
        return 'pattern_validator';
    }

    /**
     * @param array<string, mixed> $context
     */
    public function process(string $text, array &$context): string
    {
        $context['entities'] ??= [];

        foreach ($this->rules as $rule) {
            $pattern = $rule['pattern'];
            $normalizer = $rule['normalizer'];
            $type = $rule['type'];

            $text = Cast::asString(
                preg_replace_callback(
                    $pattern,
                    function (array $m) use (&$context, $type, $normalizer): string {
                        $raw = Cast::asString($m[0] ?? null);
                        $matches = [];
                        foreach ($m as $key => $value) {
                            $matches[$key] = Cast::asString($value);
                        }
                        $normalized = $normalizer !== null ? $normalizer($matches) : $raw;

                        $entities = Cast::asList($context['entities'] ?? null);
                        $entities[] = [
                            'type'  => $type,
                            'raw'   => $raw,
                            'value' => $normalized,
                        ];
                        $context['entities'] = $entities;

                        return $normalized;
                    },
                    $text,
                ),
                $text,
            );
        }

        return $text;
    }

    // ──────────────────────────────────────────────────────────────
    //  Built-in rules
    // ──────────────────────────────────────────────────────────────

    /**
     * @return list<array{name: string, pattern: string, normalizer: (callable(array<int|string, string>): string)|null, type: string}>
     */
    public static function builtInRules(): array
    {
        return [
            // ── Dates ────────────────────────────────────────────
            [
                'name'    => 'date_dmy_slash',
                'pattern' => '/\b(\d{1,2})\s*[\/\-\.]\s*(\d{1,2})\s*[\/\-\.]\s*(\d{2,4})\b/',
                'type'    => 'date',
                'normalizer' => function (array $m): string {
                    $day = Cast::asInt($m[1] ?? null);
                    $month = Cast::asInt($m[2] ?? null);
                    $year = Cast::asString($m[3] ?? null);

                    return sprintf(
                        '%02d/%02d/%s',
                        $day,
                        $month,
                        strlen($year) === 2 ? '20' . $year : $year,
                    );
                },
            ],

            // ── Phone numbers ────────────────────────────────────
            [
                'name'    => 'phone_international',
                'pattern' => '/(?:\+\d{1,3}[\s\-]?)?\(?\d{2,4}\)?[\s\-]?\d{2,3}[\s\-]?\d{2,3}[\s\-]?\d{2,4}\b/',
                'type'    => 'phone',
                'normalizer' => fn (array $m): string => Cast::asString(
                    preg_replace('/\s+/', ' ', Cast::asString($m[0] ?? null)),
                ),
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
                'normalizer' => fn (array $m): string => strtoupper(Cast::asString(
                    preg_replace('/[\s\-]/', '', Cast::asString($m[0] ?? null)),
                )),
            ],

            // ── SIRET / SIREN ────────────────────────────────────
            [
                'name'    => 'siret',
                'pattern' => '/\b\d{3}[\s\-]?\d{3}[\s\-]?\d{3}[\s\-]?\d{3}[\s\-]?\d{2}\b/',
                'type'    => 'siret',
                'normalizer' => fn (array $m): string => Cast::asString(
                    preg_replace('/[\s\-]/', ' ', Cast::asString($m[0] ?? null)),
                ),
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
                'normalizer' => fn (array $m): string => str_replace(' ', ' ', Cast::asString($m[0] ?? null)),
            ],
        ];
    }
}
