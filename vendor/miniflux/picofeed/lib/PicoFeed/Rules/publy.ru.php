<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.publy.ru/post/19988',
            'body' => array(
                '//div[@class="singlepost"]',
            ),
            'strip' => array(
                '//script',
                '//form',
                '//style',
                '//*[@class="featured"]',
                '//*[@class="toc_white no_bullets"]',
                '//*[@class="toc_title"]',
                '//*[@class="pba"]',
                '//*[@class="comments"]',
                '//*[contains(@class, "g-single")]',
                '//*[@class="ts-fab-wrapper"]',
                '//*[contains(@class, "wp_rp_wrap")]',
            ),
        ),
    ),
);
