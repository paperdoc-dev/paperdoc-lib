<?php

declare(strict_types=1);

namespace Pagina\Enum;

enum Alignment: string
{
    case LEFT    = 'left';
    case CENTER  = 'center';
    case RIGHT   = 'right';
    case JUSTIFY = 'justify';
}
