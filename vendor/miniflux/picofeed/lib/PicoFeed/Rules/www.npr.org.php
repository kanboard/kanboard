<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.npr.org/blogs/thesalt/2013/09/17/223345977/auto-brewery-syndrome-apparently-you-can-make-beer-in-your-gut',
            'body' => array(
                 '//article[contains(@class,"story")]',
            ),
            'strip' => array(
                '//div[@class="story-tools"]',
                '//h3[@class="slug"]',
                '//div[@class="storytitle"]',
                '//div[@id="story-meta"]',
                '//a[@id="mainContent"]',
                '//div[@class="credit-caption"]',
                '//div[@class="enlarge_html"]',
                '//button',
                '//div[contains(@id,"pullquote")]',
                '//div[contains(@class,"internallink")]',
                '//div[contains(@class,"video")]',
                '//div[@class="simplenodate"]',
                '//div[contains(@class,"share-")]',
                '//div[@class="tags"]',
                '//aside'
            ),
        ),
    ),
);
