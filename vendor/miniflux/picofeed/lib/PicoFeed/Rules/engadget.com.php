<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.engadget.com/2015/04/20/dark-matter-discovery/?ncid=rss_truncated',
            'body' => array('//div[@id="page_body"]/div[@class="container@m-"]'),
            'strip' => array('//aside[@role="banner"]'),
        ),
    ),
);
