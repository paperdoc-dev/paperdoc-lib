<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Llm;

use PHPUnit\Framework\TestCase;
use Paperdoc\Llm\Dto\PageContent;

class DtoPageContentTest extends TestCase
{
    public function test_default_values(): void
    {
        $dto = new PageContent();

        $this->assertSame('', $dto->title);
        $this->assertSame([], $dto->paragraphs);
        $this->assertSame([], $dto->tables);
        $this->assertSame(0.0, $dto->confidence);
    }

    public function test_can_set_properties(): void
    {
        $dto = new PageContent();
        $dto->title = 'Test Title';
        $dto->paragraphs = ['First paragraph.', 'Second paragraph.'];
        $dto->tables = [[['A', 'B'], ['1', '2']]];
        $dto->confidence = 0.95;

        $this->assertSame('Test Title', $dto->title);
        $this->assertCount(2, $dto->paragraphs);
        $this->assertCount(1, $dto->tables);
        $this->assertSame(0.95, $dto->confidence);
    }
}
