<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\BlockElementInterface;
use Paperdoc\Document\HorizontalRule;
use Paperdoc\Enum\Alignment;

class HorizontalRuleTest extends TestCase
{
    public function test_implements_block_element_interface(): void
    {
        $this->assertInstanceOf(BlockElementInterface::class, new HorizontalRule());
    }

    public function test_default_geometry_and_style(): void
    {
        $rule = new HorizontalRule();

        $this->assertSame('100%', $rule->getWidth());
        $this->assertSame(0.5, $rule->getThickness());
        $this->assertSame('#999999', $rule->getColor());
        $this->assertSame(Alignment::CENTER, $rule->getAlignment());
        $this->assertSame(6.0, $rule->getMarginTop());
        $this->assertSame(6.0, $rule->getMarginBottom());
    }

    public function test_get_type(): void
    {
        $this->assertSame('horizontal_rule', (new HorizontalRule())->getType());
    }

    public function test_make_factory(): void
    {
        $this->assertInstanceOf(HorizontalRule::class, HorizontalRule::make());
    }

    public function test_fluent_setters_return_self(): void
    {
        $rule = HorizontalRule::make();

        $result = $rule
            ->setWidth(140.0)
            ->setThickness(2.0)
            ->setColor('#abcdef')
            ->setAlignment(Alignment::LEFT)
            ->setMargins(12.0, 8.0);

        $this->assertSame($rule, $result);
        $this->assertSame(140.0, $rule->getWidth());
        $this->assertSame(2.0, $rule->getThickness());
        $this->assertSame('#abcdef', $rule->getColor());
        $this->assertSame(Alignment::LEFT, $rule->getAlignment());
        $this->assertSame(12.0, $rule->getMarginTop());
        $this->assertSame(8.0, $rule->getMarginBottom());
    }

    public function test_resolve_width_handles_percentage(): void
    {
        $rule = HorizontalRule::make()->setWidth('50%');

        $this->assertSame(200.0, $rule->resolveWidth(400.0));
    }

    public function test_resolve_width_handles_absolute_pt(): void
    {
        $rule = HorizontalRule::make()->setWidth(120.0);

        $this->assertSame(120.0, $rule->resolveWidth(400.0));
    }

    public function test_resolve_width_clamps_to_content_width(): void
    {
        $rule = HorizontalRule::make()->setWidth(800.0);

        $this->assertSame(400.0, $rule->resolveWidth(400.0));
    }

    public function test_resolve_width_handles_string_pt_value(): void
    {
        $rule = HorizontalRule::make()->setWidth('150');

        $this->assertSame(150.0, $rule->resolveWidth(400.0));
    }

    public function test_json_serialization(): void
    {
        $rule = HorizontalRule::make()
            ->setWidth('80%')
            ->setThickness(1.0)
            ->setColor('#cccccc')
            ->setAlignment(Alignment::RIGHT)
            ->setMargins(4.0, 4.0);

        $json = json_decode(json_encode($rule), true);

        $this->assertSame('horizontal_rule', $json['type']);
        $this->assertSame('80%', $json['width']);
        $this->assertEquals(1.0, $json['thickness']);
        $this->assertSame('#cccccc', $json['color']);
        $this->assertSame('right', $json['alignment']);
        $this->assertEquals(4.0, $json['marginTop']);
        $this->assertEquals(4.0, $json['marginBottom']);
    }
}
