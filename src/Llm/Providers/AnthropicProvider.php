<?php

declare(strict_types=1);

namespace Paperdoc\Llm\Providers;

use Paperdoc\Exceptions\LlmException;
use Paperdoc\Support\Cast;

class AnthropicProvider extends AbstractHttpProvider
{
    private const API_VERSION = '2023-06-01';

    protected function name(): string { return 'anthropic'; }

    public function getBaseUrl(): string
    {
        return rtrim($this->baseUrl !== '' ? $this->baseUrl : 'https://api.anthropic.com/v1', '/');
    }

    public function chat(string $systemPrompt, array $content, array $options = []): string
    {
        $blocks = [];
        foreach ($content as $block) {
            if ($block['type'] === 'image') {
                $blocks[] = [
                    'type'   => 'image',
                    'source' => [
                        'type'       => 'base64',
                        'media_type' => $block['mimeType'] ?? 'image/png',
                        'data'       => $block['data'] ?? '',
                    ],
                ];
            } else {
                $blocks[] = ['type' => 'text', 'text' => $block['text'] ?? ''];
            }
        }

        $payload = [
            'model'      => $this->model,
            'max_tokens' => Cast::asInt($options['max_tokens'] ?? 4096, 4096),
            'system'     => $systemPrompt,
            'messages'   => [['role' => 'user', 'content' => $blocks]],
        ];

        if (isset($options['temperature'])) {
            $payload['temperature'] = Cast::asFloat($options['temperature']);
        }

        $response = $this->postJson(
            $this->getBaseUrl() . '/messages',
            [
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => self::API_VERSION,
            ],
            $payload,
            $options,
        );

        $text = '';
        foreach (Cast::asList($response['content'] ?? []) as $part) {
            $partMap = Cast::asMap($part);
            if (Cast::asString($partMap['type'] ?? '') === 'text') {
                $text .= Cast::asString($partMap['text'] ?? '');
            }
        }

        if ($text === '') {
            throw LlmException::forResponse($this->name(), 'no text content block in response');
        }

        return $text;
    }
}
