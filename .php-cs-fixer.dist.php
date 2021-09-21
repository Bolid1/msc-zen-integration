<?php

use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;

$finder = PhpCsFixer\Finder::create();
$finder
    ->in([
        __DIR__.'/app',
        __DIR__.'/bootstrap',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->exclude('bootstrap/cache')
;

$config = new PhpCsFixer\Config();

// @link https://mlocati.github.io/php-cs-fixer-configurator/#version:2.19
return $config
    ->setRiskyAllowed(true)
    ->setRules(
        [
            '@Symfony'                   => true,
            '@Symfony:risky'             => true,
            // Приводим массивы к короткому синтаксису ([])
            'array_syntax'               => ['syntax' => 'short'],
            // Отключаем выравнивание в phpdoc-ах
            'phpdoc_align'               => false,
            // Оставляем phpdoc, даже если не сильно и нужен
            'no_superfluous_phpdoc_tags' => false,
            // (string)$foo
            'cast_spaces'                => ['space' => 'none'],
            // Микрооптимизация кода
            'global_namespace_import'    => [
                'import_classes'   => true,
                'import_constants' => true,
                'import_functions' => true,
            ],
            'native_function_invocation' => [
                'include' => [NativeFunctionInvocationFixer::SET_ALL],
                'scope'   => 'all',
                'strict'  => true,
            ],
            // Не все Exception-ы можно уместить в одну линию
            'single_line_throw'          => false,
            // Сортировка импортов
            'ordered_imports'            => [
                'sort_algorithm' => 'alpha',
                'imports_order'  => [
                    'class',
                    'function',
                    'const',
                ],
            ],
            'declare_strict_types'       => true,
        ]
    )
    ->setFinder($finder)
;
