<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'body' => array(
                '//div[@class="comicpane"]/a/img',
                '//div[@class="entry"]',
            ),
            'strip' => array(),
            'test_url' => 'http://thedoghousediaries.com/6023',
        ),
    ),
    'filter' => array(
        '%.*%' => array(
            '%title="(.+)" */>%' => '/><br/>$1',
        ),
    ),
);
