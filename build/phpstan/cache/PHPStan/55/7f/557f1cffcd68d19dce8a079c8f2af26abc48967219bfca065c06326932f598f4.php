<?php declare(strict_types = 1);

// odsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers/CsvParser.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Parsers\CsvParser
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.8-6cbf6f01f904f2cf519c0fa5024d70dd10b58c42a1d72fd60d1606b238f2488f',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Parsers\\CsvParser',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Parsers/CsvParser.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Parsers',
    'name' => 'Paperdoc\\Parsers\\CsvParser',
    'shortName' => 'CsvParser',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Parser CSV natif utilisant les fonctions fgetcsv de PHP.
 *
 * Stratégie : la première ligne est traitée comme en-tête,
 * le reste comme données tabulaires.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 16,
    'endLine' => 116,
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
      'MAX_ROWS' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'name' => 'MAX_ROWS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '10000',
          'attributes' => 
          array (
            'startLine' => 18,
            'endLine' => 18,
            'startTokenPos' => 78,
            'startFilePos' => 486,
            'endTokenPos' => 78,
            'endFilePos' => 491,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 36,
      ),
    ),
    'immediateProperties' => 
    array (
      'delimiter' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'name' => 'delimiter',
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
          'code' => '\',\'',
          'attributes' => 
          array (
            'startLine' => 20,
            'endLine' => 20,
            'startTokenPos' => 89,
            'startFilePos' => 527,
            'endTokenPos' => 89,
            'endFilePos' => 529,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 36,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'enclosure' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'name' => 'enclosure',
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
          'code' => '\'"\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 100,
            'startFilePos' => 564,
            'endTokenPos' => 100,
            'endFilePos' => 566,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 36,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'firstRowIsHeader' => 
      array (
        'declaringClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'name' => 'firstRowIsHeader',
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
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 111,
            'startFilePos' => 608,
            'endTokenPos' => 111,
            'endFilePos' => 611,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 44,
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
      'setDelimiter' => 
      array (
        'name' => 'setDelimiter',
        'parameters' => 
        array (
          'delimiter' => 
          array (
            'name' => 'delimiter',
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
            'startLine' => 28,
            'endLine' => 28,
            'startColumn' => 34,
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
        'startLine' => 28,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'currentClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'aliasName' => NULL,
      ),
      'setEnclosure' => 
      array (
        'name' => 'setEnclosure',
        'parameters' => 
        array (
          'enclosure' => 
          array (
            'name' => 'enclosure',
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
            'startLine' => 35,
            'endLine' => 35,
            'startColumn' => 34,
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
        'startLine' => 35,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'currentClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'aliasName' => NULL,
      ),
      'setFirstRowIsHeader' => 
      array (
        'name' => 'setFirstRowIsHeader',
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
            'startLine' => 42,
            'endLine' => 42,
            'startColumn' => 41,
            'endColumn' => 47,
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
        'startLine' => 42,
        'endLine' => 47,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'currentClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'aliasName' => NULL,
      ),
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
            'startLine' => 53,
            'endLine' => 53,
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
        'startLine' => 53,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'currentClassName' => 'Paperdoc\\Parsers\\CsvParser',
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
            'startLine' => 58,
            'endLine' => 58,
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
        'startLine' => 58,
        'endLine' => 115,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Parsers',
        'declaringClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'implementingClassName' => 'Paperdoc\\Parsers\\CsvParser',
        'currentClassName' => 'Paperdoc\\Parsers\\CsvParser',
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