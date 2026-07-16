<?php declare(strict_types = 1);

// osfsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Factory/ParserFactory.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Factory\ParserFactory
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-ea1c7ed58a09a5725279a01edc46ebe385ca9c375fd0a7f552a1ed365c41eb14-8.5.8-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Factory\\ParserFactory',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Factory/ParserFactory.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Factory',
    'name' => 'Paperdoc\\Factory\\ParserFactory',
    'shortName' => 'ParserFactory',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 74,
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
    ),
    'immediateProperties' => 
    array (
      'parsers' => 
      array (
        'declaringClassName' => 'Paperdoc\\Factory\\ParserFactory',
        'implementingClassName' => 'Paperdoc\\Factory\\ParserFactory',
        'name' => 'parsers',
        'modifiers' => 20,
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
            'startLine' => 13,
            'endLine' => 13,
            'startTokenPos' => 74,
            'startFilePos' => 337,
            'endTokenPos' => 75,
            'endFilePos' => 338,
          ),
        ),
        'docComment' => '/** @var ParserInterface[] */',
        'attributes' => 
        array (
        ),
        'startLine' => 13,
        'endLine' => 13,
        'startColumn' => 5,
        'endColumn' => 39,
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
      'getParsers' => 
      array (
        'name' => 'getParsers',
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
        'docComment' => '/** @return ParserInterface[] */',
        'startLine' => 16,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'Paperdoc\\Factory',
        'declaringClassName' => 'Paperdoc\\Factory\\ParserFactory',
        'implementingClassName' => 'Paperdoc\\Factory\\ParserFactory',
        'currentClassName' => 'Paperdoc\\Factory\\ParserFactory',
        'aliasName' => NULL,
      ),
      'getParser' => 
      array (
        'name' => 'getParser',
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
            'startLine' => 39,
            'endLine' => 39,
            'startColumn' => 38,
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
            'name' => 'Paperdoc\\Contracts\\ParserInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @throws \\InvalidArgumentException
 */',
        'startLine' => 39,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Paperdoc\\Factory',
        'declaringClassName' => 'Paperdoc\\Factory\\ParserFactory',
        'implementingClassName' => 'Paperdoc\\Factory\\ParserFactory',
        'currentClassName' => 'Paperdoc\\Factory\\ParserFactory',
        'aliasName' => NULL,
      ),
      'registerParser' => 
      array (
        'name' => 'registerParser',
        'parameters' => 
        array (
          'parser' => 
          array (
            'name' => 'parser',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Paperdoc\\Contracts\\ParserInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 52,
            'endLine' => 52,
            'startColumn' => 43,
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
        'startLine' => 52,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Paperdoc\\Factory',
        'declaringClassName' => 'Paperdoc\\Factory\\ParserFactory',
        'implementingClassName' => 'Paperdoc\\Factory\\ParserFactory',
        'currentClassName' => 'Paperdoc\\Factory\\ParserFactory',
        'aliasName' => NULL,
      ),
      'getSupportedExtensions' => 
      array (
        'name' => 'getSupportedExtensions',
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
 * @return string[]
 */',
        'startLine' => 60,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Paperdoc\\Factory',
        'declaringClassName' => 'Paperdoc\\Factory\\ParserFactory',
        'implementingClassName' => 'Paperdoc\\Factory\\ParserFactory',
        'currentClassName' => 'Paperdoc\\Factory\\ParserFactory',
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