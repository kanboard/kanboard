<?php
return array(
    'grabber' => array(
        '%/index.php.*comic=.*%' => array(
            'test_url' => 'http://www.awkwardzombie.com/index.php?comic=041315',
            'body' => array('//*[@id="comic"]/img'),
            'strip' => array(),
        ),
    ),
);
