<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\DocumentInterface;
use Paperdoc\Document\Document;
use Paperdoc\Document\Section;
use Paperdoc\Document\Style\TextStyle;

class DocumentTest extends TestCase
{
    public function test_implements_interface(): void
    {
        $doc = new Document('pdf');

        $this->assertInstanceOf(DocumentInterface::class, $doc);
    }

    public function test_constructor_sets_format_and_title(): void
    {
        $doc = new Document('pdf', 'Mon Rapport');

        $this->assertSame('pdf', $doc->getFormat());
        $this->assertSame('Mon Rapport', $doc->getTitle());
    }

    public function test_constructor_default_title_is_empty(): void
    {
        $doc = new Document('html');

        $this->assertSame('', $doc->getTitle());
    }

    public function test_make_factory(): void
    {
        $doc = Document::make('csv', 'Export');

        $this->assertInstanceOf(Document::class, $doc);
        $this->assertSame('csv', $doc->getFormat());
        $this->assertSame('Export', $doc->getTitle());
    }

    public function test_set_title_is_fluent(): void
    {
        $doc = new Document('pdf');
        $result = $doc->setTitle('Nouveau titre');

        $this->assertSame($doc, $result);
        $this->assertSame('Nouveau titre', $doc->getTitle());
    }

    public function test_add_section(): void
    {
        $doc = new Document('pdf');
        $section = new Section('intro');

        $result = $doc->addSection($section);

        $this->assertSame($doc, $result);
        $this->assertCount(1, $doc->getSections());
        $this->assertSame($section, $doc->getSections()[0]);
    }

    public function test_add_section_without_argument_appends_empty_section(): void
    {
        $doc = new Document('pdf');

        $result = $doc->addSection();

        $this->assertSame($doc, $result);
        $this->assertCount(1, $doc->getSections());
        $this->assertSame('', $doc->getSections()[0]->getName());
    }

    public function test_open_section_returns_section_for_fluent_content(): void
    {
        $doc = new Document('pdf');
        $section = $doc->openSection(Section::make('body'));

        $this->assertInstanceOf(Section::class, $section);
        $this->assertCount(1, $doc->getSections());
        $this->assertSame('body', $doc->getSections()[0]->getName());
        $paragraph = $section->addParagraph('Hello');
        $this->assertSame('Hello', $paragraph->getPlainText());
        $this->assertSame($paragraph, $doc->getSections()[0]->getElements()[0]);
    }

    public function test_add_multiple_sections(): void
    {
        $doc = new Document('pdf');
        $doc->addSection(new Section('s1'))
            ->addSection(new Section('s2'))
            ->addSection(new Section('s3'));

        $this->assertCount(3, $doc->getSections());
        $this->assertSame('s1', $doc->getSections()[0]->getName());
        $this->assertSame('s3', $doc->getSections()[2]->getName());
    }

    public function test_remove_section_reindexes(): void
    {
        $doc = new Document('pdf');
        $doc->addSection(new Section('a'))
            ->addSection(new Section('b'))
            ->addSection(new Section('c'));

        $doc->removeSection(1);

        $sections = $doc->getSections();
        $this->assertCount(2, $sections);
        $this->assertSame('a', $sections[0]->getName());
        $this->assertSame('c', $sections[1]->getName());
    }

    public function test_remove_first_section(): void
    {
        $doc = new Document('pdf');
        $doc->addSection(new Section('first'))
            ->addSection(new Section('second'));

        $doc->removeSection(0);

        $this->assertCount(1, $doc->getSections());
        $this->assertSame('second', $doc->getSections()[0]->getName());
    }

    public function test_metadata(): void
    {
        $doc = new Document('pdf');

        $this->assertEmpty($doc->getMetadata());

        $result = $doc->setMetadata('author', 'Akram');

        $this->assertSame($doc, $result);
        $this->assertSame('Akram', $doc->getMetadata()['author']);
    }

    public function test_metadata_multiple_keys(): void
    {
        $doc = new Document('pdf');
        $doc->setMetadata('author', 'Akram')
            ->setMetadata('date', '2026-02-28')
            ->setMetadata('version', 2);

        $meta = $doc->getMetadata();
        $this->assertCount(3, $meta);
        $this->assertSame('Akram', $meta['author']);
        $this->assertSame(2, $meta['version']);
    }

    public function test_metadata_overwrites_existing_key(): void
    {
        $doc = new Document('pdf');
        $doc->setMetadata('author', 'Alice');
        $doc->setMetadata('author', 'Bob');

        $this->assertSame('Bob', $doc->getMetadata()['author']);
    }

    public function test_default_text_style(): void
    {
        $doc = new Document('pdf');
        $defaultStyle = $doc->getDefaultTextStyle();

        $this->assertInstanceOf(TextStyle::class, $defaultStyle);
        $this->assertSame('Helvetica', $defaultStyle->getFontFamily());
        $this->assertSame(12.0, $defaultStyle->getFontSize());
    }

    public function test_set_default_text_style(): void
    {
        $doc = new Document('pdf');
        $style = TextStyle::make()->setFontFamily('Times')->setFontSize(14.0);

        $result = $doc->setDefaultTextStyle($style);

        $this->assertSame($doc, $result);
        $this->assertSame('Times', $doc->getDefaultTextStyle()->getFontFamily());
        $this->assertSame(14.0, $doc->getDefaultTextStyle()->getFontSize());
    }

    public function test_empty_document_has_no_sections(): void
    {
        $doc = new Document('pdf');

        $this->assertEmpty($doc->getSections());
    }

    /* -------------------------------------------------------------
     | Typed properties (Metadata) introduced in v0.4.0
     |------------------------------------------------------------- */

    public function test_properties_are_null_by_default(): void
    {
        $doc = new Document('pdf');

        $this->assertNull($doc->getProperties());
    }

    public function test_set_properties_is_fluent(): void
    {
        $doc  = new Document('pdf');
        $meta = \Paperdoc\Document\Metadata::make()->setAuthor('Alice');

        $result = $doc->setProperties($meta);

        $this->assertSame($doc, $result);
        $this->assertSame($meta, $doc->getProperties());
    }

    public function test_set_properties_null_clears(): void
    {
        $doc = (new Document('pdf'))->setProperties(\Paperdoc\Document\Metadata::make());

        $doc->setProperties(null);

        $this->assertNull($doc->getProperties());
    }

    public function test_json_serialize_omits_properties_when_null(): void
    {
        $doc = new Document('pdf', 'Title');

        $json = json_decode(json_encode($doc, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertArrayNotHasKey('properties', $json);
    }

    public function test_json_serialize_includes_properties_when_set(): void
    {
        $doc = new Document('pdf', 'Title');
        $doc->setProperties(\Paperdoc\Document\Metadata::make()->setAuthor('Alice'));

        $json = json_decode(json_encode($doc, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('properties', $json);
        $this->assertSame(['author' => 'Alice'], $json['properties']);
    }

    /* -------------------------------------------------------------
     | Header / Footer (running elements) — v0.5.0
     |------------------------------------------------------------- */

    public function test_header_and_footer_are_null_by_default(): void
    {
        $doc = new Document('pdf');

        $this->assertNull($doc->getHeader());
        $this->assertNull($doc->getFooter());
    }

    public function test_set_header_is_fluent(): void
    {
        $doc    = new Document('pdf');
        $header = \Paperdoc\Document\Style\RunningElement::make('My doc');

        $result = $doc->setHeader($header);

        $this->assertSame($doc, $result);
        $this->assertSame($header, $doc->getHeader());
    }

    public function test_set_footer_is_fluent(): void
    {
        $doc    = new Document('pdf');
        $footer = \Paperdoc\Document\Style\RunningElement::make('Page {page}');

        $result = $doc->setFooter($footer);

        $this->assertSame($doc, $result);
        $this->assertSame($footer, $doc->getFooter());
    }

    public function test_set_header_null_clears(): void
    {
        $doc = (new Document('pdf'))->setHeader(\Paperdoc\Document\Style\RunningElement::make('x'));

        $doc->setHeader(null);

        $this->assertNull($doc->getHeader());
    }

    public function test_json_serialize_omits_header_footer_when_null(): void
    {
        $doc = new Document('pdf', 'Title');

        $json = json_decode(json_encode($doc, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertArrayNotHasKey('header', $json);
        $this->assertArrayNotHasKey('footer', $json);
    }

    public function test_json_serialize_includes_header_and_footer_when_set(): void
    {
        $doc = new Document('pdf', 'Title');
        $doc->setHeader(\Paperdoc\Document\Style\RunningElement::make('Top'));
        $doc->setFooter(\Paperdoc\Document\Style\RunningElement::make('Page {page}'));

        $json = json_decode(json_encode($doc, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('header', $json);
        $this->assertArrayHasKey('footer', $json);
        $this->assertSame('Top', $json['header']['template']);
        $this->assertSame('Page {page}', $json['footer']['template']);
    }
}
