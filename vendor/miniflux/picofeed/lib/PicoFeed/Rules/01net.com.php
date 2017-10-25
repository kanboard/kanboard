<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.01net.com/editorial/624550/twitter-rachete-madbits-un-specialiste-francais-de-lanalyse-dimages/',
            'body' => array(
                '//div[@class="article_ventre_box"]',
            ),
            'strip' => array(
                '//link',
                '//*[contains(@class, "article_navigation")]',
                '//h1',
                '//*[contains(@class, "article_toolbarMain")]',
                '//*[contains(@class, "article_imagehaute_box")]',
            ),
        ),
    ),
);
