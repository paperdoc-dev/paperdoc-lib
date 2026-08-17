<?php

declare(strict_types=1);

namespace Paperdoc\Support;

/**
 * Détection du sens d'écriture d'un texte.
 *
 * Les formats balisés (HTML, DOCX) savent appliquer eux-mêmes l'algorithme
 * bidirectionnel Unicode ; encore faut-il leur déclarer le sens. Cette
 * classe fournit cette déclaration — elle ne réordonne rien.
 */
final class TextDirection
{
    public const LTR = 'ltr';
    public const RTL = 'rtl';

    /**
     * Écritures de droite à gauche, par propriété de script PCRE plutôt
     * que par plages de points de code : la liste reste juste quand
     * Unicode étend un bloc.
     */
    private const RTL_PATTERN = '/[\p{Arabic}\p{Hebrew}\p{Syriac}\p{Thaana}\p{Nko}\p{Samaritan}\p{Mandaic}\p{Adlam}]/u';

    /**
     * Sens dominant : on compare les lettres fortes de chaque sens. Un
     * document majoritairement arabe ou hébreu contenant des mots latins
     * ou des chiffres reste RTL.
     */
    public static function detect(string $text): string
    {
        return self::isRtl($text) ? self::RTL : self::LTR;
    }

    public static function isRtl(string $text): bool
    {
        $rtl = preg_match_all(self::RTL_PATTERN, $text);

        if ($rtl === false || $rtl === 0) {
            return false;
        }

        $letters = preg_match_all('/\p{L}/u', $text);

        return $rtl > ($letters === false ? 0 : $letters - $rtl);
    }
}
