<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'https://www.openrightsgroup.org/blog/2014/3-days-to-go-till-orgcon2014',
            'body' => array(
                '//div[contains(@class, "content")]/div',
            ),
            'strip' => array(
                '//h2[1]',
                '//div[@class="info"]',
                '//div[@class="tags"]',
                '//div[@class="comments"]',
                '//div[@class="breadcrumbs"]',
                '//h1[@class="pageTitle"]',
                '//p[@class="bookmarkThis"]',
            ),
        ),
    ),
);
