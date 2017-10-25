<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.nba.com/2015/news/09/25/knicks-jackson-to-spend-more-time-around-coaching-staff.ap/index.html?rss=true',
            'body' => array(
            '//div[@class="paragraphs"]',
            ),
            'strip' => array(
            '//div[@id="nbaArticleSocialWrapper_bot"]',
            '//h5',
            ),
         ),
    ),
);
