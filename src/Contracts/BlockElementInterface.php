<?php

declare(strict_types=1);

namespace Paperdoc\Contracts;

/**
 * Marker interface for block-level elements — elements that can be direct
 * children of a Section (Paragraph, Table, Image, PageBreak, Heading,
 * ListBlock, CodeBlock, Blockquote, Bookmark…).
 */
interface BlockElementInterface extends DocumentElementInterface
{
}
