<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Renderers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\{
    Blockquote,
    Bookmark,
    CodeBlock,
    Document,
    Heading,
    ListBlock,
    Paragraph,
    TextRun,
};
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Document\Style\TextStyle;
use Paperdoc\Renderers\HtmlRenderer;

/**
 * v0.5.0 — HtmlRenderer support for the v0.4.0 model core.
 */
class HtmlRendererModelCoreTest extends TestCase
{
    private HtmlRenderer $renderer;
    private Document $document;

    protected function setUp(): void
    {
        $this->renderer = new HtmlRenderer();
        $this->document = Document::make('html', 'Model core');
    }

    public function test_heading_is_rendered_as_h_tag_with_id(): void
    {
        $section = $this->document->openSection();
        $section->addElement(Heading::make('Hello world', 2, 'intro'));

        $html = $this->renderer->render($this->document);

        $this->assertStringContainsString('<h2 id="intro">Hello world</h2>', $html);
    }

    public function test_heading_levels_clamped_within_1_to_6(): void
    {
        $section = $this->document->openSection();
        $section->addElement(Heading::make('Top', 1));

        $html = $this->renderer->render($this->document);

        $this->assertStringContainsString('<h1>Top</h1>', $html);
    }

    public function test_bullet_list_is_rendered_as_ul(): void
    {
        $section = $this->document->openSection();
        $list = $section->addBulletList();
        $list->addText('Buy groceries');
        $list->addText('Call Alice');

        $html = $this->renderer->render($this->document);

        $this->assertStringContainsString('<ul>', $html);
        $this->assertStringContainsString('<li>Buy groceries', $html);
        $this->assertStringContainsString('<li>Call Alice', $html);
    }

    public function test_ordered_list_with_custom_start_is_rendered_with_start_attribute(): void
    {
        $section = $this->document->openSection();
        $list = $section->addOrderedList(5);
        $list->addText('Step five');
        $list->addText('Step six');

        $html = $this->renderer->render($this->document);

        $this->assertStringContainsString('<ol start="5">', $html);
        $this->assertStringContainsString('<li>Step five', $html);
    }

    public function test_nested_list_is_rendered_inside_parent_li(): void
    {
        $section = $this->document->openSection();
        $parent = $section->addBulletList();
        $itemA = $parent->addText('Item A');

        $sub = ListBlock::ordered();
        $sub->addText('A.1');
        $sub->addText('A.2');
        $itemA->addBlock($sub);

        $parent->addText('Item B');

        $html = $this->renderer->render($this->document);

        $this->assertMatchesRegularExpression('#<li>Item A\s*<ol>#', $html);
        $this->assertStringContainsString('<li>A.1</li>', $html);
    }

    public function test_blockquote_wraps_inner_elements(): void
    {
        $section = $this->document->openSection();
        $quote = $section->addBlockquote();
        $quote->addText('Be the change');
        $quote->addText('you want to see', TextStyle::make()->setItalic());

        $html = $this->renderer->render($this->document);

        $this->assertStringContainsString('<blockquote>', $html);
        $this->assertStringContainsString('Be the change', $html);
        $this->assertStringContainsString('font-style:italic', $html);
    }

    public function test_code_block_uses_pre_code_and_language_class(): void
    {
        $section = $this->document->openSection();
        $section->addCodeBlock("<?php echo 'hi';", 'php');

        $html = $this->renderer->render($this->document);

        $this->assertStringContainsString('<pre><code class="language-php">', $html);
        $this->assertStringContainsString('&lt;?php echo &#039;hi&#039;;', $html);
    }

    public function test_code_block_without_language_has_no_class(): void
    {
        $section = $this->document->openSection();
        $section->addCodeBlock("just text");

        $html = $this->renderer->render($this->document);

        $this->assertStringContainsString('<pre><code>just text</code></pre>', $html);
    }

    public function test_bookmark_emits_anchor_tag(): void
    {
        $section = $this->document->openSection();
        $section->addBookmark('ref-1');

        $html = $this->renderer->render($this->document);

        $this->assertStringContainsString('<a id="ref-1" class="paperdoc-bookmark"></a>', $html);
    }

    public function test_hyperlink_with_internal_anchor_is_prefixed_with_hash(): void
    {
        $section = $this->document->openSection();
        $paragraph = new Paragraph();
        $paragraph->addRun(new TextRun('Jump', null, TextLink::make('', 'ref-1')));
        $section->addElement($paragraph);

        $html = $this->renderer->render($this->document);

        $this->assertStringContainsString('href="#ref-1"', $html);
        $this->assertStringNotContainsString('target="_blank"', $html);
    }

    public function test_external_link_gets_rel_noopener_and_target_blank(): void
    {
        $section = $this->document->openSection();
        $paragraph = new Paragraph();
        $paragraph->addRun(new TextRun('Docs', null, TextLink::make('https://paperdoc.dev')));
        $section->addElement($paragraph);

        $html = $this->renderer->render($this->document);

        $this->assertStringContainsString('href="https://paperdoc.dev"', $html);
        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('rel="noopener noreferrer"', $html);
    }
}
