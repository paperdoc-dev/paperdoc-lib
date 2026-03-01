<?php

declare(strict_types=1);

namespace Paperdoc\Renderers;

use Paperdoc\Contracts\DocumentInterface;
use Paperdoc\Document\{Paragraph, Table};
use Paperdoc\Support\Ole2\Ole2Writer;

/**
 * Renderer DOC natif (Word 97-2003 Binary File Format).
 *
 * Produit des fichiers .doc valides conformes à [MS-DOC]
 * en utilisant un FIB Word 97 minimal + piece table.
 * Pas de dépendance tierce.
 */
class DocRenderer extends AbstractRenderer
{
    private const FIB_CSW          = 14;  // FibRgW97 uint16 count
    private const FIB_CSLW         = 22;  // FibRgLw97 uint32 count
    private const FIB_CBRGFCLCB    = 67;  // minimum to include CLX at index 66
    private const FIB_CCP_TEXT_IDX = 3;   // ccpText position in FibRgLw97
    private const FIB_CLX_IDX      = 66;  // fcClx/lcbClx position in FibRgFcLcb

    public function getFormat(): string { return 'doc'; }

    public function render(DocumentInterface $document): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'paperdoc_doc_');

        try {
            $this->buildDoc($document, $tmp);

            return file_get_contents($tmp) ?: '';
        } finally {
            @unlink($tmp);
        }
    }

    public function save(DocumentInterface $document, string $filename): void
    {
        $this->ensureDirectoryWritable($filename);
        $this->buildDoc($document, $filename);
    }

    /* =============================================================
     | Builder
     |============================================================= */

    private function buildDoc(DocumentInterface $document, string $filename): void
    {
        $text = $this->collectText($document);
        $textBytes = mb_convert_encoding($text, 'Windows-1252', 'UTF-8');
        $ccpText = strlen($textBytes);

        $fibSize = $this->calculateFibSize();
        $textStart = $fibSize;

        $wordDocStream = $this->buildFib($ccpText, $fibSize) . $textBytes;
        $tableStream   = $this->buildClx($ccpText, $textStart);

        $writer = new Ole2Writer();
        $writer->addStream('WordDocument', $wordDocStream);
        $writer->addStream('0Table', $tableStream);

        file_put_contents($filename, $writer->build());
    }

    /* =============================================================
     | Text Collection
     |============================================================= */

    private function collectText(DocumentInterface $document): string
    {
        $parts = [];

        foreach ($document->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof Paragraph) {
                    $parts[] = $element->getPlainText();
                } elseif ($element instanceof Table) {
                    foreach ($element->getRows() as $row) {
                        $cells = [];
                        foreach ($row->getCells() as $cell) {
                            $cells[] = $cell->getPlainText();
                        }
                        $parts[] = implode("\t", $cells);
                    }
                }
            }
        }

        $text = implode("\r", $parts);

        if ($text !== '' && $text[-1] !== "\r") {
            $text .= "\r";
        }

        return $text;
    }

    /* =============================================================
     | FIB (File Information Block) — Word 97
     |============================================================= */

    private function calculateFibSize(): int
    {
        return 32                              // FibBase
             + 2 + self::FIB_CSW * 2           // csw + FibRgW97
             + 2 + self::FIB_CSLW * 4          // cslw + FibRgLw97
             + 2 + self::FIB_CBRGFCLCB * 8;    // cbRgFcLcb + pairs
    }

    private function buildFib(int $ccpText, int $fibSize): string
    {
        $fib = '';

        // FibBase (32 bytes)
        $fib .= pack('v', 0xA5EC);     // wIdent — Word magic
        $fib .= pack('v', 0x00C1);     // nFib — Word 97
        $fib .= pack('v', 0x0000);     // unused
        $fib .= pack('v', 0x0409);     // lid — English US
        $fib .= pack('v', 0x0000);     // pnNext
        // flags: fExtChar=1 (bit 12), fWhichTblStm=0 (bit 9) → use 0Table
        $fib .= pack('v', 0x1000);
        $fib .= pack('v', 0x00BF);     // nFibBack — Word 97 back-compat
        $fib .= pack('V', 0x00000000); // lKey
        $fib .= pack('C', 0);          // envr
        $fib .= pack('C', 0);          // flags2
        $fib .= str_repeat("\x00", 12); // reserved (to fill 32 bytes)

        // csw + FibRgW97 (14 uint16, all zeroed for minimal file)
        $fib .= pack('v', self::FIB_CSW);
        $fib .= str_repeat("\x00", self::FIB_CSW * 2);

        // cslw + FibRgLw97
        $fib .= pack('v', self::FIB_CSLW);
        $rgLw = str_repeat("\x00", self::FIB_CSLW * 4);
        // Set ccpText at index 3
        $rgLw = substr_replace($rgLw, pack('V', $ccpText), self::FIB_CCP_TEXT_IDX * 4, 4);
        $fib .= $rgLw;

        // cbRgFcLcb + FibRgFcLcb97
        $fib .= pack('v', self::FIB_CBRGFCLCB);
        $rgFcLcb = str_repeat("\x00", self::FIB_CBRGFCLCB * 8);
        // Set fcClx and lcbClx at index 66
        $clxSize = 21; // 1 + 4 + 8 + 8
        $offset66 = self::FIB_CLX_IDX * 8;
        $rgFcLcb = substr_replace($rgFcLcb, pack('V', 0), $offset66, 4);         // fcClx = 0 (start of table stream)
        $rgFcLcb = substr_replace($rgFcLcb, pack('V', $clxSize), $offset66 + 4, 4); // lcbClx
        $fib .= $rgFcLcb;

        return $fib;
    }

    /* =============================================================
     | CLX / Piece Table (in 0Table stream)
     |============================================================= */

    private function buildClx(int $ccpText, int $textStartInWordDoc): string
    {
        // Pcdt header
        $clx  = pack('C', 0x02);              // clxt = Pcdt
        $clx .= pack('V', 16);               // lcb: 8 (CPs) + 8 (PCD)

        // PlcPcd — CP array (n+1 entries for n=1 piece)
        $clx .= pack('V', 0);                // aCP[0]
        $clx .= pack('V', $ccpText);         // aCP[1]

        // PCD[0] (8 bytes)
        $clx .= pack('v', 0x0000);           // ABCr
        // fc: compressed (CP1252), bit 30 set, value = textStart * 2
        $fc = 0x40000000 | ($textStartInWordDoc * 2);
        $clx .= pack('V', $fc);
        $clx .= pack('v', 0x0000);           // prm

        return $clx;
    }
}
