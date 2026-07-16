<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\{Document, Metadata};
use Paperdoc\Renderers\{HtmlRenderer, MarkdownRenderer};

/**
 * v1.0.0 — typed document properties are surfaced in the HTML <head>
 * (meta tags + lang attribute) and in a Markdown YAML frontmatter.
 */
class MetadataRenderingTest extends TestCase
{
    private function documentWithProperties(): Document
    {
        $doc = Document::make('html', 'Rapport Annuel')
            ->setProperties(
                Metadata::make()
                    ->setAuthor('Alice Martin')
                    ->setDescription('Résultats consolidés 2026')
                    ->setKeywords('finance, rapport')
                    ->setLanguage('fr-FR')
                    ->setCreatedAt(new \DateTimeImmutable('2026-01-15T10:30:00+00:00')),
            );
        $doc->openSection()->addParagraph('Corps du document.');

        return $doc;
    }

    /* --------------------------- HTML --------------------------- */

    public function test_html_head_contains_meta_tags(): void
    {
        $html = (new HtmlRenderer())->render($this->documentWithProperties());

        $this->assertStringContainsString('<meta name="author" content="Alice Martin">', $html);
        $this->assertStringContainsString('<meta name="description" content="Résultats consolidés 2026">', $html);
        $this->assertStringContainsString('<meta name="keywords" content="finance, rapport">', $html);
        $this->assertStringContainsString('<meta name="dcterms.created" content="2026-01-15T10:30:00+00:00">', $html);
    }

    public function test_html_lang_attribute_uses_document_language(): void
    {
        $html = (new HtmlRenderer())->render($this->documentWithProperties());

        $this->assertStringContainsString('<html lang="fr-FR">', $html);
    }

    public function test_html_lang_falls_back_to_english(): void
    {
        $doc = Document::make('html', 'No props');
        $doc->openSection()->addParagraph('Body');

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringContainsString('<html lang="en">', $html);
        $this->assertStringNotContainsString('meta name="author"', $html);
    }

    public function test_html_meta_content_is_escaped(): void
    {
        $doc = Document::make('html', 'X')
            ->setProperties(Metadata::make()->setAuthor('A & B <script>'));
        $doc->openSection()->addParagraph('Body');

        $html = (new HtmlRenderer())->render($doc);

        $this->assertStringContainsString('content="A &amp; B &lt;script&gt;"', $html);
    }

    /* ------------------------- Markdown ------------------------- */

    public function test_markdown_frontmatter_from_typed_properties(): void
    {
        $doc = $this->documentWithProperties();

        $md = (new MarkdownRenderer())->render($doc);

        $this->assertStringStartsWith("---\n", $md);
        $this->assertStringContainsString('title: Rapport Annuel', $md);
        $this->assertStringContainsString('author: Alice Martin', $md);
        $this->assertStringContainsString('keywords: finance, rapport', $md);
        $this->assertStringContainsString('language: fr-FR', $md);
        $this->assertStringContainsString('created: "2026-01-15T10:30:00+00:00"', $md);
    }

    public function test_markdown_no_frontmatter_without_metadata(): void
    {
        $doc = Document::make('md');
        $doc->openSection()->addParagraph('Just text.');

        $md = (new MarkdownRenderer())->render($doc);

        $this->assertStringNotContainsString("---\n", $md);
        $this->assertStringContainsString('Just text.', $md);
    }

    public function test_markdown_frontmatter_quotes_yaml_sensitive_values(): void
    {
        $doc = Document::make('md', 'Report: 2026');
        $doc->setProperties(Metadata::make()->setAuthor('plain author'));
        $doc->openSection()->addParagraph('Body');

        $md = (new MarkdownRenderer())->render($doc);

        // The colon forces JSON-style quoting; the plain value stays bare.
        $this->assertStringContainsString('title: "Report: 2026"', $md);
        $this->assertStringContainsString('author: plain author', $md);
    }

    public function test_markdown_loose_metadata_bag_still_rendered(): void
    {
        $doc = Document::make('md');
        $doc->setMetadata('source_file', '/tmp/in.docx');
        $doc->openSection()->addParagraph('Body');

        $md = (new MarkdownRenderer())->render($doc);

        $this->assertStringContainsString('source_file: /tmp/in.docx', $md);
    }
}
