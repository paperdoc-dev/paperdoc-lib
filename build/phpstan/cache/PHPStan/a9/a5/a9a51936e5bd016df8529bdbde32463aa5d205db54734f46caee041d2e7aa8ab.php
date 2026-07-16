<?php declare(strict_types = 1);

// odsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/SpellCorrector.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Ocr\PostProcessing\SpellCorrector
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.8-2f22fe7dd64eb70f8b3f9fd148bb228e80e75e5f9ff41b597b0b8433666778c4',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/SpellCorrector.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
    'name' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
    'shortName' => 'SpellCorrector',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Layer 2 — Dictionary-based spell correction using Levenshtein distance.
 *
 * Loads one or more dictionaries (plain text, one word per line) and
 * corrects OCR words that are within a configurable edit distance.
 *
 * The dictionary is language-specific and must be supplied by the caller.
 * Generate one from your own corpus or download a word-frequency list.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 18,
    'endLine' => 319,
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
      'dictionary' => 
      array (
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'name' => 'dictionary',
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
            'startTokenPos' => 42,
            'startFilePos' => 636,
            'endTokenPos' => 43,
            'endFilePos' => 637,
          ),
        ),
        'docComment' => '/** @var array<string, int> word → frequency (lower = rarer) */',
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 35,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'maxDistance' => 
      array (
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'name' => 'maxDistance',
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
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 29,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'minWordLength' => 
      array (
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'name' => 'minWordLength',
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
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'ignore' => 
      array (
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'name' => 'ignore',
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
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 70,
            'startFilePos' => 806,
            'endTokenPos' => 71,
            'endFilePos' => 807,
          ),
        ),
        'docComment' => '/** @var array<string, true> Words that must never be "corrected" */',
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'minFrequency' => 
      array (
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'name' => 'minFrequency',
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
        'docComment' => '/** Minimum frequency a candidate must have to be accepted */',
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 30,
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
          'dictionary' => 
          array (
            'name' => 'dictionary',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 40,
                'endLine' => 40,
                'startTokenPos' => 102,
                'startFilePos' => 1357,
                'endTokenPos' => 102,
                'endFilePos' => 1360,
              ),
            ),
            'type' => 
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
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  2 => 
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 40,
            'endLine' => 40,
            'startColumn' => 9,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'maxDistance' => 
          array (
            'name' => 'maxDistance',
            'default' => 
            array (
              'code' => '1',
              'attributes' => 
              array (
                'startLine' => 41,
                'endLine' => 41,
                'startTokenPos' => 111,
                'startFilePos' => 1390,
                'endTokenPos' => 111,
                'endFilePos' => 1390,
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
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 9,
            'endColumn' => 28,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'minWordLength' => 
          array (
            'name' => 'minWordLength',
            'default' => 
            array (
              'code' => '5',
              'attributes' => 
              array (
                'startLine' => 42,
                'endLine' => 42,
                'startTokenPos' => 120,
                'startFilePos' => 1422,
                'endTokenPos' => 120,
                'endFilePos' => 1422,
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
            'startLine' => 42,
            'endLine' => 42,
            'startColumn' => 9,
            'endColumn' => 30,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'minFrequency' => 
          array (
            'name' => 'minFrequency',
            'default' => 
            array (
              'code' => '100',
              'attributes' => 
              array (
                'startLine' => 43,
                'endLine' => 43,
                'startTokenPos' => 129,
                'startFilePos' => 1453,
                'endTokenPos' => 129,
                'endFilePos' => 1455,
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
            'startLine' => 43,
            'endLine' => 43,
            'startColumn' => 9,
            'endColumn' => 31,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param array<string, int>|string|null $dictionary     Associative array or path
 * @param int                            $maxDistance     Maximum Levenshtein distance
 * @param int                            $minWordLength   Minimum word length to check
 * @param int                            $minFrequency    Minimum candidate frequency
 */',
        'startLine' => 39,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
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
        'startLine' => 56,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'aliasName' => NULL,
      ),
      'loadFile' => 
      array (
        'name' => 'loadFile',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
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
            'startLine' => 70,
            'endLine' => 70,
            'startColumn' => 30,
            'endColumn' => 41,
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
        'docComment' => '/**
 * Load a dictionary from a text file (one word per line, optional frequency after tab).
 *
 * Format: "word\\tfrequency" or just "word" (frequency defaults to 1).
 */',
        'startLine' => 70,
        'endLine' => 95,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'aliasName' => NULL,
      ),
      'trainFromText' => 
      array (
        'name' => 'trainFromText',
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
            'startLine' => 100,
            'endLine' => 100,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Build a dictionary from a corpus text.
 */',
        'startLine' => 100,
        'endLine' => 110,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'aliasName' => NULL,
      ),
      'saveDictionary' => 
      array (
        'name' => 'saveDictionary',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
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
            'startLine' => 115,
            'endLine' => 115,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Save the current dictionary to a file.
 */',
        'startLine' => 115,
        'endLine' => 123,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'aliasName' => NULL,
      ),
      'addWord' => 
      array (
        'name' => 'addWord',
        'parameters' => 
        array (
          'word' => 
          array (
            'name' => 'word',
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
            'startLine' => 125,
            'endLine' => 125,
            'startColumn' => 29,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'frequency' => 
          array (
            'name' => 'frequency',
            'default' => 
            array (
              'code' => '1',
              'attributes' => 
              array (
                'startLine' => 125,
                'endLine' => 125,
                'startTokenPos' => 671,
                'startFilePos' => 4042,
                'endTokenPos' => 671,
                'endFilePos' => 4042,
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
            'startLine' => 125,
            'endLine' => 125,
            'startColumn' => 43,
            'endColumn' => 60,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 125,
        'endLine' => 128,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'aliasName' => NULL,
      ),
      'addIgnoreList' => 
      array (
        'name' => 'addIgnoreList',
        'parameters' => 
        array (
          'words' => 
          array (
            'name' => 'words',
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @param list<string> $words */',
        'startLine' => 131,
        'endLine' => 136,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'aliasName' => NULL,
      ),
      'getDictionarySize' => 
      array (
        'name' => 'getDictionarySize',
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
        'startLine' => 138,
        'endLine' => 141,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'aliasName' => NULL,
      ),
      'getDictionary' => 
      array (
        'name' => 'getDictionary',
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
        'docComment' => '/** @return array<string, int> */',
        'startLine' => 144,
        'endLine' => 147,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'aliasName' => NULL,
      ),
      'filterByFrequency' => 
      array (
        'name' => 'filterByFrequency',
        'parameters' => 
        array (
          'minFreq' => 
          array (
            'name' => 'minFreq',
            'default' => 
            array (
              'code' => '2',
              'attributes' => 
              array (
                'startLine' => 152,
                'endLine' => 152,
                'startTokenPos' => 808,
                'startFilePos' => 4685,
                'endTokenPos' => 808,
                'endFilePos' => 4685,
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
            'startLine' => 152,
            'endLine' => 152,
            'startColumn' => 39,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'minLength' => 
          array (
            'name' => 'minLength',
            'default' => 
            array (
              'code' => '2',
              'attributes' => 
              array (
                'startLine' => 152,
                'endLine' => 152,
                'startTokenPos' => 817,
                'startFilePos' => 4705,
                'endTokenPos' => 817,
                'endFilePos' => 4705,
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
            'startLine' => 152,
            'endLine' => 152,
            'startColumn' => 57,
            'endColumn' => 74,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Remove entries below minimum frequency or word length.
 */',
        'startLine' => 152,
        'endLine' => 159,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
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
            'startLine' => 168,
            'endLine' => 168,
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
            'startLine' => 168,
            'endLine' => 168,
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
        'startLine' => 168,
        'endLine' => 184,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'aliasName' => NULL,
      ),
      'processLine' => 
      array (
        'name' => 'processLine',
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
            'startLine' => 189,
            'endLine' => 189,
            'startColumn' => 34,
            'endColumn' => 45,
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
            'startLine' => 189,
            'endLine' => 189,
            'startColumn' => 48,
            'endColumn' => 62,
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
        'startLine' => 189,
        'endLine' => 233,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'aliasName' => NULL,
      ),
      'looksLikeProperName' => 
      array (
        'name' => 'looksLikeProperName',
        'parameters' => 
        array (
          'word' => 
          array (
            'name' => 'word',
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
            'startLine' => 239,
            'endLine' => 239,
            'startColumn' => 42,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'tokens' => 
          array (
            'name' => 'tokens',
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
            'startLine' => 239,
            'endLine' => 239,
            'startColumn' => 56,
            'endColumn' => 68,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'wordIndices' => 
          array (
            'name' => 'wordIndices',
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
            'startLine' => 239,
            'endLine' => 239,
            'startColumn' => 71,
            'endColumn' => 88,
            'parameterIndex' => 2,
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
            'startLine' => 239,
            'endLine' => 239,
            'startColumn' => 91,
            'endColumn' => 98,
            'parameterIndex' => 3,
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
 * @param array<int, string> $tokens
 * @param list<int>          $wordIndices
 */',
        'startLine' => 239,
        'endLine' => 257,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'aliasName' => NULL,
      ),
      'findClosest' => 
      array (
        'name' => 'findClosest',
        'parameters' => 
        array (
          'word' => 
          array (
            'name' => 'word',
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
            'startLine' => 269,
            'endLine' => 269,
            'startColumn' => 33,
            'endColumn' => 44,
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
 * Find the closest dictionary word within max edit distance.
 *
 * Only returns a candidate if it has a high enough frequency
 * to avoid replacing valid domain words with common but wrong ones.
 */',
        'startLine' => 269,
        'endLine' => 302,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'aliasName' => NULL,
      ),
      'matchCase' => 
      array (
        'name' => 'matchCase',
        'parameters' => 
        array (
          'original' => 
          array (
            'name' => 'original',
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
            'startLine' => 307,
            'endLine' => 307,
            'startColumn' => 39,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'suggestion' => 
          array (
            'name' => 'suggestion',
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
            'startLine' => 307,
            'endLine' => 307,
            'startColumn' => 57,
            'endColumn' => 74,
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
 * Reproduce the original casing on the suggestion.
 */',
        'startLine' => 307,
        'endLine' => 318,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\SpellCorrector',
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