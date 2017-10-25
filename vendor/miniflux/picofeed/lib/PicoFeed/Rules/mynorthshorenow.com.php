<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.mynorthshorenow.com/story/news/local/fox-point/2017/04/04/fox-point-building-board-approves-dunwood-commons-project/99875570/',
            'body' => array(
                '//div[@itemprop="articleBody"]',
            ),
            'strip' => array(
                '//h1',
                '//iframe',
                '//span[@class="mycapture-small-btn mycapture-btn-with-text mycapture-expandable-photo-btn-small js-mycapture-btn-small"]',
                '//div[@class="close-wrap"]',
                '//div[contains(@class,"ui-video-wrapper")]',
                '//div[contains(@class,"media-mob")]',
                '//div[contains(@class,"left-mob")]',
                '//div[contains(@class,"nerdbox")]',
                '//p/span',
                '//div[contains(@class,"oembed-asset")]',
                '//*[contains(@class,"share")]',
                '//div[contains(@class,"gallery-asset")]',
                '//div[contains(@class,"oembed-asset")]',
                '//div[@class="article-print-url"]',
            ),
        ),
    ),
);
