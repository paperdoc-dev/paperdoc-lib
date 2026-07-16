<?php

declare(strict_types=1);

namespace Paperdoc\Llm\Providers;

use Paperdoc\Exceptions\LlmException;
use Paperdoc\Support\Cast;

class OllamaProvider extends AbstractHttpProvider
{
    protected function name(): string { return 'ollama'; }

    public function getBaseUrl(): string
    {
        return rtrim($this->baseUrl !== '' ? $this->baseUrl : 'http://localhost:11434/api', '/');
    }

    public function chat(string $systemPrompt, array $content, array $options = []): string
    {
        $text = '';
        $images = [];
        foreach ($content as $block) {
            if ($block['type'] === 'image') {
                $images[] = $block['data'] ?? '';
            } else {
                $text .= ($text !== '' ? "\n" : '') . ($block['text'] ?? '');
            }
        }

        $userMessage = ['role' => 'user', 'content' => $text];
        if ($images !== []) {
            $userMessage['images'] = $images;
        }

        $payload = [
            'model'    => $this->model,
            'stream'   => false,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                $userMessage,
            ],
        ];

        $modelOptions = [];
        if (isset($options['temperature'])) {
            $modelOptions['temperature'] = Cast::asFloat($options['temperature']);
        }
        if (isset($options['max_tokens'])) {
            $modelOptions['num_predict'] = Cast::asInt($options['max_tokens']);
        }
        if ($modelOptions !== []) {
            $payload['options'] = $modelOptions;
        }

        $response = $this->postJson($this->getBaseUrl() . '/chat', [], $payload, $options);

        $message = Cast::asMap($response['message'] ?? []);
        $answer = $message['content'] ?? null;
        if (! is_string($answer) || $answer === '') {
            throw LlmException::forResponse($this->name(), 'missing message.content');
        }

        return $answer;
    }
}
