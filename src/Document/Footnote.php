<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Factory\DocumentHydrator;

final class Footnote implements \JsonSerializable
{
    public function __construct(private string $text) {}

    public static function make(string $text): static
    {
        return new static($text);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new static(DocumentHydrator::asString($data['text'] ?? null));
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'footnote',
            'text' => $this->text,
        ];
    }
}
