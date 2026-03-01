<?php

declare(strict_types=1);

namespace Paperdoc\Llm;

use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Anthropic\Anthropic;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\Providers\Ollama\Ollama;
use NeuronAI\Providers\OpenAI\OpenAI;

class ProviderFactory
{
    /**
     * Build an AI provider from a config array.
     *
     * @param array{provider: string, model: string, api_key?: string, base_url?: string, options?: array<string, mixed>} $config
     */
    public static function make(array $config): AIProviderInterface
    {
        $provider = $config['provider'] ?? 'openai';
        $model = $config['model'] ?? 'gpt-4o-mini';
        $apiKey = $config['api_key'] ?? '';
        $baseUrl = $config['base_url'] ?? '';
        $options = $config['options'] ?? [];

        return match ($provider) {
            'anthropic', 'claude' => new Anthropic(
                key: $apiKey,
                model: $model,
                max_tokens: (int) ($options['max_tokens'] ?? 4096),
                parameters: self::filterParameters($options),
            ),

            'gemini' => new Gemini(
                key: $apiKey,
                model: $model,
                parameters: self::filterParameters($options),
            ),

            'ollama' => new Ollama(
                url: $baseUrl ?: 'http://localhost:11434/api',
                model: $model,
                parameters: self::filterParameters($options),
            ),

            default => new OpenAI(
                key: $apiKey,
                model: $model,
                parameters: self::filterParameters($options),
            ),
        };
    }

    /**
     * @param  array<string, mixed> $options
     * @return array<string, mixed>
     */
    private static function filterParameters(array $options): array
    {
        $exclude = ['max_tokens'];

        return array_diff_key($options, array_flip($exclude));
    }
}
