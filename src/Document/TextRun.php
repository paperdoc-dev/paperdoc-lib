<?php

declare(strict_types=1);

namespace Pagina\Document;

use Pagina\Document\Style\TextStyle;

class TextRun
{
    public function __construct(
        private string $text,
        private ?TextStyle $style = null,
    ) {}

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(string $text, ?TextStyle $style = null): static
    {
        return new static($text, $style);
    }

    /* -------------------------------------------------------------
     | Accessors
     |------------------------------------------------------------- */

    public function getText(): string        { return $this->text; }
    public function getStyle(): ?TextStyle   { return $this->style; }

    public function setText(string $text): static      { $this->text = $text; return $this; }
    public function setStyle(TextStyle $style): static { $this->style = $style; return $this; }
}
