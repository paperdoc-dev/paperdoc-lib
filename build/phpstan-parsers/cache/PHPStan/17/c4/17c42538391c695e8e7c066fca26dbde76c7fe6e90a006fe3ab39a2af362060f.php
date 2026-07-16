<?php declare(strict_types = 1);

// odsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers/XlsParser.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Parsers\XlsParser
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.8-476b1a51347ef339b7532c6f7f7dc3093c3024a9fa1ddbe52b77ff043f211001',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Parsers\\XlsParser',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers/XlsParser.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Parsers',
    'name' => 'Paperdoc\\Parsers\\XlsParser',
    'shortName' => 'XlsParser',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Parser pour les fichiers .xls (Excel 97-2003, format BIFF8 binaire).
 *
 * Stratégie :
 * 1. Ouvrir le fichier OLE2 via le flux « Workbook » (ou « Book »)
 * 2. Lire les enregistrements BIFF8 séquentiellement
 * 3. Collecter les SST (Shared String Table)
 * 4. Collecter les cellules (LABELSST, LABEL, NUMBER, RK, MULRK, FORMULA)
 * 5. Organiser par feuille (BOUNDSHEET) et par ligne/colonne
 *
 * Référence : [MS-XLS] — Excel (.xls) Binary File Format
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 24,
    'endLine' => 570,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Paperdoc\\Parsers\\AbstractParser',
    'implementsClassNames' => 
    array (
      0 => 'Paperdoc\\Contracts\\ParserInterface',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'RECORD_BOF' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'name' => 'RECORD_BOF',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x809',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 88,
            'startFilePos' => 860,
            'endTokenPos' => 88,
            'endFilePos' => 865,
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
      'RECORD_BOUNDSHEET' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'name' => 'RECORD_BOUNDSHEET',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x85',
          'attributes' => 
          array (
            'startLine' => 27,
            'endLine' => 27,
            'startTokenPos' => 99,
            'startFilePos' => 906,
            'endTokenPos' => 99,
            'endFilePos' => 911,
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
      'RECORD_SST' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'name' => 'RECORD_SST',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0xfc',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 110,
            'startFilePos' => 952,
            'endTokenPos' => 110,
            'endFilePos' => 957,
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
      'RECORD_CONTINUE' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'name' => 'RECORD_CONTINUE',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x3c',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 29,
            'startTokenPos' => 121,
            'startFilePos' => 998,
            'endTokenPos' => 121,
            'endFilePos' => 1003,
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
      'RECORD_LABELSST' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'name' => 'RECORD_LABELSST',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0xfd',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 132,
            'startFilePos' => 1044,
            'endTokenPos' => 132,
            'endFilePos' => 1049,
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
      'RECORD_LABEL' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'name' => 'RECORD_LABEL',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x204',
          'attributes' => 
          array (
            'startLine' => 31,
            'endLine' => 31,
            'startTokenPos' => 143,
            'startFilePos' => 1090,
            'endTokenPos' => 143,
            'endFilePos' => 1095,
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
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
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
            'startTokenPos' => 154,
            'startFilePos' => 1136,
            'endTokenPos' => 154,
            'endFilePos' => 1141,
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
      'RECORD_RK' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'name' => 'RECORD_RK',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x27e',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 33,
            'startTokenPos' => 165,
            'startFilePos' => 1182,
            'endTokenPos' => 165,
            'endFilePos' => 1187,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'RECORD_MULRK' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'name' => 'RECORD_MULRK',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0xbd',
          'attributes' => 
          array (
            'startLine' => 34,
            'endLine' => 34,
            'startTokenPos' => 176,
            'startFilePos' => 1228,
            'endTokenPos' => 176,
            'endFilePos' => 1233,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'RECORD_FORMULA' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'name' => 'RECORD_FORMULA',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x6',
          'attributes' => 
          array (
            'startLine' => 35,
            'endLine' => 35,
            'startTokenPos' => 187,
            'startFilePos' => 1274,
            'endTokenPos' => 187,
            'endFilePos' => 1279,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'RECORD_STRING' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'name' => 'RECORD_STRING',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x207',
          'attributes' => 
          array (
            'startLine' => 36,
            'endLine' => 36,
            'startTokenPos' => 198,
            'startFilePos' => 1320,
            'endTokenPos' => 198,
            'endFilePos' => 1325,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 36,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'RECORD_BOOLERR' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'name' => 'RECORD_BOOLERR',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0x205',
          'attributes' => 
          array (
            'startLine' => 37,
            'endLine' => 37,
            'startTokenPos' => 209,
            'startFilePos' => 1366,
            'endTokenPos' => 209,
            'endFilePos' => 1371,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
    ),
    'immediateProperties' => 
    array (
      'sst' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'name' => 'sst',
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
            'startLine' => 40,
            'endLine' => 40,
            'startTokenPos' => 222,
            'startFilePos' => 1425,
            'endTokenPos' => 223,
            'endFilePos' => 1426,
          ),
        ),
        'docComment' => '/** @var string[] */',
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'cellData' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'name' => 'cellData',
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
            'startLine' => 43,
            'endLine' => 43,
            'startTokenPos' => 236,
            'startFilePos' => 1556,
            'endTokenPos' => 237,
            'endFilePos' => 1557,
          ),
        ),
        'docComment' => '/** @var array<int, array<int, array<int, string>>> sheetIndex → row → col → value */',
        'attributes' => 
        array (
        ),
        'startLine' => 43,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'sheetNames' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'name' => 'sheetNames',
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
            'startLine' => 46,
            'endLine' => 46,
            'startTokenPos' => 250,
            'startFilePos' => 1630,
            'endTokenPos' => 251,
            'endFilePos' => 1631,
          ),
        ),
        'docComment' => '/** @var string[] Sheet names */',
        'attributes' => 
        array (
        ),
        'startLine' => 46,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 35,
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
      'supports' => 
      array (
        'name' => 'supports',
        'parameters' => 
        array (
          'extension' => 
          array (
            'name' => 'extension',
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
            'startLine' => 48,
            'endLine' => 48,
            'startColumn' => 30,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 48,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'currentClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'aliasName' => NULL,
      ),
      'parse' => 
      array (
        'name' => 'parse',
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
            'startLine' => 53,
            'endLine' => 53,
            'startColumn' => 27,
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
            'name' => 'Paperdoc\\Contracts\\DocumentInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 53,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'currentClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'aliasName' => NULL,
      ),
      'parseRecords' => 
      array (
        'name' => 'parseRecords',
        'parameters' => 
        array (
          'stream' => 
          array (
            'name' => 'stream',
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
            'startLine' => 97,
            'endLine' => 97,
            'startColumn' => 35,
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
        'startLine' => 97,
        'endLine' => 237,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'currentClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'aliasName' => NULL,
      ),
      'parseSst' => 
      array (
        'name' => 'parseSst',
        'parameters' => 
        array (
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
            'startLine' => 243,
            'endLine' => 243,
            'startColumn' => 31,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'stream' => 
          array (
            'name' => 'stream',
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
            'startLine' => 243,
            'endLine' => 243,
            'startColumn' => 45,
            'endColumn' => 58,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'nextPos' => 
          array (
            'name' => 'nextPos',
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
            'startLine' => 243,
            'endLine' => 243,
            'startColumn' => 61,
            'endColumn' => 72,
            'parameterIndex' => 2,
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
        'startLine' => 243,
        'endLine' => 327,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'currentClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'aliasName' => NULL,
      ),
      'readContinue' => 
      array (
        'name' => 'readContinue',
        'parameters' => 
        array (
          'stream' => 
          array (
            'name' => 'stream',
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
            'startColumn' => 35,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'pos' => 
          array (
            'name' => 'pos',
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
            'startColumn' => 51,
            'endColumn' => 58,
            'parameterIndex' => 1,
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
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return array{data: string, nextPos: int}|null
 */',
        'startLine' => 332,
        'endLine' => 349,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'currentClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'aliasName' => NULL,
      ),
      'buildTable' => 
      array (
        'name' => 'buildTable',
        'parameters' => 
        array (
          'rows' => 
          array (
            'name' => 'rows',
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
            'startLine' => 358,
            'endLine' => 358,
            'startColumn' => 33,
            'endColumn' => 43,
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
                  'name' => 'Paperdoc\\Document\\Table',
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
        'docComment' => '/**
 * @param array<int, array<int, string>> $rows
 */',
        'startLine' => 358,
        'endLine' => 397,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'currentClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'aliasName' => NULL,
      ),
      'extractSummaryInfo' => 
      array (
        'name' => 'extractSummaryInfo',
        'parameters' => 
        array (
          'ole' => 
          array (
            'name' => 'ole',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 403,
            'endLine' => 403,
            'startColumn' => 41,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'document' => 
          array (
            'name' => 'document',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Paperdoc\\Document\\Document',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 403,
            'endLine' => 403,
            'startColumn' => 58,
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
        'startLine' => 403,
        'endLine' => 415,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'currentClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'aliasName' => NULL,
      ),
      'parseSummaryInfo' => 
      array (
        'name' => 'parseSummaryInfo',
        'parameters' => 
        array (
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
            'startLine' => 417,
            'endLine' => 417,
            'startColumn' => 39,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'document' => 
          array (
            'name' => 'document',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Paperdoc\\Document\\Document',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 417,
            'endLine' => 417,
            'startColumn' => 53,
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
        'startLine' => 417,
        'endLine' => 473,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'currentClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'aliasName' => NULL,
      ),
      'readBiffString' => 
      array (
        'name' => 'readBiffString',
        'parameters' => 
        array (
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
            'startLine' => 479,
            'endLine' => 479,
            'startColumn' => 37,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 479,
            'endLine' => 479,
            'startColumn' => 51,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 479,
        'endLine' => 503,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'currentClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'aliasName' => NULL,
      ),
      'decodeRk' => 
      array (
        'name' => 'decodeRk',
        'parameters' => 
        array (
          'rk' => 
          array (
            'name' => 'rk',
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
            'startLine' => 505,
            'endLine' => 505,
            'startColumn' => 31,
            'endColumn' => 37,
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
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 505,
        'endLine' => 522,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'currentClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'aliasName' => NULL,
      ),
      'formatNumber' => 
      array (
        'name' => 'formatNumber',
        'parameters' => 
        array (
          'val' => 
          array (
            'name' => 'val',
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
            'startLine' => 524,
            'endLine' => 524,
            'startColumn' => 35,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 524,
        'endLine' => 531,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'currentClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'aliasName' => NULL,
      ),
      'unpackFloat' => 
      array (
        'name' => 'unpackFloat',
        'parameters' => 
        array (
          'bin' => 
          array (
            'name' => 'bin',
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
            'startLine' => 533,
            'endLine' => 533,
            'startColumn' => 34,
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
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 533,
        'endLine' => 541,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'currentClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'aliasName' => NULL,
      ),
      'readUint16' => 
      array (
        'name' => 'readUint16',
        'parameters' => 
        array (
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
            'startLine' => 543,
            'endLine' => 543,
            'startColumn' => 33,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 543,
            'endLine' => 543,
            'startColumn' => 47,
            'endColumn' => 57,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 543,
        'endLine' => 555,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'currentClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'aliasName' => NULL,
      ),
      'readUint32' => 
      array (
        'name' => 'readUint32',
        'parameters' => 
        array (
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
            'startLine' => 557,
            'endLine' => 557,
            'startColumn' => 33,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 557,
            'endLine' => 557,
            'startColumn' => 47,
            'endColumn' => 57,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 557,
        'endLine' => 569,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\XlsParser',
        'currentClassName' => 'Paperdoc\\Parsers\\XlsParser',
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