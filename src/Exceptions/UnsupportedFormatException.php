<?php

declare(strict_types=1);

namespace Paperdoc\Exceptions;

/**
 * Thrown when a format is requested that Paperdoc does not know how to
 * parse or render (unknown extension, missing factory entry, etc.).
 */
class UnsupportedFormatException extends PaperdocException
{
    public static function forFormat(string $format): self
    {
        return new self(sprintf('Unsupported format: "%s".', $format));
    }

    public static function forExtension(string $path): self
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        return new self(sprintf(
            'Unsupported file extension "%s" (from path "%s").',
            $ext,
            $path,
        ));
    }
}
