<?php

declare(strict_types=1);

namespace Paperdoc\Llm;

use Paperdoc\Contracts\LlmProviderInterface;
use Paperdoc\Llm\Providers\{AnthropicProvider, GeminiProvider, OllamaProvider, OpenAiProvider};
use Paperdoc\Support\Cast;

class ProviderFactory
{
    /**
     * @param array<string, mixed> $config
     */
    public static function make(array $config): LlmProviderInterface
    {
        $model   = Cast::asString($config['model'] ?? 'gpt-4o-mini', 'gpt-4o-mini');
        $apiKey  = Cast::asString($config['api_key'] ?? '');
        $baseUrl = Cast::asString($config['base_url'] ?? '');
        $provider = Cast::asString($config['provider'] ?? 'openai', 'openai');

        return match ($provider) {
            'anthropic', 'claude' => new AnthropicProvider($model, $apiKey, $baseUrl),
            'gemini'              => new GeminiProvider($model, $apiKey, $baseUrl),
            'ollama'              => new OllamaProvider($model, $apiKey, $baseUrl),
            default               => new OpenAiProvider($model, $apiKey, $baseUrl),
        };
    }
}
