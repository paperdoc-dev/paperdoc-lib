<?php

declare(strict_types=1);

namespace Paperdoc\Support\Pdf;

/**
 * Représente un objet indirect PDF (N 0 obj ... endobj).
 */
class PdfObject
{
    public function __construct(
        private readonly int $number,
        private readonly string $content,
    ) {}

    public function getNumber(): int { return $this->number; }

    public function render(): string
    {
        return "{$this->number} 0 obj\n{$this->content}\nendobj";
    }
}
