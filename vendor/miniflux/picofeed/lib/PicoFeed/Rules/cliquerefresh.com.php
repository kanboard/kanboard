<?php
return array(
    'grabber' => array(
        '%/comic.*%' => array(
            'test_url' => 'http://cliquerefresh.com/comic/078-stating-the-obvious/',
            'body' => array('//div[@class="comicImg"]/img | //div[@class="comicImg"]/a/img'),
            'strip' => array(),
        ),
    ),
);
