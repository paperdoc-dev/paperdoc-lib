<?php

declare(strict_types=1);

namespace Paperdoc\Support\Text;

/**
 * Algorithme bidirectionnel Unicode — [UAX #9].
 *
 * HTML et DOCX déclarent le sens et laissent le moteur hôte faire ce
 * travail ; PDF, lui, pose les glyphes exactement où on les lui dit.
 * Sans réordonnancement, une phrase arabe ou hébraïque sort à l'envers.
 *
 * Sont implémentées les règles P2–P3, W1–W7, N0–N2, I1–I2 et L1–L4.
 * Les codes d'incorporation explicites (LRE, RLE, LRO, RLO, PDF, LRI,
 * RLI, FSI, PDI) sont traités comme neutres bornés plutôt qu'empilés :
 * ils sont rarissimes dans du texte de document, et les ignorer vaut
 * bien mieux que de ne pas réordonner du tout.
 */
final class Bidi
{
    /**
     * Paires de parenthèses pour la règle N0, et miroirs pour la règle L4.
     *
     * @var array<int, int>
     */
    private const MIRRORED = [
        0x0028 => 0x0029, 0x0029 => 0x0028, // ( )
        0x005B => 0x005D, 0x005D => 0x005B, // [ ]
        0x007B => 0x007D, 0x007D => 0x007B, // { }
        0x003C => 0x003E, 0x003E => 0x003C, // < >
        0x00AB => 0x00BB, 0x00BB => 0x00AB, // « »
        0x2039 => 0x203A, 0x203A => 0x2039, // ‹ ›
        0x2045 => 0x2046, 0x2046 => 0x2045,
        0x207D => 0x207E, 0x207E => 0x207D,
        0x208D => 0x208E, 0x208E => 0x208D,
        0x2308 => 0x2309, 0x2309 => 0x2308,
        0x230A => 0x230B, 0x230B => 0x230A,
        0x2329 => 0x232A, 0x232A => 0x2329,
        0x3008 => 0x3009, 0x3009 => 0x3008,
        0x300A => 0x300B, 0x300B => 0x300A,
        0x300C => 0x300D, 0x300D => 0x300C,
        0x300E => 0x300F, 0x300F => 0x300E,
        0x3010 => 0x3011, 0x3011 => 0x3010,
        0x3014 => 0x3015, 0x3015 => 0x3014,
        0xFF08 => 0xFF09, 0xFF09 => 0xFF08,
        0xFF3B => 0xFF3D, 0xFF3D => 0xFF3B,
        0xFF5B => 0xFF5D, 0xFF5D => 0xFF5B,
    ];

    /**
     * Détection rapide d'un caractère droite-à-gauche, écritures et
     * chiffres arabes compris.
     */
    private const RTL_SCAN = '/[\p{Arabic}\p{Hebrew}\p{Syriac}\p{Thaana}\p{Nko}\p{Samaritan}\p{Mandaic}\p{Adlam}]/u';

    /** Parenthèses ouvrantes, pour l'appariement de la règle BD16. */
    private const OPENING = [0x0028, 0x005B, 0x007B, 0x2045, 0x207D, 0x208D, 0x2308, 0x230A, 0x2329, 0x3008, 0x300A, 0x300C, 0x300E, 0x3010, 0x3014, 0xFF08, 0xFF3B, 0xFF5B];

    /**
     * Réordonne le texte de l'ordre logique vers l'ordre visuel.
     *
     * @param bool|null $rtlParagraph forcer le sens du paragraphe ;
     *                                null = déduit du premier caractère fort (P2/P3)
     */
    public static function reorder(string $text, ?bool $rtlParagraph = null): string
    {
        // Filtre préalable : classifier caractère par caractère coûte cher,
        // et l'écrasante majorité des chaînes n'a rien de droite-à-gauche.
        // Sans lui, tout rendu latin paie le prix de l'algorithme complet.
        if ($rtlParagraph !== true && preg_match(self::RTL_SCAN, $text) !== 1) {
            return $text;
        }

        $codepoints = self::codepoints($text);

        if ($codepoints === []) {
            return $text;
        }

        $classes = array_map(static fn (int $cp): string => BidiClass::of($cp), $codepoints);

        if ($rtlParagraph !== true && ! self::hasStrongRtl($classes)) {
            return $text;
        }

        $paragraphLevel = $rtlParagraph ?? self::paragraphLevel($classes);
        $paragraphLevel = $paragraphLevel ? 1 : 0;

        $resolved = self::resolveWeakTypes($classes, $paragraphLevel);
        $resolved = self::resolveBrackets($codepoints, $resolved, $paragraphLevel);
        $resolved = self::resolveNeutrals($resolved, $paragraphLevel);

        $levels = self::resolveImplicitLevels($resolved, $paragraphLevel);
        $levels = self::resetTrailingWhitespace($classes, $levels, $paragraphLevel);

        $order = self::reverseRuns($levels);

        $visual = [];

        foreach ($order as $index) {
            $codepoint = $codepoints[$index];

            // L4 : un caractère miroir vu dans un niveau impair se retourne.
            if (($levels[$index] % 2) === 1 && isset(self::MIRRORED[$codepoint])) {
                $codepoint = self::MIRRORED[$codepoint];
            }

            $visual[] = $codepoint;
        }

        return self::toUtf8($visual);
    }

    /**
     * @param string[] $classes
     */
    private static function hasStrongRtl(array $classes): bool
    {
        foreach ($classes as $class) {
            if ($class === BidiClass::R || $class === BidiClass::AL || $class === BidiClass::AN) {
                return true;
            }
        }

        return false;
    }

    /**
     * P2/P3 : le niveau du paragraphe vient du premier caractère fort.
     *
     * @param string[] $classes
     */
    private static function paragraphLevel(array $classes): bool
    {
        foreach ($classes as $class) {
            if ($class === BidiClass::L) {
                return false;
            }

            if ($class === BidiClass::R || $class === BidiClass::AL) {
                return true;
            }
        }

        return false;
    }

    /**
     * Règles W1 à W7 : résolution des types faibles.
     *
     * @param string[] $classes
     * @return string[]
     */
    private static function resolveWeakTypes(array $classes, int $paragraphLevel): array
    {
        $sos = $paragraphLevel === 1 ? BidiClass::R : BidiClass::L;
        $count = count($classes);

        // W1 : une marque prend le type de son voisin de gauche.
        $previous = $sos;
        for ($i = 0; $i < $count; $i++) {
            if ($classes[$i] === BidiClass::NSM) {
                $classes[$i] = $previous;
            }

            $previous = $classes[$i];
        }

        // W2 : un chiffre européen devient arabe si le dernier fort est AL.
        $lastStrong = $sos;
        for ($i = 0; $i < $count; $i++) {
            if (in_array($classes[$i], [BidiClass::L, BidiClass::R, BidiClass::AL], true)) {
                $lastStrong = $classes[$i];
            } elseif ($classes[$i] === BidiClass::EN && $lastStrong === BidiClass::AL) {
                $classes[$i] = BidiClass::AN;
            }
        }

        // W3 : AL devient R.
        foreach ($classes as $i => $class) {
            if ($class === BidiClass::AL) {
                $classes[$i] = BidiClass::R;
            }
        }

        // W4 : un séparateur unique entre deux chiffres de même type.
        for ($i = 1; $i < $count - 1; $i++) {
            $before = $classes[$i - 1];
            $after  = $classes[$i + 1];

            if ($classes[$i] === BidiClass::ES && $before === BidiClass::EN && $after === BidiClass::EN) {
                $classes[$i] = BidiClass::EN;
            } elseif ($classes[$i] === BidiClass::CS && $before === $after
                && ($before === BidiClass::EN || $before === BidiClass::AN)) {
                $classes[$i] = $before;
            }
        }

        // W5 : une suite de terminateurs adjacente à un chiffre européen.
        for ($i = 0; $i < $count; $i++) {
            if ($classes[$i] !== BidiClass::ET) {
                continue;
            }

            $end = $i;
            while ($end < $count && $classes[$end] === BidiClass::ET) {
                $end++;
            }

            $adjacent = ($i > 0 && $classes[$i - 1] === BidiClass::EN)
                || ($end < $count && $classes[$end] === BidiClass::EN);

            if ($adjacent) {
                for ($j = $i; $j < $end; $j++) {
                    $classes[$j] = BidiClass::EN;
                }
            }

            $i = $end - 1;
        }

        // W6 : les séparateurs et terminateurs restants deviennent neutres.
        foreach ($classes as $i => $class) {
            if (in_array($class, [BidiClass::ES, BidiClass::ET, BidiClass::CS, BidiClass::BN], true)) {
                $classes[$i] = BidiClass::ON;
            }
        }

        // W7 : un chiffre européen devient L si le dernier fort est L.
        $lastStrong = $sos;
        for ($i = 0; $i < $count; $i++) {
            if ($classes[$i] === BidiClass::L || $classes[$i] === BidiClass::R) {
                $lastStrong = $classes[$i];
            } elseif ($classes[$i] === BidiClass::EN && $lastStrong === BidiClass::L) {
                $classes[$i] = BidiClass::L;
            }
        }

        return $classes;
    }

    /**
     * Règle N0 : une paire de parenthèses encadrant du texte fort prend
     * ce sens, pour que « (texte) » ne se retrouve pas retourné au milieu
     * d'une phrase.
     *
     * @param int[]    $codepoints
     * @param string[] $classes
     * @return string[]
     */
    private static function resolveBrackets(array $codepoints, array $classes, int $paragraphLevel): array
    {
        $embedding = $paragraphLevel === 1 ? BidiClass::R : BidiClass::L;
        $opposite  = $paragraphLevel === 1 ? BidiClass::L : BidiClass::R;

        $stack = [];
        $pairs = [];

        foreach ($codepoints as $i => $codepoint) {
            if ($classes[$i] !== BidiClass::ON) {
                continue;
            }

            if (in_array($codepoint, self::OPENING, true)) {
                $stack[] = [$i, self::MIRRORED[$codepoint]];

                continue;
            }

            for ($s = count($stack) - 1; $s >= 0; $s--) {
                if ($stack[$s][1] === $codepoint) {
                    $pairs[] = [$stack[$s][0], $i];
                    $stack = array_slice($stack, 0, $s);

                    break;
                }
            }
        }

        foreach ($pairs as [$open, $close]) {
            $found = null;

            for ($i = $open + 1; $i < $close; $i++) {
                $strong = self::strongDirection($classes[$i]);

                if ($strong === null) {
                    continue;
                }

                if ($strong === $embedding) {
                    $found = $embedding;

                    break;
                }

                $found = $opposite;
            }

            if ($found === null) {
                continue;
            }

            // Le sens opposé n'est retenu que s'il est aussi celui du
            // contexte précédant la parenthèse ouvrante.
            if ($found === $opposite && self::precedingStrong($classes, $open, $paragraphLevel) !== $opposite) {
                $found = $embedding;
            }

            $classes[$open] = $found;
            $classes[$close] = $found;
        }

        return $classes;
    }

    private static function strongDirection(string $class): ?string
    {
        return match ($class) {
            BidiClass::L                            => BidiClass::L,
            BidiClass::R, BidiClass::EN, BidiClass::AN => BidiClass::R,
            default                                 => null,
        };
    }

    /**
     * @param string[] $classes
     */
    private static function precedingStrong(array $classes, int $position, int $paragraphLevel): string
    {
        for ($i = $position - 1; $i >= 0; $i--) {
            $strong = self::strongDirection($classes[$i]);

            if ($strong !== null) {
                return $strong;
            }
        }

        return $paragraphLevel === 1 ? BidiClass::R : BidiClass::L;
    }

    /**
     * Règles N1 et N2 : les neutres prennent le sens qui les entoure,
     * sinon celui du paragraphe.
     *
     * @param string[] $classes
     * @return string[]
     */
    private static function resolveNeutrals(array $classes, int $paragraphLevel): array
    {
        $embedding = $paragraphLevel === 1 ? BidiClass::R : BidiClass::L;
        $count = count($classes);
        $i = 0;

        while ($i < $count) {
            if (! BidiClass::isNeutral($classes[$i])) {
                $i++;

                continue;
            }

            $end = $i;
            while ($end < $count && BidiClass::isNeutral($classes[$end])) {
                $end++;
            }

            $before = $i > 0 ? self::strongDirection($classes[$i - 1]) : null;
            $after  = $end < $count ? self::strongDirection($classes[$end]) : null;

            $before ??= $embedding;
            $after  ??= $embedding;

            $resolved = $before === $after ? $before : $embedding;

            for ($j = $i; $j < $end; $j++) {
                $classes[$j] = $resolved;
            }

            $i = $end;
        }

        return $classes;
    }

    /**
     * Règles I1 et I2 : niveau d'incorporation de chaque caractère.
     *
     * @param string[] $classes
     * @return int[]
     */
    private static function resolveImplicitLevels(array $classes, int $paragraphLevel): array
    {
        $levels = [];

        foreach ($classes as $class) {
            if ($paragraphLevel === 0) {
                $levels[] = match ($class) {
                    BidiClass::R              => 1,
                    BidiClass::AN, BidiClass::EN => 2,
                    default                   => 0,
                };
            } else {
                $levels[] = match ($class) {
                    BidiClass::L, BidiClass::AN, BidiClass::EN => 2,
                    default                                    => 1,
                };
            }
        }

        return $levels;
    }

    /**
     * Règle L1 : les blancs de fin de ligne reviennent au niveau du
     * paragraphe, sinon ils migreraient du mauvais côté.
     *
     * @param string[] $originalClasses
     * @param int[]    $levels
     * @return int[]
     */
    private static function resetTrailingWhitespace(array $originalClasses, array $levels, int $paragraphLevel): array
    {
        for ($i = count($levels) - 1; $i >= 0; $i--) {
            $class = $originalClasses[$i];

            if ($class === BidiClass::WS || $class === BidiClass::S
                || $class === BidiClass::B || $class === BidiClass::BN) {
                $levels[$i] = $paragraphLevel;

                continue;
            }

            break;
        }

        return $levels;
    }

    /**
     * Règle L2 : inverser les suites de caractères, du niveau le plus
     * élevé jusqu'au premier niveau impair.
     *
     * @param int[] $levels
     * @return int[] indices dans l'ordre visuel
     */
    private static function reverseRuns(array $levels): array
    {
        $order = array_keys($levels);

        if ($levels === []) {
            return $order;
        }

        $highest = max($levels);
        $lowestOdd = PHP_INT_MAX;

        foreach ($levels as $level) {
            if (($level % 2) === 1) {
                $lowestOdd = min($lowestOdd, $level);
            }
        }

        if ($lowestOdd === PHP_INT_MAX) {
            return $order;
        }

        for ($level = $highest; $level >= $lowestOdd; $level--) {
            $start = null;
            $count = count($order);

            for ($i = 0; $i <= $count; $i++) {
                $inRun = $i < $count && $levels[$order[$i]] >= $level;

                if ($inRun && $start === null) {
                    $start = $i;
                } elseif (! $inRun && $start !== null) {
                    $slice = array_reverse(array_slice($order, $start, $i - $start));
                    array_splice($order, $start, $i - $start, $slice);
                    $start = null;
                }
            }
        }

        return $order;
    }

    /**
     * @return int[]
     */
    private static function codepoints(string $text): array
    {
        $result = [];
        $converted = mb_convert_encoding($text, 'UTF-32BE', 'UTF-8');
        $length = strlen($converted);

        for ($i = 0; $i + 4 <= $length; $i += 4) {
            $unpacked = unpack('N', substr($converted, $i, 4));

            if ($unpacked !== false && is_int($unpacked[1])) {
                $result[] = $unpacked[1];
            }
        }

        return $result;
    }

    /**
     * @param int[] $codepoints
     */
    private static function toUtf8(array $codepoints): string
    {
        $packed = '';

        foreach ($codepoints as $codepoint) {
            $packed .= pack('N', $codepoint);
        }

        return mb_convert_encoding($packed, 'UTF-8', 'UTF-32BE');
    }
}
