<?php

$config = new PhpCsFixer\Config();

$config
    ->setUsingCache(true)
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'array_indentation' => true,
        'cast_spaces' => [
            'space' => 'single',
        ],
        'combine_consecutive_issets' => true,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'error_suppression' => [
            'mute_deprecation_error' => false,
            'noise_remaining_usages' => false,
            'noise_remaining_usages_exclude' => [],
        ],
        'function_to_constant' => false,
        'global_namespace_import' => true,
        'method_chaining_indentation' => true,
        'no_alias_functions' => false,
        'no_superfluous_phpdoc_tags' => false,
        'non_printable_character' => [
            'use_escape_sequences_in_strings' => true,
        ],
        'phpdoc_align' => [
            'align' => 'left',
        ],
        'phpdoc_summary' => false,
        'protected_to_private' => false,
        'self_accessor' => false,
        'yoda_style' => false,
        'single_line_throw' => false,
        'no_alias_language_construct_call' => false,
    ])
    ->getFinder()
    ->in(__DIR__)
    ->exclude('vendor');

return $config;