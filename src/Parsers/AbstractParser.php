<?php

declare(strict_types=1);

namespace Paperdoc\Parsers;

abstract class AbstractParser
{
    /**
     * @throws \RuntimeException
     */
    protected function assertFileReadable(string $filename): void
    {
        if (! file_exists($filename) || ! is_readable($filename)) {
            throw new \RuntimeException("Fichier introuvable ou illisible : {$filename}");
        }
    }
}
