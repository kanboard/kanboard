<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.vuxml.org/freebsd/a5f160fa-deee-11e4-99f8-080027ef73ec.html',
            'body' => array(
                '//body',
            ),
            'strip' => array(
                '//h1',
                '//div[@class="blurb"]',
                '//hr',
                '//p[@class="copyright"]',
            ),
        ),
    ),
);
