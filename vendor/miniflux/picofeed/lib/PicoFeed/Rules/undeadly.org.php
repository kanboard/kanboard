<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://undeadly.org/cgi?action=article&sid=20141101181155',
            'body' => array(
                '/html/body/table[3]/tbody/tr/td[1]/table[2]/tr/td[1]',
            ),
            'strip' => array(
                '//font',
            ),
        ),
    ),
);
