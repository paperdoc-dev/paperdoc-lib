<?php

declare(strict_types=1);

namespace Paperdoc\Exceptions;

/**
 * Thrown when a renderer fails to serialise a Document (zip failure,
 * unsupported element combination, write error, etc.).
 */
class RendererException extends PaperdocException
{
    public static function forFormat(string $format, string $reason, ?\Throwable $previous = null): self
    {
        return new self(
            sprintf('Failed to render document as "%s": %s', $format, $reason),
            0,
            $previous,
        );
    }
}
