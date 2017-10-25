<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.soundandvision.com/content/james-guthrie-mixing-roger-waters-and-pink-floyd-51',
            'body' => array(
                '//div[@id="left"]',
            ),
            'strip' => array(
                '//div[@class="meta"]',
                '//div[@class="ratingsbox"]',
                '//h1',
                '//h2',
                '//addthis',
                '//comment-links',
                '//div[@class="book-navigation"]',
                '//div[@class="comment-links"]',
            ),
        ),
    ),
);
