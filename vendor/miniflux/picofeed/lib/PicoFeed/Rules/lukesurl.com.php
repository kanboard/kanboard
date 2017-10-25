<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'body' => array('//div[@id="comic"]//img'),
            'strip' => array(),
            'test_url' => 'http://www.lukesurl.com/archives/comic/665-3-of-clubs',
        ),
    ),
    'filter' => array(
        '%.*%' => array(
            '%title="(.+)" */>%' => '/><br/>$1',
        ),
    ),
);
