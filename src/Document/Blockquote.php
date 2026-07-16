<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\BlockElementInterface;
use Paperdoc\Contracts\DocumentElementInterface;
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Document\Style\TextStyle;

/**
 * Quoted block, containing one or more nested block elements. Nested
 * Blockquotes are allowed (a quote inside a quote).
 */
final class Blockquote implements BlockElementInterface, \JsonSerializable
{
    /** @var DocumentElementInterface[] */
    private array $elements = [];

    public function __construct() {}

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(): static
    {
        return new static();
    }

    /* -------------------------------------------------------------
     | DocumentElementInterface
     |------------------------------------------------------------- */

    public function getType(): string { return 'blockquote'; }

    /* -------------------------------------------------------------
     | Elements
     |------------------------------------------------------------- */

    public function addElement(DocumentElementInterface $element): static
    {
        $this->elements[] = $element;

        return $this;
    }

    /**
     * Convenience: append a paragraph made of a single TextRun.
     * Returns the quote for chaining.
     */
    public function addText(string $text, ?TextStyle $style = null, ?TextLink $link = null): static
    {
        $paragraph = new Paragraph();
        $paragraph->addRun(new TextRun($text, $style, $link));

        return $this->addElement($paragraph);
    }

    /** @return DocumentElementInterface[] */
    public function getElements(): array { return $this->elements; }

    public function isEmpty(): bool { return $this->elements === []; }

    public function count(): int { return count($this->elements); }

    /* -------------------------------------------------------------
     | JsonSerializable
     |------------------------------------------------------------- */

    public function jsonSerialize(): mixed
    {
        return [
            'type'     => 'blockquote',
            'elements' => $this->elements,
        ];
    }
}
