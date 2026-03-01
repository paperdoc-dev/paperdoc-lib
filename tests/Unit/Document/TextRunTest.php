<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\TextRun;
use Paperdoc\Document\Style\TextStyle;

class TextRunTest extends TestCase
{
    public function test_constructor(): void
    {
        $run = new TextRun('Bonjour');

        $this->assertSame('Bonjour', $run->getText());
        $this->assertNull($run->getStyle());
    }

    public function test_constructor_with_style(): void
    {
        $style = TextStyle::make()->setBold();
        $run = new TextRun('Gras', $style);

        $this->assertSame('Gras', $run->getText());
        $this->assertSame($style, $run->getStyle());
    }

    public function test_make_factory(): void
    {
        $run = TextRun::make('Factory');

        $this->assertInstanceOf(TextRun::class, $run);
        $this->assertSame('Factory', $run->getText());
    }

    public function test_make_factory_with_style(): void
    {
        $style = TextStyle::make()->setItalic();
        $run = TextRun::make('Styled', $style);

        $this->assertSame('Styled', $run->getText());
        $this->assertTrue($run->getStyle()->isItalic());
    }

    public function test_set_text(): void
    {
        $run = new TextRun('Original');
        $result = $run->setText('Modifié');

        $this->assertSame($run, $result);
        $this->assertSame('Modifié', $run->getText());
    }

    public function test_set_style(): void
    {
        $run = new TextRun('Test');
        $style = TextStyle::make()->setColor('#FF0000');

        $result = $run->setStyle($style);

        $this->assertSame($run, $result);
        $this->assertSame('#FF0000', $run->getStyle()->getColor());
    }

    public function test_empty_text(): void
    {
        $run = new TextRun('');

        $this->assertSame('', $run->getText());
    }

    public function test_unicode_text(): void
    {
        $run = new TextRun('Héllo Wörld 日本語');

        $this->assertSame('Héllo Wörld 日本語', $run->getText());
    }

    public function test_special_characters(): void
    {
        $run = new TextRun('Prix: 120 000 € (+12%)');

        $this->assertSame('Prix: 120 000 € (+12%)', $run->getText());
    }
}
