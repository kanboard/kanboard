<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.thestandard.com.hk/breaking_news_detail.asp?id=67156',
            'body' => array(
                '//table/tr/td/span[@class="bodyCopy"]',
            ),
            'strip' => array(
                '//script',
                '//br',
                '//map[@name="gif_bar"]',
                '//img[contains(@usemap,"gif_bar")]',
                '//a',
                '//span[@class="bodyHeadline"]',
                '//i',
                '//b',
                '//table',
            ),
        ),
    ),
);
