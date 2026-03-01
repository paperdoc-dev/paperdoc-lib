<?php

declare(strict_types=1);

namespace Paperdoc\Contracts;

interface RendererInterface
{
    /**
     * Rend le document et retourne le contenu en string.
     */
    public function render(DocumentInterface $document): string;

    /**
     * Sauvegarde le document rendu dans un fichier.
     *
     * @throws \RuntimeException
     */
    public function save(DocumentInterface $document, string $filename): void;

    public function getFormat(): string;
}
