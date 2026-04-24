<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\ListBlock;
use Paperdoc\Document\ListItem;
use Paperdoc\Document\Paragraph;
use Paperdoc\Document\TextRun;
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Document\Style\TextStyle;

class ListItemTest extends TestCase
{
    public function test_make_with_empty_text_has_no_runs(): void
    {
        $item = ListItem::make();

        $this->assertSame([], $item->getRuns());
        $this->assertSame('', $item->getPlainText());
    }

    public function test_make_with_text_creates_initial_run(): void
    {
        $item = ListItem::make('hello');

        $this->assertCount(1, $item->getRuns());
        $this->assertSame('hello', $item->getRuns()[0]->getText());
    }

    public function test_make_with_style_creates_run_even_if_text_empty(): void
    {
        $style = TextStyle::make()->setItalic();
        $item  = ListItem::make('', $style);

        $this->assertCount(1, $item->getRuns());
        $this->assertSame($style, $item->getRuns()[0]->getStyle());
    }

    public function test_make_with_link_creates_run(): void
    {
        $link = TextLink::make('https://example.test');
        $item = ListItem::make('click', null, $link);

        $runs = $item->getRuns();
        $this->assertCount(1, $runs);
        $this->assertSame($link, $runs[0]->getLink());
    }

    public function test_add_run_is_fluent(): void
    {
        $item = new ListItem();
        $run  = new TextRun('text');

        $result = $item->addRun($run);

        $this->assertSame($item, $result);
        $this->assertSame([$run], $item->getRuns());
    }

    public function test_add_text_is_fluent_and_creates_run(): void
    {
        $item = new ListItem();
        $result = $item->addText('hello', null, null);

        $this->assertSame($item, $result);
        $this->assertSame('hello', $item->getPlainText());
    }

    public function test_plain_text_joins_runs(): void
    {
        $item = new ListItem();
        $item->addText('foo ');
        $item->addText('bar ');
        $item->addText('baz');

        $this->assertSame('foo bar baz', $item->getPlainText());
    }

    public function test_has_children_false_by_default(): void
    {
        $this->assertFalse((new ListItem())->hasChildren());
    }

    public function test_add_block_adds_child(): void
    {
        $item  = new ListItem();
        $para  = new Paragraph();
        $para->addRun(new TextRun('nested paragraph'));

        $item->addBlock($para);

        $this->assertTrue($item->hasChildren());
        $this->assertSame([$para], $item->getBlocks());
    }

    public function test_add_list_adds_nested_list(): void
    {
        $item   = new ListItem();
        $nested = ListBlock::bullet();
        $nested->addText('x');

        $item->addList($nested);

        $this->assertSame([$nested], $item->getBlocks());
    }

    public function test_json_serialize_without_blocks(): void
    {
        $item = ListItem::make('hello');

        $json = json_decode(json_encode($item, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('hello', $json['text']);
        $this->assertCount(1, $json['runs']);
        $this->assertArrayNotHasKey('blocks', $json);
    }

    public function test_json_serialize_with_nested_list(): void
    {
        $item   = ListItem::make('root');
        $nested = ListBlock::bullet();
        $nested->addText('child');
        $item->addList($nested);

        $json = json_decode(json_encode($item, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('blocks', $json);
        $this->assertCount(1, $json['blocks']);
        $this->assertSame('list', $json['blocks'][0]['type']);
    }
}
