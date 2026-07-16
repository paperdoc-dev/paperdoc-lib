<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\BlockElementInterface;

final class TableOfContents implements BlockElementInterface, \JsonSerializable
{
    public function __construct(
        private int $maxLevel = 3,
        private string $title = 'Table of Contents',
    ) {
        $this->maxLevel = max(1, min(6, $maxLevel));
    }

    public static function make(int $maxLevel = 3, string $title = 'Table of Contents'): static
    {
        return new static($maxLevel, $title);
    }

    public function getType(): string { return 'toc'; }

    public function getMaxLevel(): int { return $this->maxLevel; }

    public function setMaxLevel(int $level): static
    {
        $this->maxLevel = max(1, min(6, $level));

        return $this;
    }

    public function getTitle(): string { return $this->title; }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type'     => $this->getType(),
            'maxLevel' => $this->maxLevel,
            'title'    => $this->title,
        ];
    }
}
