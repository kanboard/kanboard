<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'https://www.bleepingcomputer.com/news/google/chromes-sandbox-feature-infringes-on-three-patents-so-google-must-now-pay-20m/',
            'body' => array(
                '//div[@class="article_section"]',
            ),
            'strip' => array(
                '//*[@itemprop="headline"]',
                '//div[@class="cz-news-story-title-section"]'
            ),
        ),
    ),
);
