<?php

declare(strict_types=1);

namespace Pagina\Contracts;

interface StyleInterface
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
