<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://techcrunch.com/2013/08/31/indias-visa-maze/',
            'body' => array(
                '//div[contains(@class, "media-container")]',
                '//div[@class="body-copy"]',
            ),
            'strip' => array(
                '//*[contains(@class, "module-crunchbase")]',
            ),
        ),
    ),
);
