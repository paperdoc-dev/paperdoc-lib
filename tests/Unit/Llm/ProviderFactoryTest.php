<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Llm;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\LlmProviderInterface;
use Paperdoc\Llm\ProviderFactory;
use Paperdoc\Llm\Providers\{AnthropicProvider, GeminiProvider, OllamaProvider, OpenAiProvider};

class ProviderFactoryTest extends TestCase
{
    public function test_default_creates_openai(): void
    {
        $provider = ProviderFactory::make(['model' => 'gpt-4o-mini', 'api_key' => 'k']);

        $this->assertInstanceOf(OpenAiProvider::class, $provider);
        $this->assertInstanceOf(LlmProviderInterface::class, $provider);
    }

    public function test_creates_anthropic(): void
    {
        $provider = ProviderFactory::make(['provider' => 'anthropic', 'model' => 'claude-sonnet-5', 'api_key' => 'k']);

        $this->assertInstanceOf(AnthropicProvider::class, $provider);
        $this->assertSame('https://api.anthropic.com/v1', $provider->getBaseUrl());
    }

    public function test_creates_anthropic_via_claude_alias(): void
    {
        $provider = ProviderFactory::make(['provider' => 'claude', 'model' => 'claude-sonnet-5', 'api_key' => 'k']);

        $this->assertInstanceOf(AnthropicProvider::class, $provider);
    }

    public function test_creates_gemini(): void
    {
        $provider = ProviderFactory::make(['provider' => 'gemini', 'model' => 'gemini-2.0-flash', 'api_key' => 'k']);

        $this->assertInstanceOf(GeminiProvider::class, $provider);
        $this->assertStringContainsString('generativelanguage.googleapis.com', $provider->getBaseUrl());
    }

    public function test_creates_ollama(): void
    {
        $provider = ProviderFactory::make(['provider' => 'ollama', 'model' => 'llama3', 'base_url' => 'http://box:11434/api']);

        $this->assertInstanceOf(OllamaProvider::class, $provider);
        $this->assertSame('http://box:11434/api', $provider->getBaseUrl());
    }

    public function test_ollama_uses_default_url(): void
    {
        $provider = ProviderFactory::make(['provider' => 'ollama', 'model' => 'llama3']);

        $this->assertSame('http://localhost:11434/api', $provider->getBaseUrl());
    }

    public function test_unknown_provider_defaults_to_openai(): void
    {
        $provider = ProviderFactory::make(['provider' => 'whatever', 'model' => 'x']);

        $this->assertInstanceOf(OpenAiProvider::class, $provider);
    }

    public function test_openai_honours_custom_base_url(): void
    {
        $provider = ProviderFactory::make(['model' => 'x', 'base_url' => 'http://vllm.local/v1/']);

        $this->assertInstanceOf(OpenAiProvider::class, $provider);
        $this->assertSame('http://vllm.local/v1', $provider->getBaseUrl());
    }
}
