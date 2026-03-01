<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\Section;

class SectionOcrTest extends TestCase
{
    public function test_clear_elements(): void
    {
        $section = new Section('test');
        $section->addText('Hello');
        $section->addText('World');

        $this->assertCount(2, $section->getElements());

        $section->clearElements();

        $this->assertCount(0, $section->getElements());
    }

    public function test_metadata(): void
    {
        $section = new Section('test');

        $this->assertSame([], $section->getMetadata());

        $section->setMetadata('ocr_processed', true);
        $section->setMetadata('ocr_confidence', 0.95);

        $this->assertTrue($section->getMetadata()['ocr_processed']);
        $this->assertSame(0.95, $section->getMetadata()['ocr_confidence']);
    }

    public function test_json_serialize_includes_metadata(): void
    {
        $section = new Section('test');
        $section->setMetadata('ocr_processed', true);

        $json = $section->jsonSerialize();

        $this->assertArrayHasKey('metadata', $json);
        $this->assertTrue($json['metadata']['ocr_processed']);
    }

    public function test_json_serialize_omits_empty_metadata(): void
    {
        $section = new Section('test');

        $json = $section->jsonSerialize();

        $this->assertArrayNotHasKey('metadata', $json);
    }
}
