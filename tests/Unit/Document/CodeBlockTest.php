<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\BlockElementInterface;
use Paperdoc\Document\CodeBlock;

class CodeBlockTest extends TestCase
{
    public function test_implements_block_element_interface(): void
    {
        $this->assertInstanceOf(BlockElementInterface::class, new CodeBlock());
    }

    public function test_type_is_code_block(): void
    {
        $this->assertSame('code_block', (new CodeBlock())->getType());
    }

    public function test_defaults_are_empty(): void
    {
        $cb = new CodeBlock();

        $this->assertSame('', $cb->getCode());
        $this->assertSame('', $cb->getLanguage());
        $this->assertFalse($cb->hasLanguage());
    }

    public function test_make_factory(): void
    {
        $cb = CodeBlock::make('echo "hi";', 'php');

        $this->assertSame('echo "hi";', $cb->getCode());
        $this->assertSame('php', $cb->getLanguage());
        $this->assertTrue($cb->hasLanguage());
    }

    public function test_set_code_is_fluent(): void
    {
        $cb = new CodeBlock();
        $result = $cb->setCode('x = 1');

        $this->assertSame($cb, $result);
        $this->assertSame('x = 1', $cb->getCode());
    }

    public function test_set_language_is_fluent(): void
    {
        $cb = new CodeBlock();
        $result = $cb->setLanguage('python');

        $this->assertSame($cb, $result);
        $this->assertSame('python', $cb->getLanguage());
    }

    public function test_append_line_builds_code(): void
    {
        $cb = new CodeBlock();
        $cb->appendLine('def foo():')
           ->appendLine('    return 42');

        $this->assertSame("def foo():\n    return 42", $cb->getCode());
    }

    public function test_append_line_on_empty_skips_leading_newline(): void
    {
        $cb = new CodeBlock();
        $cb->appendLine('first');

        $this->assertSame('first', $cb->getCode());
    }

    public function test_get_lines_splits_code(): void
    {
        $cb = new CodeBlock("one\ntwo\nthree");

        $this->assertSame(['one', 'two', 'three'], $cb->getLines());
    }

    public function test_get_lines_handles_crlf(): void
    {
        $cb = new CodeBlock("one\r\ntwo\rthree\nfour");

        $this->assertSame(['one', 'two', 'three', 'four'], $cb->getLines());
    }

    public function test_get_lines_empty_returns_empty_array(): void
    {
        $this->assertSame([], (new CodeBlock())->getLines());
    }

    public function test_json_serialize_without_language(): void
    {
        $cb = CodeBlock::make('var x = 1;');

        $json = json_decode(json_encode($cb, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('code_block', $json['type']);
        $this->assertSame('var x = 1;', $json['code']);
        $this->assertArrayNotHasKey('language', $json);
    }

    public function test_json_serialize_with_language(): void
    {
        $cb = CodeBlock::make('let x = 1', 'typescript');

        $json = json_decode(json_encode($cb, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('typescript', $json['language']);
    }
}
