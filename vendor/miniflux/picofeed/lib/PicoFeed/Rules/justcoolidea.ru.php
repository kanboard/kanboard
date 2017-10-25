<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://justcoolidea.ru/idealnyj-sad-samodelnye-proekty-dlya-berezhlivogo-domovladeltsa/',
            'body' => array(
                '//section[@class="entry-content"]',
            ),
            'strip' => array(
                '//script',
                '//form',
                '//style',
                '//*[contains(@class, "essb_links")]',
                '//*[contains(@rel, "nofollow")]',
                '//*[contains(@class, "ads")]',
            ),
        ),
    ),
);
