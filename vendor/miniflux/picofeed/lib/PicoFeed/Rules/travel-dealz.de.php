<?php
return array(
    'grabber' => array(
        '%^/blog.*%' => array(
            'test_url' => 'http://travel-dealz.de/blog/venere-gutschein/',
            'body' => array('//div[@class="post-entry"]'),
            'strip' => array(
                '//*[@id="jp-relatedposts"]',
                '//*[@class="post-meta"]',
                '//*[@class="post-data"]',
                '//*[@id="author-meta"]',
            ),
        ),
    ),
);
