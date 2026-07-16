<?php declare(strict_types = 1);

// osfsl-/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Document/HorizontalRule.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Paperdoc\Document\HorizontalRule
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-f0a120f27da3ff2d5c4165817cdd3c5a2ed3a36ea7f0b686f25d09388a49dc9b-8.5.8-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Paperdoc\\Document\\HorizontalRule',
        'filename' => '/home/akramzerarka/Projects/paperdoc/paperdoc-lib/src/Document/HorizontalRule.php',
      ),
    ),
    'namespace' => 'Paperdoc\\Document',
    'name' => 'Paperdoc\\Document\\HorizontalRule',
    'shortName' => 'HorizontalRule',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * A decorative horizontal line, akin to HTML\'s `<hr>` (since v0.8.0).
 *
 * Useful for separating sections, marking the end of a chapter, or
 * underlining a heading. Renderers translate it as follows:
 *
 *  - PDF      : a stroked line on the current page.
 *  - HTML     : `<hr>` element with inline CSS.
 *  - Markdown : `---` on its own line, surrounded by blank lines.
 *  - DOCX     : an empty paragraph with a bottom border (the
 *               canonical Word equivalent of an `<hr>`).
 *
 * Geometry:
 *  - `width`     : either an absolute width in points (`70`) or a
 *                  percentage string (`\'100%\'`, `\'50%\'`, …) relative
 *                  to the available content width. Defaults to `\'100%\'`.
 *  - `thickness` : stroke thickness in points. Defaults to 0.5pt
 *                  (the Word-style "thin rule").
 *  - `color`     : CSS hex colour, e.g. `\'#999999\'`. Defaults to a
 *                  neutral grey.
 *  - `alignment` : LEFT / CENTER / RIGHT for the horizontal placement
 *                  of partial-width rules. Defaults to CENTER.
 *  - `marginTop` / `marginBottom` : extra vertical breathing room
 *                  in points, applied above/below the rule.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 35,
    'endLine' => 142,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Paperdoc\\Contracts\\BlockElementInterface',
      1 => 'JsonSerializable',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'width' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'name' => 'width',
        'modifiers' => 4,
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
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'float',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'default' => 
        array (
          'code' => '\'100%\'',
          'attributes' => 
          array (
            'startLine' => 37,
            'endLine' => 37,
            'startTokenPos' => 52,
            'startFilePos' => 1472,
            'endTokenPos' => 52,
            'endFilePos' => 1477,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 46,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'thickness' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'name' => 'thickness',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '0.5',
          'attributes' => 
          array (
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 63,
            'startFilePos' => 1519,
            'endTokenPos' => 63,
            'endFilePos' => 1521,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 43,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'color' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'name' => 'color',
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
          'code' => '\'#999999\'',
          'attributes' => 
          array (
            'startLine' => 39,
            'endLine' => 39,
            'startTokenPos' => 74,
            'startFilePos' => 1563,
            'endTokenPos' => 74,
            'endFilePos' => 1571,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 49,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'alignment' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'name' => 'alignment',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Paperdoc\\Enum\\Alignment',
            'isIdentifier' => false,
          ),
        ),
        'default' => 
        array (
          'code' => '\\Paperdoc\\Enum\\Alignment::CENTER',
          'attributes' => 
          array (
            'startLine' => 40,
            'endLine' => 40,
            'startTokenPos' => 85,
            'startFilePos' => 1613,
            'endTokenPos' => 87,
            'endFilePos' => 1629,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 57,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'marginTop' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'name' => 'marginTop',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '6.0',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 41,
            'startTokenPos' => 98,
            'startFilePos' => 1671,
            'endTokenPos' => 98,
            'endFilePos' => 1673,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 43,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'marginBottom' => 
      array (
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'name' => 'marginBottom',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '6.0',
          'attributes' => 
          array (
            'startLine' => 42,
            'endLine' => 42,
            'startTokenPos' => 109,
            'startFilePos' => 1717,
            'endTokenPos' => 109,
            'endFilePos' => 1719,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 42,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 45,
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
      'make' => 
      array (
        'name' => 'make',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 44,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 66,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'getType' => 
      array (
        'name' => 'getType',
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
        'startLine' => 46,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 67,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'setWidth' => 
      array (
        'name' => 'setWidth',
        'parameters' => 
        array (
          'width' => 
          array (
            'name' => 'width',
            'default' => NULL,
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
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'float',
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
            'startLine' => 50,
            'endLine' => 50,
            'startColumn' => 30,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 50,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'setThickness' => 
      array (
        'name' => 'setThickness',
        'parameters' => 
        array (
          'thickness' => 
          array (
            'name' => 'thickness',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 57,
            'endLine' => 57,
            'startColumn' => 34,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 57,
        'endLine' => 62,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'setColor' => 
      array (
        'name' => 'setColor',
        'parameters' => 
        array (
          'color' => 
          array (
            'name' => 'color',
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
            'startLine' => 64,
            'endLine' => 64,
            'startColumn' => 30,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 64,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'setAlignment' => 
      array (
        'name' => 'setAlignment',
        'parameters' => 
        array (
          'alignment' => 
          array (
            'name' => 'alignment',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Paperdoc\\Enum\\Alignment',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 71,
            'endLine' => 71,
            'startColumn' => 34,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 71,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'setMarginTop' => 
      array (
        'name' => 'setMarginTop',
        'parameters' => 
        array (
          'v' => 
          array (
            'name' => 'v',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 78,
            'endLine' => 78,
            'startColumn' => 34,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 78,
        'endLine' => 83,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'setMarginBottom' => 
      array (
        'name' => 'setMarginBottom',
        'parameters' => 
        array (
          'v' => 
          array (
            'name' => 'v',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 85,
            'endLine' => 85,
            'startColumn' => 37,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 85,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'setMargins' => 
      array (
        'name' => 'setMargins',
        'parameters' => 
        array (
          'top' => 
          array (
            'name' => 'top',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 92,
            'endLine' => 92,
            'startColumn' => 32,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bottom' => 
          array (
            'name' => 'bottom',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 92,
            'endLine' => 92,
            'startColumn' => 44,
            'endColumn' => 56,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 92,
        'endLine' => 98,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'getWidth' => 
      array (
        'name' => 'getWidth',
        'parameters' => 
        array (
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
                  'name' => 'float',
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
        'startLine' => 102,
        'endLine' => 102,
        'startColumn' => 5,
        'endColumn' => 69,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'getThickness' => 
      array (
        'name' => 'getThickness',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 103,
        'endLine' => 103,
        'startColumn' => 5,
        'endColumn' => 73,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'getColor' => 
      array (
        'name' => 'getColor',
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
        'startLine' => 104,
        'endLine' => 104,
        'startColumn' => 5,
        'endColumn' => 69,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'getAlignment' => 
      array (
        'name' => 'getAlignment',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Paperdoc\\Enum\\Alignment',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 105,
        'endLine' => 105,
        'startColumn' => 5,
        'endColumn' => 74,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'getMarginTop' => 
      array (
        'name' => 'getMarginTop',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 106,
        'endLine' => 106,
        'startColumn' => 5,
        'endColumn' => 73,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'getMarginBottom' => 
      array (
        'name' => 'getMarginBottom',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 107,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 76,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'resolveWidth' => 
      array (
        'name' => 'resolveWidth',
        'parameters' => 
        array (
          'contentWidth' => 
          array (
            'name' => 'contentWidth',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 113,
            'endLine' => 113,
            'startColumn' => 34,
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
            'name' => 'float',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Resolves the rule\'s width against an available content width.
 * Returns a clamped pt value not greater than `$contentWidth`.
 */',
        'startLine' => 113,
        'endLine' => 128,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'aliasName' => NULL,
      ),
      'jsonSerialize' => 
      array (
        'name' => 'jsonSerialize',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'mixed',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 130,
        'endLine' => 141,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Paperdoc\\Document',
        'declaringClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'implementingClassName' => 'Paperdoc\\Document\\HorizontalRule',
        'currentClassName' => 'Paperdoc\\Document\\HorizontalRule',
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