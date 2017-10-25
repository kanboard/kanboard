<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.bangkokpost.com/news/politics/704204/new-us-ambassador-arrives-in-bangkok',
            'body' => array(
            '//article/div[@class="articleContents"]',
            ),
            'strip' => array(
            '//h2',
            '//h4',
            '//div[@class="text-size"]',
            '//div[@class="relate-story"]',
            '//div[@class="text-ads"]',
            '//ul',
            ),
        ),
    ),
);
