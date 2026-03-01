<?php

declare(strict_types=1);

namespace Paperdoc\Parsers;

use Paperdoc\Contracts\{DocumentInterface, ParserInterface};
use Paperdoc\Document\{Document, Paragraph, Section, Table, TableCell, TableRow, TextRun};

/**
 * Parser CSV natif utilisant les fonctions fgetcsv de PHP.
 *
 * Stratégie : la première ligne est traitée comme en-tête,
 * le reste comme données tabulaires.
 */
class CsvParser extends AbstractParser implements ParserInterface
{
    private string $delimiter = ',';
    private string $enclosure = '"';
    private bool   $firstRowIsHeader = true;

    /* -------------------------------------------------------------
     | Configuration
     |------------------------------------------------------------- */

    public function setDelimiter(string $delimiter): static
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function setEnclosure(string $enclosure): static
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    public function setFirstRowIsHeader(bool $v): static
    {
        $this->firstRowIsHeader = $v;

        return $this;
    }

    /* -------------------------------------------------------------
     | ParserInterface
     |------------------------------------------------------------- */

    public function supports(string $extension): bool
    {
        return in_array(strtolower($extension), ['csv', 'tsv'], true);
    }

    public function parse(string $filename): DocumentInterface
    {
        $this->assertFileReadable($filename);

        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'tsv') {
            $this->delimiter = "\t";
        }

        $document = new Document('csv');
        $document->setTitle(pathinfo($filename, PATHINFO_FILENAME));

        $section = new Section('data');
        $table   = new Table();
        $isFirst = true;

        $handle = fopen($filename, 'r');

        if ($handle === false) {
            throw new \RuntimeException("Impossible de lire le fichier : {$filename}");
        }

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        while (($data = fgetcsv($handle, 0, $this->delimiter, $this->enclosure, '')) !== false) {
            $row = new TableRow();

            if ($isFirst && $this->firstRowIsHeader) {
                $row->setHeader();
                $isFirst = false;
            }

            foreach ($data as $cellValue) {
                $cell = new TableCell();
                $cell->addElement(
                    (new Paragraph())->addRun(new TextRun((string) $cellValue))
                );
                $row->addCell($cell);
            }

            $table->addRow($row);
        }

        fclose($handle);

        $section->addElement($table);
        $document->addSection($section);

        return $document;
    }
}
