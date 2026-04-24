<?php

declare(strict_types=1);

namespace Paperdoc\Contracts;

/**
 * Marker interface for inline elements — elements that live inside a
 * block (TextRun today; reserved for future InlineBookmark, FieldRef,
 * Image-inline, etc.).
 */
interface InlineElementInterface extends DocumentElementInterface
{
}
