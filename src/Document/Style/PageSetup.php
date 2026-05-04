<?php

declare(strict_types=1);

namespace Paperdoc\Document\Style;

use Paperdoc\Document\Image;
use Paperdoc\Enum\PageSize;

/**
 * Configuration physique d'une page : dimensions, marges (padding) et
 * habillage (image et/ou couleur de fond).
 *
 * Toutes les distances sont exprimées en points PDF (1 pt = 1/72 pouce),
 * ce qui correspond aussi à l'unité par défaut utilisée par les renderers
 * HTML (`pt`).
 */
class PageSetup implements \JsonSerializable
{
    public const ORIENTATION_PORTRAIT  = 'portrait';
    public const ORIENTATION_LANDSCAPE = 'landscape';

    /**
     * Stratégie de redimensionnement de l'image de fond.
     *
     *  - BG_SIZE_COVER   : couvre toute la page en préservant le ratio
     *                      (déborde et est rognée si nécessaire). Défaut.
     *  - BG_SIZE_CONTAIN : tient en entier dans la page en préservant le
     *                      ratio (peut laisser des bandes vides).
     *  - BG_SIZE_AUTO    : taille naturelle de l'image (centrée ;
     *                      l'excédent éventuel est rogné).
     *  - BG_SIZE_STRETCH : étire l'image pour remplir la page sans
     *                      préserver le ratio (`100% 100%`).
     *
     * Toute autre chaîne CSS valide (`50% 50%`, `300pt 200pt`, …) est
     * acceptée et passée telle quelle au HTML.
     */
    public const BG_SIZE_COVER   = 'cover';
    public const BG_SIZE_CONTAIN = 'contain';
    public const BG_SIZE_AUTO    = 'auto';
    public const BG_SIZE_STRETCH = '100% 100%';

    private float $width;
    private float $height;

    private float $paddingTop    = 40.0;
    private float $paddingRight  = 40.0;
    private float $paddingBottom = 40.0;
    private float $paddingLeft   = 40.0;

    private ?Image $backgroundImage = null;

    private ?string $backgroundColor = null;

    private string $backgroundSize     = self::BG_SIZE_COVER;
    private string $backgroundPosition = 'center center';
    private string $backgroundRepeat   = 'no-repeat';

    private string $orientation = self::ORIENTATION_PORTRAIT;

    private ?PageSize $size = null;

    public function __construct(float $width = 595.28, float $height = 841.89)
    {
        $this->width  = $width;
        $this->height = $height;
    }

    /* -------------------------------------------------------------
     | Static factories
     |------------------------------------------------------------- */

    public static function make(): static
    {
        return new static();
    }

    public static function fromSize(PageSize $size, string $orientation = self::ORIENTATION_PORTRAIT): static
    {
        $setup = new static();
        $setup->setSize($size, $orientation);

        return $setup;
    }

    public static function custom(float $width, float $height): static
    {
        return new static($width, $height);
    }

    /* -------------------------------------------------------------
     | Size & orientation
     |------------------------------------------------------------- */

    public function setSize(PageSize $size, string $orientation = self::ORIENTATION_PORTRAIT): static
    {
        $this->size        = $size;
        $this->orientation = $orientation;

        [$w, $h] = $size->dimensions();

        if ($orientation === self::ORIENTATION_LANDSCAPE) {
            $this->width  = $h;
            $this->height = $w;
        } else {
            $this->width  = $w;
            $this->height = $h;
        }

        return $this;
    }

    public function setDimensions(float $width, float $height): static
    {
        $this->width       = $width;
        $this->height      = $height;
        $this->size        = null;
        $this->orientation = $width > $height ? self::ORIENTATION_LANDSCAPE : self::ORIENTATION_PORTRAIT;

        return $this;
    }

    public function landscape(): static
    {
        if ($this->orientation === self::ORIENTATION_LANDSCAPE) {
            return $this;
        }

        [$this->width, $this->height] = [$this->height, $this->width];
        $this->orientation = self::ORIENTATION_LANDSCAPE;

        return $this;
    }

    public function portrait(): static
    {
        if ($this->orientation === self::ORIENTATION_PORTRAIT) {
            return $this;
        }

        [$this->width, $this->height] = [$this->height, $this->width];
        $this->orientation = self::ORIENTATION_PORTRAIT;

        return $this;
    }

    public function getWidth(): float       { return $this->width; }
    public function getHeight(): float      { return $this->height; }
    public function getOrientation(): string { return $this->orientation; }
    public function getSize(): ?PageSize    { return $this->size; }

    /* -------------------------------------------------------------
     | Padding (a.k.a. margins)
     |------------------------------------------------------------- */

    /**
     * Définit les quatre marges. Suit la convention CSS shorthand :
     *  - 1 valeur  : toutes les marges
     *  - 2 valeurs : (vertical, horizontal)
     *  - 3 valeurs : (top, horizontal, bottom)
     *  - 4 valeurs : (top, right, bottom, left)
     */
    public function setPadding(float ...$values): static
    {
        switch (count($values)) {
            case 1:
                $this->paddingTop = $this->paddingRight = $this->paddingBottom = $this->paddingLeft = $values[0];
                break;
            case 2:
                $this->paddingTop    = $this->paddingBottom = $values[0];
                $this->paddingRight  = $this->paddingLeft   = $values[1];
                break;
            case 3:
                $this->paddingTop    = $values[0];
                $this->paddingRight  = $this->paddingLeft = $values[1];
                $this->paddingBottom = $values[2];
                break;
            case 4:
                $this->paddingTop    = $values[0];
                $this->paddingRight  = $values[1];
                $this->paddingBottom = $values[2];
                $this->paddingLeft   = $values[3];
                break;
            default:
                throw new \InvalidArgumentException(
                    'setPadding() attend 1 à 4 valeurs (top[, right[, bottom[, left]]])'
                );
        }

        return $this;
    }

    public function setPaddingTop(float $v): static    { $this->paddingTop = $v;    return $this; }
    public function setPaddingRight(float $v): static  { $this->paddingRight = $v;  return $this; }
    public function setPaddingBottom(float $v): static { $this->paddingBottom = $v; return $this; }
    public function setPaddingLeft(float $v): static   { $this->paddingLeft = $v;   return $this; }

    public function getPaddingTop(): float    { return $this->paddingTop; }
    public function getPaddingRight(): float  { return $this->paddingRight; }
    public function getPaddingBottom(): float { return $this->paddingBottom; }
    public function getPaddingLeft(): float   { return $this->paddingLeft; }

    /* -------------------------------------------------------------
     | Background (image + color)
     |------------------------------------------------------------- */

    public function setBackgroundImage(?Image $image): static
    {
        $this->backgroundImage = $image;

        return $this;
    }

    public function getBackgroundImage(): ?Image
    {
        return $this->backgroundImage;
    }

    public function setBackgroundColor(?string $color): static
    {
        $this->backgroundColor = $color;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundSize(string $size): static
    {
        $this->backgroundSize = $size;

        return $this;
    }

    public function getBackgroundSize(): string { return $this->backgroundSize; }

    public function setBackgroundPosition(string $position): static
    {
        $this->backgroundPosition = $position;

        return $this;
    }

    public function getBackgroundPosition(): string { return $this->backgroundPosition; }

    public function setBackgroundRepeat(string $repeat): static
    {
        $this->backgroundRepeat = $repeat;

        return $this;
    }

    public function getBackgroundRepeat(): string { return $this->backgroundRepeat; }

    /* -------------------------------------------------------------
     | Convenience
     |------------------------------------------------------------- */

    public function getContentWidth(): float
    {
        return $this->width - $this->paddingLeft - $this->paddingRight;
    }

    public function getContentHeight(): float
    {
        return $this->height - $this->paddingTop - $this->paddingBottom;
    }

    /* -------------------------------------------------------------
     | JsonSerializable
     |------------------------------------------------------------- */

    public function jsonSerialize(): mixed
    {
        return [
            'width'              => $this->width,
            'height'             => $this->height,
            'orientation'        => $this->orientation,
            'size'               => $this->size?->value,
            'padding'            => [
                'top'    => $this->paddingTop,
                'right'  => $this->paddingRight,
                'bottom' => $this->paddingBottom,
                'left'   => $this->paddingLeft,
            ],
            'backgroundColor'    => $this->backgroundColor,
            'backgroundImage'    => $this->backgroundImage,
            'backgroundSize'     => $this->backgroundSize,
            'backgroundPosition' => $this->backgroundPosition,
            'backgroundRepeat'   => $this->backgroundRepeat,
        ];
    }
}
