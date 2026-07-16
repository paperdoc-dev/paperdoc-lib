<?php declare(strict_types = 1);

// odsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Document/Style/PageSetup.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Document\Style\PageSetup
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.8-68a7c6593f57454a3b28ec2ac78a37de25c7b00ad30e0e0bea318f8ac2c89e18',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Document\\Style\\PageSetup',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Document/Style/PageSetup.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Document\\Style',
    'name' => 'Paperdoc\\Document\\Style\\PageSetup',
    'shortName' => 'PageSetup',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Configuration physique d\'une page : dimensions, marges (padding) et
 * habillage (image et/ou couleur de fond).
 *
 * Toutes les distances sont exprimées en points PDF (1 pt = 1/72 pouce),
 * ce qui correspond aussi à l\'unité par défaut utilisée par les renderers
 * HTML (`pt`).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 18,
    'endLine' => 346,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'JsonSerializable',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'ORIENTATION_PORTRAIT' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'ORIENTATION_PORTRAIT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'portrait\'',
          'attributes' => 
          array (
            'startLine' => 20,
            'endLine' => 20,
            'startTokenPos' => 47,
            'startFilePos' => 517,
            'endTokenPos' => 47,
            'endFilePos' => 526,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 52,
      ),
      'ORIENTATION_LANDSCAPE' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'ORIENTATION_LANDSCAPE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'landscape\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 58,
            'startFilePos' => 570,
            'endTokenPos' => 58,
            'endFilePos' => 580,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 53,
      ),
      'BG_SIZE_COVER' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'BG_SIZE_COVER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'cover\'',
          'attributes' => 
          array (
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 71,
            'startFilePos' => 1389,
            'endTokenPos' => 71,
            'endFilePos' => 1395,
          ),
        ),
        'docComment' => '/**
 * Stratégie de redimensionnement de l\'image de fond.
 *
 *  - BG_SIZE_COVER   : couvre toute la page en préservant le ratio
 *                      (déborde et est rognée si nécessaire). Défaut.
 *  - BG_SIZE_CONTAIN : tient en entier dans la page en préservant le
 *                      ratio (peut laisser des bandes vides).
 *  - BG_SIZE_AUTO    : taille naturelle de l\'image (centrée ;
 *                      l\'excédent éventuel est rogné).
 *  - BG_SIZE_STRETCH : étire l\'image pour remplir la page sans
 *                      préserver le ratio (`100% 100%`).
 *
 * Toute autre chaîne CSS valide (`50% 50%`, `300pt 200pt`, …) est
 * acceptée et passée telle quelle au HTML.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 43,
      ),
      'BG_SIZE_CONTAIN' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'BG_SIZE_CONTAIN',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'contain\'',
          'attributes' => 
          array (
            'startLine' => 39,
            'endLine' => 39,
            'startTokenPos' => 82,
            'startFilePos' => 1433,
            'endTokenPos' => 82,
            'endFilePos' => 1441,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'BG_SIZE_AUTO' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'BG_SIZE_AUTO',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'auto\'',
          'attributes' => 
          array (
            'startLine' => 40,
            'endLine' => 40,
            'startTokenPos' => 93,
            'startFilePos' => 1479,
            'endTokenPos' => 93,
            'endFilePos' => 1484,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'BG_SIZE_STRETCH' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'BG_SIZE_STRETCH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'100% 100%\'',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 41,
            'startTokenPos' => 104,
            'startFilePos' => 1522,
            'endTokenPos' => 104,
            'endFilePos' => 1532,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 47,
      ),
    ),
    'immediateProperties' => 
    array (
      'width' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'width',
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
        'startLine' => 43,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 25,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'height' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'height',
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
        'endColumn' => 26,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'paddingTop' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'paddingTop',
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
          'code' => '40.0',
          'attributes' => 
          array (
            'startLine' => 46,
            'endLine' => 46,
            'startTokenPos' => 129,
            'startFilePos' => 1625,
            'endTokenPos' => 129,
            'endFilePos' => 1628,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 46,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'paddingRight' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'paddingRight',
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
          'code' => '40.0',
          'attributes' => 
          array (
            'startLine' => 47,
            'endLine' => 47,
            'startTokenPos' => 140,
            'startFilePos' => 1666,
            'endTokenPos' => 140,
            'endFilePos' => 1669,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 47,
        'endLine' => 47,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'paddingBottom' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'paddingBottom',
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
          'code' => '40.0',
          'attributes' => 
          array (
            'startLine' => 48,
            'endLine' => 48,
            'startTokenPos' => 151,
            'startFilePos' => 1707,
            'endTokenPos' => 151,
            'endFilePos' => 1710,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 48,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'paddingLeft' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'paddingLeft',
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
          'code' => '40.0',
          'attributes' => 
          array (
            'startLine' => 49,
            'endLine' => 49,
            'startTokenPos' => 162,
            'startFilePos' => 1748,
            'endTokenPos' => 162,
            'endFilePos' => 1751,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 49,
        'endLine' => 49,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'backgroundImage' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'backgroundImage',
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
                  'name' => 'Paperdoc\\Document\\Image',
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
            'startLine' => 51,
            'endLine' => 51,
            'startTokenPos' => 174,
            'startFilePos' => 1793,
            'endTokenPos' => 174,
            'endFilePos' => 1796,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 51,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 43,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'backgroundColor' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'backgroundColor',
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
            'startLine' => 53,
            'endLine' => 53,
            'startTokenPos' => 186,
            'startFilePos' => 1839,
            'endTokenPos' => 186,
            'endFilePos' => 1842,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 53,
        'endLine' => 53,
        'startColumn' => 5,
        'endColumn' => 44,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'backgroundSize' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'backgroundSize',
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
          'code' => 'self::BG_SIZE_COVER',
          'attributes' => 
          array (
            'startLine' => 55,
            'endLine' => 55,
            'startTokenPos' => 197,
            'startFilePos' => 1887,
            'endTokenPos' => 199,
            'endFilePos' => 1905,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 55,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 61,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'backgroundPosition' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'backgroundPosition',
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
          'code' => '\'center center\'',
          'attributes' => 
          array (
            'startLine' => 56,
            'endLine' => 56,
            'startTokenPos' => 210,
            'startFilePos' => 1949,
            'endTokenPos' => 210,
            'endFilePos' => 1963,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 56,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 57,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'backgroundRepeat' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'backgroundRepeat',
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
          'code' => '\'no-repeat\'',
          'attributes' => 
          array (
            'startLine' => 57,
            'endLine' => 57,
            'startTokenPos' => 221,
            'startFilePos' => 2007,
            'endTokenPos' => 221,
            'endFilePos' => 2017,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 57,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 53,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'orientation' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'orientation',
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
          'code' => 'self::ORIENTATION_PORTRAIT',
          'attributes' => 
          array (
            'startLine' => 59,
            'endLine' => 59,
            'startTokenPos' => 232,
            'startFilePos' => 2055,
            'endTokenPos' => 234,
            'endFilePos' => 2080,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 59,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 61,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'size' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'name' => 'size',
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
                  'name' => 'Paperdoc\\Enum\\PageSize',
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
            'startLine' => 61,
            'endLine' => 61,
            'startTokenPos' => 246,
            'startFilePos' => 2114,
            'endTokenPos' => 246,
            'endFilePos' => 2117,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 61,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 35,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'columnCount' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
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
            'startLine' => 64,
            'endLine' => 64,
            'startTokenPos' => 259,
            'startFilePos' => 2232,
            'endTokenPos' => 259,
            'endFilePos' => 2232,
          ),
        ),
        'docComment' => '/** Number of text columns on the page content area (1 = single column). */',
        'attributes' => 
        array (
        ),
        'startLine' => 64,
        'endLine' => 64,
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
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
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
            'startLine' => 67,
            'endLine' => 67,
            'startTokenPos' => 272,
            'startFilePos' => 2310,
            'endTokenPos' => 272,
            'endFilePos' => 2313,
          ),
        ),
        'docComment' => '/** Gap between columns, in points. */',
        'attributes' => 
        array (
        ),
        'startLine' => 67,
        'endLine' => 67,
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
          'width' => 
          array (
            'name' => 'width',
            'default' => 
            array (
              'code' => '595.28',
              'attributes' => 
              array (
                'startLine' => 69,
                'endLine' => 69,
                'startTokenPos' => 287,
                'startFilePos' => 2364,
                'endTokenPos' => 287,
                'endFilePos' => 2369,
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
            'startLine' => 69,
            'endLine' => 69,
            'startColumn' => 33,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'height' => 
          array (
            'name' => 'height',
            'default' => 
            array (
              'code' => '841.89',
              'attributes' => 
              array (
                'startLine' => 69,
                'endLine' => 69,
                'startTokenPos' => 296,
                'startFilePos' => 2388,
                'endTokenPos' => 296,
                'endFilePos' => 2393,
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
            'startLine' => 69,
            'endLine' => 69,
            'startColumn' => 56,
            'endColumn' => 77,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 69,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'make' => 
      array (
        'name' => 'make',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 79,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'fromSize' => 
      array (
        'name' => 'fromSize',
        'parameters' => 
        array (
          'size' => 
          array (
            'name' => 'size',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Paperdoc\\Enum\\PageSize',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 84,
            'endLine' => 84,
            'startColumn' => 37,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'orientation' => 
          array (
            'name' => 'orientation',
            'default' => 
            array (
              'code' => 'self::ORIENTATION_PORTRAIT',
              'attributes' => 
              array (
                'startLine' => 84,
                'endLine' => 84,
                'startTokenPos' => 368,
                'startFilePos' => 2797,
                'endTokenPos' => 370,
                'endFilePos' => 2822,
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
            'startLine' => 84,
            'endLine' => 84,
            'startColumn' => 53,
            'endColumn' => 100,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 84,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'custom' => 
      array (
        'name' => 'custom',
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
            'startLine' => 92,
            'endLine' => 92,
            'startColumn' => 35,
            'endColumn' => 46,
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
            'startLine' => 92,
            'endLine' => 92,
            'startColumn' => 49,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 92,
        'endLine' => 95,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'setSize' => 
      array (
        'name' => 'setSize',
        'parameters' => 
        array (
          'size' => 
          array (
            'name' => 'size',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Paperdoc\\Enum\\PageSize',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 101,
            'endLine' => 101,
            'startColumn' => 29,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'orientation' => 
          array (
            'name' => 'orientation',
            'default' => 
            array (
              'code' => 'self::ORIENTATION_PORTRAIT',
              'attributes' => 
              array (
                'startLine' => 101,
                'endLine' => 101,
                'startTokenPos' => 464,
                'startFilePos' => 3308,
                'endTokenPos' => 466,
                'endFilePos' => 3333,
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
            'startLine' => 101,
            'endLine' => 101,
            'startColumn' => 45,
            'endColumn' => 92,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 101,
        'endLine' => 117,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'setDimensions' => 
      array (
        'name' => 'setDimensions',
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
            'startLine' => 119,
            'endLine' => 119,
            'startColumn' => 35,
            'endColumn' => 46,
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
            'startLine' => 119,
            'endLine' => 119,
            'startColumn' => 49,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 119,
        'endLine' => 127,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'landscape' => 
      array (
        'name' => 'landscape',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 129,
        'endLine' => 139,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'portrait' => 
      array (
        'name' => 'portrait',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 141,
        'endLine' => 151,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'getWidth' => 
      array (
        'name' => 'getWidth',
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
        'startLine' => 153,
        'endLine' => 153,
        'startColumn' => 5,
        'endColumn' => 68,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'getHeight' => 
      array (
        'name' => 'getHeight',
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
        'startLine' => 154,
        'endLine' => 154,
        'startColumn' => 5,
        'endColumn' => 69,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'getOrientation' => 
      array (
        'name' => 'getOrientation',
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
        'startLine' => 155,
        'endLine' => 155,
        'startColumn' => 5,
        'endColumn' => 75,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'getSize' => 
      array (
        'name' => 'getSize',
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
                  'name' => 'Paperdoc\\Enum\\PageSize',
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
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 156,
        'endLine' => 156,
        'startColumn' => 5,
        'endColumn' => 67,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'setPadding' => 
      array (
        'name' => 'setPadding',
        'parameters' => 
        array (
          'values' => 
          array (
            'name' => 'values',
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
            'isVariadic' => true,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 169,
            'endLine' => 169,
            'startColumn' => 32,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Définit les quatre marges. Suit la convention CSS shorthand :
 *  - 1 valeur  : toutes les marges
 *  - 2 valeurs : (vertical, horizontal)
 *  - 3 valeurs : (top, horizontal, bottom)
 *  - 4 valeurs : (top, right, bottom, left)
 */',
        'startLine' => 169,
        'endLine' => 197,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'setPaddingTop' => 
      array (
        'name' => 'setPaddingTop',
        'parameters' => 
        array (
          'v' => 
          array (
            'name' => 'v',
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
            'startLine' => 199,
            'endLine' => 199,
            'startColumn' => 35,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 199,
        'endLine' => 199,
        'startColumn' => 5,
        'endColumn' => 99,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'setPaddingRight' => 
      array (
        'name' => 'setPaddingRight',
        'parameters' => 
        array (
          'v' => 
          array (
            'name' => 'v',
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
            'startLine' => 200,
            'endLine' => 200,
            'startColumn' => 37,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 200,
        'endLine' => 200,
        'startColumn' => 5,
        'endColumn' => 99,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'setPaddingBottom' => 
      array (
        'name' => 'setPaddingBottom',
        'parameters' => 
        array (
          'v' => 
          array (
            'name' => 'v',
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
            'startLine' => 201,
            'endLine' => 201,
            'startColumn' => 38,
            'endColumn' => 45,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 201,
        'endLine' => 201,
        'startColumn' => 5,
        'endColumn' => 99,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'setPaddingLeft' => 
      array (
        'name' => 'setPaddingLeft',
        'parameters' => 
        array (
          'v' => 
          array (
            'name' => 'v',
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
            'startLine' => 202,
            'endLine' => 202,
            'startColumn' => 36,
            'endColumn' => 43,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 202,
        'endLine' => 202,
        'startColumn' => 5,
        'endColumn' => 99,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'getPaddingTop' => 
      array (
        'name' => 'getPaddingTop',
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
        'startLine' => 204,
        'endLine' => 204,
        'startColumn' => 5,
        'endColumn' => 75,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'getPaddingRight' => 
      array (
        'name' => 'getPaddingRight',
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
        'startLine' => 205,
        'endLine' => 205,
        'startColumn' => 5,
        'endColumn' => 77,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'getPaddingBottom' => 
      array (
        'name' => 'getPaddingBottom',
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
        'startLine' => 206,
        'endLine' => 206,
        'startColumn' => 5,
        'endColumn' => 78,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'getPaddingLeft' => 
      array (
        'name' => 'getPaddingLeft',
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
        'startLine' => 207,
        'endLine' => 207,
        'startColumn' => 5,
        'endColumn' => 76,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'setBackgroundImage' => 
      array (
        'name' => 'setBackgroundImage',
        'parameters' => 
        array (
          'image' => 
          array (
            'name' => 'image',
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
                      'name' => 'Paperdoc\\Document\\Image',
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
            'startLine' => 213,
            'endLine' => 213,
            'startColumn' => 40,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 213,
        'endLine' => 218,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'getBackgroundImage' => 
      array (
        'name' => 'getBackgroundImage',
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
                  'name' => 'Paperdoc\\Document\\Image',
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
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 220,
        'endLine' => 223,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'setBackgroundColor' => 
      array (
        'name' => 'setBackgroundColor',
        'parameters' => 
        array (
          'color' => 
          array (
            'name' => 'color',
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
            'startLine' => 225,
            'endLine' => 225,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 225,
        'endLine' => 230,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'getBackgroundColor' => 
      array (
        'name' => 'getBackgroundColor',
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
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'setBackgroundSize' => 
      array (
        'name' => 'setBackgroundSize',
        'parameters' => 
        array (
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
            'startLine' => 237,
            'endLine' => 237,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 237,
        'endLine' => 242,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'getBackgroundSize' => 
      array (
        'name' => 'getBackgroundSize',
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
        'startLine' => 244,
        'endLine' => 244,
        'startColumn' => 5,
        'endColumn' => 81,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'setBackgroundPosition' => 
      array (
        'name' => 'setBackgroundPosition',
        'parameters' => 
        array (
          'position' => 
          array (
            'name' => 'position',
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
            'startLine' => 246,
            'endLine' => 246,
            'startColumn' => 43,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 246,
        'endLine' => 251,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'getBackgroundPosition' => 
      array (
        'name' => 'getBackgroundPosition',
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
        'startLine' => 253,
        'endLine' => 253,
        'startColumn' => 5,
        'endColumn' => 89,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'setBackgroundRepeat' => 
      array (
        'name' => 'setBackgroundRepeat',
        'parameters' => 
        array (
          'repeat' => 
          array (
            'name' => 'repeat',
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
            'startLine' => 255,
            'endLine' => 255,
            'startColumn' => 41,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 255,
        'endLine' => 260,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'getBackgroundRepeat' => 
      array (
        'name' => 'getBackgroundRepeat',
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
        'startLine' => 262,
        'endLine' => 262,
        'startColumn' => 5,
        'endColumn' => 85,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
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
        'startLine' => 268,
        'endLine' => 271,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'getContentHeight' => 
      array (
        'name' => 'getContentHeight',
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
        'startLine' => 273,
        'endLine' => 276,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'setColumnCount' => 
      array (
        'name' => 'setColumnCount',
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
            'startLine' => 282,
            'endLine' => 282,
            'startColumn' => 36,
            'endColumn' => 45,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 282,
        'endLine' => 287,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
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
        'startLine' => 289,
        'endLine' => 292,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'setColumnGap' => 
      array (
        'name' => 'setColumnGap',
        'parameters' => 
        array (
          'gap' => 
          array (
            'name' => 'gap',
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
            'startLine' => 294,
            'endLine' => 294,
            'startColumn' => 34,
            'endColumn' => 43,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 294,
        'endLine' => 299,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'getColumnGap' => 
      array (
        'name' => 'getColumnGap',
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
        'startLine' => 301,
        'endLine' => 304,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
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
        'docComment' => '/**
 * Width of a single text column inside the content area.
 */',
        'startLine' => 309,
        'endLine' => 318,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'aliasName' => NULL,
      ),
      'jsonSerialize' => 
      array (
        'name' => 'jsonSerialize',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'mixed',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 324,
        'endLine' => 345,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document\\Style',
        'declaringClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'implementingClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
        'currentClassName' => 'Paperdoc\\Document\\Style\\PageSetup',
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