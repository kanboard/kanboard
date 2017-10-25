<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://putaindecode.fr/posts/js/etat-lieux-js-modulaire-front/',
            'body' => array(
                '//*[@class="putainde-Post-md"]',
            ),
            'strip' => array(
                '//*[contains(@class, "inlineimg")]',
                '//*[contains(@class, "comment-respond")]',
                '//header',
            ),
        ),
    ),
);
