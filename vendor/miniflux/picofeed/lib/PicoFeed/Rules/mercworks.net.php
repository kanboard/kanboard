<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'body' => array('//div[@id="comic"]',
                            '//div[contains(@class,"entry-content")]',
                           ),
            'strip' => array(),
            'test_url' => 'http://mercworks.net/comicland/healthy-choice/',
        ),
    ),
    'filter' => array(
        '%.*%' => array(
            '%title="(.+)" */>%' => '/><br/>$1',
        ),
    ),
);
