<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\BlockElementInterface;
use Paperdoc\Document\Paragraph;
use Paperdoc\Document\TextRun;
use Paperdoc\Document\TextZone;
use Paperdoc\Document\Style\TextStyle;

class TextZoneTest extends TestCase
{
    public function test_implements_block_element_interface(): void
    {
        $this->assertInstanceOf(BlockElementInterface::class, new TextZone());
    }

    public function test_default_geometry(): void
    {
        $zone = new TextZone();

        $this->assertSame(0.0, $zone->getX());
        $this->assertSame(0.0, $zone->getY());
        $this->assertSame(200.0, $zone->getWidth());
        $this->assertSame(100.0, $zone->getHeight());
        $this->assertSame('text_zone', $zone->getType());
    }

    public function test_constructor_assigns_geometry(): void
    {
        $zone = new TextZone(50.0, 80.0, 300.0, 120.0);

        $this->assertSame(50.0, $zone->getX());
        $this->assertSame(80.0, $zone->getY());
        $this->assertSame(300.0, $zone->getWidth());
        $this->assertSame(120.0, $zone->getHeight());
    }

    public function test_make_factory(): void
    {
        $zone = TextZone::make(10.0, 20.0, 100.0, 50.0);

        $this->assertSame(10.0, $zone->getX());
        $this->assertSame(20.0, $zone->getY());
    }

    public function test_at_factory_alias(): void
    {
        $zone = TextZone::at(5.0, 15.0, 250.0, 75.0);

        $this->assertSame(5.0, $zone->getX());
        $this->assertSame(75.0, $zone->getHeight());
    }

    public function test_set_position_is_fluent(): void
    {
        $zone = new TextZone();
        $result = $zone->setPosition(40.0, 60.0);

        $this->assertSame($zone, $result);
        $this->assertSame(40.0, $zone->getX());
        $this->assertSame(60.0, $zone->getY());
    }

    public function test_set_size_is_fluent(): void
    {
        $zone = new TextZone();
        $zone->setSize(400.0, 200.0);

        $this->assertSame(400.0, $zone->getWidth());
        $this->assertSame(200.0, $zone->getHeight());
    }

    public function test_padding_default_is_zero(): void
    {
        $this->assertSame(0.0, (new TextZone())->getPadding());
    }

    public function test_set_padding(): void
    {
        $zone = (new TextZone())->setPadding(8.0);

        $this->assertSame(8.0, $zone->getPadding());
    }

    public function test_decoration(): void
    {
        $zone = (new TextZone())
            ->setBackgroundColor('#000000')
            ->setBorder('#FF0000', 2.0);

        $this->assertSame('#000000', $zone->getBackgroundColor());
        $this->assertSame('#FF0000', $zone->getBorderColor());
        $this->assertSame(2.0, $zone->getBorderWidth());
    }

    public function test_add_paragraph_appends(): void
    {
        $zone = new TextZone();
        $paragraph = new Paragraph();
        $paragraph->addRun(new TextRun('hello'));

        $zone->addParagraph($paragraph);

        $this->assertCount(1, $zone->getParagraphs());
        $this->assertSame($paragraph, $zone->getParagraphs()[0]);
    }

    public function test_add_text_creates_paragraph_with_run(): void
    {
        $zone = new TextZone();
        $style = TextStyle::make()->setBold();

        $paragraph = $zone->addText('Bonjour', $style);

        $this->assertInstanceOf(Paragraph::class, $paragraph);
        $this->assertCount(1, $zone->getParagraphs());
        $this->assertSame('Bonjour', $paragraph->getRuns()[0]->getText());
        $this->assertTrue($paragraph->getRuns()[0]->getStyle()->isBold());
    }

    public function test_clear_removes_all_paragraphs(): void
    {
        $zone = new TextZone();
        $zone->addText('a');
        $zone->addText('b');
        $zone->clear();

        $this->assertEmpty($zone->getParagraphs());
    }

    public function test_overflow_default_is_clip(): void
    {
        $this->assertSame(TextZone::OVERFLOW_CLIP, (new TextZone())->getOverflow());
    }

    public function test_set_overflow_modes(): void
    {
        $zone = new TextZone();

        $this->assertSame(TextZone::OVERFLOW_ELLIPSIS, $zone->setOverflow(TextZone::OVERFLOW_ELLIPSIS)->getOverflow());
        $this->assertSame(TextZone::OVERFLOW_VISIBLE, $zone->setOverflow(TextZone::OVERFLOW_VISIBLE)->getOverflow());
        $this->assertSame(TextZone::OVERFLOW_CLIP, $zone->setOverflow(TextZone::OVERFLOW_CLIP)->getOverflow());
    }

    public function test_set_overflow_invalid_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new TextZone())->setOverflow('infinite');
    }

    public function test_json_serialization(): void
    {
        $zone = TextZone::at(10.0, 20.0, 300.0, 100.0)
            ->setPadding(4.0)
            ->setBackgroundColor('#EEE')
            ->setBorder('#000', 1.0);
        $zone->addText('hello');

        $json = json_decode(json_encode($zone), true);

        $this->assertSame('text_zone', $json['type']);
        $this->assertEquals(10.0, $json['x']);
        $this->assertEquals(20.0, $json['y']);
        $this->assertEquals(300.0, $json['width']);
        $this->assertEquals(100.0, $json['height']);
        $this->assertEquals(4.0, $json['padding']);
        $this->assertSame('#EEE', $json['backgroundColor']);
        $this->assertSame('#000', $json['borderColor']);
        $this->assertEquals(1.0, $json['borderWidth']);
        $this->assertCount(1, $json['paragraphs']);
    }
}
