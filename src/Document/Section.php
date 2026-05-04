<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\DocumentElementInterface;
use Paperdoc\Document\Style\{PageSetup, ParagraphStyle, TextStyle};
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Enum\PageSize;

class Section implements \JsonSerializable
{
    /** @var DocumentElementInterface[] */
    private array $elements = [];

    /** @var array<string, mixed> */
    private array $metadata = [];

    private ?PageSetup $pageSetup = null;

    public function __construct(private string $name = '') {}

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(string $name = ''): static
    {
        return new static($name);
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

        return $result;
    }
}
