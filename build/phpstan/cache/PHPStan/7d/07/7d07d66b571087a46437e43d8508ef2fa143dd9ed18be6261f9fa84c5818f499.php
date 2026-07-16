<?php declare(strict_types = 1);

// odsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Support/Pdf/PdfEngine.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Support\Pdf\PdfEngine
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.8-79f2754c049f5f3b4d2084d08f7045c0e5cf6e1f98e1c5c05bbd93905b40113f',
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
    'endLine' => 2130,
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
            'startLine' => 168,
            'endLine' => 183,
            'startTokenPos' => 590,
            'startFilePos' => 5851,
            'endTokenPos' => 690,
            'endFilePos' => 6304,
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
        'startLine' => 168,
        'endLine' => 183,
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
            'startLine' => 1211,
            'endLine' => 1211,
            'startTokenPos' => 6901,
            'startFilePos' => 43200,
            'endTokenPos' => 6901,
            'endFilePos' => 43202,
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
        'startLine' => 1211,
        'endLine' => 1211,
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
            'startLine' => 1220,
            'endLine' => 1220,
            'startTokenPos' => 6914,
            'startFilePos' => 43619,
            'endTokenPos' => 6914,
            'endFilePos' => 43622,
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
        'startLine' => 1220,
        'endLine' => 1220,
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
      'beforeFlushPage' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'beforeFlushPage',
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
            'startLine' => 150,
            'endLine' => 150,
            'startTokenPos' => 520,
            'startFilePos' => 5131,
            'endTokenPos' => 520,
            'endFilePos' => 5134,
          ),
        ),
        'docComment' => '/**
 * Fired at the start of {@see flushPage()} while the page content
 * stream is still open — used to paint footnotes into the reserved
 * bottom band before the page is sealed.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 150,
        'endLine' => 150,
        'startColumn' => 5,
        'endColumn' => 46,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'reservedBottom' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'reservedBottom',
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
        'default' => 
        array (
          'code' => '0.0',
          'attributes' => 
          array (
            'startLine' => 153,
            'endLine' => 153,
            'startTokenPos' => 533,
            'startFilePos' => 5237,
            'endTokenPos' => 533,
            'endFilePos' => 5239,
          ),
        ),
        'docComment' => '/** Extra bottom inset reserved for footnotes (points). */',
        'attributes' => 
        array (
        ),
        'startLine' => 153,
        'endLine' => 153,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'columnCount' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'columnCount',
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
          'code' => '1',
          'attributes' => 
          array (
            'startLine' => 155,
            'endLine' => 155,
            'startTokenPos' => 544,
            'startFilePos' => 5274,
            'endTokenPos' => 544,
            'endFilePos' => 5274,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 155,
        'endLine' => 155,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'columnGap' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'columnGap',
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
        'default' => 
        array (
          'code' => '18.0',
          'attributes' => 
          array (
            'startLine' => 156,
            'endLine' => 156,
            'startTokenPos' => 555,
            'startFilePos' => 5308,
            'endTokenPos' => 555,
            'endFilePos' => 5311,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 156,
        'endLine' => 156,
        'startColumn' => 5,
        'endColumn' => 36,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'currentColumn' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'currentColumn',
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
            'startLine' => 157,
            'endLine' => 157,
            'startTokenPos' => 566,
            'startFilePos' => 5347,
            'endTokenPos' => 566,
            'endFilePos' => 5347,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 157,
        'endLine' => 157,
        'startColumn' => 5,
        'endColumn' => 35,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'columnTopY' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'implementingClassName' => 'Paperdoc\\Support\\Pdf\\PdfEngine',
        'name' => 'columnTopY',
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
        'default' => 
        array (
          'code' => '0.0',
          'attributes' => 
          array (
            'startLine' => 158,
            'endLine' => 158,
            'startTokenPos' => 577,
            'startFilePos' => 5382,
            'endTokenPos' => 577,
            'endFilePos' => 5384,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 158,
        'endLine' => 158,
        'startColumn' => 5,
        'endColumn' => 36,
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
                'startLine' => 186,
                'endLine' => 186,
                'startTokenPos' => 706,
                'startFilePos' => 6368,
                'endTokenPos' => 706,
                'endFilePos' => 6373,
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
            'startLine' => 186,
            'endLine' => 186,
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
                'startLine' => 187,
                'endLine' => 187,
                'startTokenPos' => 715,
                'startFilePos' => 6404,
                'endTokenPos' => 715,
                'endFilePos' => 6409,
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
            'startLine' => 187,
            'endLine' => 187,
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
                'startLine' => 188,
                'endLine' => 188,
                'startTokenPos' => 724,
                'startFilePos' => 6439,
                'endTokenPos' => 724,
                'endFilePos' => 6440,
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
            'startLine' => 188,
            'endLine' => 188,
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
                'startLine' => 189,
                'endLine' => 189,
                'startTokenPos' => 733,
                'startFilePos' => 6473,
                'endTokenPos' => 733,
                'endFilePos' => 6474,
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
            'startLine' => 189,
            'endLine' => 189,
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
                'startLine' => 190,
                'endLine' => 190,
                'startTokenPos' => 742,
                'startFilePos' => 6505,
                'endTokenPos' => 742,
                'endFilePos' => 6506,
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
            'startLine' => 190,
            'endLine' => 190,
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
                'startLine' => 191,
                'endLine' => 191,
                'startTokenPos' => 751,
                'startFilePos' => 6538,
                'endTokenPos' => 751,
                'endFilePos' => 6539,
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
            'startLine' => 191,
            'endLine' => 191,
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
        'startLine' => 185,
        'endLine' => 204,
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
            'startLine' => 210,
            'endLine' => 210,
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
        'startLine' => 210,
        'endLine' => 210,
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
            'startLine' => 211,
            'endLine' => 211,
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
        'startLine' => 211,
        'endLine' => 211,
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
            'startLine' => 217,
            'endLine' => 217,
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
        'startLine' => 217,
        'endLine' => 217,
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
            'startLine' => 218,
            'endLine' => 218,
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
        'startLine' => 218,
        'endLine' => 218,
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
            'startLine' => 219,
            'endLine' => 219,
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
        'startLine' => 219,
        'endLine' => 219,
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
            'startLine' => 220,
            'endLine' => 220,
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
        'startLine' => 220,
        'endLine' => 220,
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
            'startLine' => 221,
            'endLine' => 221,
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
        'startLine' => 221,
        'endLine' => 221,
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
            'startLine' => 222,
            'endLine' => 222,
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
        'startLine' => 222,
        'endLine' => 222,
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
            'startLine' => 223,
            'endLine' => 223,
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
        'startLine' => 223,
        'endLine' => 223,
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
            'startLine' => 236,
            'endLine' => 236,
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
                'startLine' => 236,
                'endLine' => 236,
                'startTokenPos' => 1132,
                'startFilePos' => 8730,
                'endTokenPos' => 1132,
                'endFilePos' => 8733,
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
            'startLine' => 236,
            'endLine' => 236,
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
        'startLine' => 236,
        'endLine' => 245,
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
        'startLine' => 247,
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
                'startLine' => 258,
                'endLine' => 258,
                'startTokenPos' => 1270,
                'startFilePos' => 9381,
                'endTokenPos' => 1270,
                'endFilePos' => 9385,
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
            'startLine' => 258,
            'endLine' => 258,
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
                'startLine' => 259,
                'endLine' => 259,
                'startTokenPos' => 1279,
                'startFilePos' => 9418,
                'endTokenPos' => 1279,
                'endFilePos' => 9422,
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
            'startLine' => 259,
            'endLine' => 259,
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
                'startLine' => 260,
                'endLine' => 260,
                'startTokenPos' => 1289,
                'startFilePos' => 9454,
                'endTokenPos' => 1289,
                'endFilePos' => 9457,
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
            'startLine' => 260,
            'endLine' => 260,
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
        'startLine' => 257,
        'endLine' => 265,
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
        'startLine' => 267,
        'endLine' => 270,
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
            'startLine' => 278,
            'endLine' => 278,
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
            'startLine' => 279,
            'endLine' => 279,
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
            'startLine' => 280,
            'endLine' => 280,
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
            'startLine' => 281,
            'endLine' => 281,
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
            'startLine' => 282,
            'endLine' => 282,
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
            'startLine' => 283,
            'endLine' => 283,
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
        'startLine' => 277,
        'endLine' => 314,
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
            'startLine' => 316,
            'endLine' => 316,
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
        'startLine' => 316,
        'endLine' => 332,
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
            'startLine' => 340,
            'endLine' => 340,
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
                'startLine' => 340,
                'endLine' => 340,
                'startTokenPos' => 1857,
                'startFilePos' => 12236,
                'endTokenPos' => 1857,
                'endFilePos' => 12239,
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
            'startLine' => 340,
            'endLine' => 340,
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
        'startLine' => 340,
        'endLine' => 350,
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
            'startLine' => 357,
            'endLine' => 357,
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
            'startLine' => 357,
            'endLine' => 357,
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
                'startLine' => 357,
                'endLine' => 357,
                'startTokenPos' => 1960,
                'startFilePos' => 12789,
                'endTokenPos' => 1960,
                'endFilePos' => 12792,
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
            'startLine' => 357,
            'endLine' => 357,
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
        'startLine' => 357,
        'endLine' => 369,
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
            'startLine' => 386,
            'endLine' => 386,
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
        'startLine' => 386,
        'endLine' => 389,
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
      'setBeforeFlushPage' => 
      array (
        'name' => 'setBeforeFlushPage',
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
            'startLine' => 391,
            'endLine' => 391,
            'startColumn' => 40,
            'endColumn' => 58,
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
        'startLine' => 391,
        'endLine' => 394,
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
      'setReservedBottom' => 
      array (
        'name' => 'setReservedBottom',
        'parameters' => 
        array (
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
            'startLine' => 396,
            'endLine' => 396,
            'startColumn' => 39,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 396,
        'endLine' => 399,
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
      'getReservedBottom' => 
      array (
        'name' => 'getReservedBottom',
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
        'startLine' => 401,
        'endLine' => 404,
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
      'setColumns' => 
      array (
        'name' => 'setColumns',
        'parameters' => 
        array (
          'count' => 
          array (
            'name' => 'count',
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
            'startLine' => 409,
            'endLine' => 409,
            'startColumn' => 32,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'gap' => 
          array (
            'name' => 'gap',
            'default' => 
            array (
              'code' => '18.0',
              'attributes' => 
              array (
                'startLine' => 409,
                'endLine' => 409,
                'startTokenPos' => 2192,
                'startFilePos' => 14505,
                'endTokenPos' => 2192,
                'endFilePos' => 14508,
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
            'startLine' => 409,
            'endLine' => 409,
            'startColumn' => 44,
            'endColumn' => 60,
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
 * Configure multi-column body flow. Call after {@see setPageGeometry()}.
 */',
        'startLine' => 409,
        'endLine' => 416,
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
      'getColumnCount' => 
      array (
        'name' => 'getColumnCount',
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
        'startLine' => 418,
        'endLine' => 421,
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
      'getColumnWidth' => 
      array (
        'name' => 'getColumnWidth',
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
        'startLine' => 423,
        'endLine' => 432,
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
      'getColumnOriginX' => 
      array (
        'name' => 'getColumnOriginX',
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
        'startLine' => 434,
        'endLine' => 437,
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
        'startLine' => 443,
        'endLine' => 463,
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
            'startLine' => 472,
            'endLine' => 472,
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
            'startLine' => 473,
            'endLine' => 473,
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
            'startLine' => 474,
            'endLine' => 474,
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
            'startLine' => 475,
            'endLine' => 475,
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
            'startLine' => 476,
            'endLine' => 476,
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
            'startLine' => 477,
            'endLine' => 477,
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
        'startLine' => 471,
        'endLine' => 491,
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
        'startLine' => 493,
        'endLine' => 493,
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
        'startLine' => 494,
        'endLine' => 494,
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
        'startLine' => 501,
        'endLine' => 510,
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
        'startLine' => 512,
        'endLine' => 515,
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
        'startLine' => 517,
        'endLine' => 517,
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
        'startLine' => 530,
        'endLine' => 530,
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
        'startLine' => 531,
        'endLine' => 531,
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
        'startLine' => 532,
        'endLine' => 532,
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
        'startLine' => 533,
        'endLine' => 533,
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
            'startLine' => 535,
            'endLine' => 535,
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
        'startLine' => 535,
        'endLine' => 538,
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
            'startLine' => 540,
            'endLine' => 540,
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
        'startLine' => 540,
        'endLine' => 543,
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
      'advanceColumnOrPage' => 
      array (
        'name' => 'advanceColumnOrPage',
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
        'docComment' => '/**
 * Advance to the next column, or start a new page when the last
 * column of the current page is full.
 */',
        'startLine' => 549,
        'endLine' => 560,
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
            'startLine' => 571,
            'endLine' => 571,
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
        'startLine' => 571,
        'endLine' => 574,
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
            'startLine' => 595,
            'endLine' => 595,
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
                'startLine' => 595,
                'endLine' => 595,
                'startTokenPos' => 3193,
                'startFilePos' => 21244,
                'endTokenPos' => 3193,
                'endFilePos' => 21250,
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
            'startLine' => 595,
            'endLine' => 595,
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
        'startLine' => 595,
        'endLine' => 634,
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
            'startLine' => 644,
            'endLine' => 644,
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
            'startLine' => 645,
            'endLine' => 645,
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
            'startLine' => 646,
            'endLine' => 646,
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
            'startLine' => 647,
            'endLine' => 647,
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
            'startLine' => 648,
            'endLine' => 648,
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
        'startLine' => 643,
        'endLine' => 695,
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
            'startLine' => 703,
            'endLine' => 703,
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
        'startLine' => 703,
        'endLine' => 716,
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
            'startLine' => 726,
            'endLine' => 726,
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
                'startLine' => 727,
                'endLine' => 727,
                'startTokenPos' => 3991,
                'startFilePos' => 25101,
                'endTokenPos' => 3991,
                'endFilePos' => 25111,
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
            'startLine' => 727,
            'endLine' => 727,
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
                'startLine' => 728,
                'endLine' => 728,
                'startTokenPos' => 4000,
                'startFilePos' => 25140,
                'endTokenPos' => 4000,
                'endFilePos' => 25141,
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
            'startLine' => 728,
            'endLine' => 728,
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
                'startLine' => 729,
                'endLine' => 729,
                'startTokenPos' => 4009,
                'startFilePos' => 25163,
                'endTokenPos' => 4009,
                'endFilePos' => 25163,
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
            'startLine' => 729,
            'endLine' => 729,
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
                'startLine' => 730,
                'endLine' => 730,
                'startTokenPos' => 4018,
                'startFilePos' => 25185,
                'endTokenPos' => 4018,
                'endFilePos' => 25185,
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
            'startLine' => 730,
            'endLine' => 730,
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
                'startLine' => 731,
                'endLine' => 731,
                'startTokenPos' => 4027,
                'startFilePos' => 25207,
                'endTokenPos' => 4027,
                'endFilePos' => 25207,
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
            'startLine' => 731,
            'endLine' => 731,
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
        'startLine' => 725,
        'endLine' => 741,
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
            'startLine' => 754,
            'endLine' => 754,
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
                'startLine' => 755,
                'endLine' => 755,
                'startTokenPos' => 4170,
                'startFilePos' => 26227,
                'endTokenPos' => 4170,
                'endFilePos' => 26237,
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
            'startLine' => 755,
            'endLine' => 755,
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
                'startLine' => 756,
                'endLine' => 756,
                'startTokenPos' => 4179,
                'startFilePos' => 26266,
                'endTokenPos' => 4179,
                'endFilePos' => 26267,
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
            'startLine' => 756,
            'endLine' => 756,
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
                'startLine' => 757,
                'endLine' => 757,
                'startTokenPos' => 4188,
                'startFilePos' => 26289,
                'endTokenPos' => 4188,
                'endFilePos' => 26289,
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
            'startLine' => 757,
            'endLine' => 757,
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
                'startLine' => 758,
                'endLine' => 758,
                'startTokenPos' => 4197,
                'startFilePos' => 26311,
                'endTokenPos' => 4197,
                'endFilePos' => 26311,
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
            'startLine' => 758,
            'endLine' => 758,
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
                'startLine' => 759,
                'endLine' => 759,
                'startTokenPos' => 4206,
                'startFilePos' => 26333,
                'endTokenPos' => 4206,
                'endFilePos' => 26333,
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
            'startLine' => 759,
            'endLine' => 759,
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
                'startLine' => 760,
                'endLine' => 760,
                'startTokenPos' => 4215,
                'startFilePos' => 26362,
                'endTokenPos' => 4215,
                'endFilePos' => 26362,
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
            'startLine' => 760,
            'endLine' => 760,
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
                'startLine' => 761,
                'endLine' => 761,
                'startTokenPos' => 4224,
                'startFilePos' => 26394,
                'endTokenPos' => 4224,
                'endFilePos' => 26397,
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
            'startLine' => 761,
            'endLine' => 761,
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
                'startLine' => 762,
                'endLine' => 762,
                'startTokenPos' => 4233,
                'startFilePos' => 26419,
                'endTokenPos' => 4233,
                'endFilePos' => 26419,
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
            'startLine' => 762,
            'endLine' => 762,
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
                'startLine' => 763,
                'endLine' => 763,
                'startTokenPos' => 4242,
                'startFilePos' => 26446,
                'endTokenPos' => 4242,
                'endFilePos' => 26451,
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
            'startLine' => 763,
            'endLine' => 763,
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
                'startLine' => 764,
                'endLine' => 764,
                'startTokenPos' => 4251,
                'startFilePos' => 26485,
                'endTokenPos' => 4251,
                'endFilePos' => 26487,
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
            'startLine' => 764,
            'endLine' => 764,
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
                'startLine' => 765,
                'endLine' => 765,
                'startTokenPos' => 4260,
                'startFilePos' => 26523,
                'endTokenPos' => 4260,
                'endFilePos' => 26525,
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
            'startLine' => 765,
            'endLine' => 765,
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
        'startLine' => 753,
        'endLine' => 839,
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
            'startLine' => 845,
            'endLine' => 845,
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
            'startLine' => 845,
            'endLine' => 845,
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
            'startLine' => 845,
            'endLine' => 845,
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
            'startLine' => 845,
            'endLine' => 845,
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
                'startLine' => 845,
                'endLine' => 845,
                'startTokenPos' => 4786,
                'startFilePos' => 29566,
                'endTokenPos' => 4786,
                'endFilePos' => 29568,
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
            'startLine' => 845,
            'endLine' => 845,
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
        'startLine' => 845,
        'endLine' => 849,
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
            'startLine' => 857,
            'endLine' => 857,
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
            'startLine' => 858,
            'endLine' => 858,
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
            'startLine' => 859,
            'endLine' => 859,
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
            'startLine' => 860,
            'endLine' => 860,
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
            'startLine' => 861,
            'endLine' => 861,
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
            'startLine' => 862,
            'endLine' => 862,
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
            'startLine' => 863,
            'endLine' => 863,
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
            'startLine' => 864,
            'endLine' => 864,
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
        'startLine' => 856,
        'endLine' => 869,
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
            'startLine' => 878,
            'endLine' => 878,
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
            'startLine' => 879,
            'endLine' => 879,
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
            'startLine' => 880,
            'endLine' => 880,
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
            'startLine' => 881,
            'endLine' => 881,
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
            'startLine' => 882,
            'endLine' => 882,
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
        'startLine' => 877,
        'endLine' => 891,
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
        'startLine' => 906,
        'endLine' => 909,
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
            'startLine' => 923,
            'endLine' => 923,
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
                'startLine' => 923,
                'endLine' => 923,
                'startTokenPos' => 5123,
                'startFilePos' => 32619,
                'endTokenPos' => 5123,
                'endFilePos' => 32620,
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
            'startLine' => 923,
            'endLine' => 923,
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
                'startLine' => 923,
                'endLine' => 923,
                'startTokenPos' => 5132,
                'startFilePos' => 32640,
                'endTokenPos' => 5132,
                'endFilePos' => 32641,
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
            'startLine' => 923,
            'endLine' => 923,
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
        'startLine' => 923,
        'endLine' => 934,
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
            'startLine' => 937,
            'endLine' => 937,
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
            'startLine' => 938,
            'endLine' => 938,
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
            'startLine' => 939,
            'endLine' => 939,
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
            'startLine' => 940,
            'endLine' => 940,
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
                'startLine' => 941,
                'endLine' => 941,
                'startTokenPos' => 5286,
                'startFilePos' => 33153,
                'endTokenPos' => 5286,
                'endFilePos' => 33156,
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
            'startLine' => 941,
            'endLine' => 941,
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
                'startLine' => 942,
                'endLine' => 942,
                'startTokenPos' => 5296,
                'startFilePos' => 33190,
                'endTokenPos' => 5296,
                'endFilePos' => 33193,
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
            'startLine' => 942,
            'endLine' => 942,
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
                'startLine' => 943,
                'endLine' => 943,
                'startTokenPos' => 5305,
                'startFilePos' => 33225,
                'endTokenPos' => 5305,
                'endFilePos' => 33227,
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
            'startLine' => 943,
            'endLine' => 943,
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
        'startLine' => 936,
        'endLine' => 966,
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
            'startLine' => 972,
            'endLine' => 972,
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
            'startLine' => 972,
            'endLine' => 972,
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
            'startLine' => 972,
            'endLine' => 972,
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
            'startLine' => 972,
            'endLine' => 972,
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
            'startLine' => 972,
            'endLine' => 972,
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
        'startLine' => 972,
        'endLine' => 988,
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
            'startLine' => 1014,
            'endLine' => 1014,
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
            'startLine' => 1015,
            'endLine' => 1015,
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
            'startLine' => 1016,
            'endLine' => 1016,
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
                'startLine' => 1017,
                'endLine' => 1017,
                'startTokenPos' => 5733,
                'startFilePos' => 36159,
                'endTokenPos' => 5733,
                'endFilePos' => 36161,
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
            'startLine' => 1017,
            'endLine' => 1017,
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
        'startLine' => 1013,
        'endLine' => 1049,
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
            'startLine' => 1059,
            'endLine' => 1059,
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
        'startLine' => 1059,
        'endLine' => 1071,
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
            'startLine' => 1077,
            'endLine' => 1077,
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
            'startLine' => 1078,
            'endLine' => 1078,
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
            'startLine' => 1079,
            'endLine' => 1079,
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
            'startLine' => 1080,
            'endLine' => 1080,
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
            'startLine' => 1081,
            'endLine' => 1081,
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
                'startLine' => 1082,
                'endLine' => 1082,
                'startTokenPos' => 6139,
                'startFilePos' => 38393,
                'endTokenPos' => 6139,
                'endFilePos' => 38393,
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
            'startLine' => 1082,
            'endLine' => 1082,
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
                'startLine' => 1083,
                'endLine' => 1083,
                'startTokenPos' => 6148,
                'startFilePos' => 38415,
                'endTokenPos' => 6148,
                'endFilePos' => 38415,
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
            'startLine' => 1083,
            'endLine' => 1083,
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
                'startLine' => 1084,
                'endLine' => 1084,
                'startTokenPos' => 6157,
                'startFilePos' => 38437,
                'endTokenPos' => 6157,
                'endFilePos' => 38437,
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
            'startLine' => 1084,
            'endLine' => 1084,
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
        'startLine' => 1076,
        'endLine' => 1094,
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
            'startLine' => 1108,
            'endLine' => 1108,
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
            'startLine' => 1109,
            'endLine' => 1109,
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
            'startLine' => 1110,
            'endLine' => 1110,
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
            'startLine' => 1111,
            'endLine' => 1111,
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
            'startLine' => 1112,
            'endLine' => 1112,
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
            'startLine' => 1113,
            'endLine' => 1113,
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
                'startLine' => 1114,
                'endLine' => 1114,
                'startTokenPos' => 6321,
                'startFilePos' => 39662,
                'endTokenPos' => 6321,
                'endFilePos' => 39662,
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
            'startLine' => 1114,
            'endLine' => 1114,
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
                'startLine' => 1115,
                'endLine' => 1115,
                'startTokenPos' => 6330,
                'startFilePos' => 39684,
                'endTokenPos' => 6330,
                'endFilePos' => 39684,
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
            'startLine' => 1115,
            'endLine' => 1115,
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
                'startLine' => 1116,
                'endLine' => 1116,
                'startTokenPos' => 6339,
                'startFilePos' => 39706,
                'endTokenPos' => 6339,
                'endFilePos' => 39706,
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
            'startLine' => 1116,
            'endLine' => 1116,
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
                'startLine' => 1117,
                'endLine' => 1117,
                'startTokenPos' => 6348,
                'startFilePos' => 39738,
                'endTokenPos' => 6348,
                'endFilePos' => 39741,
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
            'startLine' => 1117,
            'endLine' => 1117,
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
                'startLine' => 1118,
                'endLine' => 1118,
                'startTokenPos' => 6358,
                'startFilePos' => 39772,
                'endTokenPos' => 6358,
                'endFilePos' => 39775,
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
            'startLine' => 1118,
            'endLine' => 1118,
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
                'startLine' => 1119,
                'endLine' => 1119,
                'startTokenPos' => 6367,
                'startFilePos' => 39803,
                'endTokenPos' => 6367,
                'endFilePos' => 39807,
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
            'startLine' => 1119,
            'endLine' => 1119,
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
                'startLine' => 1120,
                'endLine' => 1120,
                'startTokenPos' => 6376,
                'startFilePos' => 39834,
                'endTokenPos' => 6376,
                'endFilePos' => 39839,
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
            'startLine' => 1120,
            'endLine' => 1120,
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
                'startLine' => 1121,
                'endLine' => 1121,
                'startTokenPos' => 6385,
                'startFilePos' => 39873,
                'endTokenPos' => 6385,
                'endFilePos' => 39875,
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
            'startLine' => 1121,
            'endLine' => 1121,
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
                'startLine' => 1122,
                'endLine' => 1122,
                'startTokenPos' => 6394,
                'startFilePos' => 39911,
                'endTokenPos' => 6394,
                'endFilePos' => 39913,
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
            'startLine' => 1122,
            'endLine' => 1122,
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
        'startLine' => 1107,
        'endLine' => 1189,
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
            'startLine' => 1223,
            'endLine' => 1223,
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
            'startLine' => 1224,
            'endLine' => 1224,
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
            'startLine' => 1225,
            'endLine' => 1225,
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
            'startLine' => 1226,
            'endLine' => 1226,
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
            'startLine' => 1227,
            'endLine' => 1227,
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
            'startLine' => 1228,
            'endLine' => 1228,
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
            'startLine' => 1229,
            'endLine' => 1229,
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
            'startLine' => 1230,
            'endLine' => 1230,
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
            'startLine' => 1231,
            'endLine' => 1231,
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
            'startLine' => 1232,
            'endLine' => 1232,
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
            'startLine' => 1233,
            'endLine' => 1233,
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
            'startLine' => 1234,
            'endLine' => 1234,
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
                'startLine' => 1235,
                'endLine' => 1235,
                'startTokenPos' => 6990,
                'startFilePos' => 43962,
                'endTokenPos' => 6990,
                'endFilePos' => 43964,
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
            'startLine' => 1235,
            'endLine' => 1235,
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
        'startLine' => 1222,
        'endLine' => 1329,
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
            'startLine' => 1351,
            'endLine' => 1351,
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
            'startLine' => 1351,
            'endLine' => 1351,
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
            'startLine' => 1351,
            'endLine' => 1351,
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
            'startLine' => 1351,
            'endLine' => 1351,
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
        'startLine' => 1351,
        'endLine' => 1396,
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
            'startLine' => 1402,
            'endLine' => 1402,
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
            'startLine' => 1402,
            'endLine' => 1402,
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
            'startLine' => 1402,
            'endLine' => 1402,
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
            'startLine' => 1402,
            'endLine' => 1402,
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
        'startLine' => 1402,
        'endLine' => 1411,
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
            'startLine' => 1419,
            'endLine' => 1419,
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
            'startLine' => 1420,
            'endLine' => 1420,
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
            'startLine' => 1421,
            'endLine' => 1421,
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
            'startLine' => 1422,
            'endLine' => 1422,
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
                'startLine' => 1423,
                'endLine' => 1423,
                'startTokenPos' => 8364,
                'startFilePos' => 51653,
                'endTokenPos' => 8364,
                'endFilePos' => 51656,
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
            'startLine' => 1423,
            'endLine' => 1423,
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
                'startLine' => 1424,
                'endLine' => 1424,
                'startTokenPos' => 8374,
                'startFilePos' => 51690,
                'endTokenPos' => 8374,
                'endFilePos' => 51693,
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
            'startLine' => 1424,
            'endLine' => 1424,
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
                'startLine' => 1425,
                'endLine' => 1425,
                'startTokenPos' => 8383,
                'startFilePos' => 51725,
                'endTokenPos' => 8383,
                'endFilePos' => 51727,
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
            'startLine' => 1425,
            'endLine' => 1425,
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
        'startLine' => 1418,
        'endLine' => 1429,
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
            'startLine' => 1434,
            'endLine' => 1434,
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
            'startLine' => 1434,
            'endLine' => 1434,
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
            'startLine' => 1434,
            'endLine' => 1434,
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
            'startLine' => 1434,
            'endLine' => 1434,
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
            'startLine' => 1434,
            'endLine' => 1434,
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
        'startLine' => 1434,
        'endLine' => 1438,
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
            'startLine' => 1444,
            'endLine' => 1444,
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
            'startLine' => 1445,
            'endLine' => 1445,
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
            'startLine' => 1446,
            'endLine' => 1446,
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
            'startLine' => 1447,
            'endLine' => 1447,
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
                'startLine' => 1448,
                'endLine' => 1448,
                'startTokenPos' => 8550,
                'startFilePos' => 52424,
                'endTokenPos' => 8550,
                'endFilePos' => 52426,
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
            'startLine' => 1448,
            'endLine' => 1448,
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
                'startLine' => 1449,
                'endLine' => 1449,
                'startTokenPos' => 8560,
                'startFilePos' => 52465,
                'endTokenPos' => 8560,
                'endFilePos' => 52468,
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
            'startLine' => 1449,
            'endLine' => 1449,
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
        'startLine' => 1443,
        'endLine' => 1478,
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
        'startLine' => 1484,
        'endLine' => 1538,
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
            'startLine' => 1550,
            'endLine' => 1550,
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
            'startLine' => 1550,
            'endLine' => 1550,
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
        'startLine' => 1550,
        'endLine' => 1591,
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
            'startLine' => 1604,
            'endLine' => 1604,
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
        'startLine' => 1604,
        'endLine' => 1660,
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
        'startLine' => 1665,
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
        'startLine' => 1744,
        'endLine' => 1775,
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
            'startLine' => 1782,
            'endLine' => 1782,
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
        'startLine' => 1782,
        'endLine' => 1785,
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
        'startLine' => 1787,
        'endLine' => 1796,
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
        'startLine' => 1798,
        'endLine' => 1808,
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
            'startLine' => 1810,
            'endLine' => 1810,
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
            'startLine' => 1810,
            'endLine' => 1810,
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
        'startLine' => 1810,
        'endLine' => 1813,
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
            'startLine' => 1815,
            'endLine' => 1815,
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
            'startLine' => 1815,
            'endLine' => 1815,
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
        'startLine' => 1815,
        'endLine' => 1818,
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
            'startLine' => 1820,
            'endLine' => 1820,
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
            'startLine' => 1820,
            'endLine' => 1820,
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
                'startLine' => 1820,
                'endLine' => 1820,
                'startTokenPos' => 11367,
                'startFilePos' => 65805,
                'endTokenPos' => 11367,
                'endFilePos' => 65809,
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
            'startLine' => 1820,
            'endLine' => 1820,
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
        'startLine' => 1820,
        'endLine' => 1829,
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
            'startLine' => 1831,
            'endLine' => 1831,
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
        'startLine' => 1831,
        'endLine' => 1834,
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
        'startLine' => 1840,
        'endLine' => 1843,
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
        'startLine' => 1845,
        'endLine' => 1886,
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
            'startLine' => 1888,
            'endLine' => 1888,
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
        'startLine' => 1888,
        'endLine' => 1919,
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
            'startLine' => 1921,
            'endLine' => 1921,
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
        'startLine' => 1921,
        'endLine' => 1942,
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
            'startLine' => 1949,
            'endLine' => 1949,
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
            'startLine' => 1949,
            'endLine' => 1949,
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
        'startLine' => 1949,
        'endLine' => 2034,
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
            'startLine' => 2040,
            'endLine' => 2040,
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
            'startLine' => 2040,
            'endLine' => 2040,
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
        'startLine' => 2040,
        'endLine' => 2080,
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
            'startLine' => 2097,
            'endLine' => 2097,
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
        'startLine' => 2097,
        'endLine' => 2115,
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
            'startLine' => 2120,
            'endLine' => 2120,
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
        'startLine' => 2120,
        'endLine' => 2129,
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