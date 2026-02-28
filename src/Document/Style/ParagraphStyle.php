<?php

declare(strict_types=1);

namespace Pagina\Document\Style;

use Pagina\Contracts\StyleInterface;
use Pagina\Enum\Alignment;

class ParagraphStyle implements StyleInterface
{
    private Alignment $alignment   = Alignment::LEFT;
    private float     $spaceBefore = 0.0;
    private float     $spaceAfter  = 6.0;
    private float     $lineSpacing = 1.15;

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(): static
    {
        return new static();
    }

    /* -------------------------------------------------------------
     | Getters
     |------------------------------------------------------------- */

    public function getAlignment(): Alignment { return $this->alignment; }
    public function getSpaceBefore(): float   { return $this->spaceBefore; }
    public function getSpaceAfter(): float    { return $this->spaceAfter; }
    public function getLineSpacing(): float   { return $this->lineSpacing; }

    /* -------------------------------------------------------------
     | Fluent Setters
     |------------------------------------------------------------- */

    public function setAlignment(Alignment $v): static  { $this->alignment = $v; return $this; }
    public function setSpaceBefore(float $v): static    { $this->spaceBefore = $v; return $this; }
    public function setSpaceAfter(float $v): static     { $this->spaceAfter = $v; return $this; }
    public function setLineSpacing(float $v): static    { $this->lineSpacing = $v; return $this; }

    /* -------------------------------------------------------------
     | Serialization
     |------------------------------------------------------------- */

    public function toArray(): array
    {
        return [
            'alignment'   => $this->alignment->value,
            'spaceBefore' => $this->spaceBefore,
            'spaceAfter'  => $this->spaceAfter,
            'lineSpacing' => $this->lineSpacing,
        ];
    }
}
