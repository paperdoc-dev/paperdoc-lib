<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document\Style;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\StyleInterface;
use Paperdoc\Document\Style\TableStyle;
use Paperdoc\Enum\Alignment;
use Paperdoc\Enum\BorderStyle;

class TableStyleTest extends TestCase
{
    public function test_implements_style_interface(): void
    {
        $this->assertInstanceOf(StyleInterface::class, new TableStyle());
    }

    public function test_defaults(): void
    {
        $style = new TableStyle();

        $this->assertSame(Alignment::LEFT, $style->getAlignment());
        $this->assertSame(BorderStyle::SOLID, $style->getBorderStyle());
        $this->assertSame(0.5, $style->getBorderWidth());
        $this->assertSame('#000000', $style->getBorderColor());
        $this->assertSame(4.0, $style->getCellPadding());
        $this->assertSame('#f3f4f6', $style->getHeaderBg());
        $this->assertNull($style->getStripedBg());
    }

    public function test_make_factory(): void
    {
        $this->assertInstanceOf(TableStyle::class, TableStyle::make());
    }

    public function test_set_border_style(): void
    {
        $style = TableStyle::make()->setBorderStyle(BorderStyle::DASHED);

        $this->assertSame(BorderStyle::DASHED, $style->getBorderStyle());
    }

    public function test_set_border_width(): void
    {
        $style = TableStyle::make()->setBorderWidth(2.0);

        $this->assertSame(2.0, $style->getBorderWidth());
    }

    public function test_set_border_color(): void
    {
        $style = TableStyle::make()->setBorderColor('#FF0000');

        $this->assertSame('#FF0000', $style->getBorderColor());
    }

    public function test_set_cell_padding(): void
    {
        $style = TableStyle::make()->setCellPadding(8.0);

        $this->assertSame(8.0, $style->getCellPadding());
    }

    public function test_set_header_bg(): void
    {
        $style = TableStyle::make()->setHeaderBg('#e5e7eb');

        $this->assertSame('#e5e7eb', $style->getHeaderBg());
    }

    public function test_set_header_bg_null(): void
    {
        $style = TableStyle::make()->setHeaderBg(null);

        $this->assertNull($style->getHeaderBg());
    }

    public function test_set_striped_bg(): void
    {
        $style = TableStyle::make()->setStripedBg('#f9fafb');

        $this->assertSame('#f9fafb', $style->getStripedBg());
    }

    public function test_fluent_chaining(): void
    {
        $style = TableStyle::make()
            ->setAlignment(Alignment::CENTER)
            ->setBorderStyle(BorderStyle::DOTTED)
            ->setBorderWidth(1.0)
            ->setCellPadding(6.0);

        $this->assertSame(Alignment::CENTER, $style->getAlignment());
        $this->assertSame(BorderStyle::DOTTED, $style->getBorderStyle());
        $this->assertSame(1.0, $style->getBorderWidth());
        $this->assertSame(6.0, $style->getCellPadding());
    }

    public function test_to_array(): void
    {
        $style = TableStyle::make()->setBorderStyle(BorderStyle::DASHED)->setCellPadding(10.0);
        $arr = $style->toArray();

        $this->assertSame('left', $arr['alignment']);
        $this->assertSame('dashed', $arr['borderStyle']);
        $this->assertSame(0.5, $arr['borderWidth']);
        $this->assertSame(10.0, $arr['cellPadding']);
        $this->assertSame('#f3f4f6', $arr['headerBg']);
        $this->assertNull($arr['stripedBg']);
    }
}
