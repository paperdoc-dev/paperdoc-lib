<?php

declare(strict_types=1);

namespace Paperdoc\Exceptions;

/**
 * Thrown when a parser fails to read or decode an input file (malformed
 * DOCX/XLSX/PDF, unreadable file, unsupported internal structure, etc.).
 */
class ParserException extends PaperdocException
{
    public static function forFile(string $path, string $reason, ?\Throwable $previous = null): self
    {
        return new self(
            sprintf('Failed to parse "%s": %s', $path, $reason),
            0,
            $previous,
        );
    }
}
