<?php declare(strict_types = 1);

// odsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/PatternValidator.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Ocr\PostProcessing\PatternValidator
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.8-d906616b09e0b574daffafbfccf7e7f9bf7b84966fbdf0230f3e3bcecfdb6d5e',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/PatternValidator.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
    'name' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
    'shortName' => 'PatternValidator',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Layer 4 — Regex-based entity recognition, validation and normalisation.
 *
 * Detects structured patterns in OCR text (dates, phone numbers, emails,
 * amounts, document identifiers) and normalises them.
 *
 * Also exposes every found entity via $context[\'entities\'].
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 191,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Paperdoc\\Ocr\\PostProcessing\\PostProcessorInterface',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'rules' => 
      array (
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
        'name' => 'rules',
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
        'default' => NULL,
        'docComment' => '/**
 * Each rule: [\'name\' => string, \'pattern\' => regex, \'normalizer\' => ?callable, \'type\' => string]
 *
 * @var list<array{name: string, pattern: string, normalizer: (callable(array<int|string, string>): string)|null, type: string}>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 25,
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
          'customRules' => 
          array (
            'name' => 'customRules',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 30,
                'endLine' => 30,
                'startTokenPos' => 55,
                'startFilePos' => 998,
                'endTokenPos' => 56,
                'endFilePos' => 999,
              ),
            ),
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
            'startLine' => 30,
            'endLine' => 30,
            'startColumn' => 33,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param list<array{name: string, pattern: string, normalizer?: callable(array<int|string, string>): string, type: string}> $customRules
 *        Additional rules merged after the built-in set
 */',
        'startLine' => 30,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
        'aliasName' => NULL,
      ),
      'getName' => 
      array (
        'name' => 'getName',
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
        'startLine' => 44,
        'endLine' => 47,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
        'aliasName' => NULL,
      ),
      'process' => 
      array (
        'name' => 'process',
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
            'startLine' => 52,
            'endLine' => 52,
            'startColumn' => 29,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'context' => 
          array (
            'name' => 'context',
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
            'byRef' => true,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 52,
            'endLine' => 52,
            'startColumn' => 43,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param array<string, mixed> $context
 */',
        'startLine' => 52,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
        'aliasName' => NULL,
      ),
      'builtInRules' => 
      array (
        'name' => 'builtInRules',
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
 * @return list<array{name: string, pattern: string, normalizer: (callable(array<int|string, string>): string)|null, type: string}>
 */',
        'startLine' => 98,
        'endLine' => 190,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PatternValidator',
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