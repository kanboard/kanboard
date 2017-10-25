<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://dailyjs.com/2014/08/07/p5js/',
            'body' => array(
                '//div[@id="post"]',
            ),
            'strip' => array(
                '//h2[@class="post"]',
                '//div[@class="meta"]',
                '//*[contains(@class, "addthis_toolbox")]',
                '//*[contains(@class, "addthis_default_style")]',
                '//*[@class="navigation small"]',
                '//*[@id="related"]',
            ),
        ),
    ),
);
