<?php

declare(strict_types=1);

namespace Pagina\Writers;

use Pagina\Contracts\WriterInterface;

abstract class AbstractWriter implements WriterInterface
{
    /**
     * @throws \RuntimeException
     */
    protected function ensureDirectoryWritable(string $filename): void
    {
        $dir = dirname($filename);

        if (! is_dir($dir) && ! mkdir($dir, 0755, true)) {
            throw new \RuntimeException("Impossible de créer le répertoire : {$dir}");
        }

        if (! is_writable($dir)) {
            throw new \RuntimeException("Répertoire non accessible en écriture : {$dir}");
        }
    }
}
