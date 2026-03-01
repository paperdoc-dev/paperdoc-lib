<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\DocumentElementInterface;
use Paperdoc\Document\Table;
use Paperdoc\Document\TableRow;
use Paperdoc\Document\TableCell;
use Paperdoc\Document\Paragraph;
use Paperdoc\Document\TextRun;
use Paperdoc\Document\Style\TableStyle;
use Paperdoc\Enum\BorderStyle;

class TableTest extends TestCase
{
    public function test_implements_element_interface(): void
    {
        $this->assertInstanceOf(DocumentElementInterface::class, new Table());
    }

    public function test_type_is_table(): void
    {
        $this->assertSame('table', (new Table())->getType());
    }

    public function test_make_factory(): void
    {
        $table = Table::make();

        $this->assertInstanceOf(Table::class, $table);
        $this->assertNull($table->getStyle());
    }

    public function test_make_factory_with_style(): void
    {
        $style = TableStyle::make()->setCellPadding(8.0);
        $table = Table::make($style);

        $this->assertSame(8.0, $table->getStyle()->getCellPadding());
    }

    public function test_add_row(): void
    {
        $table = new Table();
        $row = new TableRow();

        $result = $table->addRow($row);

        $this->assertSame($table, $result);
        $this->assertCount(1, $table->getRows());
    }

    public function test_add_row_from_array(): void
    {
        $table = new Table();
        $row = $table->addRowFromArray(['A', 'B', 'C']);

        $this->assertInstanceOf(TableRow::class, $row);
        $this->assertCount(1, $table->getRows());
        $this->assertCount(3, $row->getCells());
        $this->assertFalse($row->isHeader());
    }

    public function test_add_row_from_array_as_header(): void
    {
        $table = new Table();
        $row = $table->addRowFromArray(['Col1', 'Col2'], isHeader: true);

        $this->assertTrue($row->isHeader());
    }

    public function test_set_headers(): void
    {
        $table = new Table();
        $table->setHeaders(['Nom', 'Valeur', 'Tendance']);

        $rows = $table->getRows();
        $this->assertCount(1, $rows);
        $this->assertTrue($rows[0]->isHeader());
        $this->assertCount(3, $rows[0]->getCells());
    }

    public function test_full_table_with_headers_and_data(): void
    {
        $table = new Table();
        $table->setHeaders(['Métrique', 'Valeur']);
        $table->addRowFromArray(['CA', '120k']);
        $table->addRowFromArray(['Clients', '34']);

        $this->assertCount(3, $table->getRows());
        $this->assertTrue($table->getRows()[0]->isHeader());
        $this->assertFalse($table->getRows()[1]->isHeader());
        $this->assertFalse($table->getRows()[2]->isHeader());
    }

    public function test_get_column_count_empty(): void
    {
        $table = new Table();

        $this->assertSame(0, $table->getColumnCount());
    }

    public function test_get_column_count(): void
    {
        $table = new Table();
        $table->addRowFromArray(['A', 'B', 'C']);
        $table->addRowFromArray(['1', '2', '3']);

        $this->assertSame(3, $table->getColumnCount());
    }

    public function test_get_column_count_uneven_rows(): void
    {
        $table = new Table();
        $table->addRowFromArray(['A', 'B']);
        $table->addRowFromArray(['1', '2', '3', '4']);

        $this->assertSame(4, $table->getColumnCount());
    }

    public function test_set_column_widths(): void
    {
        $table = new Table();
        $result = $table->setColumnWidths([30, 40, 30]);

        $this->assertSame($table, $result);
        $this->assertEquals([30, 40, 30], $table->getColumnWidths());
    }

    public function test_default_column_widths_empty(): void
    {
        $table = new Table();

        $this->assertEmpty($table->getColumnWidths());
    }

    public function test_set_style(): void
    {
        $table = new Table();
        $style = TableStyle::make()->setBorderStyle(BorderStyle::DASHED);

        $result = $table->setStyle($style);

        $this->assertSame($table, $result);
        $this->assertSame(BorderStyle::DASHED, $table->getStyle()->getBorderStyle());
    }

    public function test_cell_plain_text(): void
    {
        $table = new Table();
        $table->addRowFromArray(['Hello World']);

        $cell = $table->getRows()[0]->getCells()[0];
        $this->assertSame('Hello World', $cell->getPlainText());
    }
}
