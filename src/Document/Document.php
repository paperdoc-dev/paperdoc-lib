<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\DocumentInterface;
use Paperdoc\Document\Style\TextStyle;
use Paperdoc\Support\ThumbnailGenerator;

class Document implements DocumentInterface, \JsonSerializable
{
    /** @var Section[] */
    private array $sections = [];

    /** @var array<string, mixed> */
    private array $metadata = [];

    private TextStyle $defaultTextStyle;

    public function __construct(
        private string $format,
        private string $title = '',
    ) {
        $this->defaultTextStyle = new TextStyle();
    }

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(string $format, string $title = ''): static
    {
        return new static($format, $title);
    }

    /* -------------------------------------------------------------
     | Title & Format
     |------------------------------------------------------------- */

    public function getTitle(): string  { return $this->title; }
    public function getFormat(): string { return $this->format; }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /* -------------------------------------------------------------
     | Sections
     |------------------------------------------------------------- */

    /** @return Section[] */
    public function getSections(): array { return $this->sections; }

    public function addSection(Section $section): static
    {
        $this->sections[] = $section;

        return $this;
    }

    public function removeSection(int $index): static
    {
        unset($this->sections[$index]);
        $this->sections = array_values($this->sections);

        return $this;
    }

    /* -------------------------------------------------------------
     | Metadata
     |------------------------------------------------------------- */

    /** @return array<string, mixed> */
    public function getMetadata(): array { return $this->metadata; }

    public function setMetadata(string $key, mixed $value): static
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /* -------------------------------------------------------------
     | Thumbnail (dynamic – scans sections for the first image)
     |------------------------------------------------------------- */

    public function getFirstImage(): ?Image
    {
        foreach ($this->sections as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof Image) {
                    return $element;
                }
            }
        }

        return null;
    }

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    public function getThumbnail(
        int $maxWidth = ThumbnailGenerator::DEFAULT_WIDTH,
        int $maxHeight = ThumbnailGenerator::DEFAULT_HEIGHT,
        int $quality = ThumbnailGenerator::DEFAULT_QUALITY,
    ): ?array {
        $fromFile = $this->thumbnailFromSourceFile($maxWidth, $maxHeight, $quality);

        if ($fromFile !== null) {
            return $fromFile;
        }

        $image = $this->getFirstImage();

        if ($image !== null) {
            $result = $image->getThumbnail($maxWidth, $maxHeight, $quality);
            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    public function getThumbnailDataUri(
        int $maxWidth = ThumbnailGenerator::DEFAULT_WIDTH,
        int $maxHeight = ThumbnailGenerator::DEFAULT_HEIGHT,
        int $quality = ThumbnailGenerator::DEFAULT_QUALITY,
    ): ?string {
        $thumb = $this->getThumbnail($maxWidth, $maxHeight, $quality);

        if ($thumb === null) {
            return null;
        }

        return 'data:' . $thumb['mimeType'] . ';base64,' . base64_encode($thumb['data']);
    }

    public function getThumbnailBase64(
        int $maxWidth = ThumbnailGenerator::DEFAULT_WIDTH,
        int $maxHeight = ThumbnailGenerator::DEFAULT_HEIGHT,
        int $quality = ThumbnailGenerator::DEFAULT_QUALITY,
    ): ?string {
        $thumb = $this->getThumbnail($maxWidth, $maxHeight, $quality);

        return $thumb !== null ? base64_encode($thumb['data']) : null;
    }

    /**
     * Fallback: render the first page of the source file as a thumbnail.
     *
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    private function thumbnailFromSourceFile(int $maxWidth, int $maxHeight, int $quality): ?array
    {
        $sourceFile = $this->metadata['source_file'] ?? null;

        if ($sourceFile === null || ! is_string($sourceFile) || ! file_exists($sourceFile)) {
            return null;
        }

        return ThumbnailGenerator::fromFile($sourceFile, $maxWidth, $maxHeight, $quality);
    }

    /* -------------------------------------------------------------
     | Default Text Style
     |------------------------------------------------------------- */

    public function getDefaultTextStyle(): TextStyle { return $this->defaultTextStyle; }

    public function setDefaultTextStyle(TextStyle $style): static
    {
        $this->defaultTextStyle = $style;

        return $this;
    }

    /* -------------------------------------------------------------
     | JsonSerializable
     |------------------------------------------------------------- */

    public function jsonSerialize(): mixed
    {
        return [
            'format'   => $this->format,
            'title'    => $this->title,
            'metadata' => $this->metadata,
            'sections' => $this->sections,
        ];
    }
}
