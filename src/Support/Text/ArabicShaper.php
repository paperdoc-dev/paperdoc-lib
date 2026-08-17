<?php

declare(strict_types=1);

namespace Paperdoc\Support\Text;

/**
 * Façonnage contextuel de l'arabe.
 *
 * Une lettre arabe change de forme selon ses voisines : isolée, initiale,
 * médiane ou finale. Les polices OpenType font ce travail via leurs tables
 * GSUB, mais un PDF adresse les glyphes directement — sans façonnage
 * préalable les lettres sortent toutes en forme isolée, détachées les unes
 * des autres, ce qu'un lecteur arabe lit très mal.
 *
 * Cette classe remplace chaque lettre par la forme de présentation
 * correspondante (bloc Arabic Presentation Forms-B, U+FE70–U+FEFF), que
 * toute police couvrant l'arabe expose dans sa cmap.
 *
 * À appliquer dans l'ordre logique, avant le réordonnancement bidi.
 */
final class ArabicShaper
{
    /** Ne se lie qu'à droite (pas de forme initiale ni médiane). */
    private const JOIN_RIGHT = 'R';

    /** Se lie des deux côtés. */
    private const JOIN_DUAL = 'D';

    /** Marques et diacritiques : ignorés pour déterminer le contexte. */
    private const JOIN_TRANSPARENT = 'T';

    /**
     * Formes de présentation : point de code → [isolée, finale, initiale, médiane].
     * Une entrée à 0 signifie que la forme n'existe pas pour cette lettre.
     *
     * @var array<int, array{int, int, int, int}>
     */
    private const FORMS = [
        0x0621 => [0xFE80, 0,      0,      0],      // hamza
        0x0622 => [0xFE81, 0xFE82, 0,      0],      // alef madda
        0x0623 => [0xFE83, 0xFE84, 0,      0],      // alef hamza above
        0x0624 => [0xFE85, 0xFE86, 0,      0],      // waw hamza
        0x0625 => [0xFE87, 0xFE88, 0,      0],      // alef hamza below
        0x0626 => [0xFE89, 0xFE8A, 0xFE8B, 0xFE8C], // yeh hamza
        0x0627 => [0xFE8D, 0xFE8E, 0,      0],      // alef
        0x0628 => [0xFE8F, 0xFE90, 0xFE91, 0xFE92], // beh
        0x0629 => [0xFE93, 0xFE94, 0,      0],      // teh marbuta
        0x062A => [0xFE95, 0xFE96, 0xFE97, 0xFE98], // teh
        0x062B => [0xFE99, 0xFE9A, 0xFE9B, 0xFE9C], // theh
        0x062C => [0xFE9D, 0xFE9E, 0xFE9F, 0xFEA0], // jeem
        0x062D => [0xFEA1, 0xFEA2, 0xFEA3, 0xFEA4], // hah
        0x062E => [0xFEA5, 0xFEA6, 0xFEA7, 0xFEA8], // khah
        0x062F => [0xFEA9, 0xFEAA, 0,      0],      // dal
        0x0630 => [0xFEAB, 0xFEAC, 0,      0],      // thal
        0x0631 => [0xFEAD, 0xFEAE, 0,      0],      // reh
        0x0632 => [0xFEAF, 0xFEB0, 0,      0],      // zain
        0x0633 => [0xFEB1, 0xFEB2, 0xFEB3, 0xFEB4], // seen
        0x0634 => [0xFEB5, 0xFEB6, 0xFEB7, 0xFEB8], // sheen
        0x0635 => [0xFEB9, 0xFEBA, 0xFEBB, 0xFEBC], // sad
        0x0636 => [0xFEBD, 0xFEBE, 0xFEBF, 0xFEC0], // dad
        0x0637 => [0xFEC1, 0xFEC2, 0xFEC3, 0xFEC4], // tah
        0x0638 => [0xFEC5, 0xFEC6, 0xFEC7, 0xFEC8], // zah
        0x0639 => [0xFEC9, 0xFECA, 0xFECB, 0xFECC], // ain
        0x063A => [0xFECD, 0xFECE, 0xFECF, 0xFED0], // ghain
        0x0640 => [0x0640, 0x0640, 0x0640, 0x0640], // tatweel
        0x0641 => [0xFED1, 0xFED2, 0xFED3, 0xFED4], // feh
        0x0642 => [0xFED5, 0xFED6, 0xFED7, 0xFED8], // qaf
        0x0643 => [0xFED9, 0xFEDA, 0xFEDB, 0xFEDC], // kaf
        0x0644 => [0xFEDD, 0xFEDE, 0xFEDF, 0xFEE0], // lam
        0x0645 => [0xFEE1, 0xFEE2, 0xFEE3, 0xFEE4], // meem
        0x0646 => [0xFEE5, 0xFEE6, 0xFEE7, 0xFEE8], // noon
        0x0647 => [0xFEE9, 0xFEEA, 0xFEEB, 0xFEEC], // heh
        0x0648 => [0xFEED, 0xFEEE, 0,      0],      // waw
        0x0649 => [0xFEEF, 0xFEF0, 0,      0],      // alef maksura
        0x064A => [0xFEF1, 0xFEF2, 0xFEF3, 0xFEF4], // yeh
        0x0671 => [0xFB50, 0xFB51, 0,      0],      // alef wasla
        0x0679 => [0xFB66, 0xFB67, 0xFB68, 0xFB69], // tteh
        0x067E => [0xFB56, 0xFB57, 0xFB58, 0xFB59], // peh
        0x0686 => [0xFB7A, 0xFB7B, 0xFB7C, 0xFB7D], // tcheh
        0x0688 => [0xFB88, 0xFB89, 0,      0],      // ddal
        0x0691 => [0xFB8C, 0xFB8D, 0,      0],      // rreh
        0x0698 => [0xFB8A, 0xFB8B, 0,      0],      // jeh
        0x06A9 => [0xFB8E, 0xFB8F, 0xFB90, 0xFB91], // keheh
        0x06AF => [0xFB92, 0xFB93, 0xFB94, 0xFB95], // gaf
        0x06BA => [0xFB9E, 0xFB9F, 0,      0],      // noon ghunna
        0x06BE => [0xFBAA, 0xFBAB, 0xFBAC, 0xFBAD], // heh doachashmee
        0x06C1 => [0xFBA6, 0xFBA7, 0xFBA8, 0xFBA9], // heh goal
        0x06CC => [0xFBFC, 0xFBFD, 0xFBFE, 0xFBFF], // farsi yeh
        0x06D2 => [0xFBAE, 0xFBAF, 0,      0],      // yeh barree
    ];

    /**
     * Ligatures lam-alef : [isolée, finale]. Une séquence lam + alef doit
     * fusionner en un seul glyphe, c'est obligatoire en arabe.
     *
     * @var array<int, array{int, int}>
     */
    private const LAM_ALEF = [
        0x0622 => [0xFEF5, 0xFEF6],
        0x0623 => [0xFEF7, 0xFEF8],
        0x0625 => [0xFEF9, 0xFEFA],
        0x0627 => [0xFEFB, 0xFEFC],
    ];

    /**
     * Lettres ne se liant qu'à droite : elles acceptent une forme finale
     * mais coupent la liaison vers la lettre suivante.
     */
    private const RIGHT_JOINING = [
        0x0622, 0x0623, 0x0624, 0x0625, 0x0627, 0x0629, 0x062F, 0x0630,
        0x0631, 0x0632, 0x0648, 0x0649, 0x0671, 0x0688, 0x0691, 0x0698,
        0x06BA, 0x06D2,
    ];

    /**
     * Remplace les lettres arabes par leur forme contextuelle. Le texte
     * reste dans l'ordre logique ; c'est au réordonnancement bidi de le
     * mettre en ordre visuel.
     */
    public static function shape(string $text): string
    {
        if (! self::containsArabic($text)) {
            return $text;
        }

        $codepoints = self::codepoints($text);
        $codepoints = self::applyLamAlefLigatures($codepoints);

        $shaped = [];
        $count = count($codepoints);

        for ($i = 0; $i < $count; $i++) {
            $codepoint = $codepoints[$i];

            if (! isset(self::FORMS[$codepoint])) {
                $shaped[] = $codepoint;

                continue;
            }

            // Les marques sont transparentes : sauter par-dessus pour
            // trouver les voisines qui déterminent réellement la liaison.
            $previous = self::neighbour($codepoints, $i, -1);
            $next     = self::neighbour($codepoints, $i, 1);

            $joinsRight = $previous !== null && self::joinsToFollowing($previous);
            $joinsLeft  = $next !== null && self::joinsToPreceding($next);

            $forms = self::FORMS[$codepoint];

            $form = match (true) {
                $joinsRight && $joinsLeft => $forms[3] ?: ($forms[1] ?: $forms[0]),
                $joinsRight               => $forms[1] ?: $forms[0],
                $joinsLeft                => $forms[2] ?: $forms[0],
                default                   => $forms[0],
            };

            $shaped[] = $form;
        }

        return self::toUtf8($shaped);
    }

    public static function containsArabic(string $text): bool
    {
        return preg_match('/\p{Arabic}/u', $text) === 1;
    }

    /**
     * Fusionne lam + alef en une ligature unique — non optionnel en arabe.
     *
     * @param int[] $codepoints
     * @return int[]
     */
    private static function applyLamAlefLigatures(array $codepoints): array
    {
        $result = [];
        $count = count($codepoints);

        for ($i = 0; $i < $count; $i++) {
            $current = $codepoints[$i];
            $next = $codepoints[$i + 1] ?? null;

            if ($current === 0x0644 && $next !== null && isset(self::LAM_ALEF[$next])) {
                $previous = self::previousNonTransparent($result);
                $joinsRight = $previous !== null && self::joinsToFollowing($previous);

                $result[] = self::LAM_ALEF[$next][$joinsRight ? 1 : 0];
                $i++;

                continue;
            }

            $result[] = $current;
        }

        return $result;
    }

    /**
     * Voisine significative dans la direction donnée, marques ignorées.
     *
     * @param int[] $codepoints
     */
    private static function neighbour(array $codepoints, int $index, int $step): ?int
    {
        for ($i = $index + $step; isset($codepoints[$i]); $i += $step) {
            if (self::joiningType($codepoints[$i]) !== self::JOIN_TRANSPARENT) {
                return $codepoints[$i];
            }
        }

        return null;
    }

    /**
     * @param int[] $codepoints
     */
    private static function previousNonTransparent(array $codepoints): ?int
    {
        for ($i = count($codepoints) - 1; $i >= 0; $i--) {
            if (self::joiningType($codepoints[$i]) !== self::JOIN_TRANSPARENT) {
                return $codepoints[$i];
            }
        }

        return null;
    }

    /**
     * La lettre se lie-t-elle vers la suivante (donc à gauche) ?
     */
    private static function joinsToFollowing(int $codepoint): bool
    {
        return self::joiningType($codepoint) === self::JOIN_DUAL;
    }

    /**
     * La lettre accepte-t-elle une liaison venant de la précédente ?
     */
    private static function joinsToPreceding(int $codepoint): bool
    {
        $type = self::joiningType($codepoint);

        return $type === self::JOIN_DUAL || $type === self::JOIN_RIGHT;
    }

    private static function joiningType(int $codepoint): string
    {
        // Diacritiques et signes vocaliques arabes
        if (($codepoint >= 0x064B && $codepoint <= 0x065F)
            || $codepoint === 0x0670
            || ($codepoint >= 0x06D6 && $codepoint <= 0x06ED)
            || ($codepoint >= 0x0610 && $codepoint <= 0x061A)) {
            return self::JOIN_TRANSPARENT;
        }

        // Une ligature lam-alef déjà formée accepte encore la liaison de la
        // lettre qui la précède : sans ce cas, celle-ci sortirait en forme
        // isolée et couperait le mot en deux.
        if ($codepoint >= 0xFEF5 && $codepoint <= 0xFEFC) {
            return self::JOIN_RIGHT;
        }

        if (in_array($codepoint, self::RIGHT_JOINING, true)) {
            return self::JOIN_RIGHT;
        }

        if (isset(self::FORMS[$codepoint])) {
            return self::JOIN_DUAL;
        }

        return 'U';
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
