<?php

declare(strict_types=1);

namespace Paperdoc\Enum;

enum Alignment: string
{
    case LEFT    = 'left';
    case CENTER  = 'center';
    case RIGHT   = 'right';
    case JUSTIFY = 'justify';
}
