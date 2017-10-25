<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://blogs.discovermagazine.com/neuroskeptic/2017/01/25/publishers-jeffrey-beall/',
            'body' => array(
            '//div[@class="contentWell"]',
            ),
            'strip' => array(
            '//h1',
            '//div[@class="breadcrumbs"]',
            '//div[@class="mobile"]',
            '//div[@class="fromIssue"]',
            '//div[contains(@class,"belowDeck")]',
            '//div[@class="meta"]',
            '//div[@class="shareIcons"]',
            '//div[@class="categories"]',
            '//div[@class="navigation"]',
            '//div[@class="heading"]',
            '//div[contains(@id,"-ad")]',
            '//div[@class="relatedArticles"]',
            '//div[@id="disqus_thread"]'
            ),
         ),
    ),
);
