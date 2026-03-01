<?php

declare(strict_types=1);

namespace Paperdoc\Contracts;

interface StyleInterface
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
