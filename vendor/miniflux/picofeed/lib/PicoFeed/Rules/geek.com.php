<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.geek.com/news/the-11-best-ways-to-eat-eggs-1634076/',
            'body' => array(
            '//div[@class="articleinfo"]/figure',
            '//div[@class="articleinfo"]/article',
            '//span[@class="by"]',
            ),
            'strip' => array(
            '//span[@class="red"]',
            '//div[@class="on-target"]'
            ),
        ),
    ),
);
