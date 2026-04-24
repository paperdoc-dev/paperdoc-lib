<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\BlockElementInterface;
use Paperdoc\Document\Blockquote;
use Paperdoc\Document\Paragraph;
use Paperdoc\Document\TextRun;
use Paperdoc\Document\Style\TextStyle;

class BlockquoteTest extends TestCase
{
    public function test_implements_block_element_interface(): void
    {
        $this->assertInstanceOf(BlockElementInterface::class, new Blockquote());
    }

    public function test_type_is_blockquote(): void
    {
        $this->assertSame('blockquote', (new Blockquote())->getType());
    }

    public function test_empty_by_default(): void
    {
        $q = new Blockquote();

        $this->assertTrue($q->isEmpty());
        $this->assertSame(0, $q->count());
        $this->assertSame([], $q->getElements());
    }

    public function test_make_factory(): void
    {
        $this->assertInstanceOf(Blockquote::class, Blockquote::make());
    }

    public function test_add_element_is_fluent(): void
    {
        $q = new Blockquote();
        $p = new Paragraph();
        $p->addRun(new TextRun('quoted'));

        $result = $q->addElement($p);

        $this->assertSame($q, $result);
        $this->assertFalse($q->isEmpty());
        $this->assertSame([$p], $q->getElements());
    }

    public function test_add_text_appends_paragraph(): void
    {
        $q = Blockquote::make();
        $q->addText('first line');
        $q->addText('second line');

        $this->assertSame(2, $q->count());
        foreach ($q->getElements() as $el) {
            $this->assertInstanceOf(Paragraph::class, $el);
        }
        $this->assertSame('first line', $q->getElements()[0]->getPlainText());
    }

    public function test_add_text_with_style(): void
    {
        $q = Blockquote::make();
        $style = TextStyle::make()->setItalic();
        $q->addText('italic', $style);

        /** @var Paragraph $p */
        $p = $q->getElements()[0];
        $this->assertSame($style, $p->getRuns()[0]->getStyle());
    }

    public function test_nested_blockquote_is_allowed(): void
    {
        $outer = Blockquote::make();
        $inner = Blockquote::make();
        $inner->addText('inner quote');

        $outer->addElement($inner);

        $this->assertCount(1, $outer->getElements());
        $this->assertInstanceOf(Blockquote::class, $outer->getElements()[0]);
    }

    public function test_json_serialize_shape(): void
    {
        $q = Blockquote::make();
        $q->addText('hello');

        $json = json_decode(json_encode($q, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('blockquote', $json['type']);
        $this->assertCount(1, $json['elements']);
    }
}
