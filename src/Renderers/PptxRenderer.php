<?php

declare(strict_types=1);

namespace Paperdoc\Renderers;

use Paperdoc\Contracts\DocumentInterface;
use Paperdoc\Document\{Image, PageBreak, Paragraph, Section, Table, TextRun};

/**
 * Renderer PPTX natif utilisant ZipArchive + XML.
 *
 * Génère des fichiers Office Open XML (PresentationML) valides
 * sans aucune dépendance tierce. Chaque section devient une slide.
 */
class PptxRenderer extends AbstractRenderer
{
    private const SLIDE_WIDTH  = 9144000; // 10" in EMUs
    private const SLIDE_HEIGHT = 6858000; // 7.5" in EMUs
    private const MARGIN       = 457200;  // 0.5" in EMUs

    /** @var array{name: string, data: string, mimeType: string}[] */
    private array $mediaFiles = [];

    public function getFormat(): string { return 'pptx'; }

    public function render(DocumentInterface $document): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'paperdoc_pptx_');

        try {
            $this->buildPptx($document, $tmp);

            return file_get_contents($tmp) ?: '';
        } finally {
            @unlink($tmp);
        }
    }

    public function save(DocumentInterface $document, string $filename): void
    {
        $this->ensureDirectoryWritable($filename);
        $this->buildPptx($document, $filename);
    }

    private function buildPptx(DocumentInterface $document, string $filename): void
    {
        $this->mediaFiles = [];

        $zip = new \ZipArchive();

        if ($zip->open($filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Impossible de créer le fichier PPTX : {$filename}");
        }

        $sections = $document->getSections();

        if (empty($sections)) {
            $sections = [new Section('slide-1')];
        }

        $slideCount = count($sections);

        $zip->addFromString('[Content_Types].xml', $this->buildContentTypes($slideCount));
        $zip->addFromString('_rels/.rels', $this->buildRootRels());
        $zip->addFromString('ppt/presentation.xml', $this->buildPresentation($slideCount));
        $zip->addFromString('ppt/_rels/presentation.xml.rels', $this->buildPresentationRels($slideCount));
        $zip->addFromString('ppt/slideMasters/slideMaster1.xml', $this->buildSlideMaster());
        $zip->addFromString('ppt/slideLayouts/slideLayout1.xml', $this->buildSlideLayout());
        $zip->addFromString('ppt/slideMasters/_rels/slideMaster1.xml.rels', $this->buildSlideMasterRels());
        $zip->addFromString('ppt/slideLayouts/_rels/slideLayout1.xml.rels', $this->buildSlideLayoutRels());
        $zip->addFromString('ppt/theme/theme1.xml', $this->buildTheme());
        $zip->addFromString('docProps/core.xml', $this->buildCoreMeta($document));

        foreach ($sections as $i => $section) {
            $slideNum = $i + 1;
            $slideXml = $this->buildSlide($section, $document);
            $zip->addFromString("ppt/slides/slide{$slideNum}.xml", $slideXml);
            $zip->addFromString("ppt/slides/_rels/slide{$slideNum}.xml.rels", $this->buildSlideRels($slideNum));
        }

        foreach ($this->mediaFiles as $media) {
            $zip->addFromString('ppt/media/' . $media['name'], $media['data']);
        }

        $zip->close();
        $this->mediaFiles = [];
    }

    /* =============================================================
     | Slide Building
     |============================================================= */

    private function buildSlide(Section $section, DocumentInterface $document): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<p:sld xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" '
              . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" '
              . 'xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main">';
        $xml .= '<p:cSld><p:spTree>';
        $xml .= '<p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr>';
        $xml .= '<p:grpSpPr/>';

        $textParts = [];
        $tableParts = [];

        foreach ($section->getElements() as $element) {
            if ($element instanceof Paragraph) {
                $textParts[] = $this->renderParagraphXml($element);
            } elseif ($element instanceof Table) {
                $tableParts[] = $this->renderTableXml($element);
            }
        }

        if (! empty($textParts)) {
            $xml .= $this->wrapInTextBox(implode('', $textParts), 2);
        }

        if (! empty($tableParts)) {
            $xml .= $this->wrapInGraphicFrame(implode('', $tableParts), 3);
        }

        $xml .= '</p:spTree></p:cSld>';
        $xml .= '</p:sld>';

        return $xml;
    }

    private function renderParagraphXml(Paragraph $paragraph): string
    {
        $xml = '<a:p>';

        $style = $paragraph->getStyle();
        $headingLevel = $style?->getHeadingLevel();

        if ($headingLevel !== null) {
            $xml .= '<a:pPr lvl="0"/>';
        }

        foreach ($paragraph->getRuns() as $run) {
            $xml .= $this->renderRunXml($run, $headingLevel);
        }

        $xml .= '</a:p>';

        return $xml;
    }

    private function renderRunXml(TextRun $run, ?int $headingLevel = null): string
    {
        $style = $run->getStyle();
        $text = $this->escapeXml($run->getText());

        $fontSize = 1800;
        $bold = false;
        $italic = false;
        $underline = false;

        if ($headingLevel !== null) {
            $fontSize = match ($headingLevel) {
                1 => 3200,
                2 => 2600,
                3 => 2200,
                default => 2000,
            };
            $bold = true;
        }

        if ($style !== null) {
            $bold = $bold || $style->isBold();
            $italic = $style->isItalic();
            $underline = $style->isUnderline();

            if ($style->getFontSize() > 0) {
                $fontSize = (int) ($style->getFontSize() * 100);
            }
        }

        $rPr = '<a:rPr lang="fr-FR" dirty="0"';
        if ($bold) {
            $rPr .= ' b="1"';
        }
        if ($italic) {
            $rPr .= ' i="1"';
        }
        if ($underline) {
            $rPr .= ' u="sng"';
        }
        $rPr .= " sz=\"{$fontSize}\"";
        $rPr .= '/>';

        return "<a:r>{$rPr}<a:t>{$text}</a:t></a:r>";
    }

    private function renderTableXml(Table $table): string
    {
        $rows = $table->getRows();

        if (empty($rows)) {
            return '';
        }

        $colCount = $table->getColumnCount();
        $colW = (int) ((self::SLIDE_WIDTH - 2 * self::MARGIN) / max(1, $colCount));
        $rowH = 370840;

        $xml = '<a:tbl>';
        $xml .= '<a:tblPr firstRow="1" bandRow="1"/>';
        $xml .= '<a:tblGrid>';

        for ($c = 0; $c < $colCount; $c++) {
            $xml .= "<a:gridCol w=\"{$colW}\"/>";
        }

        $xml .= '</a:tblGrid>';

        foreach ($rows as $row) {
            $xml .= "<a:tr h=\"{$rowH}\">";

            foreach ($row->getCells() as $cell) {
                $text = $this->escapeXml($cell->getPlainText());

                $xml .= '<a:tc>';
                $xml .= '<a:txBody><a:bodyPr/><a:p><a:r><a:rPr lang="fr-FR" dirty="0" sz="1400"/>';
                $xml .= "<a:t>{$text}</a:t></a:r></a:p></a:txBody>";
                $xml .= '<a:tcPr/>';
                $xml .= '</a:tc>';
            }

            $xml .= '</a:tr>';
        }

        $xml .= '</a:tbl>';

        return $xml;
    }

    private function wrapInTextBox(string $bodyContent, int $shapeId): string
    {
        $x = self::MARGIN;
        $y = self::MARGIN;
        $cx = self::SLIDE_WIDTH - 2 * self::MARGIN;
        $cy = self::SLIDE_HEIGHT - 2 * self::MARGIN;

        return '<p:sp>'
            . "<p:nvSpPr><p:cNvPr id=\"{$shapeId}\" name=\"TextBox\"/><p:cNvSpPr txBox=\"1\"/><p:nvPr/></p:nvSpPr>"
            . "<p:spPr><a:xfrm><a:off x=\"{$x}\" y=\"{$y}\"/><a:ext cx=\"{$cx}\" cy=\"{$cy}\"/></a:xfrm>"
            . '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom></p:spPr>'
            . "<p:txBody><a:bodyPr wrap=\"square\" rtlCol=\"0\"/><a:lstStyle/>{$bodyContent}</p:txBody>"
            . '</p:sp>';
    }

    private function wrapInGraphicFrame(string $tableContent, int $shapeId): string
    {
        $x = self::MARGIN;
        $y = (int) (self::SLIDE_HEIGHT * 0.45);
        $cx = self::SLIDE_WIDTH - 2 * self::MARGIN;
        $cy = (int) (self::SLIDE_HEIGHT * 0.5);

        return '<p:graphicFrame>'
            . "<p:nvGraphicFramePr><p:cNvPr id=\"{$shapeId}\" name=\"Table\"/><p:cNvGraphicFramePr><a:graphicFrameLocks noGrp=\"1\"/></p:cNvGraphicFramePr><p:nvPr/></p:nvGraphicFramePr>"
            . "<p:xfrm><a:off x=\"{$x}\" y=\"{$y}\"/><a:ext cx=\"{$cx}\" cy=\"{$cy}\"/></p:xfrm>"
            . '<a:graphic><a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/table">'
            . $tableContent
            . '</a:graphicData></a:graphic>'
            . '</p:graphicFrame>';
    }

    /* =============================================================
     | Package Structure
     |============================================================= */

    private function buildContentTypes(int $slideCount): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">';
        $xml .= '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>';
        $xml .= '<Default Extension="xml" ContentType="application/xml"/>';
        $xml .= '<Default Extension="png" ContentType="image/png"/>';
        $xml .= '<Default Extension="jpeg" ContentType="image/jpeg"/>';
        $xml .= '<Override PartName="/ppt/presentation.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.presentation.main+xml"/>';
        $xml .= '<Override PartName="/ppt/slideMasters/slideMaster1.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slideMaster+xml"/>';
        $xml .= '<Override PartName="/ppt/slideLayouts/slideLayout1.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slideLayout+xml"/>';
        $xml .= '<Override PartName="/ppt/theme/theme1.xml" ContentType="application/vnd.openxmlformats-officedocument.theme+xml"/>';
        $xml .= '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>';

        for ($i = 1; $i <= $slideCount; $i++) {
            $xml .= "<Override PartName=\"/ppt/slides/slide{$i}.xml\" ContentType=\"application/vnd.openxmlformats-officedocument.presentationml.slide+xml\"/>";
        }

        $xml .= '</Types>';

        return $xml;
    }

    private function buildRootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="ppt/presentation.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '</Relationships>';
    }

    private function buildPresentation(int $slideCount): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<p:presentation xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" '
              . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" '
              . 'xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main">';
        $xml .= '<p:sldMasterIdLst><p:sldMasterId id="2147483648" r:id="rIdMaster"/></p:sldMasterIdLst>';
        $xml .= '<p:sldIdLst>';

        for ($i = 1; $i <= $slideCount; $i++) {
            $sldId = 255 + $i;
            $xml .= "<p:sldId id=\"{$sldId}\" r:id=\"rId{$i}\"/>";
        }

        $xml .= '</p:sldIdLst>';
        $xml .= "<p:sldSz cx=\"" . self::SLIDE_WIDTH . "\" cy=\"" . self::SLIDE_HEIGHT . "\"/>";
        $xml .= '</p:presentation>';

        return $xml;
    }

    private function buildPresentationRels(int $slideCount): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';

        for ($i = 1; $i <= $slideCount; $i++) {
            $xml .= "<Relationship Id=\"rId{$i}\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/slide\" Target=\"slides/slide{$i}.xml\"/>";
        }

        $xml .= '<Relationship Id="rIdMaster" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideMaster" Target="slideMasters/slideMaster1.xml"/>';
        $xml .= '<Relationship Id="rIdTheme" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme" Target="theme/theme1.xml"/>';
        $xml .= '</Relationships>';

        return $xml;
    }

    private function buildSlideRels(int $slideNum): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout" Target="../slideLayouts/slideLayout1.xml"/>'
            . '</Relationships>';
    }

    private function buildSlideMaster(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<p:sldMaster xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" '
            . 'xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main">'
            . '<p:cSld><p:bg><p:bgPr><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill><a:effectLst/></p:bgPr></p:bg>'
            . '<p:spTree><p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr><p:grpSpPr/></p:spTree></p:cSld>'
            . '<p:clrMap bg1="lt1" tx1="dk1" bg2="lt2" tx2="dk2" accent1="accent1" accent2="accent2" accent3="accent3" accent4="accent4" accent5="accent5" accent6="accent6" hlink="hlink" folHlink="folHlink"/>'
            . '<p:sldLayoutIdLst><p:sldLayoutId id="2147483649" r:id="rId1"/></p:sldLayoutIdLst>'
            . '</p:sldMaster>';
    }

    private function buildSlideMasterRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout" Target="../slideLayouts/slideLayout1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme" Target="../theme/theme1.xml"/>'
            . '</Relationships>';
    }

    private function buildSlideLayout(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<p:sldLayout xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" '
            . 'xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" type="blank">'
            . '<p:cSld><p:spTree><p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr><p:grpSpPr/></p:spTree></p:cSld>'
            . '</p:sldLayout>';
    }

    private function buildSlideLayoutRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideMaster" Target="../slideMasters/slideMaster1.xml"/>'
            . '</Relationships>';
    }

    private function buildTheme(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<a:theme xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" name="Paperdoc">'
            . '<a:themeElements>'
            . '<a:clrScheme name="Default">'
            . '<a:dk1><a:srgbClr val="000000"/></a:dk1>'
            . '<a:lt1><a:srgbClr val="FFFFFF"/></a:lt1>'
            . '<a:dk2><a:srgbClr val="44546A"/></a:dk2>'
            . '<a:lt2><a:srgbClr val="E7E6E6"/></a:lt2>'
            . '<a:accent1><a:srgbClr val="4472C4"/></a:accent1>'
            . '<a:accent2><a:srgbClr val="ED7D31"/></a:accent2>'
            . '<a:accent3><a:srgbClr val="A5A5A5"/></a:accent3>'
            . '<a:accent4><a:srgbClr val="FFC000"/></a:accent4>'
            . '<a:accent5><a:srgbClr val="5B9BD5"/></a:accent5>'
            . '<a:accent6><a:srgbClr val="70AD47"/></a:accent6>'
            . '<a:hlink><a:srgbClr val="0563C1"/></a:hlink>'
            . '<a:folHlink><a:srgbClr val="954F72"/></a:folHlink>'
            . '</a:clrScheme>'
            . '<a:fontScheme name="Default"><a:majorFont><a:latin typeface="Calibri Light"/></a:majorFont><a:minorFont><a:latin typeface="Calibri"/></a:minorFont></a:fontScheme>'
            . '<a:fmtScheme name="Default"><a:fillStyleLst><a:solidFill><a:schemeClr val="phClr"/></a:solidFill><a:solidFill><a:schemeClr val="phClr"/></a:solidFill><a:solidFill><a:schemeClr val="phClr"/></a:solidFill></a:fillStyleLst><a:lnStyleLst><a:ln w="6350"><a:solidFill><a:schemeClr val="phClr"/></a:solidFill></a:ln><a:ln w="6350"><a:solidFill><a:schemeClr val="phClr"/></a:solidFill></a:ln><a:ln w="6350"><a:solidFill><a:schemeClr val="phClr"/></a:solidFill></a:ln></a:lnStyleLst><a:effectStyleLst><a:effectStyle><a:effectLst/></a:effectStyle><a:effectStyle><a:effectLst/></a:effectStyle><a:effectStyle><a:effectLst/></a:effectStyle></a:effectStyleLst><a:bgFillStyleLst><a:solidFill><a:schemeClr val="phClr"/></a:solidFill><a:solidFill><a:schemeClr val="phClr"/></a:solidFill><a:solidFill><a:schemeClr val="phClr"/></a:solidFill></a:bgFillStyleLst></a:fmtScheme>'
            . '</a:themeElements>'
            . '</a:theme>';
    }

    private function buildCoreMeta(DocumentInterface $document): string
    {
        $title = $this->escapeXml($document->getTitle());
        $author = $this->escapeXml($document->getMetadata()['author'] ?? 'Paperdoc');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/">'
            . "<dc:title>{$title}</dc:title>"
            . "<dc:creator>{$author}</dc:creator>"
            . '</cp:coreProperties>';
    }

    private function escapeXml(string $text): string
    {
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
