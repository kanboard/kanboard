<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.alainonline.net/news_details.php?lang=arabic&sid=18907',
            'body' => array(
                '//div[@class="news_details"]',
            ),
            'strip' => array(
                '//div[@class="news_details"]/div/div[last()]',
            ),
        ),
    ),
);
