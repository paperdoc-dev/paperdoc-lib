<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Document\Style\TextStyle;
use Paperdoc\Document\Link\TextLink;

class TextRun implements \JsonSerializable
{
    public function __construct(
        private string $text,
        private ?TextStyle $style = null,
        private ?TextLink $link = null,
    ) {}

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(string $text, ?TextStyle $style = null, ?TextLink $link = null): static
    {
        return new static($text, $style, $link);
    }

    /* -------------------------------------------------------------
     | Accessors
     |------------------------------------------------------------- */

    public function getText(): string        { return $this->text; }
    public function getStyle(): ?TextStyle   { return $this->style; }
    public function getLink(): ?TextLink     { return $this->link; }

    public function setText(string $text): static      { $this->text = $text; return $this; }
    public function setStyle(TextStyle $style): static { $this->style = $style; return $this; }
    public function setLink(TextLink $link): static    { $this->link = $link; return $this; }

    public function jsonSerialize(): mixed
    {
        return [
            'text'  => $this->text,
            'style' => $this->style,
            'link'  => $this->link,
        ];
    }
}
