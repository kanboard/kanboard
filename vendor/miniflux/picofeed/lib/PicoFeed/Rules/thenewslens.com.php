<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://international.thenewslens.com/post/255032/',
            'body' => array(
                '//div[@class="article-section"]',
            ),
            'strip' => array(
                '//div[contains(@class,"ad-")]',
                '//div[@class="article-title-box"]',
                '//div[@class="function-box"]',
                '//p/span',
                '//aside',
                '//footer',
                '//div[@class="article-infoBot-box"]',
                '//div[contains(@class,"standard-container")]'
            ),
        ),
    ),
);
