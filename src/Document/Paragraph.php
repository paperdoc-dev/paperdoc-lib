<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\DocumentElementInterface;
use Paperdoc\Document\Style\ParagraphStyle;

class Paragraph implements DocumentElementInterface, \JsonSerializable
{
    /** @var TextRun[] */
    private array $runs = [];

    public function __construct(private ?ParagraphStyle $style = null) {}

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(?ParagraphStyle $style = null): static
    {
        return new static($style);
    }

    /* -------------------------------------------------------------
     | DocumentElementInterface
     |------------------------------------------------------------- */

    public function getType(): string { return 'paragraph'; }

    /* -------------------------------------------------------------
     | Runs
     |------------------------------------------------------------- */

    public function addRun(TextRun $run): static
    {
        $this->runs[] = $run;

        return $this;
    }

    /** @return TextRun[] */
    public function getRuns(): array { return $this->runs; }

    /* -------------------------------------------------------------
     | Style
     |------------------------------------------------------------- */

    public function getStyle(): ?ParagraphStyle { return $this->style; }

    public function setStyle(ParagraphStyle $style): static
    {
        $this->style = $style;

        return $this;
    }

    /* -------------------------------------------------------------
     | Helpers
     |------------------------------------------------------------- */

    public function getPlainText(): string
    {
        return implode('', array_map(fn (TextRun $r) => $r->getText(), $this->runs));
    }

    /* -------------------------------------------------------------
     | JsonSerializable
     |------------------------------------------------------------- */

    public function jsonSerialize(): mixed
    {
        return [
            'type'  => 'paragraph',
            'text'  => $this->getPlainText(),
            'runs'  => $this->runs,
            'style' => $this->style,
        ];
    }
}
