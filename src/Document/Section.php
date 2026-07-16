<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\DocumentElementInterface;
use Paperdoc\Document\Style\{PageSetup, ParagraphStyle, RunningElement, TextStyle};
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Enum\{PageSize, VerticalAlignment};
use Paperdoc\Factory\DocumentHydrator;

final class Section implements \JsonSerializable
{
    /** @var DocumentElementInterface[] */
    private array $elements = [];

    /** @var array<string, mixed> */
    private array $metadata = [];

    private ?PageSetup $pageSetup = null;

    /**
     * Per-section running elements (since v0.8.0). When `null`, the
     * section inherits the document's `getHeader()`/`getFooter()` if
     * any. Set explicitly via {@see setHeader()}/{@see setFooter()},
     * or call {@see hideHeader()}/{@see hideFooter()} to suppress
     * the document's running element on this section only — useful
     * for cover pages or full-bleed image pages where a footer would
     * either disappear under the artwork or need a different colour
     * to stay legible.
     */
    private ?RunningElement $header = null;
    private ?RunningElement $footer = null;
    private bool $headerHidden = false;
    private bool $footerHidden = false;

    /**
     * Vertical anchoring of the section's content within the
     * available page area (since v0.8.0). Default
     * {@see VerticalAlignment::TOP} mirrors previous behaviour.
     * {@see VerticalAlignment::CENTER} and {@see VerticalAlignment::BOTTOM}
     * are useful for chapter openers, colophons or single-paragraph
     * sections that should be visually balanced on the page.
     *
     * NOTE: vertical alignment is currently honoured by the PDF
     * renderer; the HTML renderer applies it via flexbox on the
     * `.paperdoc-page` section.
     */
    private VerticalAlignment $verticalAlignment = VerticalAlignment::TOP;

    public function __construct(private string $name = '') {}

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(string $name = ''): static
    {
        return new static($name);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $section = new static(DocumentHydrator::asString($data['name'] ?? null));

        foreach (DocumentHydrator::asMap($data['metadata'] ?? null) as $key => $value) {
            $section->setMetadata($key, $value);
        }

        $section->setPageSetup(DocumentHydrator::pageSetupFromArrayOrNull($data['pageSetup'] ?? null));
        $section->setHeader(DocumentHydrator::runningElementFromArrayOrNull($data['header'] ?? null));
        $section->setFooter(DocumentHydrator::runningElementFromArrayOrNull($data['footer'] ?? null));

        if (DocumentHydrator::asBool($data['headerHidden'] ?? null)) {
            $section->hideHeader();
        }

        if (DocumentHydrator::asBool($data['footerHidden'] ?? null)) {
            $section->hideFooter();
        }

        if (isset($data['verticalAlignment'])) {
            $section->setVerticalAlignment(VerticalAlignment::from(DocumentHydrator::asString($data['verticalAlignment'])));
        }

        foreach (DocumentHydrator::asList($data['elements'] ?? null) as $elementData) {
            if (is_array($elementData)) {
                $section->addElement(DocumentHydrator::elementFromArray(DocumentHydrator::asMap($elementData)));
            }
        }

        return $section;
    }

    /* -------------------------------------------------------------
     | Name
     |------------------------------------------------------------- */

    public function getName(): string { return $this->name; }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /* -------------------------------------------------------------
     | Elements
     |------------------------------------------------------------- */

    /** @return DocumentElementInterface[] */
    public function getElements(): array { return $this->elements; }

    public function addElement(DocumentElementInterface $element): static
    {
        $this->elements[] = $element;

        return $this;
    }

    public function clearElements(): static
    {
        $this->elements = [];

        return $this;
    }

    /* -------------------------------------------------------------
     | Metadata
     |------------------------------------------------------------- */

    /** @return array<string, mixed> */
    public function getMetadata(): array { return $this->metadata; }

    public function setMetadata(string $key, mixed $value): static
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /* -------------------------------------------------------------
     | Shortcut : Text
     |------------------------------------------------------------- */

    public function addText(string $text, ?TextStyle $style = null, ?TextLink $link = null): Paragraph
    {
        $paragraph = new Paragraph();
        $paragraph->addRun(new TextRun($text, $style, $link));
        $this->addElement($paragraph);

        return $paragraph;
    }

    /** @see addText() — same behaviour, alternative name for readability */
    public function addParagraph(string $text, ?TextStyle $style = null): Paragraph
    {
        return $this->addText($text, $style);
    }

    /* -------------------------------------------------------------
     | Shortcut : Heading
     |------------------------------------------------------------- */

    public function addHeading(string $text, int $level = 1): Paragraph
    {
        $textStyle = TextStyle::make()
            ->setFontSize(match ($level) {
                1 => 24.0,
                2 => 20.0,
                3 => 16.0,
                default => 14.0,
            })
            ->setBold();

        $paragraphStyle = ParagraphStyle::make()->setHeadingLevel($level);

        $paragraph = new Paragraph($paragraphStyle);
        $paragraph->addRun(new TextRun($text, $textStyle));
        $this->addElement($paragraph);

        return $paragraph;
    }

    /* -------------------------------------------------------------
     | Shortcut : Page Break (for PDF)
     |------------------------------------------------------------- */

    public function addPageBreak(): static
    {
        $this->addElement(new PageBreak());

        return $this;
    }

    /* -------------------------------------------------------------
     | Shortcut : List
     |------------------------------------------------------------- */

    /**
     * Append an empty ListBlock to the section and return it so items
     * can be added fluently.
     */
    public function addList(string $style = ListBlock::STYLE_BULLET, int $start = 1): ListBlock
    {
        $list = new ListBlock($style, $start);
        $this->addElement($list);

        return $list;
    }

    public function addBulletList(): ListBlock
    {
        return $this->addList(ListBlock::STYLE_BULLET);
    }

    public function addOrderedList(int $start = 1): ListBlock
    {
        return $this->addList(ListBlock::STYLE_ORDERED, $start);
    }

    /* -------------------------------------------------------------
     | Shortcut : Bookmark
     |------------------------------------------------------------- */

    public function addBookmark(string $id): Bookmark
    {
        $bookmark = new Bookmark($id);
        $this->addElement($bookmark);

        return $bookmark;
    }

    /* -------------------------------------------------------------
     | Shortcut : Code block
     |------------------------------------------------------------- */

    public function addCodeBlock(string $code = '', string $language = ''): CodeBlock
    {
        $block = new CodeBlock($code, $language);
        $this->addElement($block);

        return $block;
    }

    /* -------------------------------------------------------------
     | Shortcut : Blockquote
     |------------------------------------------------------------- */

    public function addBlockquote(): Blockquote
    {
        $quote = new Blockquote();
        $this->addElement($quote);

        return $quote;
    }

    public function addTableOfContents(int $maxLevel = 3, string $title = 'Table of Contents'): TableOfContents
    {
        $toc = TableOfContents::make($maxLevel, $title);
        $this->addElement($toc);

        return $toc;
    }

    /* -------------------------------------------------------------
     | Page setup (size, padding, background)
     |------------------------------------------------------------- */

    public function getPageSetup(): ?PageSetup { return $this->pageSetup; }

    public function setPageSetup(?PageSetup $setup): static
    {
        $this->pageSetup = $setup;

        return $this;
    }

    /**
     * Lazily creates a PageSetup so callers can chain setters
     * without instantiating it themselves.
     */
    private function ensurePageSetup(): PageSetup
    {
        if ($this->pageSetup === null) {
            $this->pageSetup = new PageSetup();
        }

        return $this->pageSetup;
    }

    public function setPageSize(PageSize $size, string $orientation = PageSetup::ORIENTATION_PORTRAIT): static
    {
        $this->ensurePageSetup()->setSize($size, $orientation);

        return $this;
    }

    public function setPageDimensions(float $width, float $height): static
    {
        $this->ensurePageSetup()->setDimensions($width, $height);

        return $this;
    }

    public function setPagePadding(float ...$values): static
    {
        $this->ensurePageSetup()->setPadding(...$values);

        return $this;
    }

    /**
     * Per-side padding shortcuts (since v0.8.0). Useful when you want
     * to push a frontispiece or a chapter opener towards the middle
     * of the page without touching the other three sides.
     */
    public function setPagePaddingTop(float $v): static
    {
        $this->ensurePageSetup()->setPaddingTop($v);

        return $this;
    }

    public function setPagePaddingRight(float $v): static
    {
        $this->ensurePageSetup()->setPaddingRight($v);

        return $this;
    }

    public function setPagePaddingBottom(float $v): static
    {
        $this->ensurePageSetup()->setPaddingBottom($v);

        return $this;
    }

    public function setPagePaddingLeft(float $v): static
    {
        $this->ensurePageSetup()->setPaddingLeft($v);

        return $this;
    }

    public function setPageBackgroundImage(?Image $image): static
    {
        $this->ensurePageSetup()->setBackgroundImage($image);

        return $this;
    }

    public function setPageBackgroundColor(?string $color): static
    {
        $this->ensurePageSetup()->setBackgroundColor($color);

        return $this;
    }

    public function setColumnCount(int $count): static
    {
        $this->ensurePageSetup()->setColumnCount($count);

        return $this;
    }

    public function setColumnGap(float $gap): static
    {
        $this->ensurePageSetup()->setColumnGap($gap);

        return $this;
    }

    /* -------------------------------------------------------------
     | Per-section running elements (header / footer) — v0.8.0
     |------------------------------------------------------------- */

    /**
     * Sets a section-specific header that overrides the document's
     * header on this section's pages. Pass `null` to clear the
     * override (and resume falling back to the document's header).
     * To draw NO header at all on this section, use {@see hideHeader()}.
     */
    public function setHeader(?RunningElement $header): static
    {
        $this->header = $header;
        $this->headerHidden = false;

        return $this;
    }

    public function setFooter(?RunningElement $footer): static
    {
        $this->footer = $footer;
        $this->footerHidden = false;

        return $this;
    }

    /**
     * Suppresses the document-level header on this section only.
     * Stronger than `setHeader(null)` — null-resets fall back to the
     * document, hideHeader() forces no header even if the document
     * has one. Common on cover pages or full-bleed image pages.
     */
    public function hideHeader(): static
    {
        $this->header = null;
        $this->headerHidden = true;

        return $this;
    }

    public function hideFooter(): static
    {
        $this->footer = null;
        $this->footerHidden = true;

        return $this;
    }

    public function getHeader(): ?RunningElement { return $this->header; }
    public function getFooter(): ?RunningElement { return $this->footer; }
    public function isHeaderHidden(): bool       { return $this->headerHidden; }
    public function isFooterHidden(): bool       { return $this->footerHidden; }

    /**
     * Resolves which header/footer (if any) should be rendered on
     * this section, taking into account the document-level fallback
     * and the per-section override / hide flags.
     *
     *  - hidden flag set → returns null (no header/footer).
     *  - section override set → returns the section's element.
     *  - otherwise          → returns the document's element (or null).
     */
    public function resolveHeader(?RunningElement $documentHeader): ?RunningElement
    {
        if ($this->headerHidden) {
            return null;
        }

        return $this->header ?? $documentHeader;
    }

    public function resolveFooter(?RunningElement $documentFooter): ?RunningElement
    {
        if ($this->footerHidden) {
            return null;
        }

        return $this->footer ?? $documentFooter;
    }

    /* -------------------------------------------------------------
     | Vertical alignment — v0.8.0
     |------------------------------------------------------------- */

    public function setVerticalAlignment(VerticalAlignment $alignment): static
    {
        $this->verticalAlignment = $alignment;

        return $this;
    }

    public function getVerticalAlignment(): VerticalAlignment
    {
        return $this->verticalAlignment;
    }

    /* -------------------------------------------------------------
     | Shortcut : TextZone
     |------------------------------------------------------------- */

    public function addTextZone(float $x = 0.0, float $y = 0.0, float $width = 200.0, float $height = 100.0): TextZone
    {
        $zone = new TextZone($x, $y, $width, $height);
        $this->addElement($zone);

        return $zone;
    }

    /* -------------------------------------------------------------
     | Shortcut : HorizontalRule (v0.8.0)
     |------------------------------------------------------------- */

    public function addRule(): HorizontalRule
    {
        $rule = new HorizontalRule();
        $this->addElement($rule);

        return $rule;
    }

    /* -------------------------------------------------------------
     | JsonSerializable
     |------------------------------------------------------------- */

    public function jsonSerialize(): mixed
    {
        $result = [
            'name'     => $this->name,
            'elements' => $this->elements,
        ];

        if (! empty($this->metadata)) {
            $result['metadata'] = $this->metadata;
        }

        if ($this->pageSetup !== null) {
            $result['pageSetup'] = $this->pageSetup;
        }

        if ($this->header !== null) {
            $result['header'] = $this->header;
        }
        if ($this->footer !== null) {
            $result['footer'] = $this->footer;
        }
        if ($this->headerHidden) {
            $result['headerHidden'] = true;
        }
        if ($this->footerHidden) {
            $result['footerHidden'] = true;
        }

        if ($this->verticalAlignment !== VerticalAlignment::TOP) {
            $result['verticalAlignment'] = $this->verticalAlignment->value;
        }

        return $result;
    }
}
