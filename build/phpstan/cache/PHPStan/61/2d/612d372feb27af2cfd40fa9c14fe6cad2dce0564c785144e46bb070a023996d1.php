<?php declare(strict_types = 1);

// odsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Support/Ole2/Ole2Reader.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Support\Ole2\Ole2Reader
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.8-88eac9e63f0987a8b1724574cb7b705989aad0c48794834a77c55673162f4066',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Support/Ole2/Ole2Reader.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Support\\Ole2',
    'name' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
    'shortName' => 'Ole2Reader',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Lecteur natif du format OLE2 Compound Binary File (Microsoft Structured Storage).
 *
 * Implémente la lecture des fichiers composites binaires (.doc, .xls, .ppt)
 * conformément à la spécification [MS-CFB].
 *
 * Structure :
 *  - Header (512 octets) : magic, taille secteurs, FAT, directory
 *  - FAT : chaîne de secteurs pour chaque flux
 *  - Mini-FAT : chaîne pour les petits flux (< 4096 octets)
 *  - Directory : entrées nommées (Root, WordDocument, 1Table, etc.)
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 19,
    'endLine' => 390,
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
      'MAGIC' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'name' => 'MAGIC',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '"\\xd0\\xcf\\x11ࡱ\\x1a\\xe1"',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 31,
            'startFilePos' => 601,
            'endTokenPos' => 31,
            'endFilePos' => 634,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 61,
      ),
      'ENDOFCHAIN' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'name' => 'ENDOFCHAIN',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0xfffffffe',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 42,
            'startFilePos' => 668,
            'endTokenPos' => 42,
            'endFilePos' => 677,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
    ),
    'immediateProperties' => 
    array (
      'data' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'name' => 'data',
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
        'default' => NULL,
        'docComment' => NULL,
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
      'sectorSize' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'name' => 'sectorSize',
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
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'miniSectorSize' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'name' => 'miniSectorSize',
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
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'miniStreamCutoff' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'name' => 'miniStreamCutoff',
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
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fat' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'name' => 'fat',
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
            'startLine' => 31,
            'endLine' => 31,
            'startTokenPos' => 83,
            'startFilePos' => 853,
            'endTokenPos' => 84,
            'endFilePos' => 854,
          ),
        ),
        'docComment' => '/** @var int[] */',
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'miniFat' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'name' => 'miniFat',
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
            'startLine' => 34,
            'endLine' => 34,
            'startTokenPos' => 97,
            'startFilePos' => 909,
            'endTokenPos' => 98,
            'endFilePos' => 910,
          ),
        ),
        'docComment' => '/** @var int[] */',
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'directory' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'name' => 'directory',
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
            'startLine' => 37,
            'endLine' => 37,
            'startTokenPos' => 111,
            'startFilePos' => 976,
            'endTokenPos' => 112,
            'endFilePos' => 977,
          ),
        ),
        'docComment' => '/** @var Ole2DirEntry[] */',
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'miniStream' => 
      array (
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'name' => 'miniStream',
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
          'code' => '\'\'',
          'attributes' => 
          array (
            'startLine' => 39,
            'endLine' => 39,
            'startTokenPos' => 123,
            'startFilePos' => 1014,
            'endTokenPos' => 123,
            'endFilePos' => 1015,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 36,
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
          'fileContent' => 
          array (
            'name' => 'fileContent',
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
            'startLine' => 44,
            'endLine' => 44,
            'startColumn' => 33,
            'endColumn' => 51,
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
 * @throws \\RuntimeException
 */',
        'startLine' => 44,
        'endLine' => 63,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'fromFile' => 
      array (
        'name' => 'fromFile',
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
            'startLine' => 68,
            'endLine' => 68,
            'startColumn' => 37,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @throws \\RuntimeException
 */',
        'startLine' => 68,
        'endLine' => 80,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'getStream' => 
      array (
        'name' => 'getStream',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
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
            'startLine' => 87,
            'endLine' => 87,
            'startColumn' => 31,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Lit un flux (stream) par son nom.
 *
 * @throws \\RuntimeException si le flux n\'existe pas
 */',
        'startLine' => 87,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'hasStream' => 
      array (
        'name' => 'hasStream',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
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
            'startLine' => 105,
            'endLine' => 105,
            'startColumn' => 31,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie si un flux existe.
 */',
        'startLine' => 105,
        'endLine' => 108,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'getStreamNames' => 
      array (
        'name' => 'getStreamNames',
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
 * @return string[] Noms de tous les flux disponibles
 */',
        'startLine' => 113,
        'endLine' => 124,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'parseHeader' => 
      array (
        'name' => 'parseHeader',
        'parameters' => 
        array (
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
        'startLine' => 130,
        'endLine' => 138,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'buildFat' => 
      array (
        'name' => 'buildFat',
        'parameters' => 
        array (
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
        'startLine' => 144,
        'endLine' => 187,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'readDirectory' => 
      array (
        'name' => 'readDirectory',
        'parameters' => 
        array (
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
        'startLine' => 193,
        'endLine' => 226,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'buildMiniFat' => 
      array (
        'name' => 'buildMiniFat',
        'parameters' => 
        array (
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
        'startLine' => 232,
        'endLine' => 247,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'loadMiniStream' => 
      array (
        'name' => 'loadMiniStream',
        'parameters' => 
        array (
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
        'startLine' => 249,
        'endLine' => 262,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'readStream' => 
      array (
        'name' => 'readStream',
        'parameters' => 
        array (
          'startSector' => 
          array (
            'name' => 'startSector',
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
            'startLine' => 268,
            'endLine' => 268,
            'startColumn' => 33,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'maxSize' => 
          array (
            'name' => 'maxSize',
            'default' => 
            array (
              'code' => 'PHP_INT_MAX',
              'attributes' => 
              array (
                'startLine' => 268,
                'endLine' => 268,
                'startTokenPos' => 1716,
                'startFilePos' => 7768,
                'endTokenPos' => 1716,
                'endFilePos' => 7778,
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
            'startLine' => 268,
            'endLine' => 268,
            'startColumn' => 51,
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
        'docComment' => NULL,
        'startLine' => 268,
        'endLine' => 297,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'readMiniStream' => 
      array (
        'name' => 'readMiniStream',
        'parameters' => 
        array (
          'startMiniSector' => 
          array (
            'name' => 'startMiniSector',
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
            'startLine' => 299,
            'endLine' => 299,
            'startColumn' => 37,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'size' => 
          array (
            'name' => 'size',
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
            'startLine' => 299,
            'endLine' => 299,
            'startColumn' => 59,
            'endColumn' => 67,
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
        'docComment' => NULL,
        'startLine' => 299,
        'endLine' => 324,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'findEntry' => 
      array (
        'name' => 'findEntry',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
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
            'startLine' => 330,
            'endLine' => 330,
            'startColumn' => 32,
            'endColumn' => 43,
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
                  'name' => 'Paperdoc\\Support\\Ole2\\Ole2DirEntry',
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
        'startLine' => 330,
        'endLine' => 341,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'sectorOffset' => 
      array (
        'name' => 'sectorOffset',
        'parameters' => 
        array (
          'sector' => 
          array (
            'name' => 'sector',
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
            'startLine' => 343,
            'endLine' => 343,
            'startColumn' => 35,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 343,
        'endLine' => 346,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'readUint16' => 
      array (
        'name' => 'readUint16',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
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
            'startLine' => 348,
            'endLine' => 348,
            'startColumn' => 33,
            'endColumn' => 43,
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
        'docComment' => NULL,
        'startLine' => 348,
        'endLine' => 351,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'readUint32' => 
      array (
        'name' => 'readUint32',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
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
            'startLine' => 353,
            'endLine' => 353,
            'startColumn' => 33,
            'endColumn' => 43,
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
        'docComment' => NULL,
        'startLine' => 353,
        'endLine' => 356,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'readInt32' => 
      array (
        'name' => 'readInt32',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
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
            'startLine' => 358,
            'endLine' => 358,
            'startColumn' => 32,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 358,
        'endLine' => 367,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'u16' => 
      array (
        'name' => 'u16',
        'parameters' => 
        array (
          'bin' => 
          array (
            'name' => 'bin',
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
            'startLine' => 369,
            'endLine' => 369,
            'startColumn' => 33,
            'endColumn' => 43,
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
        'docComment' => NULL,
        'startLine' => 369,
        'endLine' => 378,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'aliasName' => NULL,
      ),
      'u32' => 
      array (
        'name' => 'u32',
        'parameters' => 
        array (
          'bin' => 
          array (
            'name' => 'bin',
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
            'startLine' => 380,
            'endLine' => 380,
            'startColumn' => 33,
            'endColumn' => 43,
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
        'docComment' => NULL,
        'startLine' => 380,
        'endLine' => 389,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'Paperdoc\\Support\\Ole2',
        'declaringClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'implementingClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
        'currentClassName' => 'Paperdoc\\Support\\Ole2\\Ole2Reader',
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