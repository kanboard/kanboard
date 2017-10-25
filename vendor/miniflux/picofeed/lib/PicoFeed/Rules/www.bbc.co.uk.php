<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.bbc.co.uk/news/world-middle-east-23911833',
            'body' => array(
                '//div[@class="story-body__inner"] | //div[@class="article"]',
                '//div[@class="indPost"]',
            ),
            'strip' => array(
                '//form',
                '//div[@id="headline"]',
                '//*[@class="warning"]',
                '//span[@class="off-screen"]',
                '//span[@class="story-image-copyright"]',
                '//ul[@class="story-body__unordered-list"]',
                '//div[@class="ad_wrapper"]',
                '//div[@id="article-sidebar"]',
                '//div[@class="data-table-outer"]',
                '//*[@class="story-date"]',
                '//*[@class="story-header"]',
                '//figure[contains(@class,"has-caption")]',
                '//*[@class="story-related"]',
                '//*[contains(@class, "byline")]',
                '//p[contains(@class, "media-message")]',
                '//*[contains(@class, "story-feature")]',
                '//*[@id="video-carousel-container"]',
                '//*[@id="also-related-links"]',
                '//*[contains(@class, "share") or contains(@class, "hidden") or contains(@class, "hyper")]',
            ),
        ),
    ),
);
