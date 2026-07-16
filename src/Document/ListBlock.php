<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\BlockElementInterface;
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Document\Style\TextStyle;
use Paperdoc\Exceptions\InvalidDocumentException;

/**
 * Ordered or unordered list of items. Nested lists are represented by
 * adding a ListBlock as a child block of a ListItem — there is no
 * explicit "level" property on the list itself.
 *
 * ```
 * $list = ListBlock::bullet()
 *     ->addText('Buy groceries')
 *     ->addText('Call Alice');
 *
 * $ordered = ListBlock::ordered(start: 3)
 *     ->addText('Third step')
 *     ->addText('Fourth step');
 * ```
 */
final class ListBlock implements BlockElementInterface, \JsonSerializable
{
    public const STYLE_BULLET  = 'bullet';
    public const STYLE_ORDERED = 'ordered';

    /** @var ListItem[] */
    private array $items = [];

    public function __construct(
        private string $style = self::STYLE_BULLET,
        private int $start = 1,
    ) {
        $this->assertValidStyle($style);
    }

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(string $style = self::STYLE_BULLET, int $start = 1): static
    {
        return new static($style, $start);
    }

    public static function bullet(): static
    {
        return new static(self::STYLE_BULLET);
    }

    public static function ordered(int $start = 1): static
    {
        return new static(self::STYLE_ORDERED, $start);
    }

    /* -------------------------------------------------------------
     | DocumentElementInterface
     |------------------------------------------------------------- */

    public function getType(): string { return 'list'; }

    /* -------------------------------------------------------------
     | Style
     |------------------------------------------------------------- */

    public function getStyle(): string { return $this->style; }

    public function setStyle(string $style): static
    {
        $this->assertValidStyle($style);
        $this->style = $style;

        return $this;
    }

    public function isBullet(): bool  { return $this->style === self::STYLE_BULLET; }
    public function isOrdered(): bool { return $this->style === self::STYLE_ORDERED; }

    public function getStart(): int { return $this->start; }

    public function setStart(int $start): static
    {
        $this->start = $start;

        return $this;
    }

    /* -------------------------------------------------------------
     | Items
     |------------------------------------------------------------- */

    public function addItem(ListItem $item): static
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Convenience: add a one-line item from a string. Returns the newly
     * created ListItem so nested lists can be attached fluently.
     */
    public function addText(string $text, ?TextStyle $style = null, ?TextLink $link = null): ListItem
    {
        $item = ListItem::make($text, $style, $link);
        $this->items[] = $item;

        return $item;
    }

    /** @return ListItem[] */
    public function getItems(): array { return $this->items; }

    public function count(): int { return count($this->items); }

    public function isEmpty(): bool { return $this->items === []; }

    /* -------------------------------------------------------------
     | JsonSerializable
     |------------------------------------------------------------- */

    public function jsonSerialize(): mixed
    {
        $result = [
            'type'  => 'list',
            'style' => $this->style,
            'items' => $this->items,
        ];

        if ($this->style === self::STYLE_ORDERED && $this->start !== 1) {
            $result['start'] = $this->start;
        }

        return $result;
    }

    /* -------------------------------------------------------------
     | Internal
     |------------------------------------------------------------- */

    private function assertValidStyle(string $style): void
    {
        if ($style !== self::STYLE_BULLET && $style !== self::STYLE_ORDERED) {
            throw new InvalidDocumentException(sprintf(
                'Invalid list style "%s"; expected "%s" or "%s".',
                $style,
                self::STYLE_BULLET,
                self::STYLE_ORDERED,
            ));
        }
    }
}
