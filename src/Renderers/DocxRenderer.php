<?php

declare(strict_types=1);

namespace Paperdoc\Renderers;

use Paperdoc\Contracts\DocumentInterface;
use Paperdoc\Document\{PageBreak, Paragraph, Section, Table, TableCell, TableRow, TextRun};
use Paperdoc\Document\Style\{ParagraphStyle, TextStyle};
use Paperdoc\Enum\Alignment;

/**
 * Native DOCX renderer producing a valid Office Open XML
 * (WordprocessingML) package via ZipArchive — no third-party deps.
 *
 * Supports paragraphs (with run-level bold/italic/underline/color/font/size),
 * heading levels (via Heading1..Heading4 styles), simple tables with colspan,
 * and page breaks.
 */
class DocxRenderer extends AbstractRenderer
{
    private const NS_W = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    public function getFormat(): string { return 'docx'; }

    public function render(DocumentInterface $document): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'paperdoc_docx_');

        try {
            $this->buildDocx($document, $tmp);

            return file_get_contents($tmp) ?: '';
        } finally {
            @unlink($tmp);
        }
    }

    public function save(DocumentInterface $document, string $filename): void
    {
        $this->ensureDirectoryWritable($filename);
        $this->buildDocx($document, $filename);
    }

    private function buildDocx(DocumentInterface $document, string $filename): void
    {
        if (file_exists($filename)) {
            @unlink($filename);
        }

        $zip = new \ZipArchive();

        if ($zip->open($filename, \ZipArchive::CREATE) !== true) {
            throw new \RuntimeException("Unable to create DOCX file: {$filename}");
        }

        $zip->addFromString('[Content_Types].xml', $this->buildContentTypes());
        $zip->addFromString('_rels/.rels', $this->buildRootRels());
        $zip->addFromString('word/_rels/document.xml.rels', $this->buildDocumentRels());
        $zip->addFromString('word/styles.xml', $this->buildStyles());
        $zip->addFromString('word/document.xml', $this->buildDocument($document));
        $zip->addFromString('docProps/core.xml', $this->buildCoreProps($document));
        $zip->addFromString('docProps/app.xml', $this->buildAppProps());

        if ($zip->close() !== true) {
            throw new \RuntimeException("Unable to finalize DOCX file: {$filename}");
        }
    }

    /* =============================================================
     | word/document.xml
     |============================================================= */

    private function buildDocument(DocumentInterface $document): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<w:document xmlns:w="' . self::NS_W . '">';
        $xml .= '<w:body>';

        $sections = $document->getSections();

        if (empty($sections)) {
            $xml .= '<w:p/>';
        } else {
            foreach ($sections as $section) {
                $xml .= $this->renderSection($section);
            }
        }

        $xml .= $this->buildSectPr();
        $xml .= '</w:body></w:document>';

        return $xml;
    }

    private function renderSection(Section $section): string
    {
        $xml = '';

        foreach ($section->getElements() as $element) {
            $xml .= match (true) {
                $element instanceof Paragraph => $this->renderParagraph($element),
                $element instanceof Table     => $this->renderTable($element),
                $element instanceof PageBreak => '<w:p><w:r><w:br w:type="page"/></w:r></w:p>',
                default                       => '',
            };
        }

        return $xml;
    }

    private function renderParagraph(Paragraph $paragraph): string
    {
        $xml = '<w:p>';
        $pPr = $this->renderParagraphProperties($paragraph->getStyle());

        if ($pPr !== '') {
            $xml .= $pPr;
        }

        foreach ($paragraph->getRuns() as $run) {
            $xml .= $this->renderRun($run);
        }

        $xml .= '</w:p>';

        return $xml;
    }

    private function renderParagraphProperties(?ParagraphStyle $style): string
    {
        if ($style === null) {
            return '';
        }

        $parts = '';

        $heading = $style->getHeadingLevel();
        if ($heading !== null && $heading >= 1 && $heading <= 4) {
            $parts .= '<w:pStyle w:val="Heading' . $heading . '"/>';
        }

        $alignmentVal = match ($style->getAlignment()) {
            Alignment::CENTER  => 'center',
            Alignment::RIGHT   => 'right',
            Alignment::JUSTIFY => 'both',
            default            => null,
        };

        if ($alignmentVal !== null) {
            $parts .= '<w:jc w:val="' . $alignmentVal . '"/>';
        }

        $spacing = '';
        if ($style->getSpaceBefore() > 0) {
            $spacing .= ' w:before="' . (int) round($style->getSpaceBefore() * 20) . '"';
        }
        if ($style->getSpaceAfter() > 0) {
            $spacing .= ' w:after="' . (int) round($style->getSpaceAfter() * 20) . '"';
        }
        if ($style->getLineSpacing() > 0 && abs($style->getLineSpacing() - 1.0) > 0.001) {
            $spacing .= ' w:line="' . (int) round($style->getLineSpacing() * 240) . '" w:lineRule="auto"';
        }
        if ($spacing !== '') {
            $parts .= '<w:spacing' . $spacing . '/>';
        }

        return $parts === '' ? '' : '<w:pPr>' . $parts . '</w:pPr>';
    }

    private function renderRun(TextRun $run): string
    {
        $text = $run->getText();

        if ($text === '') {
            return '';
        }

        $xml = '<w:r>';
        $rPr = $this->renderRunProperties($run->getStyle());
        if ($rPr !== '') {
            $xml .= $rPr;
        }

        $parts = preg_split('/(\r\n|\r|\n)/', $text) ?: [$text];
        $first = true;
        foreach ($parts as $chunk) {
            if (! $first) {
                $xml .= '<w:br/>';
            }
            $first = false;

            if ($chunk === '') {
                continue;
            }

            $needsPreserve = $chunk !== trim($chunk, " \t");
            $space = $needsPreserve ? ' xml:space="preserve"' : '';
            $xml .= '<w:t' . $space . '>' . $this->escapeXml($chunk) . '</w:t>';
        }

        $xml .= '</w:r>';

        return $xml;
    }

    private function renderRunProperties(?TextStyle $style): string
    {
        if ($style === null) {
            return '';
        }

        $parts = '';

        $font = $style->getFontFamily();
        if ($font !== '' && $font !== 'Helvetica') {
            $escaped = $this->escapeXml($font);
            $parts .= '<w:rFonts w:ascii="' . $escaped . '" w:hAnsi="' . $escaped . '" w:cs="' . $escaped . '"/>';
        }

        if ($style->isBold()) {
            $parts .= '<w:b/><w:bCs/>';
        }

        if ($style->isItalic()) {
            $parts .= '<w:i/><w:iCs/>';
        }

        if ($style->isUnderline()) {
            $parts .= '<w:u w:val="single"/>';
        }

        $color = ltrim($style->getColor(), '#');
        if ($color !== '' && strtolower($color) !== '000000') {
            $parts .= '<w:color w:val="' . $this->escapeXml($color) . '"/>';
        }

        $size = $style->getFontSize();
        if ($size > 0 && abs($size - 12.0) > 0.001) {
            $halfPoints = (int) round($size * 2);
            $parts .= '<w:sz w:val="' . $halfPoints . '"/><w:szCs w:val="' . $halfPoints . '"/>';
        }

        return $parts === '' ? '' : '<w:rPr>' . $parts . '</w:rPr>';
    }

    /* =============================================================
     | Tables
     |============================================================= */

    private function renderTable(Table $table): string
    {
        $xml = '<w:tbl>';
        $xml .= '<w:tblPr>';
        $xml .= '<w:tblW w:w="5000" w:type="pct"/>';
        $xml .= '<w:tblBorders>';
        foreach (['top', 'left', 'bottom', 'right', 'insideH', 'insideV'] as $side) {
            $xml .= '<w:' . $side . ' w:val="single" w:sz="4" w:space="0" w:color="auto"/>';
        }
        $xml .= '</w:tblBorders>';
        $xml .= '</w:tblPr>';

        foreach ($table->getRows() as $row) {
            $xml .= $this->renderTableRow($row);
        }

        $xml .= '</w:tbl>';

        return $xml;
    }

    private function renderTableRow(TableRow $row): string
    {
        $xml = '<w:tr>';

        if ($row->isHeader()) {
            $xml .= '<w:trPr><w:tblHeader/></w:trPr>';
        }

        foreach ($row->getCells() as $cell) {
            $xml .= $this->renderTableCell($cell, $row->isHeader());
        }

        $xml .= '</w:tr>';

        return $xml;
    }

    private function renderTableCell(TableCell $cell, bool $headerRow): string
    {
        $xml = '<w:tc><w:tcPr>';

        if ($cell->getColspan() > 1) {
            $xml .= '<w:gridSpan w:val="' . $cell->getColspan() . '"/>';
        }

        $xml .= '</w:tcPr>';

        $hasContent = false;

        foreach ($cell->getElements() as $element) {
            if ($element instanceof Paragraph) {
                if ($headerRow) {
                    foreach ($element->getRuns() as $run) {
                        $runStyle = $run->getStyle() ?? TextStyle::make();
                        if (! $runStyle->isBold()) {
                            $run->setStyle(TextStyle::make()
                                ->setFontFamily($runStyle->getFontFamily())
                                ->setFontSize($runStyle->getFontSize())
                                ->setColor($runStyle->getColor())
                                ->setBold()
                                ->setItalic($runStyle->isItalic())
                                ->setUnderline($runStyle->isUnderline()));
                        }
                    }
                }
                $xml .= $this->renderParagraph($element);
                $hasContent = true;
            }
        }

        if (! $hasContent) {
            $xml .= '<w:p/>';
        }

        $xml .= '</w:tc>';

        return $xml;
    }

    /* =============================================================
     | Section Properties (page size, margins)
     |============================================================= */

    private function buildSectPr(): string
    {
        return '<w:sectPr>'
            . '<w:pgSz w:w="12240" w:h="15840"/>'
            . '<w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="720" w:footer="720" w:gutter="0"/>'
            . '</w:sectPr>';
    }

    /* =============================================================
     | Styles (headings)
     |============================================================= */

    private function buildStyles(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<w:styles xmlns:w="' . self::NS_W . '">';
        $xml .= '<w:docDefaults>'
              . '<w:rPrDefault><w:rPr>'
              . '<w:rFonts w:ascii="Calibri" w:hAnsi="Calibri" w:cs="Calibri"/>'
              . '<w:sz w:val="22"/><w:szCs w:val="22"/>'
              . '</w:rPr></w:rPrDefault>'
              . '<w:pPrDefault><w:pPr><w:spacing w:after="160" w:line="259" w:lineRule="auto"/></w:pPr></w:pPrDefault>'
              . '</w:docDefaults>';

        $xml .= '<w:style w:type="paragraph" w:default="1" w:styleId="Normal">'
              . '<w:name w:val="Normal"/><w:qFormat/>'
              . '</w:style>';

        $headings = [
            1 => ['size' => 32, 'color' => '2F5496'],
            2 => ['size' => 26, 'color' => '2F5496'],
            3 => ['size' => 24, 'color' => '1F3763'],
            4 => ['size' => 22, 'color' => '2F5496'],
        ];

        foreach ($headings as $level => $props) {
            $xml .= '<w:style w:type="paragraph" w:styleId="Heading' . $level . '">'
                  . '<w:name w:val="heading ' . $level . '"/>'
                  . '<w:basedOn w:val="Normal"/>'
                  . '<w:next w:val="Normal"/>'
                  . '<w:qFormat/>'
                  . '<w:pPr>'
                  . '<w:keepNext/><w:keepLines/>'
                  . '<w:spacing w:before="240" w:after="80"/>'
                  . '<w:outlineLvl w:val="' . ($level - 1) . '"/>'
                  . '</w:pPr>'
                  . '<w:rPr>'
                  . '<w:b/><w:bCs/>'
                  . '<w:color w:val="' . $props['color'] . '"/>'
                  . '<w:sz w:val="' . $props['size'] . '"/><w:szCs w:val="' . $props['size'] . '"/>'
                  . '</w:rPr>'
                  . '</w:style>';
        }

        $xml .= '</w:styles>';

        return $xml;
    }

    /* =============================================================
     | Package metadata (Content Types, relationships, core props)
     |============================================================= */

    private function buildContentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
            . '<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . '</Types>';
    }

    private function buildRootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';
    }

    private function buildDocumentRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private function buildCoreProps(DocumentInterface $document): string
    {
        $title = $this->escapeXml($document->getTitle());
        $author = $this->escapeXml($document->getMetadata()['author'] ?? 'Paperdoc');
        $created = gmdate('Y-m-d\TH:i:s\Z');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties"'
            . ' xmlns:dc="http://purl.org/dc/elements/1.1/"'
            . ' xmlns:dcterms="http://purl.org/dc/terms/"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:title>' . $title . '</dc:title>'
            . '<dc:creator>' . $author . '</dc:creator>'
            . '<cp:lastModifiedBy>' . $author . '</cp:lastModifiedBy>'
            . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $created . '</dcterms:created>'
            . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . $created . '</dcterms:modified>'
            . '</cp:coreProperties>';
    }

    private function buildAppProps(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"'
            . ' xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            . '<Application>Paperdoc</Application>'
            . '</Properties>';
    }

    /* =============================================================
     | Helpers
     |============================================================= */

    private function escapeXml(string $text): string
    {
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
