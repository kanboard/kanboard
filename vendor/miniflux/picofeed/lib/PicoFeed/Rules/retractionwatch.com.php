<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://retractionwatch.com/2015/11/12/psychologist-jens-forster-settles-case-by-agreeing-to-2-retractions/',
            'body' => array(
                '//*[@class="main"]',
                '//*[@class="entry-content"]',
            ),
            'strip' => array(
	        '//*[contains(@class, "sharedaddy")]', 
                '//*[contains(@class, "jp-relatedposts")]',
                '//p[@class="p1"]',
            )
        )
    )
);

