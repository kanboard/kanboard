<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://bgr.com/2015/09/27/iphone-6s-waterproof-testing/',
            'body' => array(
            '//img[contains(@class,"img")]',
            '//div[@class="text-column"]',
            ),
            'strip' => array(
            '//strong',
            ),
        ),
    ),
);
