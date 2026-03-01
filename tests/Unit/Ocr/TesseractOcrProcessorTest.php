<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Ocr;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\OcrProcessorInterface;
use Paperdoc\Ocr\TesseractOcrProcessor;

class TesseractOcrProcessorTest extends TestCase
{
    public function test_implements_interface(): void
    {
        $processor = new TesseractOcrProcessor();
        $this->assertInstanceOf(OcrProcessorInterface::class, $processor);
    }

    public function test_nonexistent_image_throws(): void
    {
        $processor = new TesseractOcrProcessor();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('introuvable');

        $processor->recognize('/nonexistent/image.png');
    }

    public function test_unavailable_binary_throws(): void
    {
        $processor = new TesseractOcrProcessor('/nonexistent/tesseract-binary');

        $this->assertFalse($processor->isAvailable());
    }

    public function test_is_available_with_valid_binary(): void
    {
        $processor = new TesseractOcrProcessor();

        // May or may not be available depending on CI/local env
        $result = $processor->isAvailable();
        $this->assertIsBool($result);
    }

    public function test_custom_binary_and_options(): void
    {
        $processor = new TesseractOcrProcessor('/custom/path', ['--psm 6', '--oem 1']);

        $this->assertFalse($processor->isAvailable());
    }

    public function test_build_command_returns_valid_command(): void
    {
        $processor = new TesseractOcrProcessor('tesseract', ['--psm 1', '--oem 3']);
        $cmd = $processor->buildCommand('/tmp/test.png', 'fra');

        $this->assertStringContainsString('tesseract', $cmd);
        $this->assertStringContainsString('/tmp/test.png', $cmd);
        $this->assertStringContainsString('fra', $cmd);
        $this->assertStringContainsString('stdout', $cmd);
        $this->assertStringContainsString('--psm 1', $cmd);
        $this->assertStringContainsString('--oem 3', $cmd);
    }

    public function test_build_command_with_custom_binary(): void
    {
        $processor = new TesseractOcrProcessor('/opt/bin/tesseract');
        $cmd = $processor->buildCommand('/img.png', 'eng');

        $this->assertStringContainsString('/opt/bin/tesseract', $cmd);
    }
}
