<?php

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader(<<<EOF
This file is part of the eluceo/iCal package.

(c) Markus Poerschke <markus@eluceo.de>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF
);

$finder = Symfony\CS\Finder\DefaultFinder::create();
$finder->in(__DIR__ . '/src');

return Symfony\CS\Config\Config::create()
    ->fixers(array(
        'header_comment',
        'concat_with_spaces',
        'align_equals',
        'align_double_arrow',
        'unused_use',
        'long_array_syntax',
    ))
    ->finder($finder)
;
