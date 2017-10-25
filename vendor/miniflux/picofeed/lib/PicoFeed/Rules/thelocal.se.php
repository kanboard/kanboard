<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'www.thelocal.se/20161219/this-swede-can-memorize-hundreds-of-numbers-in-only-five-minutes',
            'body' => array(
                '//div[@id="article-photo"]',
                '//div[@id="article-description"]',
                '//div[@id="article-body"]',
            ),
            'strip' => array(
                '//div[@id="article-info-middle"]',
        )
        )
    )
);

