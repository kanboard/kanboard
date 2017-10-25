<?php
return array(
    'grabber' => array(
        '%/archives.*%' => array(
            'test_url' => 'http://niceteethcomic.com/archives/page119/',
            'body' => array('//*[@class="comicpane"]/a/img'),
            'strip' => array(),
        ),
    ),
);
