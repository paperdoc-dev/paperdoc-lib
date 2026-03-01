<?php

declare(strict_types=1);

namespace Paperdoc\Document\Style;

use Paperdoc\Contracts\StyleInterface;

class TextStyle implements StyleInterface, \JsonSerializable
{
    private string $fontFamily = 'Helvetica';
    private float  $fontSize   = 12.0;
    private string $color      = '#000000';
    private bool   $bold       = false;
    private bool   $italic     = false;
    private bool   $underline  = false;

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(): static
    {
        return new static();
    }

    /* -------------------------------------------------------------
     | Getters
     |------------------------------------------------------------- */

    public function getFontFamily(): string { return $this->fontFamily; }
    public function getFontSize(): float    { return $this->fontSize; }
    public function getColor(): string      { return $this->color; }
    public function isBold(): bool          { return $this->bold; }
    public function isItalic(): bool        { return $this->italic; }
    public function isUnderline(): bool     { return $this->underline; }

    /* -------------------------------------------------------------
     | Fluent Setters
     |------------------------------------------------------------- */

    public function setFontFamily(string $v): static { $this->fontFamily = $v; return $this; }
    public function setFontSize(float $v): static    { $this->fontSize = $v; return $this; }
    public function setColor(string $v): static      { $this->color = $v; return $this; }
    public function setBold(bool $v = true): static  { $this->bold = $v; return $this; }
    public function setItalic(bool $v = true): static{ $this->italic = $v; return $this; }
    public function setUnderline(bool $v = true): static { $this->underline = $v; return $this; }

    /* -------------------------------------------------------------
     | Serialization
     |------------------------------------------------------------- */

    public function toArray(): array
    {
        return [
            'fontFamily' => $this->fontFamily,
            'fontSize'   => $this->fontSize,
            'color'      => $this->color,
            'bold'       => $this->bold,
            'italic'     => $this->italic,
            'underline'  => $this->underline,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * @return array{float, float, float} RGB values 0..1
     */
    public function getColorRgb(): array
    {
        $hex = ltrim($this->color, '#');

        return [
            hexdec(substr($hex, 0, 2)) / 255,
            hexdec(substr($hex, 2, 2)) / 255,
            hexdec(substr($hex, 4, 2)) / 255,
        ];
    }

    public function getPdfFontName(): string
    {
        $base = match (strtolower($this->fontFamily)) {
            'times', 'times new roman', 'serif' => 'Times',
            'courier', 'courier new', 'monospace' => 'Courier',
            default => 'Helvetica',
        };

        if ($this->bold && $this->italic) {
            return match ($base) {
                'Times'   => 'Times-BoldItalic',
                'Courier' => 'Courier-BoldOblique',
                default   => 'Helvetica-BoldOblique',
            };
        }

        if ($this->bold) {
            return match ($base) {
                'Times'   => 'Times-Bold',
                'Courier' => 'Courier-Bold',
                default   => 'Helvetica-Bold',
            };
        }

        if ($this->italic) {
            return match ($base) {
                'Times'   => 'Times-Italic',
                'Courier' => 'Courier-Oblique',
                default   => 'Helvetica-Oblique',
            };
        }

        return match ($base) {
            'Times'   => 'Times-Roman',
            default   => $base,
        };
    }
}
