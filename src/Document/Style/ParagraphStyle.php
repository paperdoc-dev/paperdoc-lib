<?php

declare(strict_types=1);

namespace Paperdoc\Document\Style;

use Paperdoc\Contracts\StyleInterface;
use Paperdoc\Enum\Alignment;

final class ParagraphStyle implements StyleInterface, \JsonSerializable
{
    private Alignment $alignment       = Alignment::LEFT;
    private float     $spaceBefore     = 0.0;
    private float     $spaceAfter      = 6.0;
    private float     $lineSpacing     = 1.15;
    private ?int      $headingLevel    = null;

    /**
     * First-line indent in points (since v0.8.0). Mirrors the CSS
     * `text-indent` property: only the first line of the paragraph
     * starts further to the right by this much; subsequent (wrapped)
     * lines start at the normal X. A negative value produces a
     * "hanging indent" (first line jutting out to the left of the
     * block) — a typographic device used in lists or dictionary
     * entries. Default: 0pt (no indent).
     */
    private float     $firstLineIndent = 0.0;

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

    public function getAlignment(): Alignment   { return $this->alignment; }
    public function getSpaceBefore(): float     { return $this->spaceBefore; }
    public function getSpaceAfter(): float      { return $this->spaceAfter; }
    public function getLineSpacing(): float     { return $this->lineSpacing; }
    public function getHeadingLevel(): ?int     { return $this->headingLevel; }
    public function getFirstLineIndent(): float { return $this->firstLineIndent; }

    /* -------------------------------------------------------------
     | Fluent Setters
     |------------------------------------------------------------- */

    public function setAlignment(Alignment $v): static    { $this->alignment = $v; return $this; }
    public function setSpaceBefore(float $v): static      { $this->spaceBefore = $v; return $this; }
    public function setSpaceAfter(float $v): static       { $this->spaceAfter = $v; return $this; }
    public function setLineSpacing(float $v): static      { $this->lineSpacing = $v; return $this; }
    public function setHeadingLevel(?int $v): static      { $this->headingLevel = $v; return $this; }
    public function setFirstLineIndent(float $v): static  { $this->firstLineIndent = $v; return $this; }

    /* -------------------------------------------------------------
     | Serialization
     |------------------------------------------------------------- */

    public function toArray(): array
    {
        return [
            'alignment'       => $this->alignment->value,
            'spaceBefore'     => $this->spaceBefore,
            'spaceAfter'      => $this->spaceAfter,
            'lineSpacing'     => $this->lineSpacing,
            'headingLevel'    => $this->headingLevel,
            'firstLineIndent' => $this->firstLineIndent,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
