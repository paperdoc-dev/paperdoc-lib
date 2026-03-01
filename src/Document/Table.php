<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\DocumentElementInterface;
use Paperdoc\Document\Style\TableStyle;

class Table implements DocumentElementInterface, \JsonSerializable
{
    /** @var TableRow[] */
    private array $rows = [];

    /** @var float[] Largeurs relatives des colonnes (en pourcentage) */
    private array $columnWidths = [];

    public function __construct(private ?TableStyle $style = null) {}

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(?TableStyle $style = null): static
    {
        return new static($style);
    }

    /* -------------------------------------------------------------
     | DocumentElementInterface
     |------------------------------------------------------------- */

    public function getType(): string { return 'table'; }

    /* -------------------------------------------------------------
     | Rows
     |------------------------------------------------------------- */

    public function addRow(TableRow $row): static
    {
        $this->rows[] = $row;

        return $this;
    }

    /** @return TableRow[] */
    public function getRows(): array { return $this->rows; }

    /**
     * @param string[] $cells
     */
    public function addRowFromArray(array $cells, bool $isHeader = false): TableRow
    {
        $row = new TableRow();
        $row->setHeader($isHeader);

        foreach ($cells as $text) {
            $cell = new TableCell();
            $cell->addElement(
                (new Paragraph())->addRun(new TextRun($text))
            );
            $row->addCell($cell);
        }

        $this->addRow($row);

        return $row;
    }

    /**
     * Raccourci : ajoute une ligne d'en-tête.
     *
     * @param string[] $headers
     */
    public function setHeaders(array $headers): static
    {
        $this->addRowFromArray($headers, isHeader: true);

        return $this;
    }

    /* -------------------------------------------------------------
     | Column Widths
     |------------------------------------------------------------- */

    /**
     * @param float[] $widths Pourcentages (ex: [30, 40, 30])
     */
    public function setColumnWidths(array $widths): static
    {
        $this->columnWidths = $widths;

        return $this;
    }

    /** @return float[] */
    public function getColumnWidths(): array { return $this->columnWidths; }

    /* -------------------------------------------------------------
     | Style
     |------------------------------------------------------------- */

    public function getStyle(): ?TableStyle { return $this->style; }

    public function setStyle(TableStyle $style): static
    {
        $this->style = $style;

        return $this;
    }

    /* -------------------------------------------------------------
     | Helpers
     |------------------------------------------------------------- */

    public function getColumnCount(): int
    {
        if (empty($this->rows)) {
            return 0;
        }

        return max(array_map(fn (TableRow $r) => count($r->getCells()), $this->rows));
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type'         => 'table',
            'rows'         => $this->rows,
            'columnWidths' => $this->columnWidths,
            'style'        => $this->style,
        ];
    }
}
