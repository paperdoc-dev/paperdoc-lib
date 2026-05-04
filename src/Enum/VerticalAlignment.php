<?php

declare(strict_types=1);

namespace Paperdoc\Enum;

/**
 * Vertical positioning of a Section's content within its page area.
 *
 * Used by {@see \Paperdoc\Document\Section::setVerticalAlignment()} to
 * place a colophon, a chapter opener or a centred title without
 * resorting to manual top-padding hacks.
 *
 *  - {@see VerticalAlignment::TOP}    : default. Content flows from
 *                                       the top padding downwards.
 *  - {@see VerticalAlignment::CENTER} : the content block is centred
 *                                       between top and bottom padding.
 *  - {@see VerticalAlignment::BOTTOM} : the content block is anchored
 *                                       to the bottom padding.
 */
enum VerticalAlignment: string
{
    case TOP    = 'top';
    case CENTER = 'center';
    case BOTTOM = 'bottom';
}
