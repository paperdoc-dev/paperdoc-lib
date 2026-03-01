<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\DocumentElementInterface;
use Paperdoc\Support\ThumbnailGenerator;

class Image implements DocumentElementInterface, \JsonSerializable
{
    private ?string $data = null;

    private ?string $mimeType = null;

    public function __construct(
        private string $src,
        private int $width = 0,
        private int $height = 0,
        private string $alt = '',
    ) {}

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(string $src, int $width = 0, int $height = 0, string $alt = ''): static
    {
        return new static($src, $width, $height, $alt);
    }

    public static function fromData(string $data, string $mimeType, int $width = 0, int $height = 0, string $alt = ''): static
    {
        $ext = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            'image/bmp'  => 'bmp',
            'image/tiff' => 'tiff',
            'image/svg+xml' => 'svg',
            'image/x-emf', 'image/emf' => 'emf',
            'image/x-wmf', 'image/wmf' => 'wmf',
            default => 'bin',
        };

        $image = new static("embedded.{$ext}", $width, $height, $alt);
        $image->data = $data;
        $image->mimeType = $mimeType;

        return $image;
    }

    /* -------------------------------------------------------------
     | DocumentElementInterface
     |------------------------------------------------------------- */

    public function getType(): string { return 'image'; }

    /* -------------------------------------------------------------
     | Accessors
     |------------------------------------------------------------- */

    public function getSrc(): string  { return $this->src; }
    public function getWidth(): int   { return $this->width; }
    public function getHeight(): int  { return $this->height; }
    public function getAlt(): string  { return $this->alt; }

    public function getData(): ?string    { return $this->data; }
    public function getMimeType(): ?string { return $this->mimeType; }
    public function hasData(): bool       { return $this->data !== null; }

    public function getDataUri(): ?string
    {
        if ($this->data === null || $this->mimeType === null) {
            return null;
        }

        return 'data:' . $this->mimeType . ';base64,' . base64_encode($this->data);
    }

    /**
     * @return int size in bytes, or 0 if no embedded data
     */
    public function getDataSize(): int
    {
        return $this->data !== null ? strlen($this->data) : 0;
    }

    public function setDimensions(int $width, int $height): static
    {
        $this->width  = $width;
        $this->height = $height;

        return $this;
    }

    public function setSrc(string $src): static { $this->src = $src; return $this; }
    public function setAlt(string $alt): static { $this->alt = $alt; return $this; }

    public function setData(string $data, string $mimeType): static
    {
        $this->data = $data;
        $this->mimeType = $mimeType;

        return $this;
    }

    /* -------------------------------------------------------------
     | Thumbnail (dynamic – always reflects current image data)
     |------------------------------------------------------------- */

    /**
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    public function getThumbnail(
        int $maxWidth = ThumbnailGenerator::DEFAULT_WIDTH,
        int $maxHeight = ThumbnailGenerator::DEFAULT_HEIGHT,
        int $quality = ThumbnailGenerator::DEFAULT_QUALITY,
    ): ?array {
        return ThumbnailGenerator::generate($this, $maxWidth, $maxHeight, $quality);
    }

    public function getThumbnailDataUri(
        int $maxWidth = ThumbnailGenerator::DEFAULT_WIDTH,
        int $maxHeight = ThumbnailGenerator::DEFAULT_HEIGHT,
        int $quality = ThumbnailGenerator::DEFAULT_QUALITY,
    ): ?string {
        return ThumbnailGenerator::generateDataUri($this, $maxWidth, $maxHeight, $quality);
    }

    /* -------------------------------------------------------------
     | Persistence
     |------------------------------------------------------------- */

    public function saveTo(string $path): bool
    {
        if ($this->data === null) {
            return false;
        }

        return file_put_contents($path, $this->data) !== false;
    }

    public function jsonSerialize(): mixed
    {
        $result = [
            'type'     => 'image',
            'src'      => $this->src,
            'width'    => $this->width,
            'height'   => $this->height,
            'alt'      => $this->alt,
            'mimeType' => $this->mimeType,
            'dataSize' => $this->getDataSize(),
        ];

        if ($this->data !== null) {
            $result['dataUri'] = $this->getDataUri();
        }

        return $result;
    }
}
