<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.linuxinsider.com/story/82526.html?rss=1',
            'body' => array(
                '//div[@id="story"]',
            ),
            'strip' => array(
                '//script',
                '//h1',
                '//div[@id="story-toolbox1"]',
                '//div[@id="story-byline"]',
                '//div[@id="story"]/p',
                '//div[@class="story-advertisement"]',
                '//iframe',
            ),
        ),
    ),
);
