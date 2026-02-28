<?php

declare(strict_types=1);

namespace Pagina\Contracts;

interface WriterInterface
{
    /**
     * @throws \RuntimeException
     */
    public function write(DocumentInterface $document, string $filename): void;

    public function getFormat(): string;
}
