<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.thehindu.com/sci-tech/science/why-is-the-shape-of-cells-in-a-honeycomb-always-hexagonal/article7692306.ece?utm_source=RSS_Feed&utm_medium=RSS&utm_campaign=RSS_Syndication',
            'body' => array(
                '//div/img[@class="main-image"]',
                '//div[@class="photo-caption"]',
                '//div[@class="articleLead"]',
                '//p',
                '//span[@class="upper"]',
            ),
            'strip' => array(
                '//div[@id="articleKeywords"]',
                '//div[@class="photo-source"]',
            ),
        ),
    ),
);
