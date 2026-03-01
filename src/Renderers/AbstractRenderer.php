<?php

declare(strict_types=1);

namespace Paperdoc\Renderers;

use Paperdoc\Contracts\{DocumentInterface, RendererInterface};

abstract class AbstractRenderer implements RendererInterface
{
    public function save(DocumentInterface $document, string $filename): void
    {
        $this->ensureDirectoryWritable($filename);
        file_put_contents($filename, $this->render($document));
    }

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
