<?php

declare(strict_types=1);

namespace Paperdoc\Concerns;

use Paperdoc\Contracts\DocumentInterface;
use Paperdoc\Support\DocumentManager;

/**
 * Trait pour intégrer Paperdoc dans n'importe quel composant
 * (Controller, Action, Livewire Component, etc.).
 */
trait InteractsWithPaperdoc
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

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    protected function documentThumbnail(
        DocumentInterface $document,
        int $maxWidth = 300,
        int $maxHeight = 300,
    ): ?array {
        return DocumentManager::thumbnail($document, $maxWidth, $maxHeight);
    }

    protected function documentThumbnailDataUri(
        DocumentInterface $document,
        int $maxWidth = 300,
        int $maxHeight = 300,
    ): ?string {
        return DocumentManager::thumbnailDataUri($document, $maxWidth, $maxHeight);
    }
}
