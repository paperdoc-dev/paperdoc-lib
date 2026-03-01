<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\DocumentElementInterface;

class TableCell implements \JsonSerializable
{
    /** @var DocumentElementInterface[] */
    private array $elements = [];

    private int $colspan = 1;
    private int $rowspan = 1;

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(): static
    {
        return new static();
    }

    /* -------------------------------------------------------------
     | Elements
     |------------------------------------------------------------- */

    public function addElement(DocumentElementInterface $element): static
    {
        $this->elements[] = $element;

        return $this;
    }

    /** @return DocumentElementInterface[] */
    public function getElements(): array { return $this->elements; }

    /* -------------------------------------------------------------
     | Span
     |------------------------------------------------------------- */

    public function getColspan(): int { return $this->colspan; }
    public function getRowspan(): int { return $this->rowspan; }

    public function setColspan(int $v): static { $this->colspan = $v; return $this; }
    public function setRowspan(int $v): static { $this->rowspan = $v; return $this; }

    /* -------------------------------------------------------------
     | Helpers
     |------------------------------------------------------------- */

    public function getPlainText(): string
    {
        $parts = [];

        foreach ($this->elements as $el) {
            if ($el instanceof Paragraph) {
                $parts[] = $el->getPlainText();
            }
        }

        return implode(' ', $parts);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'text'     => $this->getPlainText(),
            'elements' => $this->elements,
            'colspan'  => $this->colspan,
            'rowspan'  => $this->rowspan,
        ];
    }
}
