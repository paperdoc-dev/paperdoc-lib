<?php

declare(strict_types=1);

namespace Paperdoc\Support\Text;

/**
 * Classification bidirectionnelle des caractères (propriété Bidi_Class
 * d'Unicode), pour l'algorithme de [UAX #9].
 *
 * Les plages sont dérivées de DerivedBidiClass.txt et couvrent les
 * écritures que la bibliothèque sait rendre. Une classification exacte
 * de tout Unicode demanderait d'embarquer la base UCD ; le repli sur
 * L / ON est celui de la norme pour les caractères non listés.
 */
final class BidiClass
{
    public const L   = 'L';   // gauche-à-droite fort
    public const R   = 'R';   // droite-à-gauche fort
    public const AL  = 'AL';  // arabe (droite-à-gauche fort)
    public const EN  = 'EN';  // chiffre européen
    public const ES  = 'ES';  // séparateur de chiffres européens
    public const ET  = 'ET';  // terminateur de chiffres européens
    public const AN  = 'AN';  // chiffre arabe
    public const CS  = 'CS';  // séparateur commun
    public const NSM = 'NSM'; // marque sans chasse
    public const BN  = 'BN';  // neutre borné
    public const B   = 'B';   // séparateur de paragraphe
    public const S   = 'S';   // séparateur de segment
    public const WS  = 'WS';  // blanc
    public const ON  = 'ON';  // neutre

    /** @var array<int, string> mémoïsation par point de code */
    private static array $cache = [];

    public static function of(int $codepoint): string
    {
        return self::$cache[$codepoint] ??= self::classify($codepoint);
    }

    /**
     * Un caractère « neutre ou isolant » au sens des règles N1/N2.
     */
    public static function isNeutral(string $class): bool
    {
        return $class === self::B || $class === self::S
            || $class === self::WS || $class === self::ON;
    }

    private static function classify(int $codepoint): string
    {
        return match (true) {
            self::inAny($codepoint, self::separators())    => self::separatorClass($codepoint),
            self::inAny($codepoint, self::boundaryNeutral()) => self::BN,
            self::inAny($codepoint, self::arabicNumber())  => self::AN,
            self::inAny($codepoint, self::europeanNumber()) => self::EN,
            self::inAny($codepoint, self::europeanSeparator()) => self::ES,
            self::inAny($codepoint, self::commonSeparator()) => self::CS,
            self::inAny($codepoint, self::europeanTerminator()) => self::ET,
            self::isMark($codepoint)                       => self::NSM,
            self::inAny($codepoint, self::arabicLetter())  => self::AL,
            self::inAny($codepoint, self::rightToLeft())   => self::R,
            self::isNeutralCharacter($codepoint)           => self::ON,
            default                                        => self::L,
        };
    }

    /** @return array<int, array{int, int}> */
    private static function separators(): array
    {
        return [
            [0x000A, 0x000A], [0x000D, 0x000D], [0x001C, 0x001E], [0x0085, 0x0085],
            [0x2029, 0x2029],                                     // B
            [0x0009, 0x0009], [0x000B, 0x000B], [0x001F, 0x001F], // S
            [0x000C, 0x000C], [0x0020, 0x0020], [0x1680, 0x1680],
            [0x2000, 0x200A], [0x2028, 0x2028], [0x205F, 0x205F], [0x3000, 0x3000], // WS
        ];
    }

    private static function separatorClass(int $codepoint): string
    {
        if (self::inAny($codepoint, [[0x000A, 0x000A], [0x000D, 0x000D], [0x001C, 0x001E], [0x0085, 0x0085], [0x2029, 0x2029]])) {
            return self::B;
        }

        if (self::inAny($codepoint, [[0x0009, 0x0009], [0x000B, 0x000B], [0x001F, 0x001F]])) {
            return self::S;
        }

        return self::WS;
    }

    /** @return array<int, array{int, int}> */
    private static function boundaryNeutral(): array
    {
        return [
            [0x0000, 0x0008], [0x000E, 0x001B], [0x007F, 0x0084], [0x0086, 0x009F],
            [0x00AD, 0x00AD], [0x200B, 0x200D], [0x2060, 0x2064], [0x206A, 0x206F],
            [0xFEFF, 0xFEFF], [0xFFF9, 0xFFFB],
        ];
    }

    /** @return array<int, array{int, int}> */
    private static function arabicNumber(): array
    {
        return [
            [0x0600, 0x0605], [0x0660, 0x0669], [0x066B, 0x066C], [0x06DD, 0x06DD],
            [0x0890, 0x0891], [0x08E2, 0x08E2], [0x10E60, 0x10E7E],
        ];
    }

    /** @return array<int, array{int, int}> */
    private static function europeanNumber(): array
    {
        return [
            [0x0030, 0x0039], [0x00B2, 0x00B3], [0x00B9, 0x00B9], [0x06F0, 0x06F9],
            [0x2070, 0x2070], [0x2074, 0x2079], [0x2080, 0x2089], [0xFF10, 0xFF19],
        ];
    }

    /** @return array<int, array{int, int}> */
    private static function europeanSeparator(): array
    {
        return [
            [0x002B, 0x002B], [0x002D, 0x002D], [0x207A, 0x207B], [0x208A, 0x208B],
            [0x2212, 0x2212], [0xFB29, 0xFB29], [0xFE62, 0xFE63], [0xFF0B, 0xFF0B],
            [0xFF0D, 0xFF0D],
        ];
    }

    /** @return array<int, array{int, int}> */
    private static function commonSeparator(): array
    {
        return [
            [0x002C, 0x002C], [0x002E, 0x002F], [0x003A, 0x003A], [0x00A0, 0x00A0],
            [0x060C, 0x060C], [0x202F, 0x202F], [0x2044, 0x2044], [0xFE50, 0xFE50],
            [0xFE52, 0xFE52], [0xFE55, 0xFE55], [0xFF0C, 0xFF0C], [0xFF0E, 0xFF0F],
            [0xFF1A, 0xFF1A],
        ];
    }

    /** @return array<int, array{int, int}> */
    private static function europeanTerminator(): array
    {
        return [
            [0x0023, 0x0025], [0x00A2, 0x00A5], [0x00B0, 0x00B1], [0x066A, 0x066A],
            [0x09F2, 0x09F3], [0x0AF1, 0x0AF1], [0x0BF9, 0x0BF9], [0x0E3F, 0x0E3F],
            [0x17DB, 0x17DB], [0x20A0, 0x20BF], [0x212E, 0x212E], [0x2213, 0x2213],
            [0xFE5F, 0xFE5F], [0xFE69, 0xFE6A], [0xFF03, 0xFF05], [0xFFE0, 0xFFE1],
            [0xFFE5, 0xFFE6],
        ];
    }

    /** @return array<int, array{int, int}> */
    private static function arabicLetter(): array
    {
        return [
            [0x0608, 0x0608], [0x060B, 0x060B], [0x060D, 0x060D], [0x061B, 0x064A],
            [0x066D, 0x066F], [0x0671, 0x06D5], [0x06E5, 0x06E6], [0x06EE, 0x06EF],
            [0x06FA, 0x070D], [0x0710, 0x0710], [0x0712, 0x072F], [0x074D, 0x07A5],
            [0x07B1, 0x07B1], [0x0860, 0x086A], [0x08A0, 0x08C9],
            [0xFB50, 0xFD3D], [0xFD50, 0xFDFC], [0xFE70, 0xFEFC],
        ];
    }

    /** @return array<int, array{int, int}> */
    private static function rightToLeft(): array
    {
        return [
            [0x0590, 0x05FF], [0x07C0, 0x085F], [0xFB1D, 0xFB4F],
            [0x10800, 0x10CFF], [0x1E800, 0x1EC6F], [0x1ECC0, 0x1ECFF],
            [0x1ED00, 0x1EDFF], [0x1EE00, 0x1EEFF],
        ];
    }

    private static function isMark(int $codepoint): bool
    {
        // Un point de code invalide donne la chaîne vide, qui ne peut
        // correspondre à aucune classe : c'est le repli voulu.
        return preg_match('/^[\p{Mn}\p{Me}]$/u', mb_chr($codepoint, 'UTF-8') ?: '') === 1;
    }

    private static function isNeutralCharacter(int $codepoint): bool
    {
        return preg_match('/^[\p{P}\p{S}\p{Z}\p{Cf}]$/u', mb_chr($codepoint, 'UTF-8') ?: '') === 1;
    }

    /**
     * @param array<int, array{int, int}> $ranges
     */
    private static function inAny(int $codepoint, array $ranges): bool
    {
        foreach ($ranges as [$start, $end]) {
            if ($codepoint >= $start && $codepoint <= $end) {
                return true;
            }
        }

        return false;
    }
}
