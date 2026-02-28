<?php

declare(strict_types=1);

namespace Pagina\Contracts;

interface ParserInterface
{
    /**
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function parse(string $filename): DocumentInterface;

    public function supports(string $extension): bool;
}
