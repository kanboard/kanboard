<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.aljazeera.com/news/2015/09/xi-jinping-seattle-china-150922230118373.html',
            'body' => array(
                '//article[@id="main-story"]',
            ),
            'strip' => array(
                '//script',
                '//header',
                '//ul',
                '//section[contains(@class,"profile")]',
                '//a[@target="_self"]',
                '//div[contains(@id,"_2")]',
                '//div[contains(@id,"_3")]',
                '//img[@class="viewMode"]',
                '//table[contains(@class,"in-article-item")]',
                '//div[@data-embed-type="Brightcove"]',
                '//div[@class="QuoteContainer"]',
                '//div[@class="BottomByLine"]',
            ),
        ),
    ),
);
