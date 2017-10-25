<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.fakingnews.firstpost.com/2016/01/engineering-student-creates-record-in-a-decade-becomes-the-first-to-completely-exhaust-ball-pen-refill/',
            'body' => array(
            '//div[@class="entry"]',
            ),
            'strip' => array(
            '//*[@class="socialshare_bar"]',
            '//*[@class="authorbox"]',
            '//*[@class="cf5_rps"]',
            '//*[@class="60563 fb-comments fb-social-plugin"]',
            ),
        ),
    ),
);
