<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document\Style;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\StyleInterface;
use Paperdoc\Document\Style\TextStyle;

class TextStyleTest extends TestCase
{
    public function test_implements_style_interface(): void
    {
        $this->assertInstanceOf(StyleInterface::class, new TextStyle());
    }

    public function test_defaults(): void
    {
        $style = new TextStyle();

        $this->assertSame('Helvetica', $style->getFontFamily());
        $this->assertSame(12.0, $style->getFontSize());
        $this->assertSame('#000000', $style->getColor());
        $this->assertFalse($style->isBold());
        $this->assertFalse($style->isItalic());
        $this->assertFalse($style->isUnderline());
    }

    public function test_make_factory(): void
    {
        $style = TextStyle::make();

        $this->assertInstanceOf(TextStyle::class, $style);
    }

    public function test_fluent_setters(): void
    {
        $style = TextStyle::make()
            ->setFontFamily('Times')
            ->setFontSize(16.0)
            ->setColor('#1A5276')
            ->setBold()
            ->setItalic()
            ->setUnderline();

        $this->assertSame('Times', $style->getFontFamily());
        $this->assertSame(16.0, $style->getFontSize());
        $this->assertSame('#1A5276', $style->getColor());
        $this->assertTrue($style->isBold());
        $this->assertTrue($style->isItalic());
        $this->assertTrue($style->isUnderline());
    }

    public function test_set_bold_false(): void
    {
        $style = TextStyle::make()->setBold(true)->setBold(false);

        $this->assertFalse($style->isBold());
    }

    public function test_to_array(): void
    {
        $style = TextStyle::make()->setBold()->setFontSize(14.0);
        $arr = $style->toArray();

        $this->assertSame('Helvetica', $arr['fontFamily']);
        $this->assertSame(14.0, $arr['fontSize']);
        $this->assertSame('#000000', $arr['color']);
        $this->assertTrue($arr['bold']);
        $this->assertFalse($arr['italic']);
        $this->assertFalse($arr['underline']);
    }

    public function test_get_color_rgb_black(): void
    {
        $style = TextStyle::make()->setColor('#000000');
        [$r, $g, $b] = $style->getColorRgb();

        $this->assertEqualsWithDelta(0.0, $r, 0.001);
        $this->assertEqualsWithDelta(0.0, $g, 0.001);
        $this->assertEqualsWithDelta(0.0, $b, 0.001);
    }

    public function test_get_color_rgb_white(): void
    {
        $style = TextStyle::make()->setColor('#FFFFFF');
        [$r, $g, $b] = $style->getColorRgb();

        $this->assertEqualsWithDelta(1.0, $r, 0.001);
        $this->assertEqualsWithDelta(1.0, $g, 0.001);
        $this->assertEqualsWithDelta(1.0, $b, 0.001);
    }

    public function test_get_color_rgb_red(): void
    {
        $style = TextStyle::make()->setColor('#FF0000');
        [$r, $g, $b] = $style->getColorRgb();

        $this->assertEqualsWithDelta(1.0, $r, 0.001);
        $this->assertEqualsWithDelta(0.0, $g, 0.001);
        $this->assertEqualsWithDelta(0.0, $b, 0.001);
    }

    public function test_get_color_rgb_custom(): void
    {
        $style = TextStyle::make()->setColor('#1A5276');
        [$r, $g, $b] = $style->getColorRgb();

        $this->assertEqualsWithDelta(26 / 255, $r, 0.001);
        $this->assertEqualsWithDelta(82 / 255, $g, 0.001);
        $this->assertEqualsWithDelta(118 / 255, $b, 0.001);
    }

    /* -------------------------------------------------------------
     | PDF Font Name Mapping
     |------------------------------------------------------------- */

    public function test_pdf_font_helvetica(): void
    {
        $this->assertSame('Helvetica', TextStyle::make()->getPdfFontName());
    }

    public function test_pdf_font_helvetica_bold(): void
    {
        $this->assertSame('Helvetica-Bold', TextStyle::make()->setBold()->getPdfFontName());
    }

    public function test_pdf_font_helvetica_italic(): void
    {
        $this->assertSame('Helvetica-Oblique', TextStyle::make()->setItalic()->getPdfFontName());
    }

    public function test_pdf_font_helvetica_bold_italic(): void
    {
        $this->assertSame('Helvetica-BoldOblique', TextStyle::make()->setBold()->setItalic()->getPdfFontName());
    }

    public function test_pdf_font_times(): void
    {
        $this->assertSame('Times-Roman', TextStyle::make()->setFontFamily('Times')->getPdfFontName());
    }

    public function test_pdf_font_times_bold(): void
    {
        $this->assertSame('Times-Bold', TextStyle::make()->setFontFamily('Times')->setBold()->getPdfFontName());
    }

    public function test_pdf_font_times_italic(): void
    {
        $this->assertSame('Times-Italic', TextStyle::make()->setFontFamily('Times')->setItalic()->getPdfFontName());
    }

    public function test_pdf_font_times_bold_italic(): void
    {
        $this->assertSame('Times-BoldItalic', TextStyle::make()->setFontFamily('Times')->setBold()->setItalic()->getPdfFontName());
    }

    public function test_pdf_font_courier(): void
    {
        $this->assertSame('Courier', TextStyle::make()->setFontFamily('Courier')->getPdfFontName());
    }

    public function test_pdf_font_courier_bold(): void
    {
        $this->assertSame('Courier-Bold', TextStyle::make()->setFontFamily('Courier')->setBold()->getPdfFontName());
    }

    public function test_pdf_font_courier_oblique(): void
    {
        $this->assertSame('Courier-Oblique', TextStyle::make()->setFontFamily('Courier')->setItalic()->getPdfFontName());
    }

    public function test_pdf_font_courier_bold_oblique(): void
    {
        $this->assertSame('Courier-BoldOblique', TextStyle::make()->setFontFamily('Courier')->setBold()->setItalic()->getPdfFontName());
    }

    public function test_pdf_font_family_aliases(): void
    {
        $this->assertSame('Times-Roman', TextStyle::make()->setFontFamily('Times New Roman')->getPdfFontName());
        $this->assertSame('Times-Roman', TextStyle::make()->setFontFamily('serif')->getPdfFontName());
        $this->assertSame('Courier', TextStyle::make()->setFontFamily('Courier New')->getPdfFontName());
        $this->assertSame('Courier', TextStyle::make()->setFontFamily('monospace')->getPdfFontName());
    }

    public function test_pdf_font_unknown_maps_to_helvetica(): void
    {
        $this->assertSame('Helvetica', TextStyle::make()->setFontFamily('Arial')->getPdfFontName());
        $this->assertSame('Helvetica', TextStyle::make()->setFontFamily('Verdana')->getPdfFontName());
    }
}
