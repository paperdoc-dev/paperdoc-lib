<?php

declare(strict_types=1);

namespace Paperdoc\Llm\Providers;

use Paperdoc\Exceptions\LlmException;
use Paperdoc\Support\Cast;

/**
 * OpenAI Chat Completions — also works with any OpenAI-compatible
 * endpoint (Azure, vLLM, LM Studio…) via a custom base URL.
 */
class OpenAiProvider extends AbstractHttpProvider
{
    protected function name(): string { return 'openai'; }

    public function getBaseUrl(): string
    {
        return rtrim($this->baseUrl !== '' ? $this->baseUrl : 'https://api.openai.com/v1', '/');
    }

    public function chat(string $systemPrompt, array $content, array $options = []): string
    {
        $userContent = [];
        foreach ($content as $block) {
            if ($block['type'] === 'image') {
                $mimeType = $block['mimeType'] ?? 'image/png';
                $data = $block['data'] ?? '';
                $userContent[] = [
                    'type'      => 'image_url',
                    'image_url' => ['url' => "data:{$mimeType};base64,{$data}"],
                ];
            } else {
                $userContent[] = ['type' => 'text', 'text' => $block['text'] ?? ''];
            }
        }

        $payload = [
            'model'    => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userContent],
            ],
        ];

        if (isset($options['temperature'])) {
            $payload['temperature'] = Cast::asFloat($options['temperature']);
        }
        if (isset($options['max_tokens'])) {
            $payload['max_tokens'] = Cast::asInt($options['max_tokens']);
        }

        $response = $this->postJson(
            $this->getBaseUrl() . '/chat/completions',
            ['Authorization' => "Bearer {$this->apiKey}"],
            $payload,
            $options,
        );

        $choices = Cast::asList($response['choices'] ?? []);
        $choice = Cast::asMap($choices[0] ?? []);
        $message = Cast::asMap($choice['message'] ?? []);
        $text = $message['content'] ?? null;
        if (! is_string($text)) {
            throw LlmException::forResponse($this->name(), 'missing choices[0].message.content');
        }

        return $text;
    }
}
