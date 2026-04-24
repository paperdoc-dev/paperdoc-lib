<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\BlockElementInterface;
use Paperdoc\Contracts\DocumentElementInterface;
use Paperdoc\Document\ListBlock;
use Paperdoc\Document\ListItem;
use Paperdoc\Document\TextRun;
use Paperdoc\Document\Style\TextStyle;
use Paperdoc\Exceptions\InvalidDocumentException;

class ListBlockTest extends TestCase
{
    public function test_implements_block_element_interface(): void
    {
        $this->assertInstanceOf(BlockElementInterface::class, new ListBlock());
        $this->assertInstanceOf(DocumentElementInterface::class, new ListBlock());
    }

    public function test_type_is_list(): void
    {
        $this->assertSame('list', (new ListBlock())->getType());
    }

    public function test_default_style_is_bullet(): void
    {
        $list = new ListBlock();

        $this->assertSame(ListBlock::STYLE_BULLET, $list->getStyle());
        $this->assertTrue($list->isBullet());
        $this->assertFalse($list->isOrdered());
        $this->assertSame(1, $list->getStart());
    }

    public function test_bullet_factory(): void
    {
        $list = ListBlock::bullet();

        $this->assertInstanceOf(ListBlock::class, $list);
        $this->assertTrue($list->isBullet());
    }

    public function test_ordered_factory_with_start(): void
    {
        $list = ListBlock::ordered(5);

        $this->assertTrue($list->isOrdered());
        $this->assertSame(5, $list->getStart());
    }

    public function test_make_factory_accepts_style_and_start(): void
    {
        $list = ListBlock::make(ListBlock::STYLE_ORDERED, 3);

        $this->assertTrue($list->isOrdered());
        $this->assertSame(3, $list->getStart());
    }

    public function test_invalid_style_throws(): void
    {
        $this->expectException(InvalidDocumentException::class);
        $this->expectExceptionMessage('Invalid list style "random"');

        new ListBlock('random');
    }

    public function test_set_style_validates(): void
    {
        $list = new ListBlock();

        $this->expectException(InvalidDocumentException::class);
        $list->setStyle('foo');
    }

    public function test_set_start_is_fluent(): void
    {
        $list = ListBlock::ordered();
        $result = $list->setStart(42);

        $this->assertSame($list, $result);
        $this->assertSame(42, $list->getStart());
    }

    public function test_add_item_is_fluent(): void
    {
        $list = new ListBlock();
        $item = ListItem::make('one');

        $result = $list->addItem($item);

        $this->assertSame($list, $result);
        $this->assertSame([$item], $list->getItems());
    }

    public function test_add_text_creates_item_and_returns_it(): void
    {
        $list = ListBlock::bullet();
        $item = $list->addText('Buy milk');

        $this->assertInstanceOf(ListItem::class, $item);
        $this->assertSame('Buy milk', $item->getPlainText());
        $this->assertCount(1, $list->getItems());
        $this->assertSame($item, $list->getItems()[0]);
    }

    public function test_add_text_preserves_style_and_link(): void
    {
        $list = ListBlock::bullet();
        $style = TextStyle::make()->setBold();

        $item = $list->addText('Bold item', $style);

        $runs = $item->getRuns();
        $this->assertCount(1, $runs);
        $this->assertSame($style, $runs[0]->getStyle());
    }

    public function test_count_and_is_empty(): void
    {
        $list = new ListBlock();

        $this->assertTrue($list->isEmpty());
        $this->assertSame(0, $list->count());

        $list->addText('a');
        $list->addText('b');

        $this->assertFalse($list->isEmpty());
        $this->assertSame(2, $list->count());
    }

    public function test_nested_list_via_item(): void
    {
        $parent   = ListBlock::bullet();
        $groceries = $parent->addText('Groceries');

        $nested = ListBlock::bullet();
        $nested->addText('milk');
        $nested->addText('eggs');

        $groceries->addList($nested);

        $this->assertTrue($groceries->hasChildren());
        $this->assertSame([$nested], $groceries->getBlocks());
        $this->assertSame(2, $nested->count());
    }

    public function test_json_serialize_shape_bullet(): void
    {
        $list = ListBlock::bullet();
        $list->addText('one');
        $list->addText('two');

        $json = json_decode(json_encode($list, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('list', $json['type']);
        $this->assertSame('bullet', $json['style']);
        $this->assertArrayNotHasKey('start', $json);
        $this->assertCount(2, $json['items']);
    }

    public function test_json_serialize_ordered_with_custom_start(): void
    {
        $list = ListBlock::ordered(5);
        $list->addText('fifth');

        $json = json_decode(json_encode($list, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('ordered', $json['style']);
        $this->assertSame(5, $json['start']);
    }

    public function test_json_serialize_ordered_default_start_omits_key(): void
    {
        $list = ListBlock::ordered();
        $list->addText('one');

        $json = json_decode(json_encode($list, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertArrayNotHasKey('start', $json);
    }

    public function test_run_in_item_carries_text(): void
    {
        $list = ListBlock::bullet();
        $item = $list->addText('Hello');
        $item->addText(' world');

        $this->assertSame('Hello world', $item->getPlainText());
        $this->assertSame(2, count($item->getRuns()));
        $this->assertInstanceOf(TextRun::class, $item->getRuns()[0]);
    }
}
