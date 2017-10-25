<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://dailyreporter.com/2016/01/09/us-supreme-court-case-could-weaken-government-workers-unions/',
            'body' => array(
                '//div[contains(@class, "entry-content")]',
            ),
            'strip' => array(
                '//div[@class="dmcss_login_form"]',
                '//*[contains(@class, "sharedaddy")]',
            ),
        ),
    ),
);
