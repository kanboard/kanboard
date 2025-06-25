<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = (new Finder())
    ->in(__DIR__)
    ->exclude(['app/Template', 'libs'])
    ->name('*.php');

return (new Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_whitespace_before_comma_in_array' => true,
        'trim_array_spaces' => true,
        'array_indentation' => true,
        'indentation_type' => true,
        '@PSR1' => true,
        '@PSR2' => true,
    ])
    ->setFinder($finder);
