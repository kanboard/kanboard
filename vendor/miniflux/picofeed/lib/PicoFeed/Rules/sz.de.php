<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://sz.de/1.2443161',
            'body' => array('//article[@id="sitecontent"]/section[@class="topenrichment"]//img | //article[@id="sitecontent"]/section[@class="body"]/section[@class="authors"]/preceding-sibling::*[not(contains(@class, "ad"))]'),
            'strip' => array(),
        ),
    ),
);
