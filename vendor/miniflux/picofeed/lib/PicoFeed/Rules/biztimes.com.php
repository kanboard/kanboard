<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'https://www.biztimes.com/2017/02/10/settlement-would-revive-fowler-lake-condo-project-in-oconomowoc/',
            'body' => array(
                '//h2/span[@class="subhead"]',
                '//div[contains(@class,"article-content")]',
            ),
            'strip' => array(
                '//script',
                '//div[contains(@class,"mobile-article-content")]',
                '//div[contains(@class,"sharedaddy")]',
                '//div[contains(@class,"author-details")]',
                '//div[@class="row ad"]',
                '//div[contains(@class,"relatedposts")]',
                '//div[@class="col-lg-12"]',
                '//div[contains(@class,"widget")]',
            ),
        ),
    ),
);
