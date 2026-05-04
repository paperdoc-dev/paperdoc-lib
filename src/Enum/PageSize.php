<?php

declare(strict_types=1);

namespace Paperdoc\Enum;

/**
 * Tailles de page standard exprimées en points PDF (1 pt = 1/72 pouce).
 *
 * Les dimensions sont fournies en orientation portrait. Pour une orientation
 * paysage, utiliser PageSetup::landscape() qui inverse largeur/hauteur.
 */
enum PageSize: string
{
    case A3       = 'a3';
    case A4       = 'a4';
    case A5       = 'a5';
    case A6       = 'a6';
    case LETTER   = 'letter';
    case LEGAL    = 'legal';
    case TABLOID  = 'tabloid';
    case EXECUTIVE = 'executive';

    /**
     * @return array{0: float, 1: float} [width, height] en points
     */
    public function dimensions(): array
    {
        return match ($this) {
            self::A3        => [841.89, 1190.55],
            self::A4        => [595.28, 841.89],
            self::A5        => [419.53, 595.28],
            self::A6        => [297.64, 419.53],
            self::LETTER    => [612.00, 792.00],
            self::LEGAL     => [612.00, 1008.00],
            self::TABLOID   => [792.00, 1224.00],
            self::EXECUTIVE => [521.86, 756.00],
        };
    }

    public function width(): float
    {
        return $this->dimensions()[0];
    }

    public function height(): float
    {
        return $this->dimensions()[1];
    }
}
