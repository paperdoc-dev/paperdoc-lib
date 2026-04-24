<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\DocumentElementInterface;
use Paperdoc\Document\Style\{ParagraphStyle, TextStyle};
use Paperdoc\Document\Link\TextLink;

class Section implements \JsonSerializable
{
    /** @var DocumentElementInterface[] */
    private array $elements = [];

    /** @var array<string, mixed> */
    private array $metadata = [];

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

        return $result;
    }
}
