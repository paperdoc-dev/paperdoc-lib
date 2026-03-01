<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document\Style;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\StyleInterface;
use Paperdoc\Document\Style\ParagraphStyle;
use Paperdoc\Enum\Alignment;

class ParagraphStyleTest extends TestCase
{
    public function test_implements_style_interface(): void
    {
        $this->assertInstanceOf(StyleInterface::class, new ParagraphStyle());
    }

    public function test_defaults(): void
    {
        $style = new ParagraphStyle();

        $this->assertSame(Alignment::LEFT, $style->getAlignment());
        $this->assertSame(0.0, $style->getSpaceBefore());
        $this->assertSame(6.0, $style->getSpaceAfter());
        $this->assertSame(1.15, $style->getLineSpacing());
    }

    public function test_make_factory(): void
    {
        $style = ParagraphStyle::make();

        $this->assertInstanceOf(ParagraphStyle::class, $style);
    }

    public function test_set_alignment(): void
    {
        $style = ParagraphStyle::make()->setAlignment(Alignment::CENTER);

        $this->assertSame(Alignment::CENTER, $style->getAlignment());
    }

    public function test_set_alignment_justify(): void
    {
        $style = ParagraphStyle::make()->setAlignment(Alignment::JUSTIFY);

        $this->assertSame(Alignment::JUSTIFY, $style->getAlignment());
    }

    public function test_set_alignment_right(): void
    {
        $style = ParagraphStyle::make()->setAlignment(Alignment::RIGHT);

        $this->assertSame(Alignment::RIGHT, $style->getAlignment());
    }

    public function test_set_space_before(): void
    {
        $style = ParagraphStyle::make()->setSpaceBefore(12.0);

        $this->assertSame(12.0, $style->getSpaceBefore());
    }

    public function test_set_space_after(): void
    {
        $style = ParagraphStyle::make()->setSpaceAfter(18.0);

        $this->assertSame(18.0, $style->getSpaceAfter());
    }

    public function test_set_line_spacing(): void
    {
        $style = ParagraphStyle::make()->setLineSpacing(1.5);

        $this->assertSame(1.5, $style->getLineSpacing());
    }

    public function test_fluent_chaining(): void
    {
        $style = ParagraphStyle::make()
            ->setAlignment(Alignment::JUSTIFY)
            ->setSpaceBefore(6.0)
            ->setSpaceAfter(12.0)
            ->setLineSpacing(2.0);

        $this->assertSame(Alignment::JUSTIFY, $style->getAlignment());
        $this->assertSame(6.0, $style->getSpaceBefore());
        $this->assertSame(12.0, $style->getSpaceAfter());
        $this->assertSame(2.0, $style->getLineSpacing());
    }

    public function test_to_array(): void
    {
        $style = ParagraphStyle::make()
            ->setAlignment(Alignment::CENTER)
            ->setSpaceBefore(10.0);

        $arr = $style->toArray();

        $this->assertSame('center', $arr['alignment']);
        $this->assertSame(10.0, $arr['spaceBefore']);
        $this->assertSame(6.0, $arr['spaceAfter']);
        $this->assertSame(1.15, $arr['lineSpacing']);
    }
}
