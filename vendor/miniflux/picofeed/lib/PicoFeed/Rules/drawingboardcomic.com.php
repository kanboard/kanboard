<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'body' => array('//img[@id="comicimage"]'),
            'strip' => array(),
            'test_url' => 'http://drawingboardcomic.com/index.php?comic=208',
        ),
    ),
    'filter' => array(
        '%.*%' => array(
            '%title="(.+)" */>%' => '/><br/>$1',
        ),
    ),
);
