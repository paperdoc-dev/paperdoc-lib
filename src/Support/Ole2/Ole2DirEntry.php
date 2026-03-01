<?php

declare(strict_types=1);

namespace Paperdoc\Support\Ole2;

class Ole2DirEntry
{
    public const TYPE_EMPTY   = 0;
    public const TYPE_STORAGE = 1;
    public const TYPE_STREAM  = 2;
    public const TYPE_ROOT    = 5;

    public string $name = '';
    public int $type = self::TYPE_EMPTY;
    public int $startSector = 0;
    public int $size = 0;
}
