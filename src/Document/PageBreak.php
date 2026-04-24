<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\BlockElementInterface;

class PageBreak implements BlockElementInterface, \JsonSerializable
{
    public function getType(): string { return 'page_break'; }

    public function jsonSerialize(): mixed
    {
        return ['type' => 'page_break'];
    }
}
