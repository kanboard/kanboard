<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.usatoday.com/story/life/music/2017/02/13/things-you-should-know-happened-grammy-awards-2017/97833734/',
            'body' => array(
                '//div[@itemprop="articleBody"]',
            ),
            'strip' => array(
                '//script',
                '//h1',
                '//iframe',
                '//span[@class="mycapture-small-btn mycapture-btn-with-text mycapture-expandable-photo-btn-small js-mycapture-btn-small"]',
                '//div[@class="close-wrap"]',
                '//div[contains(@class,"ui-video-wrapper")]',
                '//div[contains(@class,"media-mob")]',
                '//div[contains(@class,"left-mob")]',
                '//div[contains(@class,"nerdbox")]',
                '//div[contains(@class,"oembed-asset")]',
                '//*[contains(@class,"share")]',
                '//div[contains(@class,"gallery-asset")]',
                '//div[contains(@class,"oembed-asset")]',
                '//div[@class="article-print-url"]'
            ),
        ),
    ),
);
