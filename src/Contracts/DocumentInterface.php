<?php

declare(strict_types=1);

namespace Paperdoc\Contracts;

use Paperdoc\Document\Image;
use Paperdoc\Document\Section;
use Paperdoc\Document\Style\TextStyle;

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

    public function getFirstImage(): ?Image;

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    public function getThumbnail(int $maxWidth = 300, int $maxHeight = 300, int $quality = 85): ?array;

    public function getThumbnailDataUri(int $maxWidth = 300, int $maxHeight = 300, int $quality = 85): ?string;

    public function getThumbnailBase64(int $maxWidth = 300, int $maxHeight = 300, int $quality = 85): ?string;
}
