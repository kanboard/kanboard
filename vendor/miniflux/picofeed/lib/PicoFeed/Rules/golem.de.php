<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.golem.de/news/breko-telekom-verzoegert-gezielt-den-vectoring-ausbau-1311-102974.html',
            'body' => array(
                '//header[@class="cluster-header"]',
                '//header[@class="paged-cluster-header"]',
                '//div[@class="formatted"]',
                ),
            'next_page' => array(
                '//a[@id="atoc_next"]'
            ),
            'strip' => array(
                '//header[@class="cluster-header"]/a',
                '//div[@id="iqadtile4"]',
            ),
        ),
    ),
);
