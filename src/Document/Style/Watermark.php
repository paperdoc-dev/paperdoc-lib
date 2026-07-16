<?php

declare(strict_types=1);

namespace Paperdoc\Document\Style;

final class Watermark implements \JsonSerializable
{
    public function __construct(
        private string $text,
        private float $fontSize = 60.0,
        private string $color = '#C8C8C8',
        private float $opacity = 0.35,
        private float $angle = -45.0,
        private string $fontFamily = 'Helvetica',
    ) {
        $this->opacity = max(0.0, min(1.0, $opacity));
    }

    public static function make(string $text): static
    {
        return new static($text);
    }

    public function getText(): string { return $this->text; }
    public function getFontSize(): float { return $this->fontSize; }
    public function getColor(): string { return $this->color; }
    public function getOpacity(): float { return $this->opacity; }
    public function getAngle(): float { return $this->angle; }
    public function getFontFamily(): string { return $this->fontFamily; }

    public function setText(string $v): static { $this->text = $v; return $this; }
    public function setFontSize(float $v): static { $this->fontSize = $v; return $this; }
    public function setColor(string $v): static { $this->color = $v; return $this; }
    public function setOpacity(float $v): static { $this->opacity = max(0.0, min(1.0, $v)); return $this; }
    public function setAngle(float $v): static { $this->angle = $v; return $this; }
    public function setFontFamily(string $v): static { $this->fontFamily = $v; return $this; }

    public function jsonSerialize(): mixed
    {
        return [
            'text'       => $this->text,
            'fontSize'   => $this->fontSize,
            'color'      => $this->color,
            'opacity'    => $this->opacity,
            'angle'      => $this->angle,
            'fontFamily' => $this->fontFamily,
        ];
    }
}
