<?php declare(strict_types = 1);

// odsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Renderers/XlsRenderer.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Renderers\XlsRenderer
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.8-ea41a04506b256bb6cce84443097b29eae75e3e2494398200af98e725ae2388d',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Renderers\\XlsRenderer',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Renderers/XlsRenderer.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Renderers',
    'name' => 'Paperdoc\\Renderers\\XlsRenderer',
    'shortName' => 'XlsRenderer',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Renderer XLS natif (Excel 97-2003 BIFF8).
 *
 * Produit des fichiers .xls valides conformes à [MS-XLS]
 * en utilisant des enregistrements BIFF8 dans un flux OLE2 « Workbook ».
 * Pas de dépendance tierce.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 19,
    'endLine' => 370,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Paperdoc\\Renderers\\AbstractRenderer',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'RECORD_BOF' => 
      array (
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'name' => 'RECORD_BOF',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x809',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 62,
            'startFilePos' => 518,
            'endTokenPos' => 62,
            'endFilePos' => 523,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'RECORD_EOF' => 
      array (
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'name' => 'RECORD_EOF',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0xa',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 73,
            'startFilePos' => 564,
            'endTokenPos' => 73,
            'endFilePos' => 569,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'RECORD_CODEPAGE' => 
      array (
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'name' => 'RECORD_CODEPAGE',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x42',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 23,
            'startTokenPos' => 84,
            'startFilePos' => 610,
            'endTokenPos' => 84,
            'endFilePos' => 615,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'RECORD_WINDOW1' => 
      array (
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'name' => 'RECORD_WINDOW1',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x3d',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 24,
            'startTokenPos' => 95,
            'startFilePos' => 656,
            'endTokenPos' => 95,
            'endFilePos' => 661,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'RECORD_FONT' => 
      array (
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'name' => 'RECORD_FONT',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x31',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 25,
            'startTokenPos' => 106,
            'startFilePos' => 702,
            'endTokenPos' => 106,
            'endFilePos' => 707,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'RECORD_XF' => 
      array (
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'name' => 'RECORD_XF',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0xe0',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 117,
            'startFilePos' => 748,
            'endTokenPos' => 117,
            'endFilePos' => 753,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'RECORD_STYLE' => 
      array (
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'name' => 'RECORD_STYLE',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x293',
          'attributes' => 
          array (
            'startLine' => 27,
            'endLine' => 27,
            'startTokenPos' => 128,
            'startFilePos' => 794,
            'endTokenPos' => 128,
            'endFilePos' => 799,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'RECORD_BOUNDSHEET' => 
      array (
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'name' => 'RECORD_BOUNDSHEET',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x85',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 139,
            'startFilePos' => 840,
            'endTokenPos' => 139,
            'endFilePos' => 845,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'RECORD_DIMENSION' => 
      array (
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'name' => 'RECORD_DIMENSION',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x200',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 29,
            'startTokenPos' => 150,
            'startFilePos' => 886,
            'endTokenPos' => 150,
            'endFilePos' => 891,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'RECORD_SST' => 
      array (
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'name' => 'RECORD_SST',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0xfc',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 161,
            'startFilePos' => 932,
            'endTokenPos' => 161,
            'endFilePos' => 937,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'RECORD_LABELSST' => 
      array (
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'name' => 'RECORD_LABELSST',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0xfd',
          'attributes' => 
          array (
            'startLine' => 31,
            'endLine' => 31,
            'startTokenPos' => 172,
            'startFilePos' => 978,
            'endTokenPos' => 172,
            'endFilePos' => 983,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'RECORD_NUMBER' => 
      array (
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'name' => 'RECORD_NUMBER',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x203',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 32,
            'startTokenPos' => 183,
            'startFilePos' => 1024,
            'endTokenPos' => 183,
            'endFilePos' => 1029,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'getFormat' => 
      array (
        'name' => 'getFormat',
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
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 57,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Renderers',
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'currentClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'aliasName' => NULL,
      ),
      'render' => 
      array (
        'name' => 'render',
        'parameters' => 
        array (
          'document' => 
          array (
            'name' => 'document',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Paperdoc\\Contracts\\DocumentInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 36,
            'endLine' => 36,
            'startColumn' => 28,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 36,
        'endLine' => 47,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Renderers',
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'currentClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'aliasName' => NULL,
      ),
      'save' => 
      array (
        'name' => 'save',
        'parameters' => 
        array (
          'document' => 
          array (
            'name' => 'document',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Paperdoc\\Contracts\\DocumentInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 49,
            'endLine' => 49,
            'startColumn' => 26,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 49,
            'endLine' => 49,
            'startColumn' => 55,
            'endColumn' => 70,
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
        'docComment' => NULL,
        'startLine' => 49,
        'endLine' => 53,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Renderers',
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'currentClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'aliasName' => NULL,
      ),
      'buildXls' => 
      array (
        'name' => 'buildXls',
        'parameters' => 
        array (
          'document' => 
          array (
            'name' => 'document',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Paperdoc\\Contracts\\DocumentInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 59,
            'endLine' => 59,
            'startColumn' => 31,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 59,
            'endLine' => 59,
            'startColumn' => 60,
            'endColumn' => 75,
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
        'docComment' => NULL,
        'startLine' => 59,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Renderers',
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'currentClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'aliasName' => NULL,
      ),
      'collectSheets' => 
      array (
        'name' => 'collectSheets',
        'parameters' => 
        array (
          'document' => 
          array (
            'name' => 'document',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Paperdoc\\Contracts\\DocumentInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 83,
            'endLine' => 83,
            'startColumn' => 36,
            'endColumn' => 62,
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
 * @return array<int, array{name: string, rows: array<int, array<int, string>>}>
 */',
        'startLine' => 83,
        'endLine' => 119,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Renderers',
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'currentClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'aliasName' => NULL,
      ),
      'buildSst' => 
      array (
        'name' => 'buildSst',
        'parameters' => 
        array (
          'sheets' => 
          array (
            'name' => 'sheets',
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
            'startLine' => 131,
            'endLine' => 131,
            'startColumn' => 31,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Build SST index map: unique string → SST index.
 *
 * @param array<int, array{name: string, rows: array<int, array<int, string>>}> $sheets
 * @return array{strings: list<string>, index: array<string, int>}
 */',
        'startLine' => 131,
        'endLine' => 150,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Renderers',
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'currentClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'aliasName' => NULL,
      ),
      'buildWorkbookGlobals' => 
      array (
        'name' => 'buildWorkbookGlobals',
        'parameters' => 
        array (
          'sheets' => 
          array (
            'name' => 'sheets',
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
            'startLine' => 160,
            'endLine' => 160,
            'startColumn' => 43,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'sst' => 
          array (
            'name' => 'sst',
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
            'startLine' => 160,
            'endLine' => 160,
            'startColumn' => 58,
            'endColumn' => 67,
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
 * @param array<int, array{name: string, rows: array<int, array<int, string>>}> $sheets
 * @param array{strings: list<string>, index: array<string, int>} $sst
 */',
        'startLine' => 160,
        'endLine' => 263,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Renderers',
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'currentClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'aliasName' => NULL,
      ),
      'buildSheet' => 
      array (
        'name' => 'buildSheet',
        'parameters' => 
        array (
          'sheet' => 
          array (
            'name' => 'sheet',
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
            'startLine' => 273,
            'endLine' => 273,
            'startColumn' => 33,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'sst' => 
          array (
            'name' => 'sst',
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
            'startLine' => 273,
            'endLine' => 273,
            'startColumn' => 47,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param array{name: string, rows: array<int, array<int, string>>} $sheet
 * @param array{strings: list<string>, index: array<string, int>} $sst
 */',
        'startLine' => 273,
        'endLine' => 326,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Renderers',
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'currentClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'aliasName' => NULL,
      ),
      'biffRecord' => 
      array (
        'name' => 'biffRecord',
        'parameters' => 
        array (
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
            'startLine' => 332,
            'endLine' => 332,
            'startColumn' => 33,
            'endColumn' => 41,
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
            'startLine' => 332,
            'endLine' => 332,
            'startColumn' => 44,
            'endColumn' => 55,
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
        'startLine' => 332,
        'endLine' => 335,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Renderers',
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'currentClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'aliasName' => NULL,
      ),
      'biffString' => 
      array (
        'name' => 'biffString',
        'parameters' => 
        array (
          'str' => 
          array (
            'name' => 'str',
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
            'startLine' => 341,
            'endLine' => 341,
            'startColumn' => 33,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * BIFF8 byte string (used for sheet names, font names).
 * Format: charCount(1 byte) + flags(1 byte) + chars
 */',
        'startLine' => 341,
        'endLine' => 352,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Renderers',
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'currentClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'aliasName' => NULL,
      ),
      'biffUnicodeString' => 
      array (
        'name' => 'biffUnicodeString',
        'parameters' => 
        array (
          'str' => 
          array (
            'name' => 'str',
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
            'startLine' => 358,
            'endLine' => 358,
            'startColumn' => 40,
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
 * BIFF8 Unicode string for SST entries.
 * Format: charCount(2 bytes) + flags(1 byte) + chars
 */',
        'startLine' => 358,
        'endLine' => 369,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Renderers',
        'declaringClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'implementingClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
        'currentClassName' => 'Paperdoc\\Renderers\\XlsRenderer',
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