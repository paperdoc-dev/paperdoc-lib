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
}
