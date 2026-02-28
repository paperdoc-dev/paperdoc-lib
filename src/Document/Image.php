<?php

declare(strict_types=1);

namespace Pagina\Document;

use Pagina\Contracts\DocumentElementInterface;

class Image implements DocumentElementInterface
{
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

    public function setDimensions(int $width, int $height): static
    {
        $this->width  = $width;
        $this->height = $height;

        return $this;
    }

    public function setSrc(string $src): static { $this->src = $src; return $this; }
    public function setAlt(string $alt): static { $this->alt = $alt; return $this; }
}
