<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\{
    Document,
    Heading,
    ListBlock,
    Paragraph,
    TextRun,
};
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Document\Style\TextStyle;
use Paperdoc\Renderers\MarkdownRenderer;

/**
 * v0.5.0 — MarkdownRenderer support for the v0.4.0 model core.
 */
class MarkdownRendererModelCoreTest extends TestCase
{
    private MarkdownRenderer $renderer;
    private Document $document;

    protected function setUp(): void
    {
        $this->renderer = new MarkdownRenderer();
        $this->document = Document::make('md', 'Model core');
    }

    public function test_heading_is_rendered_with_hash_markers(): void
    {
        $section = $this->document->openSection();
        $section->addElement(Heading::make('Introduction', 2));

        $md = $this->renderer->render($this->document);

        $this->assertStringContainsString('## Introduction', $md);
    }

    public function test_heading_with_id_appends_anchor_syntax(): void
    {
        $section = $this->document->openSection();
        $section->addElement(Heading::make('Anchored', 3, 'anchored'));

        $md = $this->renderer->render($this->document);

        $this->assertStringContainsString('### Anchored {#anchored}', $md);
    }

    public function test_bullet_list_uses_dash_marker(): void
    {
        $section = $this->document->openSection();
        $list = $section->addBulletList();
        $list->addText('One');
        $list->addText('Two');

        $md = $this->renderer->render($this->document);

        $this->assertStringContainsString("- One\n", $md);
        $this->assertStringContainsString("- Two\n", $md);
    }

    public function test_ordered_list_increments_counter_from_start(): void
    {
        $section = $this->document->openSection();
        $list = $section->addOrderedList(3);
        $list->addText('Troisième');
        $list->addText('Quatrième');

        $md = $this->renderer->render($this->document);

        $this->assertStringContainsString("3. Troisième", $md);
        $this->assertStringContainsString("4. Quatrième", $md);
    }

    public function test_nested_list_is_indented_with_two_spaces(): void
    {
        $section = $this->document->openSection();
        $parent = $section->addBulletList();
        $itemA = $parent->addText('A');

        $sub = ListBlock::ordered();
        $sub->addText('A.1');
        $itemA->addBlock($sub);

        $md = $this->renderer->render($this->document);

        $this->assertStringContainsString("- A\n  1. A.1\n", $md);
    }

    public function test_blockquote_prefixes_each_line_with_angle_bracket(): void
    {
        $section = $this->document->openSection();
        $quote = $section->addBlockquote();
        $quote->addText('First');
        $quote->addText('Second');

        $md = $this->renderer->render($this->document);

        $this->assertStringContainsString('> First', $md);
        $this->assertStringContainsString('> Second', $md);
    }

    public function test_code_block_uses_triple_backtick_fence_with_language(): void
    {
        $section = $this->document->openSection();
        $section->addCodeBlock("echo 1;", 'php');

        $md = $this->renderer->render($this->document);

        $this->assertStringContainsString("```php\necho 1;\n```", $md);
    }

    public function test_code_block_without_language_still_fences(): void
    {
        $section = $this->document->openSection();
        $section->addCodeBlock("plain");

        $md = $this->renderer->render($this->document);

        $this->assertStringContainsString("```\nplain\n```", $md);
    }

    public function test_bookmark_is_emitted_as_inline_html_anchor(): void
    {
        $section = $this->document->openSection();
        $section->addBookmark('target');

        $md = $this->renderer->render($this->document);

        $this->assertStringContainsString('<a id="target"></a>', $md);
    }

    public function test_external_link_keeps_inline_markdown_syntax(): void
    {
        $section = $this->document->openSection();
        $paragraph = new Paragraph();
        $paragraph->addRun(new TextRun('Paperdoc', null, TextLink::make('https://paperdoc.dev', '', 'Homepage')));
        $section->addElement($paragraph);

        $md = $this->renderer->render($this->document);

        $this->assertStringContainsString('[Paperdoc](https://paperdoc.dev "Homepage")', $md);
    }

    public function test_styled_text_keeps_emphasis_markers(): void
    {
        $section = $this->document->openSection();
        $paragraph = new Paragraph();
        $paragraph->addRun(new TextRun('bold', TextStyle::make()->setBold()));
        $paragraph->addRun(new TextRun(' and '));
        $paragraph->addRun(new TextRun('italic', TextStyle::make()->setItalic()));
        $section->addElement($paragraph);

        $md = $this->renderer->render($this->document);

        $this->assertStringContainsString('**bold**', $md);
        $this->assertStringContainsString('*italic*', $md);
    }
}
