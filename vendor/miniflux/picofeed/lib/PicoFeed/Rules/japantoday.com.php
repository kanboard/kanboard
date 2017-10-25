<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.japantoday.com/category/politics/view/japan-u-s-to-sign-new-base-environment-pact',
            'body' => array(
            '//div[@id="article_container"]',
            ),
            'strip' => array(
            '//h2',
            '//div[@id="article_info"]',
            ),
        ),
    ),
);
