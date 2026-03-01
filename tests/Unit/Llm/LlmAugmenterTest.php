<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Llm;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\LlmAugmenterInterface;
use Paperdoc\Llm\LlmAugmenter;

class LlmAugmenterTest extends TestCase
{
    public function test_implements_interface(): void
    {
        $augmenter = new LlmAugmenter([
            'provider' => 'openai',
            'model' => 'gpt-4o-mini',
            'api_key' => 'test',
        ]);

        $this->assertInstanceOf(LlmAugmenterInterface::class, $augmenter);
    }

    public function test_enhance_returns_empty_for_empty_input(): void
    {
        $augmenter = new LlmAugmenter([
            'provider' => 'openai',
            'model' => 'gpt-4o-mini',
            'api_key' => 'test',
        ]);

        $this->assertSame('', $augmenter->enhance(''));
        $this->assertSame('', $augmenter->enhance('   '));
    }
}
