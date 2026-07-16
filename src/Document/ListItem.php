<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\BlockElementInterface;
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Document\Style\TextStyle;

/**
 * Single entry in a ListBlock. An item carries:
 *
 *  - a `runs[]` array — the item label (one or more styled TextRuns)
 *  - a `blocks[]` array — optional block children (typically a nested
 *    ListBlock, but also additional paragraphs, code blocks, etc.)
 *
 * ListItem is *not* a BlockElementInterface: it is always owned by a
 * parent ListBlock. The blocks stored *inside* an item must, however,
 * be block elements.
 */
final class ListItem implements \JsonSerializable
{
    /** @var TextRun[] */
    private array $runs = [];

    /** @var BlockElementInterface[] */
    private array $blocks = [];

    public function __construct() {}

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(string $text = '', ?TextStyle $style = null, ?TextLink $link = null): static
    {
        $item = new static();

        if ($text !== '' || $style !== null || $link !== null) {
            $item->addRun(new TextRun($text, $style, $link));
        }

        return $item;
    }

    /* -------------------------------------------------------------
     | Runs (item label)
     |------------------------------------------------------------- */

    public function addRun(TextRun $run): static
    {
        $this->runs[] = $run;

        return $this;
    }

    public function addText(string $text, ?TextStyle $style = null, ?TextLink $link = null): static
    {
        return $this->addRun(new TextRun($text, $style, $link));
    }

    /** @return TextRun[] */
    public function getRuns(): array { return $this->runs; }

    public function getPlainText(): string
    {
        return implode('', array_map(fn (TextRun $r) => $r->getText(), $this->runs));
    }

    /* -------------------------------------------------------------
     | Child blocks (nested list, sub-paragraph…)
     |------------------------------------------------------------- */

    public function addBlock(BlockElementInterface $block): static
    {
        $this->blocks[] = $block;

        return $this;
    }

    public function addList(ListBlock $list): static
    {
        return $this->addBlock($list);
    }

    /** @return BlockElementInterface[] */
    public function getBlocks(): array { return $this->blocks; }

    public function hasChildren(): bool { return $this->blocks !== []; }

    /* -------------------------------------------------------------
     | JsonSerializable
     |------------------------------------------------------------- */

    public function jsonSerialize(): mixed
    {
        $result = [
            'text' => $this->getPlainText(),
            'runs' => $this->runs,
        ];

        if ($this->blocks !== []) {
            $result['blocks'] = $this->blocks;
        }

        return $result;
    }
}
