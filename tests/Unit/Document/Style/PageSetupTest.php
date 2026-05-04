<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document\Style;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\Image;
use Paperdoc\Document\Style\PageSetup;
use Paperdoc\Enum\PageSize;

class PageSetupTest extends TestCase
{
    public function test_defaults_match_a4_portrait(): void
    {
        $setup = new PageSetup();

        $this->assertEqualsWithDelta(595.28, $setup->getWidth(), 0.01);
        $this->assertEqualsWithDelta(841.89, $setup->getHeight(), 0.01);
        $this->assertSame(40.0, $setup->getPaddingTop());
        $this->assertSame(40.0, $setup->getPaddingRight());
        $this->assertSame(40.0, $setup->getPaddingBottom());
        $this->assertSame(40.0, $setup->getPaddingLeft());
        $this->assertNull($setup->getBackgroundColor());
        $this->assertNull($setup->getBackgroundImage());
        $this->assertSame(PageSetup::ORIENTATION_PORTRAIT, $setup->getOrientation());
    }

    public function test_make_factory(): void
    {
        $this->assertInstanceOf(PageSetup::class, PageSetup::make());
    }

    public function test_from_size_uses_iso_dimensions(): void
    {
        $setup = PageSetup::fromSize(PageSize::A5);

        $this->assertEqualsWithDelta(419.53, $setup->getWidth(), 0.01);
        $this->assertEqualsWithDelta(595.28, $setup->getHeight(), 0.01);
        $this->assertSame(PageSize::A5, $setup->getSize());
    }

    public function test_landscape_swaps_dimensions(): void
    {
        $setup = PageSetup::fromSize(PageSize::A4)->landscape();

        $this->assertGreaterThan($setup->getHeight(), $setup->getWidth());
        $this->assertSame(PageSetup::ORIENTATION_LANDSCAPE, $setup->getOrientation());
    }

    public function test_portrait_after_landscape_returns_to_normal(): void
    {
        $setup = PageSetup::fromSize(PageSize::A4)->landscape()->portrait();

        $this->assertLessThan($setup->getHeight(), $setup->getWidth());
        $this->assertSame(PageSetup::ORIENTATION_PORTRAIT, $setup->getOrientation());
    }

    public function test_custom_dimensions(): void
    {
        $setup = PageSetup::custom(1000.0, 500.0);

        $this->assertSame(1000.0, $setup->getWidth());
        $this->assertSame(500.0, $setup->getHeight());
        $this->assertNull($setup->getSize());
    }

    public function test_set_padding_one_value(): void
    {
        $setup = (new PageSetup())->setPadding(20.0);

        $this->assertSame(20.0, $setup->getPaddingTop());
        $this->assertSame(20.0, $setup->getPaddingRight());
        $this->assertSame(20.0, $setup->getPaddingBottom());
        $this->assertSame(20.0, $setup->getPaddingLeft());
    }

    public function test_set_padding_two_values(): void
    {
        $setup = (new PageSetup())->setPadding(10.0, 30.0);

        $this->assertSame(10.0, $setup->getPaddingTop());
        $this->assertSame(30.0, $setup->getPaddingRight());
        $this->assertSame(10.0, $setup->getPaddingBottom());
        $this->assertSame(30.0, $setup->getPaddingLeft());
    }

    public function test_set_padding_four_values(): void
    {
        $setup = (new PageSetup())->setPadding(1.0, 2.0, 3.0, 4.0);

        $this->assertSame(1.0, $setup->getPaddingTop());
        $this->assertSame(2.0, $setup->getPaddingRight());
        $this->assertSame(3.0, $setup->getPaddingBottom());
        $this->assertSame(4.0, $setup->getPaddingLeft());
    }

    public function test_set_padding_invalid_count_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new PageSetup())->setPadding(1.0, 2.0, 3.0, 4.0, 5.0);
    }

    public function test_background_color(): void
    {
        $setup = (new PageSetup())->setBackgroundColor('#FAFAFA');

        $this->assertSame('#FAFAFA', $setup->getBackgroundColor());

        $setup->setBackgroundColor(null);
        $this->assertNull($setup->getBackgroundColor());
    }

    public function test_background_image(): void
    {
        $image = Image::make('/path/to/cover.jpg', 1920, 1080);
        $setup = (new PageSetup())->setBackgroundImage($image);

        $this->assertSame($image, $setup->getBackgroundImage());
    }

    public function test_content_dimensions_subtract_padding(): void
    {
        $setup = PageSetup::fromSize(PageSize::A4)->setPadding(50.0);

        $this->assertEqualsWithDelta(595.28 - 100, $setup->getContentWidth(), 0.01);
        $this->assertEqualsWithDelta(841.89 - 100, $setup->getContentHeight(), 0.01);
    }

    public function test_json_serialization(): void
    {
        $setup = PageSetup::fromSize(PageSize::A4)
            ->setPadding(10.0, 20.0)
            ->setBackgroundColor('#FFFFFF');

        $json = json_decode(json_encode($setup), true);

        $this->assertSame('a4', $json['size']);
        $this->assertEqualsWithDelta(595.28, $json['width'], 0.01);
        $this->assertEquals(10.0, $json['padding']['top']);
        $this->assertEquals(20.0, $json['padding']['right']);
        $this->assertSame('#FFFFFF', $json['backgroundColor']);
    }
}
