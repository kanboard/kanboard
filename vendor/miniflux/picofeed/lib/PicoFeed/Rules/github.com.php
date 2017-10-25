<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'https://github.com/audreyr/favicon-cheat-sheet',
            'body' => array(
                '//article[contains(@class, "entry-content")]',
            ),
            'strip' => array(
                '//h1',
            ),
        ),
    ),
);
