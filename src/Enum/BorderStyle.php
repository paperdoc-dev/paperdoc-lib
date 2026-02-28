<?php

declare(strict_types=1);

namespace Pagina\Enum;

enum BorderStyle: string
{
    case NONE   = 'none';
    case SOLID  = 'solid';
    case DASHED = 'dashed';
    case DOTTED = 'dotted';
}
