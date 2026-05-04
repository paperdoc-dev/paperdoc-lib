<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\BlockElementInterface;
use Paperdoc\Document\Style\{ParagraphStyle, TextStyle};

/**
 * Zone de texte positionnée en absolu sur une page.
 *
 * Une TextZone permet de placer du texte dans un cadre fixe (style
 * "text box" Word/PowerPoint) défini par ses coordonnées (x,y) et ses
 * dimensions (width, height) exprimées en points PDF.
 *
 * L'origine (x=0, y=0) est en haut-gauche de la page (convention CSS
 * et utilisateurs ; le PdfRenderer convertit vers la convention PDF
 * bas-gauche en interne).
 *
 * Le contenu d'une zone est une liste d'éléments block (paragraphes,
 * titres, listes…). Le rendu reste auto-géré : les renderers wrap le
 * texte dans la largeur de la zone.
 */
class TextZone implements BlockElementInterface, \JsonSerializable
{
    public const OVERFLOW_CLIP     = 'clip';
    public const OVERFLOW_ELLIPSIS = 'ellipsis';
    public const OVERFLOW_VISIBLE  = 'visible';

    /** @var Paragraph[] */
    private array $paragraphs = [];

    private float $padding = 0.0;

    private ?string $backgroundColor = null;

    private ?string $borderColor = null;

    private float $borderWidth = 0.0;

    private string $overflow = self::OVERFLOW_CLIP;

    public function __construct(
        private float $x = 0.0,
        private float $y = 0.0,
        private float $width = 200.0,
        private float $height = 100.0,
    ) {}

    /* -------------------------------------------------------------
     | Static factories
     |------------------------------------------------------------- */

    public static function make(float $x = 0.0, float $y = 0.0, float $width = 200.0, float $height = 100.0): static
    {
        return new static($x, $y, $width, $height);
    }

    public static function at(float $x, float $y, float $width, float $height): static
    {
        return new static($x, $y, $width, $height);
    }

    /* -------------------------------------------------------------
     | DocumentElementInterface
     |------------------------------------------------------------- */

    public function getType(): string { return 'text_zone'; }

    /* -------------------------------------------------------------
     | Geometry
     |------------------------------------------------------------- */

    public function getX(): float      { return $this->x; }
    public function getY(): float      { return $this->y; }
    public function getWidth(): float  { return $this->width; }
    public function getHeight(): float { return $this->height; }

    public function setPosition(float $x, float $y): static
    {
        $this->x = $x;
        $this->y = $y;

        return $this;
    }

    public function setSize(float $width, float $height): static
    {
        $this->width  = $width;
        $this->height = $height;

        return $this;
    }

    public function setPadding(float $padding): static
    {
        $this->padding = $padding;

        return $this;
    }

    public function getPadding(): float { return $this->padding; }

    /* -------------------------------------------------------------
     | Decoration
     |------------------------------------------------------------- */

    public function setBackgroundColor(?string $color): static
    {
        $this->backgroundColor = $color;

        return $this;
    }

    public function getBackgroundColor(): ?string { return $this->backgroundColor; }

    public function setBorder(string $color, float $width = 0.5): static
    {
        $this->borderColor = $color;
        $this->borderWidth = $width;

        return $this;
    }

    public function getBorderColor(): ?string { return $this->borderColor; }
    public function getBorderWidth(): float   { return $this->borderWidth; }

    /* -------------------------------------------------------------
     | Overflow strategy
     |------------------------------------------------------------- */

    /**
     * Comportement quand le texte dépasse la zone :
     *  - OVERFLOW_CLIP     : tronque silencieusement (par défaut)
     *  - OVERFLOW_ELLIPSIS : tronque et termine la dernière ligne par '…'
     *  - OVERFLOW_VISIBLE  : aucune limitation (peut sortir de la page)
     */
    public function setOverflow(string $mode): static
    {
        if (! in_array($mode, [self::OVERFLOW_CLIP, self::OVERFLOW_ELLIPSIS, self::OVERFLOW_VISIBLE], true)) {
            throw new \InvalidArgumentException("Mode overflow invalide : {$mode}");
        }

        $this->overflow = $mode;

        return $this;
    }

    public function getOverflow(): string { return $this->overflow; }

    /* -------------------------------------------------------------
     | Content
     |------------------------------------------------------------- */

    /** @return Paragraph[] */
    public function getParagraphs(): array { return $this->paragraphs; }

    public function addParagraph(Paragraph $paragraph): static
    {
        $this->paragraphs[] = $paragraph;

        return $this;
    }

    public function addText(string $text, ?TextStyle $textStyle = null, ?ParagraphStyle $paragraphStyle = null): Paragraph
    {
        $paragraph = new Paragraph($paragraphStyle);
        $paragraph->addRun(new TextRun($text, $textStyle));
        $this->paragraphs[] = $paragraph;

        return $paragraph;
    }

    public function clear(): static
    {
        $this->paragraphs = [];

        return $this;
    }

    /* -------------------------------------------------------------
     | JsonSerializable
     |------------------------------------------------------------- */

    public function jsonSerialize(): mixed
    {
        return [
            'type'            => 'text_zone',
            'x'               => $this->x,
            'y'               => $this->y,
            'width'           => $this->width,
            'height'          => $this->height,
            'padding'         => $this->padding,
            'backgroundColor' => $this->backgroundColor,
            'borderColor'     => $this->borderColor,
            'borderWidth'     => $this->borderWidth,
            'overflow'        => $this->overflow,
            'paragraphs'      => $this->paragraphs,
        ];
    }
}
