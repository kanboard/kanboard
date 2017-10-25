<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.snopes.com/bacca-brides-on-tour/',
            'body' => array(
                '//article',
            ),
            'strip' => array(
                '//span[@itemprop="author"]',
                '//div[contains(@class,"author-")]',
                '//h1',
                '//style',
                '//div[contains(@class,"socialShares")]',
                '//div[contains(@class,"ad-unit")]',
                '//aside',
                '//div[contains(@class,"boomtrain")]',
                '//footer'
            ),
        ),
    ),
);
