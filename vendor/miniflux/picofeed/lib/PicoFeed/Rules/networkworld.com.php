<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.networkworld.com/article/3020585/security/the-incident-response-fab-five.html',
            'body' => array(
                '//figure/img[@class="hero-img"]',
                '//section[@class="deck"]',
                '//div[@itemprop="articleBody"] | //div[@itemprop="reviewBody"]',
                '//div[@class="carousel-inside-crop"]',
            ),
            'strip' => array(
                '//script',
                '//aside',
                '//div[@class="credit"]',
                '//div[@class="view-large"]',
            ),
        ),
    ),
);
