<?php

declare(strict_types=1);

namespace Pagina\Document;

class TableRow
{
    /** @var TableCell[] */
    private array $cells = [];

    private bool $isHeader = false;

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(): static
    {
        return new static();
    }

    /* -------------------------------------------------------------
     | Cells
     |------------------------------------------------------------- */

    public function addCell(TableCell $cell): static
    {
        $this->cells[] = $cell;

        return $this;
    }

    /** @return TableCell[] */
    public function getCells(): array { return $this->cells; }

    /* -------------------------------------------------------------
     | Header
     |------------------------------------------------------------- */

    public function isHeader(): bool { return $this->isHeader; }

    public function setHeader(bool $v = true): static
    {
        $this->isHeader = $v;

        return $this;
    }
}
