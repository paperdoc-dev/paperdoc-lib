<?php

declare(strict_types=1);

namespace Paperdoc\Llm\Providers;

use Paperdoc\Contracts\LlmProviderInterface;
use Paperdoc\Exceptions\LlmException;
use Paperdoc\Support\Cast;

abstract class AbstractHttpProvider implements LlmProviderInterface
{
    protected const DEFAULT_TIMEOUT = 60;

    public function __construct(
        protected readonly string $model,
        protected readonly string $apiKey = '',
        protected readonly string $baseUrl = '',
    ) {}

    abstract protected function name(): string;

    /**
     * @param array<string, string> $headers
     * @param array<string, mixed>  $payload
     * @param array<string, mixed>  $options
     * @return array<string, mixed>
     */
    protected function postJson(string $url, array $headers, array $payload, array $options = []): array
    {
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($body === false) {
            throw LlmException::forTransport($this->name(), 'payload could not be JSON-encoded');
        }

        $headers['Content-Type'] = 'application/json';
        $timeout = Cast::asInt($options['timeout'] ?? self::DEFAULT_TIMEOUT, self::DEFAULT_TIMEOUT);

        [$status, $raw] = \function_exists('curl_init')
            ? $this->sendWithCurl($url, $headers, $body, $timeout)
            : $this->sendWithStream($url, $headers, $body, $timeout);

        if ($status < 200 || $status >= 300) {
            throw LlmException::forHttp($this->name(), $status, $raw);
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            throw LlmException::forResponse($this->name(), 'body is not valid JSON');
        }

        return Cast::asMap($decoded);
    }

    /**
     * @param array<string, string> $headers
     * @return array{0: int, 1: string}
     */
    private function sendWithCurl(string $url, array $headers, string $body, int $timeout): array
    {
        $headerLines = [];
        foreach ($headers as $name => $value) {
            $headerLines[] = "{$name}: {$value}";
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => $headerLines,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => min(10, $timeout),
        ]);

        $raw = curl_exec($ch);
        if ($raw === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw LlmException::forTransport($this->name(), $error);
        }

        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        return [$status, (string) $raw];
    }

    /**
     * @param array<string, string> $headers
     * @return array{0: int, 1: string}
     */
    private function sendWithStream(string $url, array $headers, string $body, int $timeout): array
    {
        $headerLines = '';
        foreach ($headers as $name => $value) {
            $headerLines .= "{$name}: {$value}\r\n";
        }

        $context = stream_context_create(['http' => [
            'method'        => 'POST',
            'header'        => $headerLines,
            'content'       => $body,
            'timeout'       => $timeout,
            'ignore_errors' => true,
        ]]);

        $stream = @fopen($url, 'r', false, $context);
        if ($stream === false) {
            throw LlmException::forTransport($this->name(), error_get_last()['message'] ?? 'unreachable');
        }

        try {
            $raw = stream_get_contents($stream);
            if ($raw === false) {
                throw LlmException::forTransport($this->name(), error_get_last()['message'] ?? 'unreachable');
            }

            $meta = stream_get_meta_data($stream);
            $responseHeaders = $meta['wrapper_data'] ?? [];
        } finally {
            fclose($stream);
        }

        $status = 0;
        if (is_array($responseHeaders)) {
            foreach ($responseHeaders as $line) {
                if (is_string($line) && preg_match('#^HTTP/\S+\s+(\d{3})#', $line, $m)) {
                    $status = (int) $m[1];
                }
            }
        }

        return [$status, $raw];
    }
}
