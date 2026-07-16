<?php declare(strict_types = 1);

// odsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Support/Ole2/Ole2Writer.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Support\Ole2\Ole2Writer
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.8-88855dd83b3d89abb3c90045639bfd74ca80e985d4fc01a173cfac17ec9165a4',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Support/Ole2/Ole2Writer.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Support\\Ole2',
    'name' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
    'shortName' => 'Ole2Writer',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * OLE2 Compound Binary File writer (Microsoft Structured Storage).
 *
 * Produces valid [MS-CFB] files with 512-byte sectors.
 * Used by DocRenderer, XlsRenderer, and PptRenderer
 * to wrap format-specific binary streams.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 227,
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
      'MAGIC' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'name' => 'MAGIC',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '"\\xd0\\xcf\\x11ࡱ\\x1a\\xe1"',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 16,
            'startTokenPos' => 31,
            'startFilePos' => 343,
            'endTokenPos' => 31,
            'endFilePos' => 376,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 61,
      ),
      'SECTOR_SIZE' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'name' => 'SECTOR_SIZE',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '512',
          'attributes' => 
          array (
            'startLine' => 18,
            'endLine' => 18,
            'startTokenPos' => 42,
            'startFilePos' => 419,
            'endTokenPos' => 42,
            'endFilePos' => 421,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 43,
      ),
      'ENTRIES_PER_SECTOR' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'name' => 'ENTRIES_PER_SECTOR',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '128',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 53,
            'startFilePos' => 463,
            'endTokenPos' => 53,
            'endFilePos' => 465,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 43,
      ),
      'DIRS_PER_SECTOR' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'name' => 'DIRS_PER_SECTOR',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '4',
          'attributes' => 
          array (
            'startLine' => 20,
            'endLine' => 20,
            'startTokenPos' => 66,
            'startFilePos' => 518,
            'endTokenPos' => 66,
            'endFilePos' => 518,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'ENDOFCHAIN' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'name' => 'ENDOFCHAIN',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0xfffffffe',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 79,
            'startFilePos' => 568,
            'endTokenPos' => 79,
            'endFilePos' => 577,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'FREESECT' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'name' => 'FREESECT',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0xffffffff',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 23,
            'startTokenPos' => 90,
            'startFilePos' => 611,
            'endTokenPos' => 90,
            'endFilePos' => 620,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'FATSECT' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'name' => 'FATSECT',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0xfffffffd',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 24,
            'startTokenPos' => 101,
            'startFilePos' => 654,
            'endTokenPos' => 101,
            'endFilePos' => 663,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
    ),
    'immediateProperties' => 
    array (
      'streams' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'name' => 'streams',
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
            'startLine' => 27,
            'endLine' => 27,
            'startTokenPos' => 114,
            'startFilePos' => 758,
            'endTokenPos' => 115,
            'endFilePos' => 759,
          ),
        ),
        'docComment' => '/** @var array<int, array{name: string, data: string}> */',
        'attributes' => 
        array (
        ),
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 32,
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
      'addStream' => 
      array (
        'name' => 'addStream',
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
            'startLine' => 29,
            'endLine' => 29,
            'startColumn' => 31,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'data' => 
          array (
            'name' => 'data',
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
            'startLine' => 29,
            'endLine' => 29,
            'startColumn' => 45,
            'endColumn' => 56,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 29,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'aliasName' => NULL,
      ),
      'build' => 
      array (
        'name' => 'build',
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
        'docComment' => '/**
 * Build the complete OLE2 binary file and return it as a string.
 */',
        'startLine' => 39,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'aliasName' => NULL,
      ),
      'planLayout' => 
      array (
        'name' => 'planLayout',
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
 * @return array{streamStarts: int[], dirStart: int, fatStart: int,
 *               numDirSectors: int, numFatSectors: int, fat: int[]}
 */',
        'startLine' => 74,
        'endLine' => 126,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'aliasName' => NULL,
      ),
      'buildHeader' => 
      array (
        'name' => 'buildHeader',
        'parameters' => 
        array (
          'layout' => 
          array (
            'name' => 'layout',
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
            'startLine' => 135,
            'endLine' => 135,
            'startColumn' => 34,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param array{streamStarts: int[], dirStart: int, fatStart: int, numDirSectors: int, numFatSectors: int, fat: int[]} $layout
 */',
        'startLine' => 135,
        'endLine' => 161,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'aliasName' => NULL,
      ),
      'buildDirectoryData' => 
      array (
        'name' => 'buildDirectoryData',
        'parameters' => 
        array (
          'layout' => 
          array (
            'name' => 'layout',
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
            'startLine' => 170,
            'endLine' => 170,
            'startColumn' => 41,
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
        'docComment' => '/**
 * @param array{streamStarts: int[], dirStart: int, fatStart: int, numDirSectors: int, numFatSectors: int, fat: int[]} $layout
 */',
        'startLine' => 170,
        'endLine' => 194,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'aliasName' => NULL,
      ),
      'packDirEntry' => 
      array (
        'name' => 'packDirEntry',
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
            'startLine' => 200,
            'endLine' => 200,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'type' => 
          array (
            'name' => 'type',
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
            'startLine' => 201,
            'endLine' => 201,
            'startColumn' => 9,
            'endColumn' => 17,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'startSector' => 
          array (
            'name' => 'startSector',
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
            'startLine' => 202,
            'endLine' => 202,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 2,
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
            'startLine' => 203,
            'endLine' => 203,
            'startColumn' => 9,
            'endColumn' => 17,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'child' => 
          array (
            'name' => 'child',
            'default' => 
            array (
              'code' => 'self::FREESECT',
              'attributes' => 
              array (
                'startLine' => 204,
                'endLine' => 204,
                'startTokenPos' => 1467,
                'startFilePos' => 7072,
                'endTokenPos' => 1469,
                'endFilePos' => 7085,
              ),
            ),
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
            'startLine' => 204,
            'endLine' => 204,
            'startColumn' => 9,
            'endColumn' => 35,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
          'leftSibling' => 
          array (
            'name' => 'leftSibling',
            'default' => 
            array (
              'code' => 'self::FREESECT',
              'attributes' => 
              array (
                'startLine' => 205,
                'endLine' => 205,
                'startTokenPos' => 1478,
                'startFilePos' => 7115,
                'endTokenPos' => 1480,
                'endFilePos' => 7128,
              ),
            ),
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
            'startLine' => 205,
            'endLine' => 205,
            'startColumn' => 9,
            'endColumn' => 41,
            'parameterIndex' => 5,
            'isOptional' => true,
          ),
          'rightSibling' => 
          array (
            'name' => 'rightSibling',
            'default' => 
            array (
              'code' => 'self::FREESECT',
              'attributes' => 
              array (
                'startLine' => 206,
                'endLine' => 206,
                'startTokenPos' => 1489,
                'startFilePos' => 7159,
                'endTokenPos' => 1491,
                'endFilePos' => 7172,
              ),
            ),
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
            'startLine' => 206,
            'endLine' => 206,
            'startColumn' => 9,
            'endColumn' => 42,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Pack a single 128-byte directory entry.
 */',
        'startLine' => 199,
        'endLine' => 226,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Writer',
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