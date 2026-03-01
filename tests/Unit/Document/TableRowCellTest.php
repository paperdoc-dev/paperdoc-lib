<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\TableRow;
use Paperdoc\Document\TableCell;
use Paperdoc\Document\Paragraph;
use Paperdoc\Document\TextRun;

class TableRowCellTest extends TestCase
{
    /* -------------------------------------------------------------
     | TableRow
     |------------------------------------------------------------- */

    public function test_row_make_factory(): void
    {
        $row = TableRow::make();

        $this->assertInstanceOf(TableRow::class, $row);
        $this->assertFalse($row->isHeader());
    }

    public function test_row_add_cell(): void
    {
        $row = new TableRow();
        $cell = new TableCell();

        $result = $row->addCell($cell);

        $this->assertSame($row, $result);
        $this->assertCount(1, $row->getCells());
    }

    public function test_row_set_header(): void
    {
        $row = new TableRow();

        $result = $row->setHeader(true);

        $this->assertSame($row, $result);
        $this->assertTrue($row->isHeader());
    }

    public function test_row_unset_header(): void
    {
        $row = new TableRow();
        $row->setHeader(true);
        $row->setHeader(false);

        $this->assertFalse($row->isHeader());
    }

    public function test_row_multiple_cells(): void
    {
        $row = new TableRow();
        $row->addCell(new TableCell());
        $row->addCell(new TableCell());
        $row->addCell(new TableCell());

        $this->assertCount(3, $row->getCells());
    }

    /* -------------------------------------------------------------
     | TableCell
     |------------------------------------------------------------- */

    public function test_cell_make_factory(): void
    {
        $cell = TableCell::make();

        $this->assertInstanceOf(TableCell::class, $cell);
    }

    public function test_cell_add_element(): void
    {
        $cell = new TableCell();
        $p = new Paragraph();
        $p->addRun(new TextRun('Contenu'));

        $result = $cell->addElement($p);

        $this->assertSame($cell, $result);
        $this->assertCount(1, $cell->getElements());
    }

    public function test_cell_default_span(): void
    {
        $cell = new TableCell();

        $this->assertSame(1, $cell->getColspan());
        $this->assertSame(1, $cell->getRowspan());
    }

    public function test_cell_set_colspan(): void
    {
        $cell = new TableCell();
        $result = $cell->setColspan(3);

        $this->assertSame($cell, $result);
        $this->assertSame(3, $cell->getColspan());
    }

    public function test_cell_set_rowspan(): void
    {
        $cell = new TableCell();
        $result = $cell->setRowspan(2);

        $this->assertSame($cell, $result);
        $this->assertSame(2, $cell->getRowspan());
    }

    public function test_cell_plain_text_empty(): void
    {
        $cell = new TableCell();

        $this->assertSame('', $cell->getPlainText());
    }

    public function test_cell_plain_text_single_paragraph(): void
    {
        $cell = new TableCell();
        $p = new Paragraph();
        $p->addRun(new TextRun('Cell value'));
        $cell->addElement($p);

        $this->assertSame('Cell value', $cell->getPlainText());
    }

    public function test_cell_plain_text_multiple_paragraphs(): void
    {
        $cell = new TableCell();
        $p1 = new Paragraph();
        $p1->addRun(new TextRun('First'));
        $p2 = new Paragraph();
        $p2->addRun(new TextRun('Second'));
        $cell->addElement($p1);
        $cell->addElement($p2);

        $this->assertSame('First Second', $cell->getPlainText());
    }
}
