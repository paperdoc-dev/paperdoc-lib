<?php

declare(strict_types=1);

namespace Paperdoc\Document\Style;

use Paperdoc\Enum\Alignment;

/**
 * En-tête ou pied de page commun à toutes les pages d'un document.
 *
 * Le template peut contenir les placeholders suivants, remplacés au
 * moment du rendu :
 *  - {page}   : numéro de page courante (1-indexé)
 *  - {pages}  : nombre total de pages
 *  - {title}  : titre du document
 *  - {date}   : date du jour (Y-m-d)
 *  - {datetime} : date+heure (Y-m-d H:i)
 */
final class RunningElement implements \JsonSerializable
{
    public const TYPE_HEADER = 'header';
    public const TYPE_FOOTER = 'footer';

    private string    $template;
    private Alignment $alignment;
    private TextStyle $style;
    private float     $height = 30.0;

    public function __construct(string $template = '', ?Alignment $alignment = null, ?TextStyle $style = null)
    {
        $this->template  = $template;
        $this->alignment = $alignment ?? Alignment::CENTER;
        $this->style     = $style ?? (new TextStyle())->setFontSize(9.0)->setColor('#6B7280');
    }

    public static function make(string $template = ''): static
    {
        return new static($template);
    }

    /* -------------------------------------------------------------
     | Getters
     |------------------------------------------------------------- */

    public function getTemplate(): string  { return $this->template; }
    public function getAlignment(): Alignment { return $this->alignment; }
    public function getStyle(): TextStyle  { return $this->style; }
    public function getHeight(): float     { return $this->height; }

    /* -------------------------------------------------------------
     | Fluent setters
     |------------------------------------------------------------- */

    public function setTemplate(string $template): static
    {
        $this->template = $template;

        return $this;
    }

    public function setAlignment(Alignment $alignment): static
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function setStyle(TextStyle $style): static
    {
        $this->style = $style;

        return $this;
    }

    public function setHeight(float $height): static
    {
        $this->height = $height;

        return $this;
    }

    /* -------------------------------------------------------------
     | Resolution
     |------------------------------------------------------------- */

    /**
     * Remplace les placeholders par leurs valeurs.
     */
    public function resolve(int $pageNumber, int $totalPages, string $title = ''): string
    {
        return strtr($this->template, [
            '{page}'     => (string) $pageNumber,
            '{pages}'    => (string) $totalPages,
            '{title}'    => $title,
            '{date}'     => date('Y-m-d'),
            '{datetime}' => date('Y-m-d H:i'),
        ]);
    }

    /* -------------------------------------------------------------
     | JsonSerializable
     |------------------------------------------------------------- */

    public function jsonSerialize(): mixed
    {
        return [
            'template'  => $this->template,
            'alignment' => $this->alignment->value,
            'style'     => $this->style,
            'height'    => $this->height,
        ];
    }
}
