<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.neustadt-ticker.de/41302/alltag/kultur/demo-auf-der-boehmischen',
            'body' => array(
                '//div[@class="entry-content"]',
            ),
            'strip' => array(
                '//*[contains(@class, "sharedaddy")]',
                '//*[contains(@class, "yarpp-related")]',
            ),
        ),
    ),
);
