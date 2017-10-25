<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.chinafile.com/books/shanghai-faithful?utm_source=feedburner&utm_medium=feed&utm_campaign=Feed%3A+chinafile%2FAll+%28ChinaFile%29',
            'body' => array(
            '//div[contains(@class,"pane-featured-photo-panel-pane-1")]',
            '//div[contains(@class,"video-above-fold")]',
            '//div[@class="sc-media"]',
            '//div[contains(@class,"field-name-body")]',
            ),
            'strip' => array(
                '//div[contains(@class,"cboxes")]',
                '//div[contains(@class,"l-middle")]',
            ),
        ),
    ),
);
