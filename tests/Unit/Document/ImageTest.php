<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\DocumentElementInterface;
use Paperdoc\Document\Image;

class ImageTest extends TestCase
{
    public function test_implements_element_interface(): void
    {
        $this->assertInstanceOf(DocumentElementInterface::class, new Image('/path.jpg'));
    }

    public function test_type_is_image(): void
    {
        $this->assertSame('image', (new Image('/test.png'))->getType());
    }

    public function test_constructor(): void
    {
        $img = new Image('/logo.png', 200, 100, 'Company logo');

        $this->assertSame('/logo.png', $img->getSrc());
        $this->assertSame(200, $img->getWidth());
        $this->assertSame(100, $img->getHeight());
        $this->assertSame('Company logo', $img->getAlt());
    }

    public function test_constructor_defaults(): void
    {
        $img = new Image('/path.jpg');

        $this->assertSame(0, $img->getWidth());
        $this->assertSame(0, $img->getHeight());
        $this->assertSame('', $img->getAlt());
    }

    public function test_make_factory(): void
    {
        $img = Image::make('/photo.png', 300, 200, 'Photo');

        $this->assertInstanceOf(Image::class, $img);
        $this->assertSame('/photo.png', $img->getSrc());
        $this->assertSame(300, $img->getWidth());
        $this->assertSame(200, $img->getHeight());
    }

    public function test_set_dimensions(): void
    {
        $img = new Image('/img.jpg');
        $result = $img->setDimensions(640, 480);

        $this->assertSame($img, $result);
        $this->assertSame(640, $img->getWidth());
        $this->assertSame(480, $img->getHeight());
    }

    public function test_set_src(): void
    {
        $img = new Image('/old.jpg');
        $result = $img->setSrc('/new.png');

        $this->assertSame($img, $result);
        $this->assertSame('/new.png', $img->getSrc());
    }

    public function test_set_alt(): void
    {
        $img = new Image('/img.jpg');
        $result = $img->setAlt('Description');

        $this->assertSame($img, $result);
        $this->assertSame('Description', $img->getAlt());
    }

    public function test_no_embedded_data_by_default(): void
    {
        $img = new Image('/img.jpg');

        $this->assertFalse($img->hasData());
        $this->assertNull($img->getData());
        $this->assertNull($img->getMimeType());
        $this->assertNull($img->getDataUri());
        $this->assertSame(0, $img->getDataSize());
    }

    public function test_from_data_factory(): void
    {
        $data = 'fake-jpeg-data';
        $img = Image::fromData($data, 'image/jpeg', 320, 240, 'Photo');

        $this->assertTrue($img->hasData());
        $this->assertSame($data, $img->getData());
        $this->assertSame('image/jpeg', $img->getMimeType());
        $this->assertSame(320, $img->getWidth());
        $this->assertSame(240, $img->getHeight());
        $this->assertSame('Photo', $img->getAlt());
        $this->assertSame('embedded.jpg', $img->getSrc());
        $this->assertSame(strlen($data), $img->getDataSize());
    }

    public function test_from_data_mime_type_mapping(): void
    {
        $this->assertSame('embedded.png', Image::fromData('x', 'image/png')->getSrc());
        $this->assertSame('embedded.gif', Image::fromData('x', 'image/gif')->getSrc());
        $this->assertSame('embedded.webp', Image::fromData('x', 'image/webp')->getSrc());
        $this->assertSame('embedded.svg', Image::fromData('x', 'image/svg+xml')->getSrc());
        $this->assertSame('embedded.emf', Image::fromData('x', 'image/x-emf')->getSrc());
        $this->assertSame('embedded.wmf', Image::fromData('x', 'image/x-wmf')->getSrc());
        $this->assertSame('embedded.bin', Image::fromData('x', 'application/octet-stream')->getSrc());
    }

    public function test_data_uri(): void
    {
        $data = 'hello-world';
        $img = Image::fromData($data, 'image/png');

        $expected = 'data:image/png;base64,' . base64_encode($data);
        $this->assertSame($expected, $img->getDataUri());
    }

    public function test_set_data(): void
    {
        $img = new Image('/path.jpg');
        $result = $img->setData('binary-data', 'image/jpeg');

        $this->assertSame($img, $result);
        $this->assertTrue($img->hasData());
        $this->assertSame('binary-data', $img->getData());
        $this->assertSame('image/jpeg', $img->getMimeType());
    }

    public function test_save_to(): void
    {
        $data = 'test-image-content';
        $img = Image::fromData($data, 'image/png');

        $path = sys_get_temp_dir() . '/paperdoc_img_test_' . uniqid() . '.png';

        try {
            $this->assertTrue($img->saveTo($path));
            $this->assertFileExists($path);
            $this->assertSame($data, file_get_contents($path));
        } finally {
            @unlink($path);
        }
    }

    public function test_save_to_returns_false_without_data(): void
    {
        $img = new Image('/path.jpg');

        $this->assertFalse($img->saveTo('/tmp/test.jpg'));
    }
}
