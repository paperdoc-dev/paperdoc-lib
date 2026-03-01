<?php

declare(strict_types=1);

namespace Paperdoc\Ocr\PostProcessing;

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
    public static function fromConfig(array $config): ?PostProcessingPipeline
    {
        if (empty($config) || self::isDisabled($config)) {
            return null;
        }

        $pipeline = new PostProcessingPipeline;

        // Layer 1: OCR confusion correction
        $charConfig = $config['char_substitution'] ?? true;
        if (! self::isDisabled($charConfig)) {
            $pipeline->addLayer(new OcrConfusionCorrector(
                wordSubstitutions: is_array($charConfig) ? ($charConfig['word'] ?? []) : [],
                digitSubstitutions: is_array($charConfig) ? ($charConfig['digit'] ?? []) : [],
                globalPatterns: is_array($charConfig) ? ($charConfig['global'] ?? []) : [],
            ));
        }

        // Layer 2: Spell correction (requires dictionary)
        $spellConfig = $config['spell_correction'] ?? false;
        $corrector = null;
        if (! self::isDisabled($spellConfig)) {
            $dict = is_array($spellConfig) ? ($spellConfig['dictionary'] ?? null) : null;

            if ($dict !== null && file_exists($dict)) {
                $maxDist = is_array($spellConfig) ? ($spellConfig['max_distance'] ?? 1) : 1;
                $minLen = is_array($spellConfig) ? ($spellConfig['min_word_length'] ?? 5) : 5;
                $minFreq = is_array($spellConfig) ? ($spellConfig['min_frequency'] ?? 100) : 100;

                $corrector = new SpellCorrector($dict, $maxDist, $minLen, $minFreq);

                if (is_array($spellConfig) && ! empty($spellConfig['ignore'])) {
                    $corrector->addIgnoreList($spellConfig['ignore']);
                }

                $pipeline->addLayer($corrector);
            }
        }

        // Layer 3: N-gram scorer (requires trained model)
        $ngramConfig = $config['ngram'] ?? false;
        if (! self::isDisabled($ngramConfig) && is_array($ngramConfig)) {
            $modelPath = $ngramConfig['model_path'] ?? null;

            if ($modelPath !== null && file_exists($modelPath)) {
                $scorer = NgramScorer::loadModel($modelPath);
                $scorer->setMinScoreRatio((float) ($ngramConfig['min_score_ratio'] ?? 5.0));
                $scorer->setMaxEditDistance((int) ($ngramConfig['max_edit_distance'] ?? 1));

                if ($corrector !== null) {
                    $scorer->setProtectedWords($corrector->getDictionary());
                }

                $pipeline->addLayer($scorer);
            }
        }

        // Layer 4: Pattern validation
        $patternConfig = $config['patterns'] ?? true;
        if (! self::isDisabled($patternConfig)) {
            $customRules = is_array($patternConfig) ? ($patternConfig['custom_rules'] ?? []) : [];
            $pipeline->addLayer(new PatternValidator($customRules));
        }

        // Layer 5: Structure detection
        $structConfig = $config['structure'] ?? true;
        if (! self::isDisabled($structConfig)) {
            $maxHeading = is_array($structConfig) ? ($structConfig['max_heading_length'] ?? 60) : 60;
            $markdown = is_array($structConfig) ? ($structConfig['emit_markdown'] ?? true) : true;
            $pipeline->addLayer(new StructureDetector($maxHeading, $markdown));
        }

        return empty($pipeline->getLayers()) ? null : $pipeline;
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
}
