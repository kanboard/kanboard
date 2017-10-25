<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.bdgest.com/chronique-6027-BD-Adrastee-Tome-2.html',
            'body' => array(
                '//*[contains(@class, "chronique")]',
            ),
            'strip' => array(
                '//*[contains(@class, "post-review")]',
                '//*[contains(@class, "footer-review")]',
            ),
        ),
    ),
);
