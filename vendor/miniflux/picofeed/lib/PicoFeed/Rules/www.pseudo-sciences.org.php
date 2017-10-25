<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.pseudo-sciences.org/spip.php?article2275',
            'body' => array(
                '//div[@id="art_main"]',
            ),
            'strip' => array(
                '//div[@id="art_print"]',
                '//div[@id="art_chapo"]',
                '//img[@class="puce"]',
            ),
        ),
    ),
);
