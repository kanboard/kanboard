<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'https://www.nasa.gov/image-feature/jpl/pia20514/coy-dione',
            'body' => array(
                '//div[@class="article-body"]',
            ),
            'strip' => array(
                '//div[@class="title-bar"]',
            ),
        ),
    ),
);
