<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\BlockElementInterface;
use Paperdoc\Document\Bookmark;
use Paperdoc\Exceptions\InvalidDocumentException;

class BookmarkTest extends TestCase
{
    public function test_implements_block_element_interface(): void
    {
        $this->assertInstanceOf(BlockElementInterface::class, new Bookmark('anchor'));
    }

    public function test_type_is_bookmark(): void
    {
        $this->assertSame('bookmark', (new Bookmark('anchor'))->getType());
    }

    public function test_make_factory(): void
    {
        $b = Bookmark::make('intro');

        $this->assertInstanceOf(Bookmark::class, $b);
        $this->assertSame('intro', $b->getId());
    }

    public function test_empty_id_throws(): void
    {
        $this->expectException(InvalidDocumentException::class);
        $this->expectExceptionMessage('Bookmark id cannot be empty');

        new Bookmark('');
    }

    public function test_whitespace_only_id_throws(): void
    {
        $this->expectException(InvalidDocumentException::class);
        new Bookmark("   \t\n  ");
    }

    public function test_set_id_validates(): void
    {
        $b = new Bookmark('initial');

        $this->expectException(InvalidDocumentException::class);
        $b->setId('');
    }

    public function test_set_id_is_fluent(): void
    {
        $b = new Bookmark('initial');
        $result = $b->setId('renamed');

        $this->assertSame($b, $result);
        $this->assertSame('renamed', $b->getId());
    }

    public function test_json_serialize_shape(): void
    {
        $b = Bookmark::make('anchor-42');

        $json = json_decode(json_encode($b, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(['type' => 'bookmark', 'id' => 'anchor-42'], $json);
    }
}
