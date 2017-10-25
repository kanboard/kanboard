<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.treehugger.com/uncategorized/top-ten-posts-week-bunnies-2.html',
            'body' => array(
                '//div[contains(@class, "promo-image")]',
                '//div[contains(@id, "entry-body")]',
            ),
            'strip' => array(
            ),
        ),
    ),
);
