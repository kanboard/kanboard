<?php
return array(
    'grabber' => array(
        '%/comics.*%' => array(
            'test_url' => 'http://www.vgcats.com/comics/?strip_id=358',
            'body' => array('//*[@align="center"]/img'),
            'strip' => array(),
        ),
        '%/super.*%' => array(
            'test_url' => 'http://www.vgcats.com/super/?strip_id=84',
            'body' => array('//*[@align="center"]/p/img'),
            'strip' => array(),
        ),
    ),
);
