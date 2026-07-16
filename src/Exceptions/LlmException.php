<?php

declare(strict_types=1);

namespace Paperdoc\Exceptions;

class LlmException extends PaperdocException
{
    public static function forHttp(string $provider, int $status, string $body): self
    {
        return new self(sprintf(
            'LLM provider "%s" returned HTTP %d: %s',
            $provider,
            $status,
            mb_substr($body, 0, 500),
        ));
    }

    public static function forTransport(string $provider, string $reason): self
    {
        return new self(sprintf('LLM provider "%s" request failed: %s', $provider, $reason));
    }

    public static function forResponse(string $provider, string $reason): self
    {
        return new self(sprintf('LLM provider "%s" returned an unexpected response: %s', $provider, $reason));
    }
}
