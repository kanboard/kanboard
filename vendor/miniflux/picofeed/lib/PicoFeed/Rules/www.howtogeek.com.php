<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.howtogeek.com/235283/what-is-a-wireless-hard-drive-and-should-i-get-one/',
            'body' => array(
            '//div[@class="thecontent"]',
            ),
            'strip' => array(
            '//*[@class="relatedside"]',
            ),
        ),
    ),
);
