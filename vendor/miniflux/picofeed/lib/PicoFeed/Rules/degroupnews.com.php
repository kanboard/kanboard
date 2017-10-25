<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.degroupnews.com/medias/vodsvod/amazon-concurrence-la-chromecast-de-google-avec-fire-tv-stick',
            'body' => array(
                '//div[@class="contenu"]',
            ),
            'strip' => array(
                '//div[contains(@class, "a2a")]',
            ),
        ),
    ),
);
