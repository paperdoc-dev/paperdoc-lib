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
use Paperdoc\Document\TextZone;
use Paperdoc\Document\HorizontalRule;
use Paperdoc\Document\Style\PageSetup;
use Paperdoc\Document\Style\RunningElement;
use Paperdoc\Document\Style\TextStyle;
use Paperdoc\Enum\{PageSize, VerticalAlignment};

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

    public function test_add_paragraph_alias_matches_add_text(): void
    {
        $section = new Section();
        $paragraph = $section->addParagraph('Hello world');

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

    /* -------------------------------------------------------------
     | Shortcuts introduced in v0.4.0
     |------------------------------------------------------------- */

    public function test_add_bullet_list_appends_and_returns_list(): void
    {
        $section = new Section();

        $list = $section->addBulletList();

        $this->assertInstanceOf(\Paperdoc\Document\ListBlock::class, $list);
        $this->assertTrue($list->isBullet());
        $this->assertSame([$list], $section->getElements());
    }

    public function test_add_ordered_list_uses_start(): void
    {
        $section = new Section();

        $list = $section->addOrderedList(7);

        $this->assertTrue($list->isOrdered());
        $this->assertSame(7, $list->getStart());
    }

    public function test_add_list_with_explicit_style(): void
    {
        $section = new Section();

        $list = $section->addList(\Paperdoc\Document\ListBlock::STYLE_ORDERED, 2);

        $this->assertTrue($list->isOrdered());
        $this->assertSame(2, $list->getStart());
    }

    public function test_add_bookmark_appends_and_returns_bookmark(): void
    {
        $section = new Section();

        $b = $section->addBookmark('anchor-1');

        $this->assertInstanceOf(\Paperdoc\Document\Bookmark::class, $b);
        $this->assertSame('anchor-1', $b->getId());
        $this->assertSame([$b], $section->getElements());
    }

    public function test_add_code_block_appends_and_returns_code_block(): void
    {
        $section = new Section();

        $cb = $section->addCodeBlock('echo "hi";', 'php');

        $this->assertInstanceOf(\Paperdoc\Document\CodeBlock::class, $cb);
        $this->assertSame('echo "hi";', $cb->getCode());
        $this->assertSame('php', $cb->getLanguage());
        $this->assertSame([$cb], $section->getElements());
    }

    public function test_add_blockquote_appends_and_returns_blockquote(): void
    {
        $section = new Section();

        $q = $section->addBlockquote();
        $q->addText('quoted');

        $this->assertInstanceOf(\Paperdoc\Document\Blockquote::class, $q);
        $this->assertSame([$q], $section->getElements());
        $this->assertSame('quoted', $q->getElements()[0]->getPlainText());
    }

    /* -------------------------------------------------------------
     | Page setup (size, padding, background) — v0.5.0
     |------------------------------------------------------------- */

    public function test_page_setup_is_null_by_default(): void
    {
        $this->assertNull((new Section())->getPageSetup());
    }

    public function test_set_page_setup_replaces_current(): void
    {
        $section = new Section();
        $setup = PageSetup::fromSize(PageSize::A4);

        $section->setPageSetup($setup);

        $this->assertSame($setup, $section->getPageSetup());
    }

    public function test_set_page_size_creates_setup_lazily(): void
    {
        $section = new Section();

        $section->setPageSize(PageSize::A5);

        $this->assertNotNull($section->getPageSetup());
        $this->assertSame(PageSize::A5, $section->getPageSetup()->getSize());
    }

    public function test_set_page_dimensions_uses_custom_values(): void
    {
        $section = new Section();

        $section->setPageDimensions(800.0, 600.0);

        $this->assertSame(800.0, $section->getPageSetup()->getWidth());
        $this->assertSame(600.0, $section->getPageSetup()->getHeight());
    }

    public function test_set_page_padding_propagates(): void
    {
        $section = new Section();

        $section->setPagePadding(10.0, 20.0);

        $this->assertSame(10.0, $section->getPageSetup()->getPaddingTop());
        $this->assertSame(20.0, $section->getPageSetup()->getPaddingRight());
    }

    public function test_set_page_background_color_propagates(): void
    {
        $section = new Section();

        $section->setPageBackgroundColor('#F0F0F0');

        $this->assertSame('#F0F0F0', $section->getPageSetup()->getBackgroundColor());
    }

    public function test_set_page_background_image_propagates(): void
    {
        $section = new Section();
        $image = Image::make('/cover.jpg');

        $section->setPageBackgroundImage($image);

        $this->assertSame($image, $section->getPageSetup()->getBackgroundImage());
    }

    public function test_page_setup_chain_is_fluent(): void
    {
        $section = new Section();

        $result = $section
            ->setPageSize(PageSize::A4)
            ->setPagePadding(20.0)
            ->setPageBackgroundColor('#FFF');

        $this->assertSame($section, $result);
    }

    public function test_page_setup_serialised_when_present(): void
    {
        $section = new Section('cover');
        $section->setPageSize(PageSize::A4);

        $json = json_decode(json_encode($section), true);

        $this->assertArrayHasKey('pageSetup', $json);
        $this->assertSame('a4', $json['pageSetup']['size']);
    }

    public function test_page_setup_omitted_from_json_when_null(): void
    {
        $section = new Section('plain');

        $json = json_decode(json_encode($section), true);

        $this->assertArrayNotHasKey('pageSetup', $json);
    }

    /* -------------------------------------------------------------
     | TextZone shortcut — v0.5.0
     |------------------------------------------------------------- */

    public function test_add_text_zone_appends_and_returns_zone(): void
    {
        $section = new Section();

        $zone = $section->addTextZone(40.0, 60.0, 200.0, 80.0);

        $this->assertInstanceOf(TextZone::class, $zone);
        $this->assertSame(40.0, $zone->getX());
        $this->assertSame(60.0, $zone->getY());
        $this->assertSame(200.0, $zone->getWidth());
        $this->assertSame(80.0, $zone->getHeight());
        $this->assertSame([$zone], $section->getElements());
    }

    public function test_add_text_zone_default_values(): void
    {
        $section = new Section();

        $zone = $section->addTextZone();

        $this->assertSame(0.0, $zone->getX());
        $this->assertSame(0.0, $zone->getY());
    }

    /* -------------------------------------------------------------
     | Per-section header / footer override — v0.8.0 (A3)
     |------------------------------------------------------------- */

    public function test_resolve_header_falls_back_to_document_when_section_has_none(): void
    {
        $section = new Section();
        $docHeader = RunningElement::make('Doc');

        $this->assertSame($docHeader, $section->resolveHeader($docHeader));
    }

    public function test_set_header_overrides_document_header(): void
    {
        $section   = new Section();
        $docHeader = RunningElement::make('Doc');
        $sectionHeader = RunningElement::make('Section override');

        $section->setHeader($sectionHeader);

        $this->assertSame($sectionHeader, $section->resolveHeader($docHeader));
    }

    public function test_hide_header_returns_null_even_when_document_has_one(): void
    {
        $section = new Section();
        $section->hideHeader();

        $this->assertNull($section->resolveHeader(RunningElement::make('Doc')));
        $this->assertTrue($section->isHeaderHidden());
    }

    public function test_set_header_after_hide_unhides(): void
    {
        $section = new Section();
        $section->hideHeader();
        $section->setHeader(RunningElement::make('back'));

        $this->assertNotNull($section->resolveHeader(null));
        $this->assertFalse($section->isHeaderHidden());
    }

    public function test_hide_footer_independent_from_header(): void
    {
        $section = new Section();
        $docHeader = RunningElement::make('h');
        $docFooter = RunningElement::make('f');

        $section->hideFooter();

        $this->assertSame($docHeader, $section->resolveHeader($docHeader));
        $this->assertNull($section->resolveFooter($docFooter));
    }

    /* -------------------------------------------------------------
     | Vertical alignment — v0.8.0 (A4)
     |------------------------------------------------------------- */

    public function test_default_vertical_alignment_is_top(): void
    {
        $section = new Section();

        $this->assertSame(VerticalAlignment::TOP, $section->getVerticalAlignment());
    }

    public function test_set_vertical_alignment_is_fluent_and_serialises(): void
    {
        $section = new Section('center-page');
        $section->setVerticalAlignment(VerticalAlignment::CENTER);

        $this->assertSame(VerticalAlignment::CENTER, $section->getVerticalAlignment());

        $json = json_decode(json_encode($section), true);
        $this->assertSame('center', $json['verticalAlignment']);
    }

    public function test_top_vertical_alignment_is_omitted_from_json(): void
    {
        $section = new Section();
        $section->setVerticalAlignment(VerticalAlignment::TOP);

        $json = json_decode(json_encode($section), true);

        $this->assertArrayNotHasKey('verticalAlignment', $json);
    }

    /* -------------------------------------------------------------
     | Per-side padding shortcuts — v0.8.0 (A4)
     |------------------------------------------------------------- */

    public function test_set_page_padding_top_propagates(): void
    {
        $section = new Section();

        $section->setPagePaddingTop(120.0);

        $this->assertSame(120.0, $section->getPageSetup()->getPaddingTop());
    }

    public function test_set_page_padding_per_side_independent(): void
    {
        $section = new Section();

        $section->setPagePaddingTop(40)
                ->setPagePaddingRight(30)
                ->setPagePaddingBottom(20)
                ->setPagePaddingLeft(10);

        $setup = $section->getPageSetup();
        $this->assertSame(40.0, $setup->getPaddingTop());
        $this->assertSame(30.0, $setup->getPaddingRight());
        $this->assertSame(20.0, $setup->getPaddingBottom());
        $this->assertSame(10.0, $setup->getPaddingLeft());
    }

    /* -------------------------------------------------------------
     | HorizontalRule shortcut — v0.8.0 (B5)
     |------------------------------------------------------------- */

    public function test_add_rule_appends_horizontal_rule(): void
    {
        $section = new Section();

        $rule = $section->addRule()
            ->setWidth('50%')
            ->setThickness(1.5)
            ->setColor('#aabbcc');

        $this->assertInstanceOf(HorizontalRule::class, $rule);
        $this->assertSame([$rule], $section->getElements());
        $this->assertSame('50%', $rule->getWidth());
        $this->assertSame(1.5, $rule->getThickness());
        $this->assertSame('#aabbcc', $rule->getColor());
    }
}
