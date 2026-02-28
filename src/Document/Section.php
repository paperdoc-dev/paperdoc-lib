<?php

declare(strict_types=1);

namespace Pagina\Document;

use Pagina\Contracts\DocumentElementInterface;
use Pagina\Document\Style\TextStyle;

class Section
{
    /** @var DocumentElementInterface[] */
    private array $elements = [];

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
        $style = TextStyle::make()
            ->setFontSize(match ($level) {
                1 => 24.0,
                2 => 20.0,
                3 => 16.0,
                default => 14.0,
            })
            ->setBold();

        return $this->addText($text, $style);
    }

    /* -------------------------------------------------------------
     | Shortcut : Page Break (for PDF)
     |------------------------------------------------------------- */

    public function addPageBreak(): static
    {
        $this->addElement(new PageBreak());

        return $this;
    }
}
