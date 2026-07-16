<?php

declare(strict_types=1);

namespace Paperdoc\Ocr\PostProcessing;

use Paperdoc\Support\Cast;

/**
 * Builds a PostProcessingPipeline from configuration.
 *
 * @example
 *   $pipeline = PipelineFactory::fromConfig([
 *       'char_substitution' => true,
 *       'spell_correction'  => ['enabled' => true, 'dictionary' => '/path/to/dict.txt'],
 *       'ngram'             => ['enabled' => true, 'model_path' => '/path/to/model.json'],
 *       'patterns'          => true,
 *       'structure'         => true,
 *   ]);
 */
class PipelineFactory
{
    /**
     * @param array<string, mixed> $config
     */
    public static function fromConfig(array $config): ?PostProcessingPipeline
    {
        if ($config === [] || self::isDisabled($config)) {
            return null;
        }

        $pipeline = new PostProcessingPipeline;

        // Layer 1: OCR confusion correction
        $charConfig = $config['char_substitution'] ?? true;
        if (! self::isDisabled($charConfig)) {
            $charMap = Cast::asMap($charConfig);
            $pipeline->addLayer(new OcrConfusionCorrector(
                wordSubstitutions: self::stringMap($charMap['word'] ?? null),
                digitSubstitutions: self::stringMap($charMap['digit'] ?? null),
                globalPatterns: self::stringKeyedMap($charMap['global'] ?? null),
            ));
        }

        // Layer 2: Spell correction (requires dictionary)
        $spellConfig = $config['spell_correction'] ?? false;
        $corrector = null;
        if (! self::isDisabled($spellConfig)) {
            $spellMap = Cast::asMap($spellConfig);
            $dict = Cast::asString($spellMap['dictionary'] ?? null);

            if ($dict !== '' && file_exists($dict)) {
                $maxDist = Cast::asInt($spellMap['max_distance'] ?? 1, 1);
                $minLen = Cast::asInt($spellMap['min_word_length'] ?? 5, 5);
                $minFreq = Cast::asInt($spellMap['min_frequency'] ?? 100, 100);

                $corrector = new SpellCorrector($dict, $maxDist, $minLen, $minFreq);

                $ignore = $spellMap['ignore'] ?? null;
                if (is_array($ignore) && $ignore !== []) {
                    $corrector->addIgnoreList(self::stringList($ignore));
                }

                $pipeline->addLayer($corrector);
            }
        }

        // Layer 3: N-gram scorer (requires trained model)
        $ngramConfig = $config['ngram'] ?? false;
        if (! self::isDisabled($ngramConfig)) {
            $ngramMap = Cast::asMap($ngramConfig);
            $modelPath = Cast::asString($ngramMap['model_path'] ?? null);

            if ($modelPath !== '' && file_exists($modelPath)) {
                $scorer = NgramScorer::loadModel($modelPath);
                $scorer->setMinScoreRatio(Cast::asFloat($ngramMap['min_score_ratio'] ?? 5.0, 5.0));
                $scorer->setMaxEditDistance(Cast::asInt($ngramMap['max_edit_distance'] ?? 1, 1));

                if ($corrector !== null) {
                    $scorer->setProtectedWords($corrector->getDictionary());
                }

                $pipeline->addLayer($scorer);
            }
        }

        // Layer 4: Pattern validation
        $patternConfig = $config['patterns'] ?? true;
        if (! self::isDisabled($patternConfig)) {
            $patternMap = Cast::asMap($patternConfig);
            $customRules = self::patternRules($patternMap['custom_rules'] ?? null);
            $pipeline->addLayer(new PatternValidator($customRules));
        }

        // Layer 5: Structure detection
        $structConfig = $config['structure'] ?? true;
        if (! self::isDisabled($structConfig)) {
            $structMap = Cast::asMap($structConfig);
            $maxHeading = Cast::asInt($structMap['max_heading_length'] ?? 60, 60);
            $markdown = Cast::asBool($structMap['emit_markdown'] ?? true, true);
            $pipeline->addLayer(new StructureDetector($maxHeading, $markdown));
        }

        return $pipeline->getLayers() === [] ? null : $pipeline;
    }

    /**
     * Check if a config section is disabled.
     *
     * Handles: false, ['enabled' => false], 0, null
     */
    private static function isDisabled(mixed $config): bool
    {
        if ($config === false || $config === null) {
            return true;
        }

        if (is_array($config) && isset($config['enabled']) && ! $config['enabled']) {
            return true;
        }

        return false;
    }

    /**
     * @return array<int|string, string>
     */
    private static function stringMap(mixed $value): array
    {
        $map = [];
        foreach (Cast::asMap($value) as $key => $item) {
            $map[$key] = Cast::asString($item);
        }

        return $map;
    }

    /**
     * @return array<string, string>
     */
    private static function stringKeyedMap(mixed $value): array
    {
        $map = [];
        foreach (Cast::asMap($value) as $key => $item) {
            $map[$key] = Cast::asString($item);
        }

        return $map;
    }

    /**
     * @return list<string>
     */
    private static function stringList(mixed $value): array
    {
        $list = [];
        foreach (Cast::asList($value) as $item) {
            $list[] = Cast::asString($item);
        }

        return $list;
    }

    /**
     * @return list<array{name: string, pattern: string, normalizer?: callable(array<int|string, string>): string, type: string}>
     */
    private static function patternRules(mixed $value): array
    {
        $rules = [];
        foreach (Cast::asList($value) as $item) {
            $rule = Cast::asMap($item);
            $name = Cast::asString($rule['name'] ?? null);
            $pattern = Cast::asString($rule['pattern'] ?? null);
            $type = Cast::asString($rule['type'] ?? null);
            if ($name === '' || $pattern === '' || $type === '') {
                continue;
            }

            $entry = [
                'name'    => $name,
                'pattern' => $pattern,
                'type'    => $type,
            ];

            if (isset($rule['normalizer']) && is_callable($rule['normalizer'])) {
                $entry['normalizer'] = $rule['normalizer'];
            }

            $rules[] = $entry;
        }

        return $rules;
    }
}
