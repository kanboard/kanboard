<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.bgr.in/news/xiaomi-redmi-3-with-13-megapixel-camera-snapdragon-616-launched-price-specifications-and-features/',
            'body' => array(
                '//div[@class="article-content"]',
            ),
            'strip' => array(
                '//*[@class="article-meta"]',
                '//*[@class="contentAdsense300"]',
                '//*[@class="iwpl-social-hide"]',
                '//iframe[@class="iframeads"]',
                '//*[@class="disqus_thread"]',
                '//*[@class="outb-mobile OUTBRAIN"]',
                '//*[@class="wdt_smart_alerts"]',
                '//*[@class="footnote"]',
                '//*[@id="gadget-widget"]',
                '//header[@class="article-title entry-header"]',
            ),
        ),
    ),
);
