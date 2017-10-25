<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'body' => array('//a[@class="comic"]/img'),
            'strip' => array(),
            'test_url' => 'http://www.exocomics.com/379',
        ),
    ),
    'filter' => array(
        '%.*%' => array(
            '%title="(.+)" */>%' => '/><br/>$1',
        ),
    ),
);
