<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.factroom.ru/life/20-facts-about-oil',
            'body' => array(
                '//div[@class="post"]',
            ),
            'strip' => array(
                '//script',
                '//form',
                '//style',
                '//h1',
                '//div[@id="yandex_ad2"]',
                '//*[@class="jp-relatedposts"]',
                '//div[contains(@class, "likely-desktop")]',
                '//div[contains(@class, "likely-mobile")]',
                '//p[last()]',
                '//div[contains(@class, "facebook")]',
                '//div[contains(@class, "desktop-underpost-direct")]',
                '//div[contains(@class, "source-box")]',
                '//div[contains(@class, "under-likely-desktop")]',
                '//div[contains(@class, "mobile-down-post")]',
            ),
        ),
    ),
);
