#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Builds a WinAnsi (cp1252) byte-code → 1000em-width map for the
 * 14 standard PDF fonts, using URW Base35 AFM files (which are
 * metric-compatible with Adobe's Core 14, the "facts" we need
 * are pure metric numbers, not creative content).
 *
 * Run from the repo root:
 *
 *   php tools/build-afm-widths.php
 *
 * Writes src/Support/Pdf/Core14Widths.php in place.
 *
 * Source AFM files are not shipped with paperdoc-lib — only the
 * generated width tables (numbers, ~3.5 KB) are committed.
 */

const AFM_DIR = '/usr/share/fonts/type1/urw-base35';

// PDF Core 14 PostScript name => URW Base35 metric-compatible AFM file
const FONT_MAP = [
    'Helvetica'             => 'NimbusSans-Regular.afm',
    'Helvetica-Bold'        => 'NimbusSans-Bold.afm',
    'Helvetica-Oblique'     => 'NimbusSans-Italic.afm',
    'Helvetica-BoldOblique' => 'NimbusSans-BoldItalic.afm',
    'Times-Roman'           => 'NimbusRoman-Regular.afm',
    'Times-Bold'            => 'NimbusRoman-Bold.afm',
    'Times-Italic'          => 'NimbusRoman-Italic.afm',
    'Times-BoldItalic'      => 'NimbusRoman-BoldItalic.afm',
    'Courier'               => 'NimbusMonoPS-Regular.afm',
    'Courier-Bold'          => 'NimbusMonoPS-Bold.afm',
    'Courier-Oblique'       => 'NimbusMonoPS-Italic.afm',
    'Courier-BoldOblique'   => 'NimbusMonoPS-BoldItalic.afm',
    'Symbol'                => 'StandardSymbolsPS.afm',
    'ZapfDingbats'          => 'D050000L.afm',
];

/**
 * The standard WinAnsiEncoding byte-code -> Adobe glyph name map for
 * positions where it differs from AdobeStandardEncoding (or where
 * AdobeStandard has no glyph). ASCII (0x20..0x7E) is identical and is
 * read straight from AFM `C <code>` lines. The 0xA0..0xFF range mostly
 * matches but a handful of overrides apply.
 *
 * Reference: PDF 1.7 spec, Annex D.2 ("Latin Character Set and
 * Encodings"), Adobe Glyph List.
 */
const WIN_ANSI = [
    0x80 => 'Euro',          0x82 => 'quotesinglbase',
    0x83 => 'florin',        0x84 => 'quotedblbase',
    0x85 => 'ellipsis',      0x86 => 'dagger',
    0x87 => 'daggerdbl',     0x88 => 'circumflex',
    0x89 => 'perthousand',   0x8A => 'Scaron',
    0x8B => 'guilsinglleft', 0x8C => 'OE',
    0x8E => 'Zcaron',
    0x91 => 'quoteleft',     0x92 => 'quoteright',
    0x93 => 'quotedblleft',  0x94 => 'quotedblright',
    0x95 => 'bullet',        0x96 => 'endash',
    0x97 => 'emdash',        0x98 => 'tilde',
    0x99 => 'trademark',     0x9A => 'scaron',
    0x9B => 'guilsinglright',0x9C => 'oe',
    0x9E => 'zcaron',        0x9F => 'Ydieresis',
    0xA0 => 'space',         0xA1 => 'exclamdown',
    0xA2 => 'cent',          0xA3 => 'sterling',
    0xA4 => 'currency',      0xA5 => 'yen',
    0xA6 => 'brokenbar',     0xA7 => 'section',
    0xA8 => 'dieresis',      0xA9 => 'copyright',
    0xAA => 'ordfeminine',   0xAB => 'guillemotleft',
    0xAC => 'logicalnot',    0xAD => 'hyphen',
    0xAE => 'registered',    0xAF => 'macron',
    0xB0 => 'degree',        0xB1 => 'plusminus',
    0xB2 => 'twosuperior',   0xB3 => 'threesuperior',
    0xB4 => 'acute',         0xB5 => 'mu',
    0xB6 => 'paragraph',     0xB7 => 'periodcentered',
    0xB8 => 'cedilla',       0xB9 => 'onesuperior',
    0xBA => 'ordmasculine',  0xBB => 'guillemotright',
    0xBC => 'onequarter',    0xBD => 'onehalf',
    0xBE => 'threequarters', 0xBF => 'questiondown',
    0xC0 => 'Agrave',        0xC1 => 'Aacute',
    0xC2 => 'Acircumflex',   0xC3 => 'Atilde',
    0xC4 => 'Adieresis',     0xC5 => 'Aring',
    0xC6 => 'AE',            0xC7 => 'Ccedilla',
    0xC8 => 'Egrave',        0xC9 => 'Eacute',
    0xCA => 'Ecircumflex',   0xCB => 'Edieresis',
    0xCC => 'Igrave',        0xCD => 'Iacute',
    0xCE => 'Icircumflex',   0xCF => 'Idieresis',
    0xD0 => 'Eth',           0xD1 => 'Ntilde',
    0xD2 => 'Ograve',        0xD3 => 'Oacute',
    0xD4 => 'Ocircumflex',   0xD5 => 'Otilde',
    0xD6 => 'Odieresis',     0xD7 => 'multiply',
    0xD8 => 'Oslash',        0xD9 => 'Ugrave',
    0xDA => 'Uacute',        0xDB => 'Ucircumflex',
    0xDC => 'Udieresis',     0xDD => 'Yacute',
    0xDE => 'Thorn',         0xDF => 'germandbls',
    0xE0 => 'agrave',        0xE1 => 'aacute',
    0xE2 => 'acircumflex',   0xE3 => 'atilde',
    0xE4 => 'adieresis',     0xE5 => 'aring',
    0xE6 => 'ae',            0xE7 => 'ccedilla',
    0xE8 => 'egrave',        0xE9 => 'eacute',
    0xEA => 'ecircumflex',   0xEB => 'edieresis',
    0xEC => 'igrave',        0xED => 'iacute',
    0xEE => 'icircumflex',   0xEF => 'idieresis',
    0xF0 => 'eth',           0xF1 => 'ntilde',
    0xF2 => 'ograve',        0xF3 => 'oacute',
    0xF4 => 'ocircumflex',   0xF5 => 'otilde',
    0xF6 => 'odieresis',     0xF7 => 'divide',
    0xF8 => 'oslash',        0xF9 => 'ugrave',
    0xFA => 'uacute',        0xFB => 'ucircumflex',
    0xFC => 'udieresis',     0xFD => 'yacute',
    0xFE => 'thorn',         0xFF => 'ydieresis',
];

/**
 * @return array{nameToWidth: array<string,int>, codeToWidth: array<int,int>, defaultWidth: int}
 */
function parseAfm(string $path): array
{
    $lines       = file($path, FILE_IGNORE_NEW_LINES);
    $nameToWidth = [];
    $codeToWidth = [];
    $defaultWidth = 500;

    foreach ($lines as $line) {
        if (! preg_match('/^C\s+(-?\d+)\s*;\s*WX\s+(-?\d+)\s*;\s*N\s+(\S+)/', $line, $m)) {
            continue;
        }
        $code  = (int) $m[1];
        $w     = (int) $m[2];
        $name  = $m[3];

        $nameToWidth[$name] = $w;

        if ($code >= 0 && $code <= 255) {
            $codeToWidth[$code] = $w;
        }

        if ($name === '.notdef') {
            $defaultWidth = $w;
        }
    }

    return ['nameToWidth' => $nameToWidth, 'codeToWidth' => $codeToWidth, 'defaultWidth' => $defaultWidth];
}

/**
 * Build the 0..255 WinAnsi width vector from the AFM-derived data.
 *
 * @param array<string,int> $nameToWidth
 * @param array<int,int>    $codeToWidth (Adobe Standard encoded codes from AFM)
 * @return int[] indexed 0..255
 */
function buildWinAnsiVector(array $nameToWidth, array $codeToWidth, int $defaultWidth): array
{
    $vec = array_fill(0, 256, $defaultWidth);

    // 1) ASCII (0x20..0x7E) is identical between AdobeStandard and WinAnsi
    for ($c = 0x20; $c <= 0x7E; $c++) {
        if (isset($codeToWidth[$c])) {
            $vec[$c] = $codeToWidth[$c];
        }
    }

    // 2) Apply WinAnsi overrides (0x80..0xFF mostly)
    foreach (WIN_ANSI as $code => $glyph) {
        if (isset($nameToWidth[$glyph])) {
            $vec[$code] = $nameToWidth[$glyph];
        }
    }

    return $vec;
}

function compactDeltaEncoding(array $vec): string
{
    // Compact representation: same width as the previous index uses '-',
    // otherwise the int. Comma-separated. 256 cells → ~600 bytes typical.
    $out = [];
    $prev = null;
    foreach ($vec as $w) {
        $out[] = ($w === $prev) ? '' : (string) $w;
        $prev = $w;
    }
    return implode(',', $out);
}

// -----------------------------------------------------------------

$tables = [];
$missing = [];
foreach (FONT_MAP as $psName => $afmFile) {
    $afmPath = AFM_DIR . '/' . $afmFile;
    if (! is_file($afmPath)) {
        $missing[] = "$psName ($afmFile)";
        continue;
    }
    $afm = parseAfm($afmPath);
    $vec = buildWinAnsiVector($afm['nameToWidth'], $afm['codeToWidth'], $afm['defaultWidth']);

    // Ascender / Descender / CapHeight from AFM header for ascent reservation
    $head = file_get_contents($afmPath, false, null, 0, 4096) ?: '';
    preg_match('/^Ascender\s+(-?\d+)/m',   $head, $a);
    preg_match('/^Descender\s+(-?\d+)/m',  $head, $d);
    preg_match('/^CapHeight\s+(-?\d+)/m',  $head, $c);
    preg_match('/^FontBBox\s+(-?\d+)\s+(-?\d+)\s+(-?\d+)\s+(-?\d+)/m', $head, $bb);

    $bboxYMin = isset($bb[2]) ? (int) $bb[2] : -250;
    $bboxYMax = isset($bb[4]) ? (int) $bb[4] : 1000;
    $capH     = isset($c[1]) ? (int) $c[1] : 700;

    // URW AFM files often record Ascender/Descender as 0; fall back to
    // sensible per-design values from FontBBox/CapHeight in that case.
    $ascender  = isset($a[1]) ? (int) $a[1] : 0;
    $descender = isset($d[1]) ? (int) $d[1] : 0;
    if ($ascender === 0)  { $ascender  = max($capH, (int) round($bboxYMax * 0.72)); }
    if ($descender === 0) { $descender = (int) round($bboxYMin * 0.85); }

    $tables[$psName] = [
        'widths'    => $vec,
        'default'   => $afm['defaultWidth'],
        'ascender'  => $ascender,
        'descender' => $descender,
        'capHeight' => $capH,
    ];
}

if ($missing !== []) {
    fwrite(STDERR, "Missing AFM files: " . implode(', ', $missing) . "\n");
    exit(1);
}

// Render PHP file --------------------------------------------------

$out = "<?php\n\n";
$out .= "declare(strict_types=1);\n\n";
$out .= "namespace Paperdoc\\Support\\Pdf;\n\n";
$out .= "/**\n";
$out .= " * Per-glyph metric data for the 14 standard PDF fonts (Core 14).\n";
$out .= " *\n";
$out .= " * Generated from URW Base35 AFM files (metric-compatible substitutes\n";
$out .= " * for the Adobe Core 14, license: AFPL/GPL — but we ship no font\n";
$out .= " * data, only width *numbers*, which are facts).\n";
$out .= " *\n";
$out .= " * Each entry exposes:\n";
$out .= " *  - 'widths'    : 256 ints — width per WinAnsi byte code (1000em units)\n";
$out .= " *  - 'default'   : fallback width (.notdef)\n";
$out .= " *  - 'ascender'  : top of the highest glyph above baseline (1000em)\n";
$out .= " *  - 'descender' : bottom of the lowest glyph below baseline (negative)\n";
$out .= " *  - 'capHeight' : top of capital letters (e.g. 'H')\n";
$out .= " *\n";
$out .= " * Regenerate with:\n";
$out .= " *   php tools/build-afm-widths.php\n";
$out .= " */\n";
$out .= "final class Core14Widths\n";
$out .= "{\n";
$out .= "    /** @var array<string, array{widths: int[], default: int, ascender: int, descender: int, capHeight: int}> */\n";
$out .= "    public const FONTS = [\n";

foreach ($tables as $psName => $data) {
    $widthsLines = [];
    // Pretty-print 16 widths per line for diffability
    for ($i = 0; $i < 256; $i += 16) {
        $slice = array_slice($data['widths'], $i, 16);
        $widthsLines[] = '            ' . implode(',', array_map(static fn($v) => sprintf('%4d', $v), $slice)) . ',';
    }
    // Strip trailing comma from last slice
    $widthsLines[count($widthsLines) - 1] = rtrim($widthsLines[count($widthsLines) - 1], ',');

    $out .= sprintf("        '%s' => [\n", $psName);
    $out .= "            'widths' => [\n";
    $out .= implode("\n", $widthsLines) . "\n";
    $out .= "            ],\n";
    $out .= sprintf("            'default'   => %d,\n", $data['default']);
    $out .= sprintf("            'ascender'  => %d,\n", $data['ascender']);
    $out .= sprintf("            'descender' => %d,\n", $data['descender']);
    $out .= sprintf("            'capHeight' => %d,\n", $data['capHeight']);
    $out .= "        ],\n";
}

$out .= "    ];\n";
$out .= "}\n";

$dest = __DIR__ . '/../src/Support/Pdf/Core14Widths.php';
file_put_contents($dest, $out);

printf("✔ Wrote %s (%d bytes, %d fonts)\n", $dest, strlen($out), count($tables));
