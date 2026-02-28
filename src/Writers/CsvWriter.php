<?php

declare(strict_types=1);

namespace Pagina\Writers;

use Pagina\Contracts\DocumentInterface;
use Pagina\Document\{Paragraph, Table};

/**
 * Writer CSV natif.
 *
 * Stratégie : extrait les données tabulaires du document.
 * Les paragraphes deviennent des lignes à une seule colonne,
 * les tableaux sont sérialisés ligne par ligne.
 */
class CsvWriter extends AbstractWriter
{
    private string $delimiter = ',';
    private string $enclosure = '"';
    private bool   $bom       = true;

    public function getFormat(): string { return 'csv'; }

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

    public function setBom(bool $bom): static
    {
        $this->bom = $bom;

        return $this;
    }

    /* -------------------------------------------------------------
     | Write
     |------------------------------------------------------------- */

    public function write(DocumentInterface $document, string $filename): void
    {
        $this->ensureDirectoryWritable($filename);

        $handle = fopen($filename, 'w');

        if ($handle === false) {
            throw new \RuntimeException("Impossible d'ouvrir le fichier : {$filename}");
        }

        if ($this->bom) {
            fwrite($handle, "\xEF\xBB\xBF");
        }

        foreach ($document->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof Table) {
                    foreach ($element->getRows() as $row) {
                        $cells = array_map(
                            fn ($cell) => $cell->getPlainText(),
                            $row->getCells()
                        );
                        fputcsv($handle, $cells, $this->delimiter, $this->enclosure);
                    }
                } elseif ($element instanceof Paragraph) {
                    $text = $element->getPlainText();

                    if (trim($text) !== '') {
                        fputcsv($handle, [$text], $this->delimiter, $this->enclosure);
                    }
                }
            }
        }

        fclose($handle);
    }
}
