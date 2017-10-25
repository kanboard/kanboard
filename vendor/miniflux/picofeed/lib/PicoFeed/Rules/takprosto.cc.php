<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://takprosto.cc/kokteyl-dlya-pohudeniya-v-domashnih-usloviyah/',
            'body' => array(
                '//div[contains(@class, "entry-contentt")]',
            ),
            'strip' => array(
                '//script',
                '//form',
                '//style',
                '//*[@class="views_post"]',
                '//*[contains(@class, "mailchimp-box")]',
                '//*[contains(@class, "essb_links")]',
                '//*[contains(@rel, "nofollow")]',
                '//*[contains(@class, "ads")]',
            ),
        ),
    ),
);
