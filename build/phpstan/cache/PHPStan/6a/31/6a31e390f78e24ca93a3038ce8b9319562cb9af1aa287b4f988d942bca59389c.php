<?php declare(strict_types = 1);

// odsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Contracts/OcrProcessorInterface.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Contracts\OcrProcessorInterface
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.8-a15d6b5f94799be5f5494e985e06a037acc9ef641fd108bf3c0fd12b583dcbe1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Contracts\\OcrProcessorInterface',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Contracts/OcrProcessorInterface.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Contracts',
    'name' => 'Paperdoc\\Contracts\\OcrProcessorInterface',
    'shortName' => 'OcrProcessorInterface',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 7,
    'endLine' => 42,
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
    ),
    'immediateMethods' => 
    array (
      'recognize' => 
      array (
        'name' => 'recognize',
        'parameters' => 
        array (
          'imagePath' => 
          array (
            'name' => 'imagePath',
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
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 31,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'language' => 
          array (
            'name' => 'language',
            'default' => 
            array (
              'code' => '\'eng\'',
              'attributes' => 
              array (
                'startLine' => 18,
                'endLine' => 18,
                'startTokenPos' => 40,
                'startFilePos' => 460,
                'endTokenPos' => 40,
                'endFilePos' => 464,
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
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 50,
            'endColumn' => 73,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Extract text from an image file using OCR.
 *
 * @param  string $imagePath Absolute path to the image file
 * @param  string $language  Language code (e.g. \'fra\', \'eng\')
 * @return string            Recognized text
 *
 * @throws \\RuntimeException
 */',
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 83,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Contracts',
        'declaringClassName' => 'Paperdoc\\Contracts\\OcrProcessorInterface',
        'implementingClassName' => 'Paperdoc\\Contracts\\OcrProcessorInterface',
        'currentClassName' => 'Paperdoc\\Contracts\\OcrProcessorInterface',
        'aliasName' => NULL,
      ),
      'isAvailable' => 
      array (
        'name' => 'isAvailable',
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
        'docComment' => '/**
 * Whether the OCR engine is installed and usable.
 */',
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 40,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Contracts',
        'declaringClassName' => 'Paperdoc\\Contracts\\OcrProcessorInterface',
        'implementingClassName' => 'Paperdoc\\Contracts\\OcrProcessorInterface',
        'currentClassName' => 'Paperdoc\\Contracts\\OcrProcessorInterface',
        'aliasName' => NULL,
      ),
      'buildCommand' => 
      array (
        'name' => 'buildCommand',
        'parameters' => 
        array (
          'imagePath' => 
          array (
            'name' => 'imagePath',
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
            'startLine' => 34,
            'endLine' => 34,
            'startColumn' => 34,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'language' => 
          array (
            'name' => 'language',
            'default' => 
            array (
              'code' => '\'eng\'',
              'attributes' => 
              array (
                'startLine' => 34,
                'endLine' => 34,
                'startTokenPos' => 80,
                'startFilePos' => 1009,
                'endTokenPos' => 80,
                'endFilePos' => 1013,
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
            'startLine' => 34,
            'endLine' => 34,
            'startColumn' => 53,
            'endColumn' => 76,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Build the shell command for OCR without executing it.
 *
 * Used by ProcessPool to launch multiple OCR processes in parallel.
 *
 * @param  string $imagePath Absolute path to the image file
 * @param  string $language  Language code (e.g. \'fra\', \'eng\')
 * @return string            Full shell command
 */',
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 86,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Contracts',
        'declaringClassName' => 'Paperdoc\\Contracts\\OcrProcessorInterface',
        'implementingClassName' => 'Paperdoc\\Contracts\\OcrProcessorInterface',
        'currentClassName' => 'Paperdoc\\Contracts\\OcrProcessorInterface',
        'aliasName' => NULL,
      ),
      'detectScript' => 
      array (
        'name' => 'detectScript',
        'parameters' => 
        array (
          'imagePath' => 
          array (
            'name' => 'imagePath',
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
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 34,
            'endColumn' => 50,
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
 * Detect the script and orientation of an image.
 *
 * @return array{script?: string, confidence?: float, orientation?: int}|null
 */',
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 60,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Contracts',
        'declaringClassName' => 'Paperdoc\\Contracts\\OcrProcessorInterface',
        'implementingClassName' => 'Paperdoc\\Contracts\\OcrProcessorInterface',
        'currentClassName' => 'Paperdoc\\Contracts\\OcrProcessorInterface',
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