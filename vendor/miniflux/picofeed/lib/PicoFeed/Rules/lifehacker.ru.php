<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://lifehacker.ru/2016/03/03/polymail/',
            'body' => array(
                '//div[@class="post-content"]',
            ),
            'strip' => array(
                '//script',
                '//form',
                '//style',
                '//*[@class="wp-thumbnail-caption"]',
                '//*[contains(@class, "social-likes")]',
                '//*[@class="jp-relatedposts"]',
                '//*[contains(@class, "wpappbox")]',
                '//*[contains(@class, "icon__image")]',
                '//div[@id="hypercomments_widget"]',
            ),
        ),
    ),
);
