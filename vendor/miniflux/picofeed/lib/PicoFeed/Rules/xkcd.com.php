<?php
return array(
    'filter' => array(
        '%.*%' => array(
            '%alt="(.+)" */>%' => '/><br/>$1',
        ),
    ),
);
