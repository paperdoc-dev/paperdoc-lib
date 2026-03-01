<?php

declare(strict_types=1);

namespace Paperdoc\Contracts;

interface DocumentElementInterface
{
    public function getType(): string;
}
