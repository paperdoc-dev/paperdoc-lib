<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\DocumentInterface;
use Paperdoc\Document\Style\{PdfProtection, RunningElement, TextStyle, Watermark};
use Paperdoc\Enum\Format;
use Paperdoc\Factory\DocumentHydrator;
use Paperdoc\Support\ThumbnailGenerator;

final class Document implements DocumentInterface, \JsonSerializable
{
    /** @var Section[] */
    private array $sections = [];

    /** @var array<string, mixed> */
    private array $metadata = [];

    private ?Metadata $properties = null;

    private TextStyle $defaultTextStyle;

    private ?RunningElement $header = null;

    private ?RunningElement $footer = null;

    private ?Watermark $watermark = null;

    private ?PdfProtection $protection = null;

    private string $format;

    public function __construct(
        Format|string $format,
        private string $title = '',
    ) {
        $this->format = $format instanceof Format ? $format->value : $format;
        $this->defaultTextStyle = new TextStyle();
    }

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(Format|string $format, string $title = ''): static
    {
        return new static($format, $title);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $rawFormat = $data['format'] ?? Format::PDF->value;
        $format = $rawFormat instanceof Format
            ? $rawFormat
            : (is_string($rawFormat) ? $rawFormat : Format::PDF->value);
        $title = $data['title'] ?? '';
        $document = new static(
            $format,
            is_string($title) ? $title : '',
        );

        foreach (DocumentHydrator::asMap($data['metadata'] ?? null) as $key => $value) {
            $document->setMetadata($key, $value);
        }

        if (is_array($data['properties'] ?? null)) {
            $document->setProperties(Metadata::fromArray(DocumentHydrator::asMap($data['properties'])));
        }

        if (is_array($data['defaultTextStyle'] ?? null)) {
            $document->setDefaultTextStyle(DocumentHydrator::textStyleFromArrayOrNull($data['defaultTextStyle']) ?? new TextStyle());
        }

        $document->setHeader(DocumentHydrator::runningElementFromArrayOrNull($data['header'] ?? null));
        $document->setFooter(DocumentHydrator::runningElementFromArrayOrNull($data['footer'] ?? null));
        $document->setProtection(is_array($data['protection'] ?? null) ? PdfProtection::fromArray(DocumentHydrator::asMap($data['protection'])) : null);

        foreach (DocumentHydrator::asList($data['sections'] ?? null) as $sectionData) {
            if (is_array($sectionData)) {
                $document->addSection(Section::fromArray(DocumentHydrator::asMap($sectionData)));
            }
        }

        return $document;
    }

    public static function fromJson(string $json): static
    {
        /** @var array<string, mixed> $data */
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return static::fromArray($data);
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

    public function addSection(?Section $section = null): static
    {
        $this->sections[] = $section ?? Section::make();

        return $this;
    }

    public function openSection(?Section $section = null): Section
    {
        $section ??= Section::make();
        $this->sections[] = $section;

        return $section;
    }

    public function removeSection(int $index): static
    {
        unset($this->sections[$index]);
        $this->sections = array_values($this->sections);

        return $this;
    }

    /* -------------------------------------------------------------
     | Metadata (loose key/value bag: source_file, custom extras…)
     |------------------------------------------------------------- */

    /** @return array<string, mixed> */
    public function getMetadata(): array { return $this->metadata; }

    public function setMetadata(string $key, mixed $value): static
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /* -------------------------------------------------------------
     | Properties (typed: author / subject / created-at / …)
     |------------------------------------------------------------- */

    public function getProperties(): ?Metadata { return $this->properties; }

    public function setProperties(?Metadata $properties): static
    {
        $this->properties = $properties;

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
     | Header / Footer (running elements)
     |------------------------------------------------------------- */

    public function getHeader(): ?RunningElement { return $this->header; }
    public function getFooter(): ?RunningElement { return $this->footer; }

    public function setHeader(?RunningElement $header): static
    {
        $this->header = $header;

        return $this;
    }

    public function setFooter(?RunningElement $footer): static
    {
        $this->footer = $footer;

        return $this;
    }

    public function getWatermark(): ?Watermark { return $this->watermark; }

    public function setWatermark(?Watermark $watermark): static
    {
        $this->watermark = $watermark;

        return $this;
    }

    public function getProtection(): ?PdfProtection { return $this->protection; }

    public function setProtection(?PdfProtection $protection): static
    {
        $this->protection = $protection;

        return $this;
    }

    /* -------------------------------------------------------------
     | JsonSerializable
     |------------------------------------------------------------- */

    public function jsonSerialize(): mixed
    {
        $result = [
            'format'   => $this->format,
            'title'    => $this->title,
            'metadata' => $this->metadata,
            'sections' => $this->sections,
        ];

        if ($this->properties !== null) {
            $result['properties'] = $this->properties;
        }

        if ($this->header !== null) {
            $result['header'] = $this->header;
        }

        if ($this->footer !== null) {
            $result['footer'] = $this->footer;
        }

        if ($this->protection !== null) {
            $result['protection'] = $this->protection;
        }

        $result['defaultTextStyle'] = $this->defaultTextStyle;

        return $result;
    }
}
