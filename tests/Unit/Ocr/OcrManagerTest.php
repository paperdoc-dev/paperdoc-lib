<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Ocr;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\OcrProcessorInterface;
use Paperdoc\Document\{Image, Paragraph, Section, TextRun};
use Paperdoc\Ocr\OcrManager;

class OcrManagerTest extends TestCase
{
    private function createMockProcessor(string $returnText = 'OCR result'): OcrProcessorInterface
    {
        $mock = $this->createMock(OcrProcessorInterface::class);
        $mock->method('recognize')->willReturn($returnText);
        $mock->method('isAvailable')->willReturn(true);
        $mock->method('detectScript')->willReturn(null);
        $mock->method('buildCommand')->willReturn('echo ' . escapeshellarg($returnText));

        return $mock;
    }

    public function test_needs_ocr_returns_false_for_empty_section(): void
    {
        $manager = new OcrManager($this->createMockProcessor());
        $section = new Section('test');

        $this->assertFalse($manager->needsOcr($section));
    }

    public function test_needs_ocr_returns_false_for_text_only(): void
    {
        $manager = new OcrManager($this->createMockProcessor());
        $section = new Section('test');
        $section->addText('Hello world');
        $section->addText('Another paragraph');

        $this->assertFalse($manager->needsOcr($section));
    }

    public function test_needs_ocr_returns_true_for_image_only(): void
    {
        $manager = new OcrManager($this->createMockProcessor());
        $section = new Section('test');
        $section->addElement(Image::fromData('fake-image-data', 'image/png', 100, 100));

        $this->assertTrue($manager->needsOcr($section));
    }

    public function test_needs_ocr_returns_false_when_enough_text(): void
    {
        $manager = new OcrManager($this->createMockProcessor(), 'fra', 0.1);
        $section = new Section('test');

        for ($i = 0; $i < 9; $i++) {
            $section->addText("Paragraph {$i}");
        }
        $section->addElement(Image::fromData('fake', 'image/png', 100, 100));

        $this->assertFalse($manager->needsOcr($section));
    }

    public function test_needs_ocr_respects_custom_ratio(): void
    {
        $manager = new OcrManager($this->createMockProcessor(), 'fra', 0.8);
        $section = new Section('test');

        $section->addText('Some text');
        $section->addElement(Image::fromData('fake', 'image/png', 100, 100));

        // 1 text out of 2 elements = 0.5 ratio, below 0.8 threshold
        $this->assertTrue($manager->needsOcr($section));
    }

    public function test_process_image_creates_temp_file_and_cleans_up(): void
    {
        $processor = $this->createMock(OcrProcessorInterface::class);
        $processor->method('isAvailable')->willReturn(true);
        $processor->expects($this->once())
            ->method('recognize')
            ->willReturnCallback(function (string $path) {
                $this->assertFileExists($path);
                $this->assertStringEndsWith('.png', $path);

                return 'recognized text';
            });

        $manager = new OcrManager($processor);

        $pngData = $this->createMinimalPng();
        $image = Image::fromData($pngData, 'image/png', 1, 1);

        $result = $manager->processImage($image);
        $this->assertSame('recognized text', $result);
    }

    public function test_process_image_skips_when_no_data(): void
    {
        $manager = new OcrManager($this->createMockProcessor());
        $image = Image::make('no-data.png', 100, 100);

        $this->assertSame('', $manager->processImage($image));
    }

    public function test_process_section_collects_all_image_texts(): void
    {
        $processor = $this->createMock(OcrProcessorInterface::class);
        $processor->method('isAvailable')->willReturn(true);
        $processor->method('detectScript')->willReturn(null);
        $processor->method('buildCommand')->willReturnOnConsecutiveCalls(
            'echo "Formation permanente en informatique"',
            'echo "Campus Numérique Francophone"',
        );

        $manager = new OcrManager($processor, 'fra');
        $section = new Section('test');

        $pngData = $this->createMinimalPng();
        $section->addElement(Image::fromData($pngData, 'image/png', 1, 1));
        $section->addText('in between');
        $section->addElement(Image::fromData($pngData, 'image/png', 1, 1));

        $result = $manager->processSection($section);
        $this->assertSame("Formation permanente en informatique\n\nCampus Numérique Francophone", $result);
    }

    public function test_save_image_to_temp_preserves_extension(): void
    {
        $manager = new OcrManager($this->createMockProcessor());

        $jpegImage = Image::fromData('fake-jpeg', 'image/jpeg', 10, 10);
        $path = $manager->saveImageToTemp($jpegImage);
        $this->assertStringEndsWith('.jpg', $path);
        $this->assertFileExists($path);
        @unlink($path);

        $pngImage = Image::fromData('fake-png', 'image/png', 10, 10);
        $path = $manager->saveImageToTemp($pngImage);
        $this->assertStringEndsWith('.png', $path);
        $this->assertFileExists($path);
        @unlink($path);
    }

    private function createMinimalPng(): string
    {
        // 1x1 transparent PNG
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
        );
    }
}
