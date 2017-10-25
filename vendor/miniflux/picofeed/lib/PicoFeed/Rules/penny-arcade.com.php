<?php
return array(
    'grabber' => array(
        '%/news/.*%' => array(
            'test_url' => 'http://penny-arcade.com/news/post/2015/04/15/101-part-two',
            'body' => array(
                '//*[@class="postBody"]/*',
            ),
            'strip' => array(
            ),
        ),
        '%/comic/.*%' => array(
            'test_url' => 'http://penny-arcade.com/comic/2015/04/15',
            'body' => array(
                '//*[@id="comicFrame"]/a/img',
            ),
            'strip' => array(
            ),
        ),
    ),
);
