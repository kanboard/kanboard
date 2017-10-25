<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'https://publicpolicyforum.org/blog/going-extra-mile',
            'body' => array(
                '//div[contains(@class,"field-name-post-date")]',
                '//div[contains(@class,"field-name-body")]',
            ),
            'strip' => array(
                '//img[@src="http://publicpolicyforum.org/sites/default/files/logo3.jpg"]',
            ),
        ),
    ),
);
