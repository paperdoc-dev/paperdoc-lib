<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Parsers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\ParserInterface;
use Paperdoc\Document\Table;
use Paperdoc\Parsers\CsvParser;

class CsvParserTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/paperdoc_csv_parse_' . uniqid();
        mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->tmpDir . '/*'));
        @rmdir($this->tmpDir);
    }

    private function writeCsv(string $content, string $name = 'test.csv', bool $bom = false): string
    {
        $path = $this->tmpDir . '/' . $name;
        $data = $bom ? "\xEF\xBB\xBF" . $content : $content;
        file_put_contents($path, $data);

        return $path;
    }

    public function test_implements_parser_interface(): void
    {
        $this->assertInstanceOf(ParserInterface::class, new CsvParser());
    }

    public function test_supports_csv(): void
    {
        $parser = new CsvParser();

        $this->assertTrue($parser->supports('csv'));
        $this->assertTrue($parser->supports('CSV'));
        $this->assertTrue($parser->supports('tsv'));
        $this->assertFalse($parser->supports('html'));
        $this->assertFalse($parser->supports('pdf'));
    }

    public function test_parse_simple_csv(): void
    {
        $path = $this->writeCsv("Nom,Age\nAlice,30\nBob,25\n");
        $doc = (new CsvParser())->parse($path);

        $this->assertSame('csv', $doc->getFormat());
        $this->assertCount(1, $doc->getSections());

        $elements = $doc->getSections()[0]->getElements();
        $tables = array_values(array_filter($elements, fn ($e) => $e instanceof Table));
        $this->assertCount(1, $tables);

        $table = $tables[0];
        $rows = $table->getRows();
        $this->assertCount(3, $rows);
    }

    public function test_first_row_is_header(): void
    {
        $path = $this->writeCsv("Header1,Header2\nVal1,Val2\n");
        $doc = (new CsvParser())->parse($path);

        $table = $this->getFirstTable($doc);
        $rows = $table->getRows();

        $this->assertTrue($rows[0]->isHeader());
        $this->assertFalse($rows[1]->isHeader());
    }

    public function test_first_row_not_header_when_disabled(): void
    {
        $path = $this->writeCsv("A,B\nC,D\n");
        $parser = new CsvParser();
        $parser->setFirstRowIsHeader(false);

        $doc = $parser->parse($path);
        $table = $this->getFirstTable($doc);

        $this->assertFalse($table->getRows()[0]->isHeader());
        $this->assertFalse($table->getRows()[1]->isHeader());
    }

    public function test_cell_values(): void
    {
        $path = $this->writeCsv("Nom,Valeur\nCA,120000\n");
        $doc = (new CsvParser())->parse($path);
        $table = $this->getFirstTable($doc);

        $headerCells = $table->getRows()[0]->getCells();
        $this->assertSame('Nom', $headerCells[0]->getPlainText());
        $this->assertSame('Valeur', $headerCells[1]->getPlainText());

        $dataCells = $table->getRows()[1]->getCells();
        $this->assertSame('CA', $dataCells[0]->getPlainText());
        $this->assertSame('120000', $dataCells[1]->getPlainText());
    }

    public function test_handles_bom(): void
    {
        $path = $this->writeCsv("Name,Value\nTest,123\n", bom: true);
        $doc = (new CsvParser())->parse($path);
        $table = $this->getFirstTable($doc);

        $this->assertSame('Name', $table->getRows()[0]->getCells()[0]->getPlainText());
    }

    public function test_handles_quoted_values(): void
    {
        $path = $this->writeCsv("Name,Description\n\"Alice, Bob\",\"He said \"\"hi\"\"\"\n");
        $doc = (new CsvParser())->parse($path);
        $table = $this->getFirstTable($doc);

        $dataCells = $table->getRows()[1]->getCells();
        $this->assertSame('Alice, Bob', $dataCells[0]->getPlainText());
    }

    public function test_custom_delimiter(): void
    {
        $path = $this->writeCsv("A;B;C\n1;2;3\n");
        $parser = new CsvParser();
        $parser->setDelimiter(';');

        $doc = $parser->parse($path);
        $table = $this->getFirstTable($doc);

        $this->assertCount(3, $table->getRows()[0]->getCells());
    }

    public function test_tsv_auto_detection(): void
    {
        $path = $this->writeCsv("A\tB\tC\n1\t2\t3\n", 'test.tsv');
        $doc = (new CsvParser())->parse($path);
        $table = $this->getFirstTable($doc);

        $this->assertCount(3, $table->getRows()[0]->getCells());
    }

    public function test_title_from_filename(): void
    {
        $path = $this->writeCsv("A\nB\n", 'ventes_2026.csv');
        $doc = (new CsvParser())->parse($path);

        $this->assertSame('ventes_2026', $doc->getTitle());
    }

    public function test_nonexistent_file_throws(): void
    {
        $this->expectException(\RuntimeException::class);

        (new CsvParser())->parse('/nonexistent/file.csv');
    }

    public function test_empty_csv(): void
    {
        $path = $this->writeCsv('');
        $doc = (new CsvParser())->parse($path);

        $table = $this->getFirstTable($doc);
        $this->assertEmpty($table->getRows());
    }

    public function test_single_column(): void
    {
        $path = $this->writeCsv("Header\nValue1\nValue2\nValue3\n");
        $doc = (new CsvParser())->parse($path);
        $table = $this->getFirstTable($doc);

        $this->assertCount(4, $table->getRows());
        $this->assertCount(1, $table->getRows()[0]->getCells());
    }

    public function test_many_columns(): void
    {
        $path = $this->writeCsv("A,B,C,D,E,F,G,H\n1,2,3,4,5,6,7,8\n");
        $doc = (new CsvParser())->parse($path);
        $table = $this->getFirstTable($doc);

        $this->assertCount(8, $table->getRows()[0]->getCells());
    }

    private function getFirstTable($doc): Table
    {
        $elements = $doc->getSections()[0]->getElements();
        $tables = array_values(array_filter($elements, fn ($e) => $e instanceof Table));

        return $tables[0];
    }
}
