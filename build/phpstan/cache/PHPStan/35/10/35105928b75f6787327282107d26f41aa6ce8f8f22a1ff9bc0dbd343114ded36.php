<?php declare(strict_types = 1);

// osfsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Support/Pdf/PdfEngine.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Support\Pdf\PdfEngine
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-0c7eb2479edd2c0b38ca0cd1871bd9fba3940b7863634244f3f5e2f87a39cc7e-8.5.8-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Support/Pdf/PdfEngine.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Support\\Pdf',
    'name' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
    'shortName' => 'PdfEngine',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Moteur PDF natif sans dépendance tierce.
 *
 * Génère des fichiers PDF valides en implémentant directement
 * la spécification PDF 1.4. Supporte les 14 polices standard,
 * le texte stylisé, les tableaux et les images JPEG/PNG.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 16,
    'endLine' => 2034,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'CHAR_WIDTHS' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'CHAR_WIDTHS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'Helvetica\' => 550, \'Helvetica-Bold\' => 580, \'Helvetica-Oblique\' => 550, \'Helvetica-BoldOblique\' => 580, \'Times-Roman\' => 500, \'Times-Bold\' => 530, \'Times-Italic\' => 500, \'Times-BoldItalic\' => 530, \'Courier\' => 600, \'Courier-Bold\' => 600, \'Courier-Oblique\' => 600, \'Courier-BoldOblique\' => 600, \'Symbol\' => 500, \'ZapfDingbats\' => 800]',
          'attributes' => 
          array (
            'startLine' => 153,
            'endLine' => 168,
            'startTokenPos' => 519,
            'startFilePos' => 5346,
            'endTokenPos' => 619,
            'endFilePos' => 5799,
          ),
        ),
        'docComment' => '/**
 * Coarse per-font average width fallback (1000em units), used only
 * when neither a per-glyph table from {@see Core14Widths::FONTS}
 * nor the requested glyph itself is available. Centring,
 * right-alignment, justification and word wrapping all read from
 * the precise per-glyph table by default — these averages exist
 * just so the engine still produces something for unknown fonts.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 153,
        'endLine' => 168,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'JUSTIFY_MAX_EXTRA_TW_PT' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'JUSTIFY_MAX_EXTRA_TW_PT',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '3.0',
          'attributes' => 
          array (
            'startLine' => 1119,
            'endLine' => 1119,
            'startTokenPos' => 6257,
            'startFilePos' => 40319,
            'endTokenPos' => 6257,
            'endFilePos' => 40321,
          ),
        ),
        'docComment' => '/**
 * If a justified line ends up needing more than this much extra
 * word-spacing per inter-word gap, we silently fall back to
 * left-alignment for that line. Stretching beyond ~3pt produces
 * the visible "rivers" you can see in cheap typesetting; better to
 * accept a ragged right edge than a galleried look.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 1119,
        'endLine' => 1119,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
      'JUSTIFY_MAX_TC_EM' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'JUSTIFY_MAX_TC_EM',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0.1',
          'attributes' => 
          array (
            'startLine' => 1128,
            'endLine' => 1128,
            'startTokenPos' => 6270,
            'startFilePos' => 40738,
            'endTokenPos' => 6270,
            'endFilePos' => 40741,
          ),
        ),
        'docComment' => '/**
 * Cap on character-spacing assistance for justification, in 1000em
 * units. We split the leftover space between Tw (word spacing) and
 * Tc (character spacing) — Tc is per glyph so even small values
 * compound; we conservatively limit it to 0.10 em (= 1.2pt at 12pt
 * size) so individual letters never look unnaturally pulled apart.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 1128,
        'endLine' => 1128,
        'startColumn' => 5,
        'endColumn' => 43,
      ),
    ),
    'immediateProperties' => 
    array (
      'objects' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'objects',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 38,
            'startFilePos' => 430,
            'endTokenPos' => 39,
            'endFilePos' => 431,
          ),
        ),
        'docComment' => '/** @var PdfObject[] */',
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'objectCounter' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'objectCounter',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 50,
            'startFilePos' => 468,
            'endTokenPos' => 50,
            'endFilePos' => 468,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 35,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pageObjects' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'pageObjects',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 24,
            'startTokenPos' => 63,
            'startFilePos' => 564,
            'endTokenPos' => 64,
            'endFilePos' => 565,
          ),
        ),
        'docComment' => '/** @var int[] Object numbers for each page content */',
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 36,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pageGeometries' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'pageGeometries',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 32,
            'startTokenPos' => 77,
            'startFilePos' => 897,
            'endTokenPos' => 78,
            'endFilePos' => 898,
          ),
        ),
        'docComment' => '/**
 * Per-page geometry captured at flush time so each page\'s MediaBox
 * can vary (size & margins set via setPageGeometry()).
 *
 * @var array<int, array{width: float, height: float, marginTop: float, marginRight: float, marginBottom: float, marginLeft: float}>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 39,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'catalogObj' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'catalogObj',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pagesObj' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'pagesObj',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 26,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pageWidth' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'pageWidth',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 29,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pageHeight' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'pageHeight',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 30,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'marginTop' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'marginTop',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 29,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'marginBottom' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'marginBottom',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'marginLeft' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'marginLeft',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 30,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'marginRight' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'marginRight',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 42,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'cursorX' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'cursorX',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 44,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 27,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'cursorY' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'cursorY',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 45,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 27,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'currentPageContent' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'currentPageContent',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '\'\'',
          'attributes' => 
          array (
            'startLine' => 46,
            'endLine' => 46,
            'startTokenPos' => 159,
            'startFilePos' => 1244,
            'endTokenPos' => 159,
            'endFilePos' => 1245,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 46,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 44,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fonts' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'fonts',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 49,
            'endLine' => 49,
            'startTokenPos' => 172,
            'startFilePos' => 1338,
            'endTokenPos' => 173,
            'endFilePos' => 1339,
          ),
        ),
        'docComment' => '/** @var array<string, int> Font name => object number */',
        'attributes' => 
        array (
        ),
        'startLine' => 49,
        'endLine' => 49,
        'startColumn' => 5,
        'endColumn' => 30,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fontRefs' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'fontRefs',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 52,
            'endLine' => 52,
            'startTokenPos' => 186,
            'startFilePos' => 1449,
            'endTokenPos' => 187,
            'endFilePos' => 1450,
          ),
        ),
        'docComment' => '/** @var array<string, string> Font name => PDF reference (e.g. /F1) */',
        'attributes' => 
        array (
        ),
        'startLine' => 52,
        'endLine' => 52,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fontCounter' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'fontCounter',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 54,
            'endLine' => 54,
            'startTokenPos' => 198,
            'startFilePos' => 1485,
            'endTokenPos' => 198,
            'endFilePos' => 1485,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 54,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'images' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'images',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 57,
            'endLine' => 57,
            'startTokenPos' => 211,
            'startFilePos' => 1580,
            'endTokenPos' => 212,
            'endFilePos' => 1581,
          ),
        ),
        'docComment' => '/** @var array<string, int> Image hash => object number */',
        'attributes' => 
        array (
        ),
        'startLine' => 57,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'imageRefs' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'imageRefs',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 60,
            'endLine' => 60,
            'startTokenPos' => 225,
            'startFilePos' => 1682,
            'endTokenPos' => 226,
            'endFilePos' => 1683,
          ),
        ),
        'docComment' => '/** @var array<string, string> Image hash => PDF reference */',
        'attributes' => 
        array (
        ),
        'startLine' => 60,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'imageCounter' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'imageCounter',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 62,
            'endLine' => 62,
            'startTokenPos' => 237,
            'startFilePos' => 1719,
            'endTokenPos' => 237,
            'endFilePos' => 1719,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 62,
        'endLine' => 62,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'title' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'title',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '\'\'',
          'attributes' => 
          array (
            'startLine' => 64,
            'endLine' => 64,
            'startTokenPos' => 248,
            'startFilePos' => 1754,
            'endTokenPos' => 248,
            'endFilePos' => 1755,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 64,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'creator' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'creator',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '\'Paperdoc\'',
          'attributes' => 
          array (
            'startLine' => 65,
            'endLine' => 65,
            'startTokenPos' => 259,
            'startFilePos' => 1789,
            'endTokenPos' => 259,
            'endFilePos' => 1798,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 65,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 42,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'author' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'author',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '\'\'',
          'attributes' => 
          array (
            'startLine' => 66,
            'endLine' => 66,
            'startTokenPos' => 270,
            'startFilePos' => 1832,
            'endTokenPos' => 270,
            'endFilePos' => 1833,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 66,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'subject' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'subject',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '\'\'',
          'attributes' => 
          array (
            'startLine' => 67,
            'endLine' => 67,
            'startTokenPos' => 281,
            'startFilePos' => 1867,
            'endTokenPos' => 281,
            'endFilePos' => 1868,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 67,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'keywords' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'keywords',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '\'\'',
          'attributes' => 
          array (
            'startLine' => 68,
            'endLine' => 68,
            'startTokenPos' => 292,
            'startFilePos' => 1902,
            'endTokenPos' => 292,
            'endFilePos' => 1903,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 68,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'creationDate' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'creationDate',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'DateTimeInterface',
                  'isIdentifier' => false,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 69,
            'endLine' => 69,
            'startTokenPos' => 304,
            'startFilePos' => 1954,
            'endTokenPos' => 304,
            'endFilePos' => 1957,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 69,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 53,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'modificationDate' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'modificationDate',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'DateTimeInterface',
                  'isIdentifier' => false,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 70,
            'endLine' => 70,
            'startTokenPos' => 316,
            'startFilePos' => 2012,
            'endTokenPos' => 316,
            'endFilePos' => 2015,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 70,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 57,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pageAnnotations' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'pageAnnotations',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 82,
            'endLine' => 82,
            'startTokenPos' => 329,
            'startFilePos' => 2567,
            'endTokenPos' => 330,
            'endFilePos' => 2568,
          ),
        ),
        'docComment' => '/**
 * Link annotations recorded for already-flushed pages, indexed by
 * page index (0-based, aligned with $pageObjects). Each record is
 * either `[\'rect\' => [x1,y1,x2,y2], \'uri\' => string]` (external
 * URI action) or `[\'rect\' => [...], \'anchor\' => string]` (internal
 * GoTo destination, resolved by name at output() time so forward
 * references Just Work).
 *
 * @var array<int, list<array{rect: array{float,float,float,float}, uri?: string, anchor?: string}>>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 82,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'currentPageAnnotations' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'currentPageAnnotations',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 85,
            'endLine' => 85,
            'startTokenPos' => 343,
            'startFilePos' => 2713,
            'endTokenPos' => 344,
            'endFilePos' => 2714,
          ),
        ),
        'docComment' => '/** @var list<array{rect: array{float,float,float,float}, uri?: string, anchor?: string}> */',
        'attributes' => 
        array (
        ),
        'startLine' => 85,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 47,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'activeLink' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'activeLink',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'array',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 96,
            'endLine' => 96,
            'startTokenPos' => 358,
            'startFilePos' => 3156,
            'endTokenPos' => 358,
            'endFilePos' => 3159,
          ),
        ),
        'docComment' => '/**
 * Active link target while text is being emitted. When non-null,
 * every line drawn by emitTextLine() also records a clickable
 * rectangle on the current page — which is what makes multi-line
 * (wrapped) and page-spanning links work without the renderer
 * having to know anything about line geometry.
 *
 * @var array{uri?: string, anchor?: string}|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 96,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 38,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'anchors' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'anchors',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 105,
            'endLine' => 105,
            'startTokenPos' => 371,
            'startFilePos' => 3423,
            'endTokenPos' => 372,
            'endFilePos' => 3424,
          ),
        ),
        'docComment' => '/**
 * Named internal destinations: anchor name => 0-based page index
 * and the Y coordinate (PDF bottom-left) the viewport should
 * scroll to.
 *
 * @var array<string, array{page: int, y: float}>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 105,
        'endLine' => 105,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'outlineEntries' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'outlineEntries',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 114,
            'endLine' => 114,
            'startTokenPos' => 385,
            'startFilePos' => 3714,
            'endTokenPos' => 386,
            'endFilePos' => 3715,
          ),
        ),
        'docComment' => '/**
 * Flat list of document outline (bookmarks panel) entries in
 * reading order. The tree is reconstructed from the levels at
 * output() time.
 *
 * @var list<array{level: int, title: string, page: int, y: float}>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 114,
        'endLine' => 114,
        'startColumn' => 5,
        'endColumn' => 39,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'decoUnderline' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'decoUnderline',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 116,
            'endLine' => 116,
            'startTokenPos' => 397,
            'startFilePos' => 3753,
            'endTokenPos' => 397,
            'endFilePos' => 3757,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 116,
        'endLine' => 116,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'decoStrikethrough' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'decoStrikethrough',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 117,
            'endLine' => 117,
            'startTokenPos' => 408,
            'startFilePos' => 3798,
            'endTokenPos' => 408,
            'endFilePos' => 3802,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 117,
        'endLine' => 117,
        'startColumn' => 5,
        'endColumn' => 44,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'decoHighlight' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'decoHighlight',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 118,
            'endLine' => 118,
            'startTokenPos' => 420,
            'startFilePos' => 3842,
            'endTokenPos' => 420,
            'endFilePos' => 3845,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 118,
        'endLine' => 118,
        'startColumn' => 5,
        'endColumn' => 42,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'extGStates' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'extGStates',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 121,
            'endLine' => 121,
            'startTokenPos' => 433,
            'startFilePos' => 3941,
            'endTokenPos' => 434,
            'endFilePos' => 3942,
          ),
        ),
        'docComment' => '/** @var array<string, array{obj: int, ref: string}> */',
        'attributes' => 
        array (
        ),
        'startLine' => 121,
        'endLine' => 121,
        'startColumn' => 5,
        'endColumn' => 35,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'gsCounter' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'gsCounter',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 122,
            'endLine' => 122,
            'startTokenPos' => 445,
            'startFilePos' => 3974,
            'endTokenPos' => 445,
            'endFilePos' => 3974,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 122,
        'endLine' => 122,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'compressStreams' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'compressStreams',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => 'true',
          'attributes' => 
          array (
            'startLine' => 124,
            'endLine' => 124,
            'startTokenPos' => 456,
            'startFilePos' => 4014,
            'endTokenPos' => 456,
            'endFilePos' => 4017,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 124,
        'endLine' => 124,
        'startColumn' => 5,
        'endColumn' => 41,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'protection' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'protection',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'Paperdoc\\Document\\Style\\PdfProtection',
                  'isIdentifier' => false,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 125,
            'endLine' => 125,
            'startTokenPos' => 468,
            'startFilePos' => 4061,
            'endTokenPos' => 468,
            'endFilePos' => 4064,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 125,
        'endLine' => 125,
        'startColumn' => 5,
        'endColumn' => 46,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'security' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'security',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'Paperdoc\\Support\\Pdf\\PdfStandardSecurity',
                  'isIdentifier' => false,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 126,
            'endLine' => 126,
            'startTokenPos' => 480,
            'startFilePos' => 4112,
            'endTokenPos' => 480,
            'endFilePos' => 4115,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 126,
        'endLine' => 126,
        'startColumn' => 5,
        'endColumn' => 50,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fileId' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'fileId',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 127,
            'endLine' => 127,
            'startTokenPos' => 492,
            'startFilePos' => 4148,
            'endTokenPos' => 492,
            'endFilePos' => 4151,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 127,
        'endLine' => 127,
        'startColumn' => 5,
        'endColumn' => 35,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'onNewPage' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'onNewPage',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'Closure',
                  'isIdentifier' => false,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 143,
            'endLine' => 143,
            'startTokenPos' => 506,
            'startFilePos' => 4876,
            'endTokenPos' => 506,
            'endFilePos' => 4879,
          ),
        ),
        'docComment' => '/**
 * Callback fired right after {@see newPage()} has flushed the
 * previous page and reset the cursor — i.e. when a brand-new empty
 * page is ready to receive content. The renderer uses this to
 * repaint the per-page "chrome" (page background, header, footer)
 * on EVERY physical page, including those created mid-paragraph by
 * automatic text overflow in {@see writeWrappedText()}.
 *
 * The hook is NOT called by the constructor\'s initial newPage()
 * because it is set after construction — callers are expected to
 * paint the chrome of the first page manually (the renderer does).
 *
 * Set to null to disable.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 143,
        'endLine' => 143,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'pageWidth' => 
          array (
            'name' => 'pageWidth',
            'default' => 
            array (
              'code' => '595.28',
              'attributes' => 
              array (
                'startLine' => 171,
                'endLine' => 171,
                'startTokenPos' => 635,
                'startFilePos' => 5863,
                'endTokenPos' => 635,
                'endFilePos' => 5868,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 171,
            'endLine' => 171,
            'startColumn' => 9,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'pageHeight' => 
          array (
            'name' => 'pageHeight',
            'default' => 
            array (
              'code' => '841.89',
              'attributes' => 
              array (
                'startLine' => 172,
                'endLine' => 172,
                'startTokenPos' => 644,
                'startFilePos' => 5899,
                'endTokenPos' => 644,
                'endFilePos' => 5904,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 172,
            'endLine' => 172,
            'startColumn' => 9,
            'endColumn' => 34,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'marginTop' => 
          array (
            'name' => 'marginTop',
            'default' => 
            array (
              'code' => '40',
              'attributes' => 
              array (
                'startLine' => 173,
                'endLine' => 173,
                'startTokenPos' => 653,
                'startFilePos' => 5934,
                'endTokenPos' => 653,
                'endFilePos' => 5935,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 173,
            'endLine' => 173,
            'startColumn' => 9,
            'endColumn' => 29,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'marginBottom' => 
          array (
            'name' => 'marginBottom',
            'default' => 
            array (
              'code' => '40',
              'attributes' => 
              array (
                'startLine' => 174,
                'endLine' => 174,
                'startTokenPos' => 662,
                'startFilePos' => 5968,
                'endTokenPos' => 662,
                'endFilePos' => 5969,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 174,
            'endLine' => 174,
            'startColumn' => 9,
            'endColumn' => 32,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'marginLeft' => 
          array (
            'name' => 'marginLeft',
            'default' => 
            array (
              'code' => '40',
              'attributes' => 
              array (
                'startLine' => 175,
                'endLine' => 175,
                'startTokenPos' => 671,
                'startFilePos' => 6000,
                'endTokenPos' => 671,
                'endFilePos' => 6001,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 175,
            'endLine' => 175,
            'startColumn' => 9,
            'endColumn' => 30,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
          'marginRight' => 
          array (
            'name' => 'marginRight',
            'default' => 
            array (
              'code' => '40',
              'attributes' => 
              array (
                'startLine' => 176,
                'endLine' => 176,
                'startTokenPos' => 680,
                'startFilePos' => 6033,
                'endTokenPos' => 680,
                'endFilePos' => 6034,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 176,
            'endLine' => 176,
            'startColumn' => 9,
            'endColumn' => 31,
            'parameterIndex' => 5,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 170,
        'endLine' => 189,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'setTitle' => 
      array (
        'name' => 'setTitle',
        'parameters' => 
        array (
          'title' => 
          array (
            'name' => 'title',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 195,
            'endLine' => 195,
            'startColumn' => 30,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 195,
        'endLine' => 195,
        'startColumn' => 5,
        'endColumn' => 76,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'setCreator' => 
      array (
        'name' => 'setCreator',
        'parameters' => 
        array (
          'creator' => 
          array (
            'name' => 'creator',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 196,
            'endLine' => 196,
            'startColumn' => 32,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 196,
        'endLine' => 196,
        'startColumn' => 5,
        'endColumn' => 84,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'setCompression' => 
      array (
        'name' => 'setCompression',
        'parameters' => 
        array (
          'enabled' => 
          array (
            'name' => 'enabled',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 202,
            'endLine' => 202,
            'startColumn' => 36,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Content streams are Flate-compressed by default (v1.0.0). Turn
 * off to produce human-readable streams (debugging, diffing).
 */',
        'startLine' => 202,
        'endLine' => 202,
        'startColumn' => 5,
        'endColumn' => 94,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'setAuthor' => 
      array (
        'name' => 'setAuthor',
        'parameters' => 
        array (
          'author' => 
          array (
            'name' => 'author',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 203,
            'endLine' => 203,
            'startColumn' => 31,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 203,
        'endLine' => 203,
        'startColumn' => 5,
        'endColumn' => 80,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'setSubject' => 
      array (
        'name' => 'setSubject',
        'parameters' => 
        array (
          'subject' => 
          array (
            'name' => 'subject',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 204,
            'endLine' => 204,
            'startColumn' => 32,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 204,
        'endLine' => 204,
        'startColumn' => 5,
        'endColumn' => 84,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'setKeywords' => 
      array (
        'name' => 'setKeywords',
        'parameters' => 
        array (
          'keywords' => 
          array (
            'name' => 'keywords',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 205,
            'endLine' => 205,
            'startColumn' => 33,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 205,
        'endLine' => 205,
        'startColumn' => 5,
        'endColumn' => 88,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'setCreationDate' => 
      array (
        'name' => 'setCreationDate',
        'parameters' => 
        array (
          'date' => 
          array (
            'name' => 'date',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'DateTimeInterface',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 206,
            'endLine' => 206,
            'startColumn' => 37,
            'endColumn' => 61,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 206,
        'endLine' => 206,
        'startColumn' => 5,
        'endColumn' => 101,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'setModificationDate' => 
      array (
        'name' => 'setModificationDate',
        'parameters' => 
        array (
          'date' => 
          array (
            'name' => 'date',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'DateTimeInterface',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 207,
            'endLine' => 207,
            'startColumn' => 41,
            'endColumn' => 65,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 207,
        'endLine' => 207,
        'startColumn' => 5,
        'endColumn' => 109,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'setProtection' => 
      array (
        'name' => 'setProtection',
        'parameters' => 
        array (
          'protection' => 
          array (
            'name' => 'protection',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'Paperdoc\\Document\\Style\\PdfProtection',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 208,
            'endLine' => 208,
            'startColumn' => 35,
            'endColumn' => 60,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 208,
        'endLine' => 208,
        'startColumn' => 5,
        'endColumn' => 139,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'beginLink' => 
      array (
        'name' => 'beginLink',
        'parameters' => 
        array (
          'uri' => 
          array (
            'name' => 'uri',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 221,
            'endLine' => 221,
            'startColumn' => 31,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'anchor' => 
          array (
            'name' => 'anchor',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 221,
                'endLine' => 221,
                'startTokenPos' => 1061,
                'startFilePos' => 8225,
                'endTokenPos' => 1061,
                'endFilePos' => 8228,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 221,
            'endLine' => 221,
            'startColumn' => 45,
            'endColumn' => 66,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Opens a link scope: every text line emitted until {@see endLink()}
 * records a clickable rectangle on its page. Pass `$uri` for an
 * external URI action, or `$anchor` for an internal jump to a
 * destination registered (before OR after this call) via
 * {@see registerAnchor()}. When both are given, the URI wins.
 */',
        'startLine' => 221,
        'endLine' => 230,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'endLink' => 
      array (
        'name' => 'endLink',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 232,
        'endLine' => 235,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'setTextDecorations' => 
      array (
        'name' => 'setTextDecorations',
        'parameters' => 
        array (
          'underline' => 
          array (
            'name' => 'underline',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 243,
                'endLine' => 243,
                'startTokenPos' => 1199,
                'startFilePos' => 8876,
                'endTokenPos' => 1199,
                'endFilePos' => 8880,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 243,
            'endLine' => 243,
            'startColumn' => 9,
            'endColumn' => 31,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'strikethrough' => 
          array (
            'name' => 'strikethrough',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 244,
                'endLine' => 244,
                'startTokenPos' => 1208,
                'startFilePos' => 8913,
                'endTokenPos' => 1208,
                'endFilePos' => 8917,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 244,
            'endLine' => 244,
            'startColumn' => 9,
            'endColumn' => 35,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'highlight' => 
          array (
            'name' => 'highlight',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 245,
                'endLine' => 245,
                'startTokenPos' => 1218,
                'startFilePos' => 8949,
                'endTokenPos' => 1218,
                'endFilePos' => 8952,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 245,
            'endLine' => 245,
            'startColumn' => 9,
            'endColumn' => 33,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Text decorations applied to every line emitted until
 * {@see clearTextDecorations()}: real drawn underline / strike
 * lines and a highlight rectangle painted behind the text.
 */',
        'startLine' => 242,
        'endLine' => 250,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'clearTextDecorations' => 
      array (
        'name' => 'clearTextDecorations',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 252,
        'endLine' => 255,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'drawWatermarkText' => 
      array (
        'name' => 'drawWatermarkText',
        'parameters' => 
        array (
          'text' => 
          array (
            'name' => 'text',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 263,
            'endLine' => 263,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'fontName' => 
          array (
            'name' => 'fontName',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 264,
            'endLine' => 264,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'fontSize' => 
          array (
            'name' => 'fontSize',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 265,
            'endLine' => 265,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'color' => 
          array (
            'name' => 'color',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 266,
            'endLine' => 266,
            'startColumn' => 9,
            'endColumn' => 21,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'opacity' => 
          array (
            'name' => 'opacity',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 267,
            'endLine' => 267,
            'startColumn' => 9,
            'endColumn' => 22,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
          'angleDegrees' => 
          array (
            'name' => 'angleDegrees',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 268,
            'endLine' => 268,
            'startColumn' => 9,
            'endColumn' => 27,
            'parameterIndex' => 5,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Draws a rotated, semi-transparent text centred on the current
 * page — used for document watermarks. Call once per page, right
 * after the page background so it sits under the body content.
 */',
        'startLine' => 262,
        'endLine' => 299,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'ensureExtGState' => 
      array (
        'name' => 'ensureExtGState',
        'parameters' => 
        array (
          'alpha' => 
          array (
            'name' => 'alpha',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 301,
            'endLine' => 301,
            'startColumn' => 38,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 301,
        'endLine' => 317,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'registerAnchor' => 
      array (
        'name' => 'registerAnchor',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 325,
            'endLine' => 325,
            'startColumn' => 36,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'y' => 
          array (
            'name' => 'y',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 325,
                'endLine' => 325,
                'startTokenPos' => 1786,
                'startFilePos' => 11731,
                'endTokenPos' => 1786,
                'endFilePos' => 11734,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'float',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 325,
            'endLine' => 325,
            'startColumn' => 50,
            'endColumn' => 65,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Registers a named internal destination at the current cursor
 * position. `$y` (PDF bottom-left coordinate) overrides the
 * default "current baseline plus one line" target — pass the top
 * of the element you want the viewport to scroll to.
 */',
        'startLine' => 325,
        'endLine' => 335,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'addOutlineEntry' => 
      array (
        'name' => 'addOutlineEntry',
        'parameters' => 
        array (
          'level' => 
          array (
            'name' => 'level',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 342,
            'endLine' => 342,
            'startColumn' => 37,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'title' => 
          array (
            'name' => 'title',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 342,
            'endLine' => 342,
            'startColumn' => 49,
            'endColumn' => 61,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'y' => 
          array (
            'name' => 'y',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 342,
                'endLine' => 342,
                'startTokenPos' => 1889,
                'startFilePos' => 12284,
                'endTokenPos' => 1889,
                'endFilePos' => 12287,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'float',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 342,
            'endLine' => 342,
            'startColumn' => 64,
            'endColumn' => 79,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Appends an entry to the document outline (the "bookmarks" panel
 * of PDF viewers). Entries must be added in reading order; the
 * hierarchy is rebuilt from `$level` (1 = top) at output() time.
 */',
        'startLine' => 342,
        'endLine' => 354,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'setOnNewPage' => 
      array (
        'name' => 'setOnNewPage',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'Closure',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 371,
            'endLine' => 371,
            'startColumn' => 34,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Registers a hook fired on every new page started by the engine,
 * EXCEPT the first one created by the constructor (because at that
 * point no caller has had the chance to register a hook yet).
 *
 * The hook runs AFTER `flushPage()` has stored the previous page
 * and AFTER the cursor has been reset to the top-left of the new
 * page — meaning {@see getCurrentPageNumber()} already returns the
 * new page number and `cursorY` is at its initial value. Anything
 * the hook draws lands on the new page, at the very beginning of
 * its content stream, so a page-background fill emitted by the
 * hook is guaranteed to sit UNDER the body text.
 *
 * Pass `null` to disable a previously-registered hook.
 */',
        'startLine' => 371,
        'endLine' => 374,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'newPage' => 
      array (
        'name' => 'newPage',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 380,
        'endLine' => 397,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'setPageGeometry' => 
      array (
        'name' => 'setPageGeometry',
        'parameters' => 
        array (
          'width' => 
          array (
            'name' => 'width',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 406,
            'endLine' => 406,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'height' => 
          array (
            'name' => 'height',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 407,
            'endLine' => 407,
            'startColumn' => 9,
            'endColumn' => 21,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'marginTop' => 
          array (
            'name' => 'marginTop',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 408,
            'endLine' => 408,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'marginRight' => 
          array (
            'name' => 'marginRight',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 409,
            'endLine' => 409,
            'startColumn' => 9,
            'endColumn' => 26,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'marginBottom' => 
          array (
            'name' => 'marginBottom',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 410,
            'endLine' => 410,
            'startColumn' => 9,
            'endColumn' => 27,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
          'marginLeft' => 
          array (
            'name' => 'marginLeft',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 411,
            'endLine' => 411,
            'startColumn' => 9,
            'endColumn' => 25,
            'parameterIndex' => 5,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Met à jour la géométrie de la page courante (taille et marges).
 *
 * À appeler juste après {@see newPage()} pour que la nouvelle page
 * adopte les dimensions souhaitées avant d\'y écrire quoi que ce soit.
 */',
        'startLine' => 405,
        'endLine' => 422,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'getPageWidth' => 
      array (
        'name' => 'getPageWidth',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 424,
        'endLine' => 424,
        'startColumn' => 5,
        'endColumn' => 71,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'getPageHeight' => 
      array (
        'name' => 'getPageHeight',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 425,
        'endLine' => 425,
        'startColumn' => 5,
        'endColumn' => 72,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'getCurrentPageNumber' => 
      array (
        'name' => 'getCurrentPageNumber',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Numéro de la page actuellement en cours d\'écriture (1-indexé).
 * Tient compte des pages déjà flushées et de la page en cours de
 * remplissage.
 */',
        'startLine' => 432,
        'endLine' => 441,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'getContentWidth' => 
      array (
        'name' => 'getContentWidth',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 443,
        'endLine' => 446,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'getCursorY' => 
      array (
        'name' => 'getCursorY',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 448,
        'endLine' => 448,
        'startColumn' => 5,
        'endColumn' => 66,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'getLeftMargin' => 
      array (
        'name' => 'getLeftMargin',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 461,
        'endLine' => 461,
        'startColumn' => 5,
        'endColumn' => 74,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'getRightMargin' => 
      array (
        'name' => 'getRightMargin',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 462,
        'endLine' => 462,
        'startColumn' => 5,
        'endColumn' => 75,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'getTopMargin' => 
      array (
        'name' => 'getTopMargin',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 463,
        'endLine' => 463,
        'startColumn' => 5,
        'endColumn' => 73,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'getBottomMargin' => 
      array (
        'name' => 'getBottomMargin',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 464,
        'endLine' => 464,
        'startColumn' => 5,
        'endColumn' => 76,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'moveCursorY' => 
      array (
        'name' => 'moveCursorY',
        'parameters' => 
        array (
          'delta' => 
          array (
            'name' => 'delta',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 466,
            'endLine' => 466,
            'startColumn' => 33,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 466,
        'endLine' => 469,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'needsNewPage' => 
      array (
        'name' => 'needsNewPage',
        'parameters' => 
        array (
          'requiredHeight' => 
          array (
            'name' => 'requiredHeight',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 471,
            'endLine' => 471,
            'startColumn' => 34,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 471,
        'endLine' => 474,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'drawPageBackgroundColor' => 
      array (
        'name' => 'drawPageBackgroundColor',
        'parameters' => 
        array (
          'color' => 
          array (
            'name' => 'color',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 485,
            'endLine' => 485,
            'startColumn' => 45,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Remplit la page entière avec une couleur unie. À appeler juste
 * après newPage() (avant tout autre dessin) pour que le fond reste
 * sous le contenu.
 */',
        'startLine' => 485,
        'endLine' => 488,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'drawPageBackgroundImage' => 
      array (
        'name' => 'drawPageBackgroundImage',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 509,
            'endLine' => 509,
            'startColumn' => 45,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'size' => 
          array (
            'name' => 'size',
            'default' => 
            array (
              'code' => '\'cover\'',
              'attributes' => 
              array (
                'startLine' => 509,
                'endLine' => 509,
                'startTokenPos' => 2628,
                'startFilePos' => 18688,
                'endTokenPos' => 2628,
                'endFilePos' => 18694,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 509,
            'endLine' => 509,
            'startColumn' => 59,
            'endColumn' => 80,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Dessine une image en arrière-plan de la page. À appeler tout en
 * début de page (avant tout autre dessin) pour que le fond reste
 * sous le contenu.
 *
 * Le paramètre `$size` calque la sémantique CSS :
 *
 *  - `\'cover\'`     : l\'image remplit la page en préservant son
 *                    ratio ; l\'excédent est rogné via un clip path.
 *  - `\'contain\'`   : l\'image tient en entier dans la page en
 *                    préservant son ratio (peut laisser des bandes).
 *  - `\'auto\'`      : taille naturelle de l\'image, centrée (rognée
 *                    si plus grande que la page).
 *  - `\'100% 100%\'`
 *    ou `\'stretch\'`: l\'image est étirée pour remplir la page sans
 *                    préserver le ratio (comportement historique).
 *
 * Toute autre valeur retombe sur `\'100% 100%\'`.
 */',
        'startLine' => 509,
        'endLine' => 548,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'computeBackgroundPlacement' => 
      array (
        'name' => 'computeBackgroundPlacement',
        'parameters' => 
        array (
          'iw' => 
          array (
            'name' => 'iw',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 558,
            'endLine' => 558,
            'startColumn' => 9,
            'endColumn' => 17,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'ih' => 
          array (
            'name' => 'ih',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 559,
            'endLine' => 559,
            'startColumn' => 9,
            'endColumn' => 17,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'pw' => 
          array (
            'name' => 'pw',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 560,
            'endLine' => 560,
            'startColumn' => 9,
            'endColumn' => 17,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'ph' => 
          array (
            'name' => 'ph',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 561,
            'endLine' => 561,
            'startColumn' => 9,
            'endColumn' => 17,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'size' => 
          array (
            'name' => 'size',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 562,
            'endLine' => 562,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Calcule la position (en coordonnées PDF bottom-left) et la taille
 * cible d\'une image de fond selon la stratégie demandée. Renvoie
 * également un drapeau indiquant si un clip path est nécessaire.
 *
 * @return array{0:float,1:float,2:float,3:float,4:bool} [x, y, w, h, needsClip]
 */',
        'startLine' => 557,
        'endLine' => 609,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'getImageNaturalSize' => 
      array (
        'name' => 'getImageNaturalSize',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 617,
            'endLine' => 617,
            'startColumn' => 42,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Lit les dimensions naturelles d\'une image (en pixels). Renvoie
 * `[0, 0]` si l\'image est illisible.
 *
 * @return array{0:int,1:int}
 */',
        'startLine' => 617,
        'endLine' => 630,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'writeText' => 
      array (
        'name' => 'writeText',
        'parameters' => 
        array (
          'text' => 
          array (
            'name' => 'text',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 640,
            'endLine' => 640,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'fontName' => 
          array (
            'name' => 'fontName',
            'default' => 
            array (
              'code' => '\'Helvetica\'',
              'attributes' => 
              array (
                'startLine' => 641,
                'endLine' => 641,
                'startTokenPos' => 3426,
                'startFilePos' => 22545,
                'endTokenPos' => 3426,
                'endFilePos' => 22555,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 641,
            'endLine' => 641,
            'startColumn' => 9,
            'endColumn' => 38,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'fontSize' => 
          array (
            'name' => 'fontSize',
            'default' => 
            array (
              'code' => '12',
              'attributes' => 
              array (
                'startLine' => 642,
                'endLine' => 642,
                'startTokenPos' => 3435,
                'startFilePos' => 22584,
                'endTokenPos' => 3435,
                'endFilePos' => 22585,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 642,
            'endLine' => 642,
            'startColumn' => 9,
            'endColumn' => 28,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'r' => 
          array (
            'name' => 'r',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 643,
                'endLine' => 643,
                'startTokenPos' => 3444,
                'startFilePos' => 22607,
                'endTokenPos' => 3444,
                'endFilePos' => 22607,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 643,
            'endLine' => 643,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'g' => 
          array (
            'name' => 'g',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 644,
                'endLine' => 644,
                'startTokenPos' => 3453,
                'startFilePos' => 22629,
                'endTokenPos' => 3453,
                'endFilePos' => 22629,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 644,
            'endLine' => 644,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
          'b' => 
          array (
            'name' => 'b',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 645,
                'endLine' => 645,
                'startTokenPos' => 3462,
                'startFilePos' => 22651,
                'endTokenPos' => 3462,
                'endFilePos' => 22651,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 645,
            'endLine' => 645,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 5,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Écrit une ligne de texte à la position courante.
 */',
        'startLine' => 639,
        'endLine' => 655,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'writeWrappedText' => 
      array (
        'name' => 'writeWrappedText',
        'parameters' => 
        array (
          'text' => 
          array (
            'name' => 'text',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 668,
            'endLine' => 668,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'fontName' => 
          array (
            'name' => 'fontName',
            'default' => 
            array (
              'code' => '\'Helvetica\'',
              'attributes' => 
              array (
                'startLine' => 669,
                'endLine' => 669,
                'startTokenPos' => 3605,
                'startFilePos' => 23671,
                'endTokenPos' => 3605,
                'endFilePos' => 23681,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 669,
            'endLine' => 669,
            'startColumn' => 9,
            'endColumn' => 38,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'fontSize' => 
          array (
            'name' => 'fontSize',
            'default' => 
            array (
              'code' => '12',
              'attributes' => 
              array (
                'startLine' => 670,
                'endLine' => 670,
                'startTokenPos' => 3614,
                'startFilePos' => 23710,
                'endTokenPos' => 3614,
                'endFilePos' => 23711,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 670,
            'endLine' => 670,
            'startColumn' => 9,
            'endColumn' => 28,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'r' => 
          array (
            'name' => 'r',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 671,
                'endLine' => 671,
                'startTokenPos' => 3623,
                'startFilePos' => 23733,
                'endTokenPos' => 3623,
                'endFilePos' => 23733,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 671,
            'endLine' => 671,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'g' => 
          array (
            'name' => 'g',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 672,
                'endLine' => 672,
                'startTokenPos' => 3632,
                'startFilePos' => 23755,
                'endTokenPos' => 3632,
                'endFilePos' => 23755,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 672,
            'endLine' => 672,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
          'b' => 
          array (
            'name' => 'b',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 673,
                'endLine' => 673,
                'startTokenPos' => 3641,
                'startFilePos' => 23777,
                'endTokenPos' => 3641,
                'endFilePos' => 23777,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 673,
            'endLine' => 673,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 5,
            'isOptional' => true,
          ),
          'maxWidth' => 
          array (
            'name' => 'maxWidth',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 674,
                'endLine' => 674,
                'startTokenPos' => 3650,
                'startFilePos' => 23806,
                'endTokenPos' => 3650,
                'endFilePos' => 23806,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 674,
            'endLine' => 674,
            'startColumn' => 9,
            'endColumn' => 27,
            'parameterIndex' => 6,
            'isOptional' => true,
          ),
          'lineSpacing' => 
          array (
            'name' => 'lineSpacing',
            'default' => 
            array (
              'code' => '1.15',
              'attributes' => 
              array (
                'startLine' => 675,
                'endLine' => 675,
                'startTokenPos' => 3659,
                'startFilePos' => 23838,
                'endTokenPos' => 3659,
                'endFilePos' => 23841,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 675,
            'endLine' => 675,
            'startColumn' => 9,
            'endColumn' => 33,
            'parameterIndex' => 7,
            'isOptional' => true,
          ),
          'x' => 
          array (
            'name' => 'x',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 676,
                'endLine' => 676,
                'startTokenPos' => 3668,
                'startFilePos' => 23863,
                'endTokenPos' => 3668,
                'endFilePos' => 23863,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 676,
            'endLine' => 676,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 8,
            'isOptional' => true,
          ),
          'align' => 
          array (
            'name' => 'align',
            'default' => 
            array (
              'code' => '\'left\'',
              'attributes' => 
              array (
                'startLine' => 677,
                'endLine' => 677,
                'startTokenPos' => 3677,
                'startFilePos' => 23890,
                'endTokenPos' => 3677,
                'endFilePos' => 23895,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 677,
            'endLine' => 677,
            'startColumn' => 9,
            'endColumn' => 30,
            'parameterIndex' => 9,
            'isOptional' => true,
          ),
          'letterSpacing' => 
          array (
            'name' => 'letterSpacing',
            'default' => 
            array (
              'code' => '0.0',
              'attributes' => 
              array (
                'startLine' => 678,
                'endLine' => 678,
                'startTokenPos' => 3686,
                'startFilePos' => 23929,
                'endTokenPos' => 3686,
                'endFilePos' => 23931,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 678,
            'endLine' => 678,
            'startColumn' => 9,
            'endColumn' => 34,
            'parameterIndex' => 10,
            'isOptional' => true,
          ),
          'firstLineIndent' => 
          array (
            'name' => 'firstLineIndent',
            'default' => 
            array (
              'code' => '0.0',
              'attributes' => 
              array (
                'startLine' => 679,
                'endLine' => 679,
                'startTokenPos' => 3695,
                'startFilePos' => 23967,
                'endTokenPos' => 3695,
                'endFilePos' => 23969,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 679,
            'endLine' => 679,
            'startColumn' => 9,
            'endColumn' => 36,
            'parameterIndex' => 11,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Écrit du texte avec retour à la ligne automatique.
 *
 * Le paramètre `$align` accepte `left` (défaut), `center`, `right`
 * et `justify`. Pour `justify`, l\'espace résiduel est réparti via
 * l\'opérateur PDF `Tw` (word spacing) ; la dernière ligne reste
 * alignée à gauche pour ne pas étirer une ligne courte.
 *
 * @return float Hauteur totale consommée
 */',
        'startLine' => 667,
        'endLine' => 747,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'drawLine' => 
      array (
        'name' => 'drawLine',
        'parameters' => 
        array (
          'x1' => 
          array (
            'name' => 'x1',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 753,
            'endLine' => 753,
            'startColumn' => 30,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'y1' => 
          array (
            'name' => 'y1',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 753,
            'endLine' => 753,
            'startColumn' => 41,
            'endColumn' => 49,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'x2' => 
          array (
            'name' => 'x2',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 753,
            'endLine' => 753,
            'startColumn' => 52,
            'endColumn' => 60,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'y2' => 
          array (
            'name' => 'y2',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 753,
            'endLine' => 753,
            'startColumn' => 63,
            'endColumn' => 71,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'width' => 
          array (
            'name' => 'width',
            'default' => 
            array (
              'code' => '0.5',
              'attributes' => 
              array (
                'startLine' => 753,
                'endLine' => 753,
                'startTokenPos' => 4142,
                'startFilePos' => 26685,
                'endTokenPos' => 4142,
                'endFilePos' => 26687,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 753,
            'endLine' => 753,
            'startColumn' => 74,
            'endColumn' => 91,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 753,
        'endLine' => 757,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'drawColoredLine' => 
      array (
        'name' => 'drawColoredLine',
        'parameters' => 
        array (
          'x1' => 
          array (
            'name' => 'x1',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 765,
            'endLine' => 765,
            'startColumn' => 9,
            'endColumn' => 17,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'y1' => 
          array (
            'name' => 'y1',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 766,
            'endLine' => 766,
            'startColumn' => 9,
            'endColumn' => 17,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'x2' => 
          array (
            'name' => 'x2',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 767,
            'endLine' => 767,
            'startColumn' => 9,
            'endColumn' => 17,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'y2' => 
          array (
            'name' => 'y2',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 768,
            'endLine' => 768,
            'startColumn' => 9,
            'endColumn' => 17,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'width' => 
          array (
            'name' => 'width',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 769,
            'endLine' => 769,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
          'r' => 
          array (
            'name' => 'r',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 770,
            'endLine' => 770,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 5,
            'isOptional' => false,
          ),
          'g' => 
          array (
            'name' => 'g',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 771,
            'endLine' => 771,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 6,
            'isOptional' => false,
          ),
          'b' => 
          array (
            'name' => 'b',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 772,
            'endLine' => 772,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 7,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Like {@see drawLine()} but emits a stroke-colour change before
 * drawing and resets it to black afterwards. Useful for a single
 * coloured rule without leaking the colour into subsequent ops.
 */',
        'startLine' => 764,
        'endLine' => 777,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'drawHorizontalLine' => 
      array (
        'name' => 'drawHorizontalLine',
        'parameters' => 
        array (
          'x' => 
          array (
            'name' => 'x',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 786,
            'endLine' => 786,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'yTopLeft' => 
          array (
            'name' => 'yTopLeft',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 787,
            'endLine' => 787,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'width' => 
          array (
            'name' => 'width',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 788,
            'endLine' => 788,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'thickness' => 
          array (
            'name' => 'thickness',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 789,
            'endLine' => 789,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'color' => 
          array (
            'name' => 'color',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 790,
            'endLine' => 790,
            'startColumn' => 9,
            'endColumn' => 21,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Draws a single coloured horizontal stroke. The y coordinate is
 * interpreted in user/CSS convention (origin at the top-left,
 * grows downwards) for symmetry with `drawImageAt` and
 * `writeWrappedTextAt`.
 */',
        'startLine' => 785,
        'endLine' => 799,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'getPageContentLength' => 
      array (
        'name' => 'getPageContentLength',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns the current byte length of the in-flight page content
 * stream. Use it to mark a position before rendering a span of
 * elements, then later call {@see wrapPageContentSince()} with
 * that mark to wrap the rendered span in `q ... Q` graphics
 * state and an optional CTM (for translation/rotation).
 */',
        'startLine' => 814,
        'endLine' => 817,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'wrapPageContentSince' => 
      array (
        'name' => 'wrapPageContentSince',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 831,
            'endLine' => 831,
            'startColumn' => 42,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'prefix' => 
          array (
            'name' => 'prefix',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 831,
                'endLine' => 831,
                'startTokenPos' => 4479,
                'startFilePos' => 29738,
                'endTokenPos' => 4479,
                'endFilePos' => 29739,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 831,
            'endLine' => 831,
            'startColumn' => 55,
            'endColumn' => 73,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'suffix' => 
          array (
            'name' => 'suffix',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 831,
                'endLine' => 831,
                'startTokenPos' => 4488,
                'startFilePos' => 29759,
                'endTokenPos' => 4488,
                'endFilePos' => 29760,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 831,
            'endLine' => 831,
            'startColumn' => 76,
            'endColumn' => 94,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Wraps the page content emitted since {@see getPageContentLength()}
 * was sampled, by inserting `q\\n<prefix>` at the offset and
 * appending `<suffix>\\nQ\\n` at the tail. Used by the renderer to
 * apply a translation matrix (`1 0 0 1 dx dy cm`) to a section\'s
 * body when it should be vertically centred or bottom-anchored.
 *
 * The graphics-state push/pop is mandatory: PDF content streams
 * accumulate transforms, so without `q ... Q` the cm would leak
 * into all subsequent content (header, footer, page background of
 * the next page, …).
 */',
        'startLine' => 831,
        'endLine' => 842,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'drawRect' => 
      array (
        'name' => 'drawRect',
        'parameters' => 
        array (
          'x' => 
          array (
            'name' => 'x',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 845,
            'endLine' => 845,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'y' => 
          array (
            'name' => 'y',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 846,
            'endLine' => 846,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'w' => 
          array (
            'name' => 'w',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 847,
            'endLine' => 847,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'h' => 
          array (
            'name' => 'h',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 848,
            'endLine' => 848,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'fillColor' => 
          array (
            'name' => 'fillColor',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 849,
                'endLine' => 849,
                'startTokenPos' => 4642,
                'startFilePos' => 30272,
                'endTokenPos' => 4642,
                'endFilePos' => 30275,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 849,
            'endLine' => 849,
            'startColumn' => 9,
            'endColumn' => 33,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
          'strokeColor' => 
          array (
            'name' => 'strokeColor',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 850,
                'endLine' => 850,
                'startTokenPos' => 4652,
                'startFilePos' => 30309,
                'endTokenPos' => 4652,
                'endFilePos' => 30312,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 850,
            'endLine' => 850,
            'startColumn' => 9,
            'endColumn' => 35,
            'parameterIndex' => 5,
            'isOptional' => true,
          ),
          'strokeWidth' => 
          array (
            'name' => 'strokeWidth',
            'default' => 
            array (
              'code' => '0.5',
              'attributes' => 
              array (
                'startLine' => 851,
                'endLine' => 851,
                'startTokenPos' => 4661,
                'startFilePos' => 30344,
                'endTokenPos' => 4661,
                'endFilePos' => 30346,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 851,
            'endLine' => 851,
            'startColumn' => 9,
            'endColumn' => 32,
            'parameterIndex' => 6,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 844,
        'endLine' => 874,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'drawImage' => 
      array (
        'name' => 'drawImage',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 880,
            'endLine' => 880,
            'startColumn' => 31,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'x' => 
          array (
            'name' => 'x',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 880,
            'endLine' => 880,
            'startColumn' => 45,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'y' => 
          array (
            'name' => 'y',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 880,
            'endLine' => 880,
            'startColumn' => 55,
            'endColumn' => 62,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'w' => 
          array (
            'name' => 'w',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 880,
            'endLine' => 880,
            'startColumn' => 65,
            'endColumn' => 72,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'h' => 
          array (
            'name' => 'h',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 880,
            'endLine' => 880,
            'startColumn' => 75,
            'endColumn' => 82,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 880,
        'endLine' => 896,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'measureTextWidth' => 
      array (
        'name' => 'measureTextWidth',
        'parameters' => 
        array (
          'text' => 
          array (
            'name' => 'text',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 922,
            'endLine' => 922,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'fontName' => 
          array (
            'name' => 'fontName',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 923,
            'endLine' => 923,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'fontSize' => 
          array (
            'name' => 'fontSize',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 924,
            'endLine' => 924,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'letterSpacing' => 
          array (
            'name' => 'letterSpacing',
            'default' => 
            array (
              'code' => '0.0',
              'attributes' => 
              array (
                'startLine' => 925,
                'endLine' => 925,
                'startTokenPos' => 5089,
                'startFilePos' => 33278,
                'endTokenPos' => 5089,
                'endFilePos' => 33280,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 925,
            'endLine' => 925,
            'startColumn' => 9,
            'endColumn' => 34,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Mesure la largeur réelle d\'une chaîne dans une police PDF en
 * utilisant la table de métriques per-glyphe Core 14 (générée à
 * partir des AFM URW Base35, métriquement compatibles avec les
 * polices standard PDF). Les calculs de centrage,
 * d\'alignement-droit, de justification et de wrapping reposent
 * tous sur cette mesure, donc une bonne précision ici fait
 * littéralement la différence entre un titre centré et un titre
 * « presque centré ».
 *
 * Pipeline :
 *   1. La chaîne UTF-8 est convertie en WinAnsi (cp1252) — c\'est
 *      l\'encodage qu\'on émet réellement dans le PDF, donc c\'est
 *      bien sur cette représentation qu\'il faut additionner.
 *   2. Pour chaque octet, on lit la largeur dans la table Core 14.
 *   3. Si la police n\'est pas dans la table (police custom future)
 *      ou si l\'octet n\'a pas de largeur (cas .notdef), on retombe
 *      sur la largeur moyenne par police (CHAR_WIDTHS).
 */',
        'startLine' => 921,
        'endLine' => 957,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'getFontMetrics' => 
      array (
        'name' => 'getFontMetrics',
        'parameters' => 
        array (
          'fontName' => 
          array (
            'name' => 'fontName',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 967,
            'endLine' => 967,
            'startColumn' => 36,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns vertical metrics for a font in 1000em units. Used by the
 * renderer to reserve enough vertical space when the next paragraph
 * uses a much larger font size than the previous one (otherwise the
 * top of the new glyphs collides with the previous baseline).
 *
 * @return array{ascender:int, descender:int, capHeight:int}
 */',
        'startLine' => 967,
        'endLine' => 979,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'writeTextAt' => 
      array (
        'name' => 'writeTextAt',
        'parameters' => 
        array (
          'text' => 
          array (
            'name' => 'text',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 985,
            'endLine' => 985,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'fontName' => 
          array (
            'name' => 'fontName',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 986,
            'endLine' => 986,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'fontSize' => 
          array (
            'name' => 'fontSize',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 987,
            'endLine' => 987,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'x' => 
          array (
            'name' => 'x',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 988,
            'endLine' => 988,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'y' => 
          array (
            'name' => 'y',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 989,
            'endLine' => 989,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
          'r' => 
          array (
            'name' => 'r',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 990,
                'endLine' => 990,
                'startTokenPos' => 5495,
                'startFilePos' => 35512,
                'endTokenPos' => 5495,
                'endFilePos' => 35512,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 990,
            'endLine' => 990,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 5,
            'isOptional' => true,
          ),
          'g' => 
          array (
            'name' => 'g',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 991,
                'endLine' => 991,
                'startTokenPos' => 5504,
                'startFilePos' => 35534,
                'endTokenPos' => 5504,
                'endFilePos' => 35534,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 991,
            'endLine' => 991,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 6,
            'isOptional' => true,
          ),
          'b' => 
          array (
            'name' => 'b',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 992,
                'endLine' => 992,
                'startTokenPos' => 5513,
                'startFilePos' => 35556,
                'endTokenPos' => 5513,
                'endFilePos' => 35556,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 992,
            'endLine' => 992,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 7,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Écrit du texte à une position absolue sans déplacer le curseur.
 */',
        'startLine' => 984,
        'endLine' => 1002,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'writeWrappedTextAt' => 
      array (
        'name' => 'writeWrappedTextAt',
        'parameters' => 
        array (
          'text' => 
          array (
            'name' => 'text',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1016,
            'endLine' => 1016,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'fontName' => 
          array (
            'name' => 'fontName',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1017,
            'endLine' => 1017,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'fontSize' => 
          array (
            'name' => 'fontSize',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1018,
            'endLine' => 1018,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'x' => 
          array (
            'name' => 'x',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1019,
            'endLine' => 1019,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'yTopLeft' => 
          array (
            'name' => 'yTopLeft',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1020,
            'endLine' => 1020,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
          'maxWidth' => 
          array (
            'name' => 'maxWidth',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1021,
            'endLine' => 1021,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 5,
            'isOptional' => false,
          ),
          'r' => 
          array (
            'name' => 'r',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 1022,
                'endLine' => 1022,
                'startTokenPos' => 5677,
                'startFilePos' => 36781,
                'endTokenPos' => 5677,
                'endFilePos' => 36781,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1022,
            'endLine' => 1022,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 6,
            'isOptional' => true,
          ),
          'g' => 
          array (
            'name' => 'g',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 1023,
                'endLine' => 1023,
                'startTokenPos' => 5686,
                'startFilePos' => 36803,
                'endTokenPos' => 5686,
                'endFilePos' => 36803,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1023,
            'endLine' => 1023,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 7,
            'isOptional' => true,
          ),
          'b' => 
          array (
            'name' => 'b',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 1024,
                'endLine' => 1024,
                'startTokenPos' => 5695,
                'startFilePos' => 36825,
                'endTokenPos' => 5695,
                'endFilePos' => 36825,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1024,
            'endLine' => 1024,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 8,
            'isOptional' => true,
          ),
          'lineSpacing' => 
          array (
            'name' => 'lineSpacing',
            'default' => 
            array (
              'code' => '1.15',
              'attributes' => 
              array (
                'startLine' => 1025,
                'endLine' => 1025,
                'startTokenPos' => 5704,
                'startFilePos' => 36857,
                'endTokenPos' => 5704,
                'endFilePos' => 36860,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1025,
            'endLine' => 1025,
            'startColumn' => 9,
            'endColumn' => 33,
            'parameterIndex' => 9,
            'isOptional' => true,
          ),
          'maxHeight' => 
          array (
            'name' => 'maxHeight',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1026,
                'endLine' => 1026,
                'startTokenPos' => 5714,
                'startFilePos' => 36891,
                'endTokenPos' => 5714,
                'endFilePos' => 36894,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'float',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1026,
            'endLine' => 1026,
            'startColumn' => 9,
            'endColumn' => 32,
            'parameterIndex' => 10,
            'isOptional' => true,
          ),
          'ellipsis' => 
          array (
            'name' => 'ellipsis',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 1027,
                'endLine' => 1027,
                'startTokenPos' => 5723,
                'startFilePos' => 36922,
                'endTokenPos' => 5723,
                'endFilePos' => 36926,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1027,
            'endLine' => 1027,
            'startColumn' => 9,
            'endColumn' => 30,
            'parameterIndex' => 11,
            'isOptional' => true,
          ),
          'align' => 
          array (
            'name' => 'align',
            'default' => 
            array (
              'code' => '\'left\'',
              'attributes' => 
              array (
                'startLine' => 1028,
                'endLine' => 1028,
                'startTokenPos' => 5732,
                'startFilePos' => 36953,
                'endTokenPos' => 5732,
                'endFilePos' => 36958,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1028,
            'endLine' => 1028,
            'startColumn' => 9,
            'endColumn' => 30,
            'parameterIndex' => 12,
            'isOptional' => true,
          ),
          'letterSpacing' => 
          array (
            'name' => 'letterSpacing',
            'default' => 
            array (
              'code' => '0.0',
              'attributes' => 
              array (
                'startLine' => 1029,
                'endLine' => 1029,
                'startTokenPos' => 5741,
                'startFilePos' => 36992,
                'endTokenPos' => 5741,
                'endFilePos' => 36994,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1029,
            'endLine' => 1029,
            'startColumn' => 9,
            'endColumn' => 34,
            'parameterIndex' => 13,
            'isOptional' => true,
          ),
          'firstLineIndent' => 
          array (
            'name' => 'firstLineIndent',
            'default' => 
            array (
              'code' => '0.0',
              'attributes' => 
              array (
                'startLine' => 1030,
                'endLine' => 1030,
                'startTokenPos' => 5750,
                'startFilePos' => 37030,
                'endTokenPos' => 5750,
                'endFilePos' => 37032,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1030,
            'endLine' => 1030,
            'startColumn' => 9,
            'endColumn' => 36,
            'parameterIndex' => 14,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Écrit un bloc de texte avec retour à la ligne automatique dans une
 * largeur donnée, à une position absolue (x, y) où l\'origine est le
 * coin supérieur gauche de la page (convention utilisateur/CSS).
 *
 * Si $maxHeight est fourni, les lignes qui dépassent sont tronquées.
 * Si $ellipsis vaut true, la dernière ligne visible reçoit \'…\' lorsqu\'il
 * reste du contenu non rendu.
 *
 * @return array{consumed: float, truncated: bool, totalLines: int, drawnLines: int}
 */',
        'startLine' => 1015,
        'endLine' => 1097,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'emitTextLine' => 
      array (
        'name' => 'emitTextLine',
        'parameters' => 
        array (
          'line' => 
          array (
            'name' => 'line',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1131,
            'endLine' => 1131,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'fontRef' => 
          array (
            'name' => 'fontRef',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1132,
            'endLine' => 1132,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'fontName' => 
          array (
            'name' => 'fontName',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1133,
            'endLine' => 1133,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'fontSize' => 
          array (
            'name' => 'fontSize',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1134,
            'endLine' => 1134,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'x' => 
          array (
            'name' => 'x',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1135,
            'endLine' => 1135,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
          'baselineY' => 
          array (
            'name' => 'baselineY',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1136,
            'endLine' => 1136,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 5,
            'isOptional' => false,
          ),
          'maxWidth' => 
          array (
            'name' => 'maxWidth',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1137,
            'endLine' => 1137,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 6,
            'isOptional' => false,
          ),
          'r' => 
          array (
            'name' => 'r',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1138,
            'endLine' => 1138,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 7,
            'isOptional' => false,
          ),
          'g' => 
          array (
            'name' => 'g',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1139,
            'endLine' => 1139,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 8,
            'isOptional' => false,
          ),
          'b' => 
          array (
            'name' => 'b',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1140,
            'endLine' => 1140,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 9,
            'isOptional' => false,
          ),
          'align' => 
          array (
            'name' => 'align',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1141,
            'endLine' => 1141,
            'startColumn' => 9,
            'endColumn' => 21,
            'parameterIndex' => 10,
            'isOptional' => false,
          ),
          'isLastLine' => 
          array (
            'name' => 'isLastLine',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1142,
            'endLine' => 1142,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 11,
            'isOptional' => false,
          ),
          'letterSpacing' => 
          array (
            'name' => 'letterSpacing',
            'default' => 
            array (
              'code' => '0.0',
              'attributes' => 
              array (
                'startLine' => 1143,
                'endLine' => 1143,
                'startTokenPos' => 6346,
                'startFilePos' => 41081,
                'endTokenPos' => 6346,
                'endFilePos' => 41083,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1143,
            'endLine' => 1143,
            'startColumn' => 9,
            'endColumn' => 34,
            'parameterIndex' => 12,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 1130,
        'endLine' => 1237,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'computeJustifySpacing' => 
      array (
        'name' => 'computeJustifySpacing',
        'parameters' => 
        array (
          'line' => 
          array (
            'name' => 'line',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1259,
            'endLine' => 1259,
            'startColumn' => 44,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'maxWidth' => 
          array (
            'name' => 'maxWidth',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1259,
            'endLine' => 1259,
            'startColumn' => 58,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'lineWidth' => 
          array (
            'name' => 'lineWidth',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1259,
            'endLine' => 1259,
            'startColumn' => 75,
            'endColumn' => 90,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'fontSize' => 
          array (
            'name' => 'fontSize',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1259,
            'endLine' => 1259,
            'startColumn' => 93,
            'endColumn' => 107,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Splits the missing horizontal space between word-spacing (Tw)
 * and character-spacing (Tc), so a justified line doesn\'t
 * concentrate all of the extra slack on inter-word gaps (which is
 * what creates the visual "rivers" of whitespace classic to cheap
 * justification).
 *
 * Strategy:
 *   1. Try pure word-spacing first (visually preferred).
 *   2. If Tw alone would exceed JUSTIFY_MAX_EXTRA_TW_PT, give part
 *      of the gap to Tc (character spacing). Tc is bounded to
 *      JUSTIFY_MAX_TC_EM × fontSize — beyond that, individual
 *      letters look unnaturally stretched.
 *   3. If even with Tc capped we still need Tw above the threshold,
 *      give up: return [null, 0] and the caller falls back to
 *      left-alignment for this single line.
 *
 * @return array{0:?float,1:float} [Tw_pt, Tc_pt] or [null, 0] when
 *         the line should fall back to flush-left
 */',
        'startLine' => 1259,
        'endLine' => 1304,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'appendEllipsis' => 
      array (
        'name' => 'appendEllipsis',
        'parameters' => 
        array (
          'line' => 
          array (
            'name' => 'line',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1310,
            'endLine' => 1310,
            'startColumn' => 37,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'fontName' => 
          array (
            'name' => 'fontName',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1310,
            'endLine' => 1310,
            'startColumn' => 51,
            'endColumn' => 66,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'fontSize' => 
          array (
            'name' => 'fontSize',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1310,
            'endLine' => 1310,
            'startColumn' => 69,
            'endColumn' => 83,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'maxWidth' => 
          array (
            'name' => 'maxWidth',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1310,
            'endLine' => 1310,
            'startColumn' => 86,
            'endColumn' => 100,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Tronque la fin d\'une ligne pour y faire tenir \'…\' dans la largeur
 * disponible.
 */',
        'startLine' => 1310,
        'endLine' => 1319,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'drawRectTopLeft' => 
      array (
        'name' => 'drawRectTopLeft',
        'parameters' => 
        array (
          'x' => 
          array (
            'name' => 'x',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1327,
            'endLine' => 1327,
            'startColumn' => 9,
            'endColumn' => 16,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'yTopLeft' => 
          array (
            'name' => 'yTopLeft',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1328,
            'endLine' => 1328,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'width' => 
          array (
            'name' => 'width',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1329,
            'endLine' => 1329,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'height' => 
          array (
            'name' => 'height',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1330,
            'endLine' => 1330,
            'startColumn' => 9,
            'endColumn' => 21,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'fillColor' => 
          array (
            'name' => 'fillColor',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1331,
                'endLine' => 1331,
                'startTokenPos' => 7720,
                'startFilePos' => 48772,
                'endTokenPos' => 7720,
                'endFilePos' => 48775,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1331,
            'endLine' => 1331,
            'startColumn' => 9,
            'endColumn' => 33,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
          'strokeColor' => 
          array (
            'name' => 'strokeColor',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1332,
                'endLine' => 1332,
                'startTokenPos' => 7730,
                'startFilePos' => 48809,
                'endTokenPos' => 7730,
                'endFilePos' => 48812,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1332,
            'endLine' => 1332,
            'startColumn' => 9,
            'endColumn' => 35,
            'parameterIndex' => 5,
            'isOptional' => true,
          ),
          'strokeWidth' => 
          array (
            'name' => 'strokeWidth',
            'default' => 
            array (
              'code' => '0.5',
              'attributes' => 
              array (
                'startLine' => 1333,
                'endLine' => 1333,
                'startTokenPos' => 7739,
                'startFilePos' => 48844,
                'endTokenPos' => 7739,
                'endFilePos' => 48846,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1333,
            'endLine' => 1333,
            'startColumn' => 9,
            'endColumn' => 32,
            'parameterIndex' => 6,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Dessine un rectangle aux coordonnées top-left (origine page haut-gauche),
 * en convertissant vers la convention PDF interne. Pratique pour
 * encadrer une zone de texte.
 */',
        'startLine' => 1326,
        'endLine' => 1337,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'drawImageTopLeft' => 
      array (
        'name' => 'drawImageTopLeft',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1342,
            'endLine' => 1342,
            'startColumn' => 38,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'x' => 
          array (
            'name' => 'x',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1342,
            'endLine' => 1342,
            'startColumn' => 52,
            'endColumn' => 59,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'yTopLeft' => 
          array (
            'name' => 'yTopLeft',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1342,
            'endLine' => 1342,
            'startColumn' => 62,
            'endColumn' => 76,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'width' => 
          array (
            'name' => 'width',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1342,
            'endLine' => 1342,
            'startColumn' => 79,
            'endColumn' => 90,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'height' => 
          array (
            'name' => 'height',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1342,
            'endLine' => 1342,
            'startColumn' => 93,
            'endColumn' => 105,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Dessine une image aux coordonnées top-left.
 */',
        'startLine' => 1342,
        'endLine' => 1346,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'wrapText' => 
      array (
        'name' => 'wrapText',
        'parameters' => 
        array (
          'text' => 
          array (
            'name' => 'text',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1352,
            'endLine' => 1352,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'fontName' => 
          array (
            'name' => 'fontName',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1353,
            'endLine' => 1353,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'fontSize' => 
          array (
            'name' => 'fontSize',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1354,
            'endLine' => 1354,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'maxWidth' => 
          array (
            'name' => 'maxWidth',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1355,
            'endLine' => 1355,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'letterSpacing' => 
          array (
            'name' => 'letterSpacing',
            'default' => 
            array (
              'code' => '0.0',
              'attributes' => 
              array (
                'startLine' => 1356,
                'endLine' => 1356,
                'startTokenPos' => 7906,
                'startFilePos' => 49543,
                'endTokenPos' => 7906,
                'endFilePos' => 49545,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1356,
            'endLine' => 1356,
            'startColumn' => 9,
            'endColumn' => 34,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
          'firstLineMaxWidth' => 
          array (
            'name' => 'firstLineMaxWidth',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1357,
                'endLine' => 1357,
                'startTokenPos' => 7916,
                'startFilePos' => 49584,
                'endTokenPos' => 7916,
                'endFilePos' => 49587,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'float',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1357,
            'endLine' => 1357,
            'startColumn' => 9,
            'endColumn' => 40,
            'parameterIndex' => 5,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return string[]
 */',
        'startLine' => 1351,
        'endLine' => 1386,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'output' => 
      array (
        'name' => 'output',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 1392,
        'endLine' => 1446,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'buildPageAnnotations' => 
      array (
        'name' => 'buildPageAnnotations',
        'parameters' => 
        array (
          'pageIndex' => 
          array (
            'name' => 'pageIndex',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1458,
            'endLine' => 1458,
            'startColumn' => 43,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'pageObjNumbers' => 
          array (
            'name' => 'pageObjNumbers',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1458,
            'endLine' => 1458,
            'startColumn' => 59,
            'endColumn' => 79,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Builds the /Annots array for a page: one Link annotation object
 * per rectangle recorded while a link scope was open. Internal
 * anchors are resolved by name here — an annotation pointing at an
 * anchor that was never registered is silently dropped (the text
 * itself was still drawn, it just isn\'t clickable).
 *
 * @param int[] $pageObjNumbers
 * @return string PDF array literal (e.g. "[12 0 R 13 0 R]") or \'\'
 */',
        'startLine' => 1458,
        'endLine' => 1499,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'buildOutlines' => 
      array (
        'name' => 'buildOutlines',
        'parameters' => 
        array (
          'pageObjNumbers' => 
          array (
            'name' => 'pageObjNumbers',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1512,
            'endLine' => 1512,
            'startColumn' => 36,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'int',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Builds the document outline tree (the "bookmarks" panel) from
 * the flat, reading-ordered entry list. Returns the root Outlines
 * object number, or null when no entry was registered.
 *
 * Levels deeper than `parent level + 1` are clamped (an H4 right
 * after an H2 becomes a direct child of the H2) so the tree stays
 * well-formed whatever the heading sequence.
 *
 * @param int[] $pageObjNumbers
 */',
        'startLine' => 1512,
        'endLine' => 1568,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'materializeOutlineItems' => 
      array (
        'name' => 'materializeOutlineItems',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return list<array{obj: int, title: string, page: int, y: float, parent: int|null, children: list<int>, prev: int|null, next: int|null}>
 */',
        'startLine' => 1573,
        'endLine' => 1645,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'buildInfoObject' => 
      array (
        'name' => 'buildInfoObject',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Builds the /Info dictionary from the typed metadata setters.
 * Empty fields are omitted so a minimal document keeps a minimal
 * Info dict.
 */',
        'startLine' => 1652,
        'endLine' => 1683,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'escapePdfLiteral' => 
      array (
        'name' => 'escapePdfLiteral',
        'parameters' => 
        array (
          'text' => 
          array (
            'name' => 'text',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1690,
            'endLine' => 1690,
            'startColumn' => 39,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Escapes a byte string for a PDF literal string WITHOUT charset
 * conversion — used for URIs, which must keep their original bytes
 * (they are typically ASCII / percent-encoded already).
 */',
        'startLine' => 1690,
        'endLine' => 1693,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'bootstrapSecurity' => 
      array (
        'name' => 'bootstrapSecurity',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 1695,
        'endLine' => 1704,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'buildEncryptObject' => 
      array (
        'name' => 'buildEncryptObject',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'int',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 1706,
        'endLine' => 1716,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'encryptBytesIfNeeded' => 
      array (
        'name' => 'encryptBytesIfNeeded',
        'parameters' => 
        array (
          'bytes' => 
          array (
            'name' => 'bytes',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1718,
            'endLine' => 1718,
            'startColumn' => 43,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'objectNumber' => 
          array (
            'name' => 'objectNumber',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1718,
            'endLine' => 1718,
            'startColumn' => 58,
            'endColumn' => 74,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 1718,
        'endLine' => 1721,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'formatTextString' => 
      array (
        'name' => 'formatTextString',
        'parameters' => 
        array (
          'text' => 
          array (
            'name' => 'text',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1723,
            'endLine' => 1723,
            'startColumn' => 39,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'objectNumber' => 
          array (
            'name' => 'objectNumber',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1723,
            'endLine' => 1723,
            'startColumn' => 53,
            'endColumn' => 69,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 1723,
        'endLine' => 1726,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'formatLiteralString' => 
      array (
        'name' => 'formatLiteralString',
        'parameters' => 
        array (
          'text' => 
          array (
            'name' => 'text',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1728,
            'endLine' => 1728,
            'startColumn' => 42,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'objectNumber' => 
          array (
            'name' => 'objectNumber',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1728,
            'endLine' => 1728,
            'startColumn' => 56,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'alreadyEscaped' => 
          array (
            'name' => 'alreadyEscaped',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 1728,
                'endLine' => 1728,
                'startTokenPos' => 10715,
                'startFilePos' => 62934,
                'endTokenPos' => 10715,
                'endFilePos' => 62938,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1728,
            'endLine' => 1728,
            'startColumn' => 75,
            'endColumn' => 102,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 1728,
        'endLine' => 1737,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'save' => 
      array (
        'name' => 'save',
        'parameters' => 
        array (
          'filename' => 
          array (
            'name' => 'filename',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1739,
            'endLine' => 1739,
            'startColumn' => 26,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 1739,
        'endLine' => 1742,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'allocateObject' => 
      array (
        'name' => 'allocateObject',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 1748,
        'endLine' => 1751,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'flushPage' => 
      array (
        'name' => 'flushPage',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 1753,
        'endLine' => 1790,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'buildResourceDict' => 
      array (
        'name' => 'buildResourceDict',
        'parameters' => 
        array (
          'pageIndex' => 
          array (
            'name' => 'pageIndex',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1792,
            'endLine' => 1792,
            'startColumn' => 40,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 1792,
        'endLine' => 1823,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'ensureFont' => 
      array (
        'name' => 'ensureFont',
        'parameters' => 
        array (
          'fontName' => 
          array (
            'name' => 'fontName',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1825,
            'endLine' => 1825,
            'startColumn' => 33,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 1825,
        'endLine' => 1846,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'registerImage' => 
      array (
        'name' => 'registerImage',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1853,
            'endLine' => 1853,
            'startColumn' => 36,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'hash' => 
          array (
            'name' => 'hash',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1853,
            'endLine' => 1853,
            'startColumn' => 50,
            'endColumn' => 61,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Enregistre l\'image comme XObject DCT. N\'écrit jamais de référence
 * partielle : GIF/WebP/PNG/… passent d\'abord par GD (imagecreatefromstring
 * puis re-encodage JPEG), en secours DCT direct pour le JPEG, puis PNG via fichier.
 */',
        'startLine' => 1853,
        'endLine' => 1938,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'buildPdf' => 
      array (
        'name' => 'buildPdf',
        'parameters' => 
        array (
          'infoObj' => 
          array (
            'name' => 'infoObj',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1944,
            'endLine' => 1944,
            'startColumn' => 31,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'encryptObj' => 
          array (
            'name' => 'encryptObj',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'int',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1944,
            'endLine' => 1944,
            'startColumn' => 45,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 1944,
        'endLine' => 1984,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'escapePdfString' => 
      array (
        'name' => 'escapePdfString',
        'parameters' => 
        array (
          'text' => 
          array (
            'name' => 'text',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2001,
            'endLine' => 2001,
            'startColumn' => 38,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Encode a PHP UTF-8 string for inclusion in a PDF text string
 * literal. PDF Type 1 fonts using /WinAnsiEncoding can only
 * represent characters present in cp1252; anything outside
 * (Greek, CJK, mathematical operators not in WinAnsi, …) is
 * replaced by \'?\' rather than silently dropped, so the missing
 * characters are visible in the output instead of producing
 * mysterious word-spacing or wrong widths downstream.
 *
 * Backslash, \'(\' and \')\' are escaped per PDF 1.7 §7.3.4.2.
 */',
        'startLine' => 2001,
        'endLine' => 2019,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
      'hexToRgb' => 
      array (
        'name' => 'hexToRgb',
        'parameters' => 
        array (
          'hex' => 
          array (
            'name' => 'hex',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2024,
            'endLine' => 2024,
            'startColumn' => 31,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return array{float, float, float}
 */',
        'startLine' => 2024,
        'endLine' => 2033,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Pdf',
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'currentClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));