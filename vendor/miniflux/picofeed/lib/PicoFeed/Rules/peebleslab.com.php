<?php
return array(
    'filter' => array(
        '%.*%' => array(
            // the extra space is required to strip the title cleanly
            '%title="(.+) " */>%' => '/><br/>$1',
        ),
    ),
);
