<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use Paperdoc\Enum\PageSize;

class PageSizeTest extends TestCase
{
    public function test_a4_dimensions_match_iso_216(): void
    {
        $this->assertEqualsWithDelta(595.28, PageSize::A4->width(), 0.01);
        $this->assertEqualsWithDelta(841.89, PageSize::A4->height(), 0.01);
    }

    public function test_letter_dimensions_match_us_standard(): void
    {
        $this->assertSame(612.0, PageSize::LETTER->width());
        $this->assertSame(792.0, PageSize::LETTER->height());
    }

    public function test_legal_is_taller_than_letter(): void
    {
        $this->assertGreaterThan(PageSize::LETTER->height(), PageSize::LEGAL->height());
        $this->assertSame(PageSize::LETTER->width(), PageSize::LEGAL->width());
    }

    public function test_a3_is_double_a4(): void
    {
        $this->assertEqualsWithDelta(PageSize::A4->height(), PageSize::A3->width(), 0.5);
    }

    public function test_dimensions_returns_pair(): void
    {
        [$w, $h] = PageSize::A5->dimensions();

        $this->assertEqualsWithDelta(419.53, $w, 0.01);
        $this->assertEqualsWithDelta(595.28, $h, 0.01);
    }

    public function test_all_sizes_resolved(): void
    {
        foreach (PageSize::cases() as $size) {
            [$w, $h] = $size->dimensions();
            $this->assertGreaterThan(0, $w, "Width missing for {$size->value}");
            $this->assertGreaterThan(0, $h, "Height missing for {$size->value}");
        }
    }
}
