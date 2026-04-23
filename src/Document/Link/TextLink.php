<?php

declare(strict_types=1);

namespace Paperdoc\Document\Link;

use Paperdoc\Contracts\LinkInterface;

class TextLink implements LinkInterface, \JsonSerializable
{
    private string $url = '';

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

    public function getUrl(): string { return $this->url; }

    /* -------------------------------------------------------------
     | Fluent Setters
     |------------------------------------------------------------- */

    public function setUrl(string $v): static { $this->url = $v; return $this; }

    /* -------------------------------------------------------------
     | Serialization
     |------------------------------------------------------------- */

    public function toArray(): array
    {
        return [
            'url' => $this->url,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

}
