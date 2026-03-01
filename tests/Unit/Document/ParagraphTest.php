<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\DocumentElementInterface;
use Paperdoc\Document\Paragraph;
use Paperdoc\Document\TextRun;
use Paperdoc\Document\Style\ParagraphStyle;
use Paperdoc\Enum\Alignment;

class ParagraphTest extends TestCase
{
    public function test_implements_element_interface(): void
    {
        $this->assertInstanceOf(DocumentElementInterface::class, new Paragraph());
    }

    public function test_type_is_paragraph(): void
    {
        $this->assertSame('paragraph', (new Paragraph())->getType());
    }

    public function test_make_factory(): void
    {
        $p = Paragraph::make();

        $this->assertInstanceOf(Paragraph::class, $p);
        $this->assertNull($p->getStyle());
    }

    public function test_make_factory_with_style(): void
    {
        $style = ParagraphStyle::make()->setAlignment(Alignment::CENTER);
        $p = Paragraph::make($style);

        $this->assertSame($style, $p->getStyle());
    }

    public function test_add_run(): void
    {
        $p = new Paragraph();
        $run = new TextRun('Hello');

        $result = $p->addRun($run);

        $this->assertSame($p, $result);
        $this->assertCount(1, $p->getRuns());
        $this->assertSame($run, $p->getRuns()[0]);
    }

    public function test_add_multiple_runs(): void
    {
        $p = new Paragraph();
        $p->addRun(new TextRun('Hello '))
          ->addRun(new TextRun('world'))
          ->addRun(new TextRun('!'));

        $this->assertCount(3, $p->getRuns());
    }

    public function test_get_plain_text_single_run(): void
    {
        $p = new Paragraph();
        $p->addRun(new TextRun('Simple text'));

        $this->assertSame('Simple text', $p->getPlainText());
    }

    public function test_get_plain_text_multiple_runs(): void
    {
        $p = new Paragraph();
        $p->addRun(new TextRun('Les '));
        $p->addRun(new TextRun('résultats '));
        $p->addRun(new TextRun('clés'));

        $this->assertSame('Les résultats clés', $p->getPlainText());
    }

    public function test_get_plain_text_empty(): void
    {
        $p = new Paragraph();

        $this->assertSame('', $p->getPlainText());
    }

    public function test_set_style(): void
    {
        $p = new Paragraph();
        $style = ParagraphStyle::make()->setAlignment(Alignment::JUSTIFY);

        $result = $p->setStyle($style);

        $this->assertSame($p, $result);
        $this->assertSame(Alignment::JUSTIFY, $p->getStyle()->getAlignment());
    }

    public function test_default_style_is_null(): void
    {
        $p = new Paragraph();

        $this->assertNull($p->getStyle());
    }
}
