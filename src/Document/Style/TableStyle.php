<?php

declare(strict_types=1);

namespace Paperdoc\Document\Style;

use Paperdoc\Contracts\StyleInterface;
use Paperdoc\Enum\Alignment;
use Paperdoc\Enum\BorderStyle;

class TableStyle implements StyleInterface, \JsonSerializable
{
    private Alignment   $alignment   = Alignment::LEFT;
    private BorderStyle $borderStyle = BorderStyle::SOLID;
    private float       $borderWidth = 0.5;
    private string      $borderColor = '#000000';
    private float       $cellPadding = 4.0;
    private ?string     $headerBg    = '#f3f4f6';
    private ?string     $stripedBg   = null;

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

    public function getAlignment(): Alignment     { return $this->alignment; }
    public function getBorderStyle(): BorderStyle  { return $this->borderStyle; }
    public function getBorderWidth(): float        { return $this->borderWidth; }
    public function getBorderColor(): string       { return $this->borderColor; }
    public function getCellPadding(): float        { return $this->cellPadding; }
    public function getHeaderBg(): ?string         { return $this->headerBg; }
    public function getStripedBg(): ?string        { return $this->stripedBg; }

    /* -------------------------------------------------------------
     | Fluent Setters
     |------------------------------------------------------------- */

    public function setAlignment(Alignment $v): static    { $this->alignment = $v; return $this; }
    public function setBorderStyle(BorderStyle $v): static{ $this->borderStyle = $v; return $this; }
    public function setBorderWidth(float $v): static      { $this->borderWidth = $v; return $this; }
    public function setBorderColor(string $v): static     { $this->borderColor = $v; return $this; }
    public function setCellPadding(float $v): static      { $this->cellPadding = $v; return $this; }
    public function setHeaderBg(?string $v): static       { $this->headerBg = $v; return $this; }
    public function setStripedBg(?string $v): static      { $this->stripedBg = $v; return $this; }

    /* -------------------------------------------------------------
     | Serialization
     |------------------------------------------------------------- */

    public function toArray(): array
    {
        return [
            'alignment'   => $this->alignment->value,
            'borderStyle' => $this->borderStyle->value,
            'borderWidth' => $this->borderWidth,
            'borderColor' => $this->borderColor,
            'cellPadding' => $this->cellPadding,
            'headerBg'    => $this->headerBg,
            'stripedBg'   => $this->stripedBg,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
