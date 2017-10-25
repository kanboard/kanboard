<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://fototelegraf.ru/?p=348232',
            'body' => array(
                '//div[@class="post-content"]',
            ),
            'strip' => array(
                '//script',
                '//form',
                '//style',
                '//div[@class="imageButtonsBlock"]',
                '//div[@class="adOnPostBtwImg"]',
                '//div[contains(@class, "post-tags")]',
            ),
        ),
    ),
);
