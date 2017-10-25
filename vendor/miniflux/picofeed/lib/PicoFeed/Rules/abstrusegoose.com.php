<?php
return array(
    'filter' => array(
        '%.*%' => array(
            '%alt="(.+)" title="(.+)" */>%' => '/><br/>$1<br/>$2',
        ),
    ),
);
