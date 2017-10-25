<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://smallhousebliss.com/2013/08/29/house-g-by-lode-architecture/',
            'body' => array(
                '//div[@class="post-content"]',
            ),
            'strip' => array(
                '//*[contains(@class, "gallery")]',
                '//*[contains(@class, "share")]',
                '//*[contains(@class, "wpcnt")]',
                '//*[contains(@class, "meta")]',
                '//*[contains(@class, "postitle")]',
                '//*[@id="nav-below"]',
            ),
        ),
    ),
);
