<?php

declare(strict_types=1);

namespace Paperdoc\Llm\Providers;

use Paperdoc\Exceptions\LlmException;
use Paperdoc\Support\Cast;

class GeminiProvider extends AbstractHttpProvider
{
    protected function name(): string { return 'gemini'; }

    public function getBaseUrl(): string
    {
        return rtrim(
            $this->baseUrl !== '' ? $this->baseUrl : 'https://generativelanguage.googleapis.com/v1beta',
            '/',
        );
    }

    public function chat(string $systemPrompt, array $content, array $options = []): string
    {
        $parts = [];
        foreach ($content as $block) {
            if ($block['type'] === 'image') {
                $parts[] = ['inline_data' => [
                    'mime_type' => $block['mimeType'] ?? 'image/png',
                    'data'      => $block['data'] ?? '',
                ]];
            } else {
                $parts[] = ['text' => $block['text'] ?? ''];
            }
        }

        $payload = [
            'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
            'contents'           => [['role' => 'user', 'parts' => $parts]],
        ];

        $generationConfig = [];
        if (isset($options['temperature'])) {
            $generationConfig['temperature'] = Cast::asFloat($options['temperature']);
        }
        if (isset($options['max_tokens'])) {
            $generationConfig['maxOutputTokens'] = Cast::asInt($options['max_tokens']);
        }
        if ($generationConfig !== []) {
            $payload['generationConfig'] = $generationConfig;
        }

        $response = $this->postJson(
            sprintf('%s/models/%s:generateContent', $this->getBaseUrl(), $this->model),
            ['x-goog-api-key' => $this->apiKey],
            $payload,
            $options,
        );

        $text = '';
        $candidates = Cast::asList($response['candidates'] ?? []);
        $first = Cast::asMap($candidates[0] ?? []);
        $contentMap = Cast::asMap($first['content'] ?? []);

        foreach (Cast::asList($contentMap['parts'] ?? []) as $part) {
            $partMap = Cast::asMap($part);
            $text .= Cast::asString($partMap['text'] ?? '');
        }

        if ($text === '') {
            throw LlmException::forResponse($this->name(), 'no text part in first candidate');
        }

        return $text;
    }
}
