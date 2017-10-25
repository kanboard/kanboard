<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'body' => array(
                '//img[@id="strip"]',
                '//a/div[@id="nx"]/..',
            ),
            'strip' => array(),
            'test_url' => 'http://oglaf.com/slodging/',
        ),
    ),
    'filter' => array(
        '%.*%' => array(
            '%alt="(.+)" title="(.+)" */>%' => '/><br/>$1<br/>$2<br/>',
            '%</a>%' => 'Next page</a>',
        ),
    ),
);
