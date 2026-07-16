<?php declare(strict_types = 1);

// osfsl-/home/akramzerarka/Projects/paperdoc/vendor/composer/../laravel/framework/src/Illuminate/Console/Concerns/InteractsWithIO.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Console\Concerns\InteractsWithIO
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-df2cda3a62b98915ab72eb4361b57cbd878f9d77c49976a7327adfc6bcdd7606-8.5.8-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'filename' => '/home/akramzerarka/Projects/paperdoc/vendor/composer/../laravel/framework/src/Illuminate/Console/Concerns/InteractsWithIO.php',
      ),
    ),
    'namespace' => 'Illuminate\\Console\\Concerns',
    'name' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
    'shortName' => 'InteractsWithIO',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 16,
    'endLine' => 467,
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
      'components' => 
      array (
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'name' => 'components',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The console components factory.
 *
 * @var \\Illuminate\\Console\\View\\Components\\Factory
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 26,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'input' => 
      array (
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'name' => 'input',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The input interface implementation.
 *
 * @var \\Symfony\\Component\\Console\\Input\\InputInterface
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'output' => 
      array (
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'name' => 'output',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The output interface implementation.
 *
 * @var \\Illuminate\\Console\\OutputStyle
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 22,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'verbosity' => 
      array (
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'name' => 'verbosity',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_NORMAL',
          'attributes' => 
          array (
            'startLine' => 44,
            'endLine' => 44,
            'startTokenPos' => 92,
            'startFilePos' => 1113,
            'endTokenPos' => 94,
            'endFilePos' => 1145,
          ),
        ),
        'docComment' => '/**
 * The default verbosity of output commands.
 *
 * @var \\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_*
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 44,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 61,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'verbosityMap' => 
      array (
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'name' => 'verbosityMap',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'v\' => \\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_VERBOSE, \'vv\' => \\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_VERY_VERBOSE, \'vvv\' => \\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_DEBUG, \'quiet\' => \\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_QUIET, \'normal\' => \\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_NORMAL]',
          'attributes' => 
          array (
            'startLine' => 51,
            'endLine' => 57,
            'startTokenPos' => 105,
            'startFilePos' => 1382,
            'endTokenPos' => 152,
            'endFilePos' => 1655,
          ),
        ),
        'docComment' => '/**
 * The mapping between human-readable verbosity levels and Symfony\'s OutputInterface.
 *
 * @var array<string, \\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_*>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 51,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 6,
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
      'hasArgument' => 
      array (
        'name' => 'hasArgument',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 65,
            'endLine' => 65,
            'startColumn' => 33,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the given argument is present.
 *
 * @param  string|int  $name
 * @return bool
 */',
        'startLine' => 65,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'argument' => 
      array (
        'name' => 'argument',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 76,
                'endLine' => 76,
                'startTokenPos' => 194,
                'startFilePos' => 2130,
                'endTokenPos' => 194,
                'endFilePos' => 2133,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 76,
            'endLine' => 76,
            'startColumn' => 30,
            'endColumn' => 40,
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
 * Get the value of a command argument.
 *
 * @param  string|null  $key
 * @return ($key is null ? array<array|string|float|int|bool|null> : array|string|float|int|bool|null)
 */',
        'startLine' => 76,
        'endLine' => 83,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'arguments' => 
      array (
        'name' => 'arguments',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get all of the arguments passed to the command.
 *
 * @return array<array|string|float|int|bool|null>
 */',
        'startLine' => 90,
        'endLine' => 93,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'hasOption' => 
      array (
        'name' => 'hasOption',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 101,
            'endLine' => 101,
            'startColumn' => 31,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine whether the option is defined in the command signature.
 *
 * @param  string  $name
 * @return bool
 */',
        'startLine' => 101,
        'endLine' => 104,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'option' => 
      array (
        'name' => 'option',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 112,
                'endLine' => 112,
                'startTokenPos' => 299,
                'startFilePos' => 2979,
                'endTokenPos' => 299,
                'endFilePos' => 2982,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 112,
            'endLine' => 112,
            'startColumn' => 28,
            'endColumn' => 38,
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
 * Get the value of a command option.
 *
 * @param  string|null  $key
 * @return ($key is null ? array<array|string|float|int|bool|null> : array|string|float|int|bool|null)
 */',
        'startLine' => 112,
        'endLine' => 119,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'options' => 
      array (
        'name' => 'options',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get all of the options passed to the command.
 *
 * @return array<array|string|float|int|bool|null>
 */',
        'startLine' => 126,
        'endLine' => 129,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'confirm' => 
      array (
        'name' => 'confirm',
        'parameters' => 
        array (
          'question' => 
          array (
            'name' => 'question',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 138,
            'endLine' => 138,
            'startColumn' => 29,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'default' => 
          array (
            'name' => 'default',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 138,
                'endLine' => 138,
                'startTokenPos' => 380,
                'startFilePos' => 3534,
                'endTokenPos' => 380,
                'endFilePos' => 3538,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 138,
            'endLine' => 138,
            'startColumn' => 40,
            'endColumn' => 55,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Confirm a question with the user.
 *
 * @param  string  $question
 * @param  bool  $default
 * @return bool
 */',
        'startLine' => 138,
        'endLine' => 141,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'ask' => 
      array (
        'name' => 'ask',
        'parameters' => 
        array (
          'question' => 
          array (
            'name' => 'question',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 150,
            'endLine' => 150,
            'startColumn' => 25,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'default' => 
          array (
            'name' => 'default',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 150,
                'endLine' => 150,
                'startTokenPos' => 417,
                'startFilePos' => 3808,
                'endTokenPos' => 417,
                'endFilePos' => 3811,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 150,
            'endLine' => 150,
            'startColumn' => 36,
            'endColumn' => 50,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prompt the user for input.
 *
 * @param  string  $question
 * @param  string|null  $default
 * @return mixed
 */',
        'startLine' => 150,
        'endLine' => 153,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'anticipate' => 
      array (
        'name' => 'anticipate',
        'parameters' => 
        array (
          'question' => 
          array (
            'name' => 'question',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 163,
            'endLine' => 163,
            'startColumn' => 32,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'choices' => 
          array (
            'name' => 'choices',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 163,
            'endLine' => 163,
            'startColumn' => 43,
            'endColumn' => 50,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'default' => 
          array (
            'name' => 'default',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 163,
                'endLine' => 163,
                'startTokenPos' => 457,
                'startFilePos' => 4155,
                'endTokenPos' => 457,
                'endFilePos' => 4158,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 163,
            'endLine' => 163,
            'startColumn' => 53,
            'endColumn' => 67,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prompt the user for input with auto completion.
 *
 * @param  string  $question
 * @param  array|callable  $choices
 * @param  string|null  $default
 * @return mixed
 */',
        'startLine' => 163,
        'endLine' => 166,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'askWithCompletion' => 
      array (
        'name' => 'askWithCompletion',
        'parameters' => 
        array (
          'question' => 
          array (
            'name' => 'question',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 176,
            'endLine' => 176,
            'startColumn' => 39,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'choices' => 
          array (
            'name' => 'choices',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 176,
            'endLine' => 176,
            'startColumn' => 50,
            'endColumn' => 57,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'default' => 
          array (
            'name' => 'default',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 176,
                'endLine' => 176,
                'startTokenPos' => 498,
                'startFilePos' => 4548,
                'endTokenPos' => 498,
                'endFilePos' => 4551,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 176,
            'endLine' => 176,
            'startColumn' => 60,
            'endColumn' => 74,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prompt the user for input with auto completion.
 *
 * @param  string  $question
 * @param  iterable|(callable(string): string[])  $choices
 * @param  string|null  $default
 * @return mixed
 */',
        'startLine' => 176,
        'endLine' => 185,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'secret' => 
      array (
        'name' => 'secret',
        'parameters' => 
        array (
          'question' => 
          array (
            'name' => 'question',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 194,
            'endLine' => 194,
            'startColumn' => 28,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'fallback' => 
          array (
            'name' => 'fallback',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 194,
                'endLine' => 194,
                'startTokenPos' => 571,
                'startFilePos' => 5056,
                'endTokenPos' => 571,
                'endFilePos' => 5059,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 194,
            'endLine' => 194,
            'startColumn' => 39,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prompt the user for input but hide the answer from the console.
 *
 * @param  string  $question
 * @param  bool  $fallback
 * @return mixed
 */',
        'startLine' => 194,
        'endLine' => 201,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'choice' => 
      array (
        'name' => 'choice',
        'parameters' => 
        array (
          'question' => 
          array (
            'name' => 'question',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 213,
            'endLine' => 213,
            'startColumn' => 28,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'choices' => 
          array (
            'name' => 'choices',
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
            'startLine' => 213,
            'endLine' => 213,
            'startColumn' => 39,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'default' => 
          array (
            'name' => 'default',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 213,
                'endLine' => 213,
                'startTokenPos' => 635,
                'startFilePos' => 5632,
                'endTokenPos' => 635,
                'endFilePos' => 5635,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 213,
            'endLine' => 213,
            'startColumn' => 55,
            'endColumn' => 69,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'attempts' => 
          array (
            'name' => 'attempts',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 213,
                'endLine' => 213,
                'startTokenPos' => 642,
                'startFilePos' => 5650,
                'endTokenPos' => 642,
                'endFilePos' => 5653,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 213,
            'endLine' => 213,
            'startColumn' => 72,
            'endColumn' => 87,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'multiple' => 
          array (
            'name' => 'multiple',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 213,
                'endLine' => 213,
                'startTokenPos' => 649,
                'startFilePos' => 5668,
                'endTokenPos' => 649,
                'endFilePos' => 5672,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 213,
            'endLine' => 213,
            'startColumn' => 90,
            'endColumn' => 106,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Give the user a single choice from an array of answers.
 *
 * @param  string  $question
 * @param  array<\\Stringable|string|float|int|bool>  $choices
 * @param  string|int|null  $default
 * @param  ?positive-int  $attempts
 * @param  bool  $multiple
 * @return string|array
 */',
        'startLine' => 213,
        'endLine' => 220,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'table' => 
      array (
        'name' => 'table',
        'parameters' => 
        array (
          'headers' => 
          array (
            'name' => 'headers',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 231,
            'endLine' => 231,
            'startColumn' => 27,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'rows' => 
          array (
            'name' => 'rows',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 231,
            'endLine' => 231,
            'startColumn' => 37,
            'endColumn' => 41,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'tableStyle' => 
          array (
            'name' => 'tableStyle',
            'default' => 
            array (
              'code' => '\'default\'',
              'attributes' => 
              array (
                'startLine' => 231,
                'endLine' => 231,
                'startTokenPos' => 717,
                'startFilePos' => 6299,
                'endTokenPos' => 717,
                'endFilePos' => 6307,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 231,
            'endLine' => 231,
            'startColumn' => 44,
            'endColumn' => 66,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'columnStyles' => 
          array (
            'name' => 'columnStyles',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 231,
                'endLine' => 231,
                'startTokenPos' => 726,
                'startFilePos' => 6332,
                'endTokenPos' => 727,
                'endFilePos' => 6333,
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
            'startLine' => 231,
            'endLine' => 231,
            'startColumn' => 69,
            'endColumn' => 92,
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
 * Format input to textual table.
 *
 * @param  array  $headers
 * @param  \\Illuminate\\Contracts\\Support\\Arrayable|array  $rows
 * @param  \\Symfony\\Component\\Console\\Helper\\TableStyle|string  $tableStyle
 * @param  array<int, \\Symfony\\Component\\Console\\Helper\\TableStyle|string>  $columnStyles
 * @return void
 */',
        'startLine' => 231,
        'endLine' => 246,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'withProgressBar' => 
      array (
        'name' => 'withProgressBar',
        'parameters' => 
        array (
          'totalSteps' => 
          array (
            'name' => 'totalSteps',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 259,
            'endLine' => 259,
            'startColumn' => 37,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Closure',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 259,
            'endLine' => 259,
            'startColumn' => 50,
            'endColumn' => 66,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Execute a given callback while advancing a progress bar.
 *
 * @template TKey of array-key
 * @template TValue
 * @template TIterable of iterable<TKey, TValue>
 *
 * @param  TIterable|int  $totalSteps
 * @param  \\Closure(\\Symfony\\Component\\Console\\Helper\\ProgressBar): mixed|\\Closure(TValue, \\Symfony\\Component\\Console\\Helper\\ProgressBar, TKey): mixed  $callback
 * @return ($totalSteps is iterable ? TIterable : void)
 */',
        'startLine' => 259,
        'endLine' => 282,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'info' => 
      array (
        'name' => 'info',
        'parameters' => 
        array (
          'string' => 
          array (
            'name' => 'string',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 291,
            'endLine' => 291,
            'startColumn' => 26,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'verbosity' => 
          array (
            'name' => 'verbosity',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 291,
                'endLine' => 291,
                'startTokenPos' => 988,
                'startFilePos' => 8095,
                'endTokenPos' => 988,
                'endFilePos' => 8098,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 291,
            'endLine' => 291,
            'startColumn' => 35,
            'endColumn' => 51,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Write a string as information output.
 *
 * @param  string  $string
 * @param  \'v\'|\'vv\'|\'vvv\'|\'quiet\'|\'normal\'|\\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_*|null  $verbosity
 * @return void
 */',
        'startLine' => 291,
        'endLine' => 294,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'line' => 
      array (
        'name' => 'line',
        'parameters' => 
        array (
          'string' => 
          array (
            'name' => 'string',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 304,
            'endLine' => 304,
            'startColumn' => 26,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'style' => 
          array (
            'name' => 'style',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 304,
                'endLine' => 304,
                'startTokenPos' => 1024,
                'startFilePos' => 8529,
                'endTokenPos' => 1024,
                'endFilePos' => 8532,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 304,
            'endLine' => 304,
            'startColumn' => 35,
            'endColumn' => 47,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'verbosity' => 
          array (
            'name' => 'verbosity',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 304,
                'endLine' => 304,
                'startTokenPos' => 1031,
                'startFilePos' => 8548,
                'endTokenPos' => 1031,
                'endFilePos' => 8551,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 304,
            'endLine' => 304,
            'startColumn' => 50,
            'endColumn' => 66,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Write a string as standard output.
 *
 * @param  string  $string
 * @param  \'info\'|\'comment\'|\'question\'|\'error\'|\'warn\'|\'alert\'|null  $style
 * @param  \'v\'|\'vv\'|\'vvv\'|\'quiet\'|\'normal\'|\\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_*|null  $verbosity
 * @return void
 */',
        'startLine' => 304,
        'endLine' => 309,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'comment' => 
      array (
        'name' => 'comment',
        'parameters' => 
        array (
          'string' => 
          array (
            'name' => 'string',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 318,
            'endLine' => 318,
            'startColumn' => 29,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'verbosity' => 
          array (
            'name' => 'verbosity',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 318,
                'endLine' => 318,
                'startTokenPos' => 1094,
                'startFilePos' => 9001,
                'endTokenPos' => 1094,
                'endFilePos' => 9004,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 318,
            'endLine' => 318,
            'startColumn' => 38,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Write a string as comment output.
 *
 * @param  string  $string
 * @param  \'v\'|\'vv\'|\'vvv\'|\'quiet\'|\'normal\'|\\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_*|null  $verbosity
 * @return void
 */',
        'startLine' => 318,
        'endLine' => 321,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'question' => 
      array (
        'name' => 'question',
        'parameters' => 
        array (
          'string' => 
          array (
            'name' => 'string',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 330,
            'endLine' => 330,
            'startColumn' => 30,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'verbosity' => 
          array (
            'name' => 'verbosity',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 330,
                'endLine' => 330,
                'startTokenPos' => 1130,
                'startFilePos' => 9367,
                'endTokenPos' => 1130,
                'endFilePos' => 9370,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 330,
            'endLine' => 330,
            'startColumn' => 39,
            'endColumn' => 55,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Write a string as question output.
 *
 * @param  string  $string
 * @param  \'v\'|\'vv\'|\'vvv\'|\'quiet\'|\'normal\'|\\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_*|null  $verbosity
 * @return void
 */',
        'startLine' => 330,
        'endLine' => 333,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'error' => 
      array (
        'name' => 'error',
        'parameters' => 
        array (
          'string' => 
          array (
            'name' => 'string',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 342,
            'endLine' => 342,
            'startColumn' => 27,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'verbosity' => 
          array (
            'name' => 'verbosity',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 342,
                'endLine' => 342,
                'startTokenPos' => 1166,
                'startFilePos' => 9728,
                'endTokenPos' => 1166,
                'endFilePos' => 9731,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 342,
            'endLine' => 342,
            'startColumn' => 36,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Write a string as error output.
 *
 * @param  string  $string
 * @param  \'v\'|\'vv\'|\'vvv\'|\'quiet\'|\'normal\'|\\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_*|null  $verbosity
 * @return void
 */',
        'startLine' => 342,
        'endLine' => 345,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'warn' => 
      array (
        'name' => 'warn',
        'parameters' => 
        array (
          'string' => 
          array (
            'name' => 'string',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 354,
            'endLine' => 354,
            'startColumn' => 26,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'verbosity' => 
          array (
            'name' => 'verbosity',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 354,
                'endLine' => 354,
                'startTokenPos' => 1202,
                'startFilePos' => 10087,
                'endTokenPos' => 1202,
                'endFilePos' => 10090,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 354,
            'endLine' => 354,
            'startColumn' => 35,
            'endColumn' => 51,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Write a string as warning output.
 *
 * @param  string  $string
 * @param  \'v\'|\'vv\'|\'vvv\'|\'quiet\'|\'normal\'|\\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_*|null  $verbosity
 * @return void
 */',
        'startLine' => 354,
        'endLine' => 363,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'alert' => 
      array (
        'name' => 'alert',
        'parameters' => 
        array (
          'string' => 
          array (
            'name' => 'string',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 372,
            'endLine' => 372,
            'startColumn' => 27,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'verbosity' => 
          array (
            'name' => 'verbosity',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 372,
                'endLine' => 372,
                'startTokenPos' => 1290,
                'startFilePos' => 10656,
                'endTokenPos' => 1290,
                'endFilePos' => 10659,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 372,
            'endLine' => 372,
            'startColumn' => 36,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Write a string in an alert box.
 *
 * @param  string  $string
 * @param  \'v\'|\'vv\'|\'vvv\'|\'quiet\'|\'normal\'|\\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_*|null  $verbosity
 * @return void
 */',
        'startLine' => 372,
        'endLine' => 381,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'newLine' => 
      array (
        'name' => 'newLine',
        'parameters' => 
        array (
          'count' => 
          array (
            'name' => 'count',
            'default' => 
            array (
              'code' => '1',
              'attributes' => 
              array (
                'startLine' => 389,
                'endLine' => 389,
                'startTokenPos' => 1388,
                'startFilePos' => 11096,
                'endTokenPos' => 1388,
                'endFilePos' => 11096,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 389,
            'endLine' => 389,
            'startColumn' => 29,
            'endColumn' => 38,
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
 * Write a blank line.
 *
 * @param  int  $count
 * @return $this
 */',
        'startLine' => 389,
        'endLine' => 394,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'setInput' => 
      array (
        'name' => 'setInput',
        'parameters' => 
        array (
          'input' => 
          array (
            'name' => 'input',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Symfony\\Component\\Console\\Input\\InputInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 402,
            'endLine' => 402,
            'startColumn' => 30,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the input interface implementation.
 *
 * @param  \\Symfony\\Component\\Console\\Input\\InputInterface  $input
 * @return void
 */',
        'startLine' => 402,
        'endLine' => 405,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'setOutput' => 
      array (
        'name' => 'setOutput',
        'parameters' => 
        array (
          'output' => 
          array (
            'name' => 'output',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Console\\OutputStyle',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 413,
            'endLine' => 413,
            'startColumn' => 31,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the output interface implementation.
 *
 * @param  \\Illuminate\\Console\\OutputStyle  $output
 * @return void
 */',
        'startLine' => 413,
        'endLine' => 416,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'setVerbosity' => 
      array (
        'name' => 'setVerbosity',
        'parameters' => 
        array (
          'level' => 
          array (
            'name' => 'level',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 424,
            'endLine' => 424,
            'startColumn' => 37,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the verbosity level.
 *
 * @param  \'v\'|\'vv\'|\'vvv\'|\'quiet\'|\'normal\'|\\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_*  $level
 * @return void
 */',
        'startLine' => 424,
        'endLine' => 427,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'parseVerbosity' => 
      array (
        'name' => 'parseVerbosity',
        'parameters' => 
        array (
          'level' => 
          array (
            'name' => 'level',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 435,
                'endLine' => 435,
                'startTokenPos' => 1503,
                'startFilePos' => 12272,
                'endTokenPos' => 1503,
                'endFilePos' => 12275,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 435,
            'endLine' => 435,
            'startColumn' => 39,
            'endColumn' => 51,
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
 * Get the verbosity level in terms of Symfony\'s OutputInterface level.
 *
 * @param  \'v\'|\'vv\'|\'vvv\'|\'quiet\'|\'normal\'|\\Symfony\\Component\\Console\\Output\\OutputInterface::VERBOSITY_*|null  $level
 * @return int
 */',
        'startLine' => 435,
        'endLine' => 446,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'getOutput' => 
      array (
        'name' => 'getOutput',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the output implementation.
 *
 * @return \\Illuminate\\Console\\OutputStyle
 */',
        'startLine' => 453,
        'endLine' => 456,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'aliasName' => NULL,
      ),
      'outputComponents' => 
      array (
        'name' => 'outputComponents',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the output component factory implementation.
 *
 * @return \\Illuminate\\Console\\View\\Components\\Factory
 */',
        'startLine' => 463,
        'endLine' => 466,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Concerns',
        'declaringClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'implementingClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
        'currentClassName' => 'Illuminate\\Console\\Concerns\\InteractsWithIO',
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