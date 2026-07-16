<?php declare(strict_types = 1);

// odsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Console/BuildDictionaryCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Console\BuildDictionaryCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.8-f4a8ce5e25953526fb3f5f84913199e222829b82025d0bcde6acfbfc5361549d',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Console\\BuildDictionaryCommand',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Console/BuildDictionaryCommand.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Console',
    'name' => 'Paperdoc\\Console\\BuildDictionaryCommand',
    'shortName' => 'BuildDictionaryCommand',
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
    'endLine' => 109,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Console\\Command',
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
      'signature' => 
      array (
        'declaringClassName' => 'Paperdoc\\Console\\BuildDictionaryCommand',
        'implementingClassName' => 'Paperdoc\\Console\\BuildDictionaryCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'paperdoc:build-dictionary
        {sources* : Text files or directories to read}
        {--output=storage/paperdoc/dictionary.txt : Output dictionary path}
        {--min-freq=2 : Minimum word frequency to keep}
        {--min-length=2 : Minimum word length}
        {--append : Append to existing dictionary instead of overwriting}\'',
          'attributes' => 
          array (
            'startLine' => 12,
            'endLine' => 17,
            'startTokenPos' => 41,
            'startFilePos' => 217,
            'endTokenPos' => 41,
            'endFilePos' => 551,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 12,
        'endLine' => 17,
        'startColumn' => 5,
        'endColumn' => 75,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Paperdoc\\Console\\BuildDictionaryCommand',
        'implementingClassName' => 'Paperdoc\\Console\\BuildDictionaryCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Build a spell-correction dictionary from text files for OCR Layer 2\'',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 50,
            'startFilePos' => 584,
            'endTokenPos' => 50,
            'endFilePos' => 652,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 99,
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
      'handle' => 
      array (
        'name' => 'handle',
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
        'startLine' => 21,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Console',
        'declaringClassName' => 'Paperdoc\\Console\\BuildDictionaryCommand',
        'implementingClassName' => 'Paperdoc\\Console\\BuildDictionaryCommand',
        'currentClassName' => 'Paperdoc\\Console\\BuildDictionaryCommand',
        'aliasName' => NULL,
      ),
      'resolveFiles' => 
      array (
        'name' => 'resolveFiles',
        'parameters' => 
        array (
          'sources' => 
          array (
            'name' => 'sources',
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
            'startLine' => 87,
            'endLine' => 87,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @return string[] */',
        'startLine' => 87,
        'endLine' => 108,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Console',
        'declaringClassName' => 'Paperdoc\\Console\\BuildDictionaryCommand',
        'implementingClassName' => 'Paperdoc\\Console\\BuildDictionaryCommand',
        'currentClassName' => 'Paperdoc\\Console\\BuildDictionaryCommand',
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