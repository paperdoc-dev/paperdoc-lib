<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\RendererInterface;
use Paperdoc\Document\{Document, Paragraph, Section, Table, TextRun};
use Paperdoc\Renderers\CsvRenderer;

class CsvRendererTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/paperdoc_csv_' . uniqid();
        mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->tmpDir . '/*'));
        @rmdir($this->tmpDir);
    }

    private function outputPath(string $name = 'test.csv'): string
    {
        return $this->tmpDir . '/' . $name;
    }

    public function test_implements_renderer_interface(): void
    {
        $this->assertInstanceOf(RendererInterface::class, new CsvRenderer());
    }

    public function test_format_is_csv(): void
    {
        $this->assertSame('csv', (new CsvRenderer())->getFormat());
    }

    public function test_saves_table_data(): void
    {
        $doc = Document::make('csv');
        $section = Section::make('data');
        $table = Table::make();
        $table->setHeaders(['Nom', 'Age']);
        $table->addRowFromArray(['Alice', '30']);
        $table->addRowFromArray(['Bob', '25']);
        $section->addElement($table);
        $doc->addSection($section);

        $renderer = new CsvRenderer();
        $renderer->save($doc, $this->outputPath());

        $this->assertFileExists($this->outputPath());
        $content = file_get_contents($this->outputPath());

        $bom = "\xEF\xBB\xBF";
        $this->assertStringStartsWith($bom, $content);

        $lines = explode("\n", trim(substr($content, 3)));
        $this->assertCount(3, $lines);
        $this->assertStringContainsString('Nom', $lines[0]);
        $this->assertStringContainsString('Alice', $lines[1]);
        $this->assertStringContainsString('Bob', $lines[2]);
    }

    public function test_render_returns_string(): void
    {
        $doc = Document::make('csv');
        $section = Section::make('data');
        $table = Table::make();
        $table->addRowFromArray(['A', 'B']);
        $section->addElement($table);
        $doc->addSection($section);

        $renderer = new CsvRenderer();
        $content = $renderer->render($doc);

        $this->assertIsString($content);
        $this->assertStringContainsString('A', $content);
        $this->assertStringContainsString('B', $content);
    }

    public function test_renders_without_bom(): void
    {
        $doc = Document::make('csv');
        $section = Section::make('data');
        $table = Table::make();
        $table->addRowFromArray(['A', 'B']);
        $section->addElement($table);
        $doc->addSection($section);

        $renderer = new CsvRenderer();
        $renderer->setBom(false);
        $renderer->save($doc, $this->outputPath());

        $content = file_get_contents($this->outputPath());
        $this->assertStringStartsNotWith("\xEF\xBB\xBF", $content);
    }

    public function test_renders_paragraphs_as_single_column(): void
    {
        $doc = Document::make('csv');
        $section = Section::make('text');
        $section->addText('First paragraph');
        $section->addText('Second paragraph');
        $doc->addSection($section);

        $renderer = new CsvRenderer();
        $renderer->setBom(false);
        $content = $renderer->render($doc);

        $this->assertStringContainsString('First paragraph', $content);
        $this->assertStringContainsString('Second paragraph', $content);
    }

    public function test_skips_empty_paragraphs(): void
    {
        $doc = Document::make('csv');
        $section = Section::make('data');
        $section->addText('');
        $section->addText('  ');
        $section->addText('Real content');
        $doc->addSection($section);

        $renderer = new CsvRenderer();
        $renderer->setBom(false);

        $content = trim($renderer->render($doc));
        $lines = array_filter(explode("\n", $content));
        $this->assertCount(1, $lines);
    }

    public function test_custom_delimiter(): void
    {
        $doc = Document::make('csv');
        $section = Section::make('data');
        $table = Table::make();
        $table->addRowFromArray(['A', 'B', 'C']);
        $section->addElement($table);
        $doc->addSection($section);

        $renderer = new CsvRenderer();
        $renderer->setDelimiter(';')->setBom(false);

        $content = $renderer->render($doc);
        $this->assertStringContainsString(';', $content);
    }

    public function test_handles_special_characters(): void
    {
        $doc = Document::make('csv');
        $section = Section::make('data');
        $table = Table::make();
        $table->addRowFromArray(['Value with, comma', 'Value with "quotes"']);
        $section->addElement($table);
        $doc->addSection($section);

        $renderer = new CsvRenderer();
        $renderer->setBom(false);

        $content = $renderer->render($doc);
        $this->assertStringContainsString('comma', $content);
        $this->assertStringContainsString('quotes', $content);
    }

    public function test_multiple_sections(): void
    {
        $doc = Document::make('csv');

        $s1 = Section::make('s1');
        $t1 = Table::make();
        $t1->addRowFromArray(['A1', 'B1']);
        $s1->addElement($t1);

        $s2 = Section::make('s2');
        $t2 = Table::make();
        $t2->addRowFromArray(['A2', 'B2']);
        $s2->addElement($t2);

        $doc->addSection($s1)->addSection($s2);

        $renderer = new CsvRenderer();
        $renderer->setBom(false);

        $content = $renderer->render($doc);
        $this->assertStringContainsString('A1', $content);
        $this->assertStringContainsString('A2', $content);
    }

    public function test_creates_directory_if_needed(): void
    {
        $doc = Document::make('csv');
        $section = Section::make('data');
        $table = Table::make();
        $table->addRowFromArray(['X']);
        $section->addElement($table);
        $doc->addSection($section);

        $path = $this->tmpDir . '/sub/output.csv';
        $renderer = new CsvRenderer();
        $renderer->save($doc, $path);

        $this->assertFileExists($path);

        unlink($path);
        rmdir($this->tmpDir . '/sub');
    }
}
