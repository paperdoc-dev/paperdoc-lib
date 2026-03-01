<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\Section;
use Paperdoc\Document\Paragraph;
use Paperdoc\Document\TextRun;
use Paperdoc\Document\Table;
use Paperdoc\Document\Image;
use Paperdoc\Document\PageBreak;
use Paperdoc\Document\Style\TextStyle;

class SectionTest extends TestCase
{
    public function test_constructor_default_name(): void
    {
        $section = new Section();

        $this->assertSame('', $section->getName());
    }

    public function test_constructor_with_name(): void
    {
        $section = new Section('introduction');

        $this->assertSame('introduction', $section->getName());
    }

    public function test_make_factory(): void
    {
        $section = Section::make('chapitre-1');

        $this->assertInstanceOf(Section::class, $section);
        $this->assertSame('chapitre-1', $section->getName());
    }

    public function test_set_name_is_fluent(): void
    {
        $section = new Section();
        $result = $section->setName('nouveau');

        $this->assertSame($section, $result);
        $this->assertSame('nouveau', $section->getName());
    }

    public function test_add_element(): void
    {
        $section = new Section();
        $paragraph = new Paragraph();
        $paragraph->addRun(new TextRun('Bonjour'));

        $result = $section->addElement($paragraph);

        $this->assertSame($section, $result);
        $this->assertCount(1, $section->getElements());
        $this->assertSame($paragraph, $section->getElements()[0]);
    }

    public function test_add_mixed_elements(): void
    {
        $section = new Section();
        $section->addElement(new Paragraph());
        $section->addElement(new Table());
        $section->addElement(new Image('/path.jpg'));

        $elements = $section->getElements();
        $this->assertCount(3, $elements);
        $this->assertInstanceOf(Paragraph::class, $elements[0]);
        $this->assertInstanceOf(Table::class, $elements[1]);
        $this->assertInstanceOf(Image::class, $elements[2]);
    }

    public function test_add_text_shortcut(): void
    {
        $section = new Section();
        $paragraph = $section->addText('Hello world');

        $this->assertInstanceOf(Paragraph::class, $paragraph);
        $this->assertCount(1, $section->getElements());
        $this->assertSame('Hello world', $paragraph->getPlainText());
    }

    public function test_add_text_with_style(): void
    {
        $section = new Section();
        $style = TextStyle::make()->setBold()->setColor('#FF0000');
        $paragraph = $section->addText('Important', $style);

        $runs = $paragraph->getRuns();
        $this->assertCount(1, $runs);
        $this->assertTrue($runs[0]->getStyle()->isBold());
        $this->assertSame('#FF0000', $runs[0]->getStyle()->getColor());
    }

    public function test_add_heading_level_1(): void
    {
        $section = new Section();
        $paragraph = $section->addHeading('Titre Principal', 1);

        $runs = $paragraph->getRuns();
        $this->assertCount(1, $runs);
        $this->assertSame('Titre Principal', $runs[0]->getText());
        $this->assertSame(24.0, $runs[0]->getStyle()->getFontSize());
        $this->assertTrue($runs[0]->getStyle()->isBold());
        $this->assertSame(1, $paragraph->getStyle()->getHeadingLevel());
    }

    public function test_add_heading_level_2(): void
    {
        $section = new Section();
        $paragraph = $section->addHeading('Sous-titre', 2);

        $this->assertSame(20.0, $paragraph->getRuns()[0]->getStyle()->getFontSize());
        $this->assertSame(2, $paragraph->getStyle()->getHeadingLevel());
    }

    public function test_add_heading_level_3(): void
    {
        $section = new Section();
        $paragraph = $section->addHeading('Section', 3);

        $this->assertSame(16.0, $paragraph->getRuns()[0]->getStyle()->getFontSize());
        $this->assertSame(3, $paragraph->getStyle()->getHeadingLevel());
    }

    public function test_add_heading_level_4_and_above(): void
    {
        $section = new Section();
        $p4 = $section->addHeading('H4', 4);
        $p5 = $section->addHeading('H5', 5);

        $this->assertSame(14.0, $p4->getRuns()[0]->getStyle()->getFontSize());
        $this->assertSame(14.0, $p5->getRuns()[0]->getStyle()->getFontSize());
        $this->assertSame(4, $p4->getStyle()->getHeadingLevel());
        $this->assertSame(5, $p5->getStyle()->getHeadingLevel());
    }

    public function test_add_page_break(): void
    {
        $section = new Section();
        $result = $section->addPageBreak();

        $this->assertSame($section, $result);
        $this->assertCount(1, $section->getElements());
        $this->assertInstanceOf(PageBreak::class, $section->getElements()[0]);
    }

    public function test_page_break_type(): void
    {
        $pb = new PageBreak();

        $this->assertSame('page_break', $pb->getType());
    }

    public function test_empty_section_has_no_elements(): void
    {
        $section = new Section();

        $this->assertEmpty($section->getElements());
    }
}
