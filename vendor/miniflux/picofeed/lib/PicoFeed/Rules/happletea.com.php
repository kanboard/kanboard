<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'body' => array(
                '//div[@id="comic"]',
                '//div[@class="entry"]',
            ),
            'strip' => array('//div[@class="ssba"]'),
            'test_url' => 'http://www.happletea.com/comic/mans-best-friend/',
        ),
    ),
    'filter' => array(
        '%.*%' => array(
            '%title="(.+)" */>%' => '/><br/>$1',
        ),
    ),
);
