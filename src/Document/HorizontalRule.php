<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\BlockElementInterface;
use Paperdoc\Enum\Alignment;

/**
 * A decorative horizontal line, akin to HTML's `<hr>` (since v0.8.0).
 *
 * Useful for separating sections, marking the end of a chapter, or
 * underlining a heading. Renderers translate it as follows:
 *
 *  - PDF      : a stroked line on the current page.
 *  - HTML     : `<hr>` element with inline CSS.
 *  - Markdown : `---` on its own line, surrounded by blank lines.
 *  - DOCX     : an empty paragraph with a bottom border (the
 *               canonical Word equivalent of an `<hr>`).
 *
 * Geometry:
 *  - `width`     : either an absolute width in points (`70`) or a
 *                  percentage string (`'100%'`, `'50%'`, …) relative
 *                  to the available content width. Defaults to `'100%'`.
 *  - `thickness` : stroke thickness in points. Defaults to 0.5pt
 *                  (the Word-style "thin rule").
 *  - `color`     : CSS hex colour, e.g. `'#999999'`. Defaults to a
 *                  neutral grey.
 *  - `alignment` : LEFT / CENTER / RIGHT for the horizontal placement
 *                  of partial-width rules. Defaults to CENTER.
 *  - `marginTop` / `marginBottom` : extra vertical breathing room
 *                  in points, applied above/below the rule.
 */
class HorizontalRule implements BlockElementInterface, \JsonSerializable
{
    private string|float $width      = '100%';
    private float        $thickness  = 0.5;
    private string       $color      = '#999999';
    private Alignment    $alignment  = Alignment::CENTER;
    private float        $marginTop  = 6.0;
    private float        $marginBottom = 6.0;

    public static function make(): static { return new static(); }

    public function getType(): string { return 'horizontal_rule'; }

    /* -------- Fluent setters -------- */

    public function setWidth(string|float $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function setThickness(float $thickness): static
    {
        $this->thickness = $thickness;

        return $this;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function setAlignment(Alignment $alignment): static
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function setMarginTop(float $v): static
    {
        $this->marginTop = $v;

        return $this;
    }

    public function setMarginBottom(float $v): static
    {
        $this->marginBottom = $v;

        return $this;
    }

    public function setMargins(float $top, float $bottom): static
    {
        $this->marginTop    = $top;
        $this->marginBottom = $bottom;

        return $this;
    }

    /* -------- Getters -------- */

    public function getWidth(): string|float { return $this->width; }
    public function getThickness(): float    { return $this->thickness; }
    public function getColor(): string       { return $this->color; }
    public function getAlignment(): Alignment { return $this->alignment; }
    public function getMarginTop(): float    { return $this->marginTop; }
    public function getMarginBottom(): float { return $this->marginBottom; }

    /**
     * Resolves the rule's width against an available content width.
     * Returns a clamped pt value not greater than `$contentWidth`.
     */
    public function resolveWidth(float $contentWidth): float
    {
        if (is_string($this->width)) {
            $trimmed = trim($this->width);
            if (str_ends_with($trimmed, '%')) {
                $pct = (float) substr($trimmed, 0, -1);
                $resolved = $contentWidth * ($pct / 100.0);
            } else {
                $resolved = (float) $trimmed;
            }
        } else {
            $resolved = (float) $this->width;
        }

        return min(max($resolved, 0.0), $contentWidth);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type'         => 'horizontal_rule',
            'width'        => $this->width,
            'thickness'    => $this->thickness,
            'color'        => $this->color,
            'alignment'    => $this->alignment->value,
            'marginTop'    => $this->marginTop,
            'marginBottom' => $this->marginBottom,
        ];
    }
}
