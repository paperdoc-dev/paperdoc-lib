<?php

declare(strict_types=1);

namespace Paperdoc\Exceptions;

use RuntimeException;

/**
 * Base exception for all Paperdoc errors. Every other Paperdoc exception
 * extends this class so consumers can catch library errors with a single
 * catch block.
 */
class PaperdocException extends RuntimeException
{
}
