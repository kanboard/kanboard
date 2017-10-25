<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.economist.com/blogs/buttonwood/2017/02/mixed-signals?fsrc=rss',
            'body' => array(
                '//article',
            ),
            'strip' => array(
                '//span[@class="blog-post__siblings-list-header "]',
                '//h1',
                '//aside',
                '//div[@class="blog-post__asideable-wrapper"]',
                '//div[@class="share_inline_header"]',
                '//div[@id="column-right"]',
                '//div[contains(@class,"blog-post__siblings-list-aside")]',
                '//div[@class="video-player__wrapper"]',
                '//div[@class="blog-post__bottom-panel"]',
                '//div[contains(@class,"latest-updates-panel__container")]',
                '//div[contains(@class,"blog-post__asideable-content")]',
                '//div[@aria-label="Advertisement"]'
            ),
        ),
    ),
);
