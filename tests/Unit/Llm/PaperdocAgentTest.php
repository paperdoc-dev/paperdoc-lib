<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Llm;

use NeuronAI\Agent\Agent;
use NeuronAI\Providers\AIProviderInterface;
use PHPUnit\Framework\TestCase;
use Paperdoc\Llm\PaperdocAgent;

class PaperdocAgentTest extends TestCase
{
    public function test_extends_agent(): void
    {
        $provider = $this->createMock(AIProviderInterface::class);
        $agent = new PaperdocAgent($provider);

        $this->assertInstanceOf(Agent::class, $agent);
    }

    public function test_resolves_correct_provider(): void
    {
        $provider = $this->createMock(AIProviderInterface::class);
        $agent = new PaperdocAgent($provider);

        $this->assertSame($provider, $agent->resolveProvider());
    }

    public function test_instructions_contain_ocr_guidance(): void
    {
        $provider = $this->createMock(AIProviderInterface::class);
        $agent = new PaperdocAgent($provider);

        $instructions = $agent->resolveInstructions();

        $this->assertStringContainsString('OCR', $instructions);
        $this->assertStringContainsString('CORRECT', $instructions);
        $this->assertStringContainsString('STRUCTURE', $instructions);
        $this->assertStringContainsString('confidence', $instructions);
    }
}
