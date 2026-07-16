<?php

declare(strict_types=1);

namespace Paperdoc\Contracts;

interface LlmProviderInterface
{
    /**
     * Send a single-turn chat request and return the assistant's text.
     *
     * @param list<array{type: string, text?: string, data?: string, mimeType?: string}> $content
     *        Ordered content blocks: `['type' => 'text', 'text' => …]` or
     *        `['type' => 'image', 'data' => base64, 'mimeType' => …]`.
     * @param array<string, mixed> $options temperature, max_tokens, timeout…
     */
    public function chat(string $systemPrompt, array $content, array $options = []): string;
}
