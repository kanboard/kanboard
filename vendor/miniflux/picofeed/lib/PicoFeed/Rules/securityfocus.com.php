<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.securityfocus.com/archive/1/540139',
            'body' => array(
                '//div[@id="vulnerability"]',
                '//div[@class="comments_reply"]',
            ),
            'strip' => array(
                '//span[@class="title"]',
                '//div[@id="logo_new"]',
                '//div[@id="bannerAd"]',
            ),
        ),
    ),
);
