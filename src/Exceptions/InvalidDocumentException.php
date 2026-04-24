<?php

declare(strict_types=1);

namespace Paperdoc\Exceptions;

/**
 * Thrown when a Document instance is used in an invalid state — e.g. an
 * Image without `src` nor embedded data, a Heading with an out-of-range
 * level, a Section nested in an unexpected way.
 */
class InvalidDocumentException extends PaperdocException
{
}
