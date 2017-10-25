<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'body' => array(
                '//img[@id="comic_image"]',
                '//div[@class="comment-wrapper"][position()=1]',
            ),
            'strip' => array(),
            'test_url' => 'http://www.anythingcomic.com/comics/2108929/stress-free/',
        ),
    ),
);
