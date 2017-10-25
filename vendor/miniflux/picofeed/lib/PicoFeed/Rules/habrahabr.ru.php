<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'https://habrahabr.ru/company/pentestit/blog/328606/',
            'body' => array(
            "//div[contains(concat(' ',normalize-space(@class),' '),' content ')]"
            ),
            'strip' => array(),
        ),
    ),
);
