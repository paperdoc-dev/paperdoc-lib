<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Llm;

use NeuronAI\Providers\Anthropic\Anthropic;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\Providers\Ollama\Ollama;
use NeuronAI\Providers\OpenAI\OpenAI;
use PHPUnit\Framework\TestCase;
use Paperdoc\Llm\ProviderFactory;

class ProviderFactoryTest extends TestCase
{
    public function test_default_creates_openai(): void
    {
        $provider = ProviderFactory::make([
            'provider' => 'openai',
            'model' => 'gpt-4o-mini',
            'api_key' => 'test-key',
        ]);

        $this->assertInstanceOf(OpenAI::class, $provider);
    }

    public function test_creates_anthropic(): void
    {
        $provider = ProviderFactory::make([
            'provider' => 'anthropic',
            'model' => 'claude-sonnet-4-20250514',
            'api_key' => 'test-key',
        ]);

        $this->assertInstanceOf(Anthropic::class, $provider);
    }

    public function test_creates_anthropic_via_claude_alias(): void
    {
        $provider = ProviderFactory::make([
            'provider' => 'claude',
            'model' => 'claude-sonnet-4-20250514',
            'api_key' => 'test-key',
        ]);

        $this->assertInstanceOf(Anthropic::class, $provider);
    }

    public function test_creates_gemini(): void
    {
        $provider = ProviderFactory::make([
            'provider' => 'gemini',
            'model' => 'gemini-2.0-flash',
            'api_key' => 'test-key',
        ]);

        $this->assertInstanceOf(Gemini::class, $provider);
    }

    public function test_creates_ollama(): void
    {
        $provider = ProviderFactory::make([
            'provider' => 'ollama',
            'model' => 'llama3',
            'base_url' => 'http://localhost:11434/api',
        ]);

        $this->assertInstanceOf(Ollama::class, $provider);
    }

    public function test_ollama_uses_default_url(): void
    {
        $provider = ProviderFactory::make([
            'provider' => 'ollama',
            'model' => 'llama3',
        ]);

        $this->assertInstanceOf(Ollama::class, $provider);
    }

    public function test_unknown_provider_defaults_to_openai(): void
    {
        $provider = ProviderFactory::make([
            'provider' => 'unknown-provider',
            'model' => 'some-model',
            'api_key' => 'key',
        ]);

        $this->assertInstanceOf(OpenAI::class, $provider);
    }

    public function test_passes_parameters(): void
    {
        $provider = ProviderFactory::make([
            'provider' => 'openai',
            'model' => 'gpt-4o',
            'api_key' => 'test-key',
            'options' => [
                'temperature' => 0.1,
            ],
        ]);

        $this->assertInstanceOf(OpenAI::class, $provider);
    }
}
