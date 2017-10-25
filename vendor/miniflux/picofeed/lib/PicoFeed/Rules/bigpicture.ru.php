<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://bigpicture.ru/?p=556658',
            'body' => array(
                '//div[@class="article container"]',
            ),
            'strip' => array(
                '//script',
                '//form',
                '//style',
                '//h1',
                '//*[@class="wp-smiley"]',
                '//div[@class="ipmd"]',
                '//div[@class="tags"]',
                '//div[@class="social-button"]',
                '//div[@class="bottom-share"]',
                '//div[@class="raccoonbox"]',
                '//div[@class="yndadvert"]',
                '//div[@class="we-recommend"]',
                '//div[@class="relap-bigpicture_ru-wrapper"]',
                '//div[@id="mmail"]',
                '//div[@id="mobile-ads-cut"]',
                '//div[@id="liquidstorm-alt-html"]',
                '//div[contains(@class, "post-tags")]',
                '//*[contains(text(),"Смотрите также")]',
            ),
        ),
    ),
);
