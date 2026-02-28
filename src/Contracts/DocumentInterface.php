<?php

declare(strict_types=1);

namespace Pagina\Contracts;

use Pagina\Document\Section;
use Pagina\Document\Style\TextStyle;

interface DocumentInterface
{
    public function getTitle(): string;

    public function setTitle(string $title): static;

    public function getFormat(): string;

    /**
     * @return Section[]
     */
    public function getSections(): array;

    public function addSection(Section $section): static;

    public function removeSection(int $index): static;

    /**
     * @return array<string, mixed>
     */
    public function getMetadata(): array;

    public function setMetadata(string $key, mixed $value): static;

    public function getDefaultTextStyle(): TextStyle;

    public function setDefaultTextStyle(TextStyle $style): static;
}
