<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document\Style;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\Style\RunningElement;
use Paperdoc\Document\Style\TextStyle;
use Paperdoc\Enum\Alignment;

class RunningElementTest extends TestCase
{
    public function test_defaults(): void
    {
        $element = new RunningElement();

        $this->assertSame('', $element->getTemplate());
        $this->assertSame(Alignment::CENTER, $element->getAlignment());
        $this->assertSame(30.0, $element->getHeight());
        $this->assertInstanceOf(TextStyle::class, $element->getStyle());
        $this->assertSame(9.0, $element->getStyle()->getFontSize());
        $this->assertSame('#6B7280', $element->getStyle()->getColor());
    }

    public function test_make_factory(): void
    {
        $element = RunningElement::make('Page {page} / {pages}');

        $this->assertSame('Page {page} / {pages}', $element->getTemplate());
    }

    public function test_fluent_setters(): void
    {
        $style = TextStyle::make()->setFontSize(12)->setBold();

        $element = RunningElement::make()
            ->setTemplate('{title}')
            ->setAlignment(Alignment::RIGHT)
            ->setStyle($style)
            ->setHeight(40.0);

        $this->assertSame('{title}', $element->getTemplate());
        $this->assertSame(Alignment::RIGHT, $element->getAlignment());
        $this->assertSame($style, $element->getStyle());
        $this->assertSame(40.0, $element->getHeight());
    }

    public function test_resolve_page_placeholder(): void
    {
        $element = RunningElement::make('Page {page} of {pages}');

        $this->assertSame('Page 3 of 7', $element->resolve(3, 7));
    }

    public function test_resolve_title_placeholder(): void
    {
        $element = RunningElement::make('{title} — {page}');

        $this->assertSame('Brochure — 1', $element->resolve(1, 1, 'Brochure'));
    }

    public function test_resolve_date_placeholder_format(): void
    {
        $element = RunningElement::make('{date}');

        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}$/',
            $element->resolve(1, 1),
        );
    }

    public function test_resolve_datetime_placeholder_format(): void
    {
        $element = RunningElement::make('{datetime}');

        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/',
            $element->resolve(1, 1),
        );
    }

    public function test_resolve_unknown_placeholder_kept_as_is(): void
    {
        $element = RunningElement::make('Section {chapter} — {page}');

        $this->assertSame('Section {chapter} — 4', $element->resolve(4, 10));
    }

    public function test_json_serialization(): void
    {
        $element = RunningElement::make('Page {page}')
            ->setAlignment(Alignment::RIGHT)
            ->setHeight(25.0);

        $json = json_decode(json_encode($element), true);

        $this->assertSame('Page {page}', $json['template']);
        $this->assertSame('right', $json['alignment']);
        $this->assertEquals(25.0, $json['height']);
        $this->assertIsArray($json['style']);
    }
}
