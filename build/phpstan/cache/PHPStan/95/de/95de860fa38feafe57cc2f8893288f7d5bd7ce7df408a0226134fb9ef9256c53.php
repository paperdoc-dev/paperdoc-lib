<?php declare(strict_types = 1);

// osfsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Support/TocResolver.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Support\TocResolver
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-2a82d1f48f87cb7d67a22fc3417e87941cd9e51ced9d17f78f2ad5f57d4c14ca-8.5.8-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Support\\TocResolver',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Support/TocResolver.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Support',
    'name' => 'Paperdoc\\Support\\TocResolver',
    'shortName' => 'TocResolver',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Walks a document once, assigns every Heading a stable anchor
 * (explicit id, or a generated one) and exposes the flat entry list
 * used by the TableOfContents renderers.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 15,
    'endLine' => 75,
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
      'anchors' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\TocResolver',
        'implementingClassName' => 'Paperdoc\\Support\\TocResolver',
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
            'startLine' => 18,
            'endLine' => 18,
            'startTokenPos' => 55,
            'startFilePos' => 467,
            'endTokenPos' => 56,
            'endFilePos' => 468,
          ),
        ),
        'docComment' => '/** @var array<int, string> spl_object_id(Heading) => anchor */',
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'entries' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\TocResolver',
        'implementingClassName' => 'Paperdoc\\Support\\TocResolver',
        'name' => 'entries',
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
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 69,
            'startFilePos' => 588,
            'endTokenPos' => 70,
            'endFilePos' => 589,
          ),
        ),
        'docComment' => '/** @var list<array{level: int, text: string, anchor: string, generated: bool}> */',
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
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
      '__construct' => 
      array (
        'name' => '__construct',
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
            'startLine' => 23,
            'endLine' => 23,
            'startColumn' => 33,
            'endColumn' => 59,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 23,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support',
        'declaringClassName' => 'Paperdoc\\Support\\TocResolver',
        'implementingClassName' => 'Paperdoc\\Support\\TocResolver',
        'currentClassName' => 'Paperdoc\\Support\\TocResolver',
        'aliasName' => NULL,
      ),
      'anchorFor' => 
      array (
        'name' => 'anchorFor',
        'parameters' => 
        array (
          'heading' => 
          array (
            'name' => 'heading',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Paperdoc\\Document\\Heading',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 30,
            'endLine' => 30,
            'startColumn' => 31,
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
        'docComment' => NULL,
        'startLine' => 30,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support',
        'declaringClassName' => 'Paperdoc\\Support\\TocResolver',
        'implementingClassName' => 'Paperdoc\\Support\\TocResolver',
        'currentClassName' => 'Paperdoc\\Support\\TocResolver',
        'aliasName' => NULL,
      ),
      'entries' => 
      array (
        'name' => 'entries',
        'parameters' => 
        array (
          'maxLevel' => 
          array (
            'name' => 'maxLevel',
            'default' => 
            array (
              'code' => '6',
              'attributes' => 
              array (
                'startLine' => 37,
                'endLine' => 37,
                'startTokenPos' => 185,
                'startFilePos' => 1103,
                'endTokenPos' => 185,
                'endFilePos' => 1103,
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
            'startLine' => 37,
            'endLine' => 37,
            'startColumn' => 29,
            'endColumn' => 45,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @return list<array{level: int, text: string, anchor: string, generated: bool}> */',
        'startLine' => 37,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support',
        'declaringClassName' => 'Paperdoc\\Support\\TocResolver',
        'implementingClassName' => 'Paperdoc\\Support\\TocResolver',
        'currentClassName' => 'Paperdoc\\Support\\TocResolver',
        'aliasName' => NULL,
      ),
      'hasEntries' => 
      array (
        'name' => 'hasEntries',
        'parameters' => 
        array (
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
        'startLine' => 45,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support',
        'declaringClassName' => 'Paperdoc\\Support\\TocResolver',
        'implementingClassName' => 'Paperdoc\\Support\\TocResolver',
        'currentClassName' => 'Paperdoc\\Support\\TocResolver',
        'aliasName' => NULL,
      ),
      'walk' => 
      array (
        'name' => 'walk',
        'parameters' => 
        array (
          'elements' => 
          array (
            'name' => 'elements',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'iterable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 51,
            'endLine' => 51,
            'startColumn' => 27,
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
        'docComment' => '/** @param iterable<mixed> $elements */',
        'startLine' => 51,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support',
        'declaringClassName' => 'Paperdoc\\Support\\TocResolver',
        'implementingClassName' => 'Paperdoc\\Support\\TocResolver',
        'currentClassName' => 'Paperdoc\\Support\\TocResolver',
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