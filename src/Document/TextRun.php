<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Document\Style\TextStyle;
use Paperdoc\Document\Link\TextLink;

final class TextRun implements \JsonSerializable
{
    public function __construct(
        private string $text,
        private ?TextStyle $style = null,
        private ?TextLink $link = null,
        private ?Footnote $footnote = null,
    ) {}

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(string $text, ?TextStyle $style = null, ?TextLink $link = null, ?Footnote $footnote = null): static
    {
        return new static($text, $style, $link, $footnote);
    }

    /* -------------------------------------------------------------
     | Accessors
     |------------------------------------------------------------- */

    public function getText(): string        { return $this->text; }
    public function getStyle(): ?TextStyle   { return $this->style; }
    public function getLink(): ?TextLink     { return $this->link; }
    public function getFootnote(): ?Footnote { return $this->footnote; }

    public function setText(string $text): static      { $this->text = $text; return $this; }
    public function setStyle(TextStyle $style): static { $this->style = $style; return $this; }
    public function setLink(TextLink $link): static    { $this->link = $link; return $this; }
    public function setFootnote(?Footnote $footnote): static { $this->footnote = $footnote; return $this; }

    public function jsonSerialize(): mixed
    {
        return [
            'text'  => $this->text,
            'style' => $this->style,
            'link'  => $this->link,
            'footnote' => $this->footnote,
        ];
    }
}
