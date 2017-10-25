<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.lesnumeriques.com/blender/kitchenaid-diamond-5ksb1585-p27473/test.html',
            'body' => array(
                '//*[@id="product-content"]',
                '//*[@id="news-content"]',
                '//*[@id="article-content"]',
            ),
            'strip' => array(
                '//form',
                '//div[contains(@class, "price-v4"])',
                '//div[contains(@class, "authors-and-date")]',
                '//div[contains(@class, "mini-product")]',
                '//div[@id="articles-related-authors"]',
                '//div[@id="tags-socials"]',
                '//div[@id="user-reviews"]',
                '//div[@id="product-reviews"]',
                '//div[@id="publication-breadcrumbs-and-date"]',
                '//div[@id="publication-breadcrumbs-and-date"]',
            ),
        ),
    ),
);
