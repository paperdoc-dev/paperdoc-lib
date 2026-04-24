<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\BlockElementInterface;
use Paperdoc\Contracts\DocumentElementInterface;
use Paperdoc\Document\Heading;
use Paperdoc\Document\TextRun;
use Paperdoc\Document\Style\TextStyle;
use Paperdoc\Exceptions\InvalidDocumentException;

class HeadingTest extends TestCase
{
    public function test_implements_block_element_interface(): void
    {
        $this->assertInstanceOf(BlockElementInterface::class, new Heading());
        $this->assertInstanceOf(DocumentElementInterface::class, new Heading());
    }

    public function test_type_is_heading(): void
    {
        $this->assertSame('heading', (new Heading())->getType());
    }

    public function test_default_level_is_one(): void
    {
        $this->assertSame(1, (new Heading())->getLevel());
    }

    public function test_default_id_is_empty(): void
    {
        $h = new Heading();

        $this->assertSame('', $h->getId());
        $this->assertFalse($h->hasId());
    }

    public function test_make_factory_creates_run_for_text(): void
    {
        $h = Heading::make('Chapter 1', 2, 'ch1');

        $this->assertSame(2, $h->getLevel());
        $this->assertSame('ch1', $h->getId());
        $this->assertTrue($h->hasId());
        $this->assertSame('Chapter 1', $h->getPlainText());
        $this->assertCount(1, $h->getRuns());
    }

    public function test_make_empty_text_creates_no_runs(): void
    {
        $h = Heading::make();

        $this->assertSame([], $h->getRuns());
        $this->assertSame('', $h->getPlainText());
    }

    public function test_make_with_style(): void
    {
        $style = TextStyle::make()->setBold();
        $h     = Heading::make('Title', 1, '', $style);

        $this->assertCount(1, $h->getRuns());
        $this->assertSame($style, $h->getRuns()[0]->getStyle());
    }

    public function test_invalid_level_zero_throws(): void
    {
        $this->expectException(InvalidDocumentException::class);
        new Heading(0);
    }

    public function test_invalid_level_seven_throws(): void
    {
        $this->expectException(InvalidDocumentException::class);
        new Heading(7);
    }

    public function test_set_level_validates(): void
    {
        $h = new Heading();

        $this->expectException(InvalidDocumentException::class);
        $h->setLevel(99);
    }

    public function test_set_level_is_fluent(): void
    {
        $h = new Heading();
        $result = $h->setLevel(3);

        $this->assertSame($h, $result);
        $this->assertSame(3, $h->getLevel());
    }

    public function test_set_id_is_fluent(): void
    {
        $h = new Heading();
        $result = $h->setId('intro');

        $this->assertSame($h, $result);
        $this->assertSame('intro', $h->getId());
    }

    public function test_add_run_and_add_text(): void
    {
        $h = new Heading(2);
        $h->addRun(new TextRun('Section '));
        $h->addText('two');

        $this->assertSame('Section two', $h->getPlainText());
        $this->assertCount(2, $h->getRuns());
    }

    public function test_all_valid_levels_accepted(): void
    {
        foreach (range(Heading::MIN_LEVEL, Heading::MAX_LEVEL) as $level) {
            $h = new Heading($level);
            $this->assertSame($level, $h->getLevel());
        }
    }

    public function test_json_serialize_without_id(): void
    {
        $h = Heading::make('Intro', 1);

        $json = json_decode(json_encode($h, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('heading', $json['type']);
        $this->assertSame(1, $json['level']);
        $this->assertSame('Intro', $json['text']);
        $this->assertCount(1, $json['runs']);
        $this->assertArrayNotHasKey('id', $json);
    }

    public function test_json_serialize_with_id(): void
    {
        $h = Heading::make('Chapter', 2, 'chap1');

        $json = json_decode(json_encode($h, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('chap1', $json['id']);
    }
}
