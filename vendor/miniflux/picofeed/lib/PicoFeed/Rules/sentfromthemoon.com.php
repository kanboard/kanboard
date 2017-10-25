<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'body' => array(
                '//div[@class="comicpane"]/a/img',
                '//div[@class="entry"]',
            ),
            'strip' => array(),
            'test_url' => 'http://sentfromthemoon.com/archives/1417',
        ),
    ),
    'filter' => array(
        '%.*%' => array(
            '%title="(.+)" */>%' => '/><br/>$1',
        ),
    ),
);
