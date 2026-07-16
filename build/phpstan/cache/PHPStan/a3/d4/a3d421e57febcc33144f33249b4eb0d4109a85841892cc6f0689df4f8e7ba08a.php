<?php declare(strict_types = 1);

// odsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/StructureDetector.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Ocr\PostProcessing\StructureDetector
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.8-fa0ee5f3b3e6876426ce2d021e15dcb4895c433b47dc404b309a381e7551eb73',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/StructureDetector.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
    'name' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
    'shortName' => 'StructureDetector',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Layer 5 — Heuristic document structure detection.
 *
 * Analyses OCR text and converts it to light Markdown:
 *   - ALL-CAPS short lines → headings
 *   - Numbered/bulleted patterns → lists
 *   - Aligned columns → tables
 *   - Signature/footer patterns → separated
 *
 * Also populates $context[\'structure\'] with detected blocks.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 20,
    'endLine' => 279,
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
      'maxHeadingLength' => 
      array (
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'name' => 'maxHeadingLength',
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
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'emitMarkdown' => 
      array (
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'name' => 'emitMarkdown',
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
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 31,
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
          'maxHeadingLength' => 
          array (
            'name' => 'maxHeadingLength',
            'default' => 
            array (
              'code' => '60',
              'attributes' => 
              array (
                'startLine' => 26,
                'endLine' => 26,
                'startTokenPos' => 58,
                'startFilePos' => 639,
                'endTokenPos' => 58,
                'endFilePos' => 640,
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
            'startLine' => 26,
            'endLine' => 26,
            'startColumn' => 33,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'emitMarkdown' => 
          array (
            'name' => 'emitMarkdown',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 26,
                'endLine' => 26,
                'startTokenPos' => 67,
                'startFilePos' => 664,
                'endTokenPos' => 67,
                'endFilePos' => 667,
              ),
            ),
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
            'startLine' => 26,
            'endLine' => 26,
            'startColumn' => 61,
            'endColumn' => 85,
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
        'startLine' => 26,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
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
        'startLine' => 32,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
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
            'startLine' => 40,
            'endLine' => 40,
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
            'startLine' => 40,
            'endLine' => 40,
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
        'startLine' => 40,
        'endLine' => 52,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'aliasName' => NULL,
      ),
      'detectBlocks' => 
      array (
        'name' => 'detectBlocks',
        'parameters' => 
        array (
          'lines' => 
          array (
            'name' => 'lines',
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
            'startLine' => 62,
            'endLine' => 62,
            'startColumn' => 35,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  list<string> $lines
 * @return list<array{type: string, lines: list<string>, level?: int}>
 */',
        'startLine' => 62,
        'endLine' => 132,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'aliasName' => NULL,
      ),
      'detectHeading' => 
      array (
        'name' => 'detectHeading',
        'parameters' => 
        array (
          'line' => 
          array (
            'name' => 'line',
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
            'startLine' => 141,
            'endLine' => 141,
            'startColumn' => 36,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Detect if a line is a heading. Returns heading level (1-3) or 0.
 */',
        'startLine' => 141,
        'endLine' => 178,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'aliasName' => NULL,
      ),
      'detectListItem' => 
      array (
        'name' => 'detectListItem',
        'parameters' => 
        array (
          'line' => 
          array (
            'name' => 'line',
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
            'startLine' => 183,
            'endLine' => 183,
            'startColumn' => 37,
            'endColumn' => 48,
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
        'docComment' => '/**
 * Detect a list item. Returns the cleaned item text or null.
 */',
        'startLine' => 183,
        'endLine' => 196,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'aliasName' => NULL,
      ),
      'isSeparator' => 
      array (
        'name' => 'isSeparator',
        'parameters' => 
        array (
          'line' => 
          array (
            'name' => 'line',
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
            'startLine' => 201,
            'endLine' => 201,
            'startColumn' => 34,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Lines that look like separators / horizontal rules.
 */',
        'startLine' => 201,
        'endLine' => 211,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'aliasName' => NULL,
      ),
      'renderMarkdown' => 
      array (
        'name' => 'renderMarkdown',
        'parameters' => 
        array (
          'blocks' => 
          array (
            'name' => 'blocks',
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
            'startLine' => 220,
            'endLine' => 220,
            'startColumn' => 37,
            'endColumn' => 49,
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
 * @param list<array{type: string, lines: list<string>, level?: int}> $blocks
 */',
        'startLine' => 220,
        'endLine' => 252,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'aliasName' => NULL,
      ),
      'toTitleCase' => 
      array (
        'name' => 'toTitleCase',
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
            'startLine' => 257,
            'endLine' => 257,
            'startColumn' => 34,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Convert ALL-CAPS text to Title Case, preserving short words.
 */',
        'startLine' => 257,
        'endLine' => 278,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\StructureDetector',
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