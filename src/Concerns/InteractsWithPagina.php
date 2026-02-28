<?php

declare(strict_types=1);

namespace Pagina\Concerns;

use Pagina\Contracts\DocumentInterface;
use Pagina\Support\DocumentManager;

/**
 * Trait pour intégrer Pagina dans n'importe quel composant
 * (Controller, Action, Livewire Component, etc.).
 */
trait InteractsWithPagina
{
    protected function createDocument(string $format, string $title = ''): DocumentInterface
    {
        return DocumentManager::create($format, $title);
    }

    protected function openDocument(string $filename): DocumentInterface
    {
        return DocumentManager::open($filename);
    }

    protected function saveDocument(
        DocumentInterface $document,
        string $filename,
        ?string $format = null,
    ): void {
        DocumentManager::save($document, $filename, $format);
    }

    protected function convertDocument(
        string $sourceFile,
        string $targetFile,
        string $targetFormat,
    ): void {
        DocumentManager::convert($sourceFile, $targetFile, $targetFormat);
    }
}
