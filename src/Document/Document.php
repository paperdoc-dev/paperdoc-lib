<?php

declare(strict_types=1);

namespace Pagina\Document;

use Pagina\Contracts\DocumentInterface;
use Pagina\Document\Style\TextStyle;

class Document implements DocumentInterface
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
     | Default Text Style
     |------------------------------------------------------------- */

    public function getDefaultTextStyle(): TextStyle { return $this->defaultTextStyle; }

    public function setDefaultTextStyle(TextStyle $style): static
    {
        $this->defaultTextStyle = $style;

        return $this;
    }
}
