<?php
return array(
    'grabber' => array(
        '%/cad/.+%' => array(
            'test_url' => 'http://www.cad-comic.com/cad/20150417',
            'body' => array(
                '//*[@id="content"]/img',
            ),
            'strip' => array(),
        ),
    ),
);
