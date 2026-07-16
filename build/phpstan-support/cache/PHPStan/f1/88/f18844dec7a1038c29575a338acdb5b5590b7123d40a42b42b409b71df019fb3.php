<?php declare(strict_types = 1);

// osfsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/PipelineFactory.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Ocr\PostProcessing\PipelineFactory
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-bceaeec718e6650fd7e093822cea9fa55ac18e3aa36c6a59c8f881a3bf29ca02-8.5.8-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Ocr\\PostProcessing\\PipelineFactory',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Ocr/PostProcessing/PipelineFactory.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
    'name' => 'Paperdoc\\Ocr\\PostProcessing\\PipelineFactory',
    'shortName' => 'PipelineFactory',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Builds a PostProcessingPipeline from configuration.
 *
 * @example
 *   $pipeline = PipelineFactory::fromConfig([
 *       \'char_substitution\' => true,
 *       \'spell_correction\'  => [\'enabled\' => true, \'dictionary\' => \'/path/to/dict.txt\'],
 *       \'ngram\'             => [\'enabled\' => true, \'model_path\' => \'/path/to/model.json\'],
 *       \'patterns\'          => true,
 *       \'structure\'         => true,
 *   ]);
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 19,
    'endLine' => 113,
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
      'fromConfig' => 
      array (
        'name' => 'fromConfig',
        'parameters' => 
        array (
          'config' => 
          array (
            'name' => 'config',
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
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 39,
            'endColumn' => 51,
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
                  'name' => 'Paperdoc\\Ocr\\PostProcessing\\PostProcessingPipeline',
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
        'docComment' => NULL,
        'startLine' => 21,
        'endLine' => 94,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PipelineFactory',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PipelineFactory',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PipelineFactory',
        'aliasName' => NULL,
      ),
      'isDisabled' => 
      array (
        'name' => 'isDisabled',
        'parameters' => 
        array (
          'config' => 
          array (
            'name' => 'config',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'mixed',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 101,
            'endLine' => 101,
            'startColumn' => 40,
            'endColumn' => 52,
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
 * Check if a config section is disabled.
 *
 * Handles: false, [\'enabled\' => false], 0, null
 */',
        'startLine' => 101,
        'endLine' => 112,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'Paperdoc\\Ocr\\PostProcessing',
        'declaringClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PipelineFactory',
        'implementingClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PipelineFactory',
        'currentClassName' => 'Paperdoc\\Ocr\\PostProcessing\\PipelineFactory',
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