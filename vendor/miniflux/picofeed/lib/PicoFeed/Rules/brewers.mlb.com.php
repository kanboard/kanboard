<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://m.brewers.mlb.com/news/article/161364798',
            'body' => array(
                '//article[contains(@class,"article")]',
            ),
            'strip' => array(
                '//div[contains(@class,"ad-slot")]',
                '//h1',
                '//span[@class="timestamp"]',
                '//div[contains(@class,"contributor-bottom")]',
                '//div[contains(@class,"video")]',
                '//ul[contains(@class,"social")]',
                '//p[@class="tagline"]',
                '//div[contains(@class,"social")]',
                '//div[@class="button-wrap"]',
            ),
        ),
    ),
);
