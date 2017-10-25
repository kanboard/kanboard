<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.nationaljournal.com/s/354962/south-carolina-evangelicals-outstrip-establishment?mref=home_top_main',
            'body' => array(
                '//div[@class="section-body"]',
            ),
            'strip' => array(
                '//*[contains(@class, "-related")]',
                '//*[contains(@class, "social")]',
            ),
        ),
    ),
);
