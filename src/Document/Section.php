<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\DocumentElementInterface;
use Paperdoc\Document\Style\{ParagraphStyle, TextStyle};

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

    public function addText(string $text, ?TextStyle $style = null): Paragraph
    {
        $paragraph = new Paragraph();
        $paragraph->addRun(new TextRun($text, $style));
        $this->addElement($paragraph);

        return $paragraph;
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
