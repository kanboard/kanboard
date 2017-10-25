<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.subtraction.com/2015/06/06/time-lapse-video-of-one-world-trade-center/',
            'body' => array('//article/div[@class="entry-content"]'),
            'strip' => array(),
        ),
    ),
    'filter' => array(
        '%.*%' => array(
            '%\+<h3.*/ul>%' => '',
        ),
    ),
);
