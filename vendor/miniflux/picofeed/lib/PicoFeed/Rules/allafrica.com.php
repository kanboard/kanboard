<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.aljazeera.com/news/2015/09/xi-jinping-seattle-china-150922230118373.html',
            'body' => array(
            '//div[@class="story-body"]',
            ),
            'strip' => array(
            '//p[@class="kindofstory"]',
            '//cite[@class="byline"]',
            '//div[@class="useful-top"]',
            '//div[contains(@class,"related-topics")]',
            '//links',
            '//sharebar',
            '//related-topics',
            ),
        ),
    ),
);
