<?php
return array(
    'filter' => array(
        '%.*%' => array(
            '%title="(.+)" */>%' => '/><br/>$1',
        ),
    ),
);
