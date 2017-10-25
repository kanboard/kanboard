<?php
return array(
    'grabber' => array(
        '%.%/picture-gallery/%' => array(
            'test_url' => 'http://www.jsonline.com/picture-gallery/news/local/milwaukee/2017/02/22/photos-aclu-sues-milwaukee-police-over-profiling-stop-and-frisk/98250836/',
            'body' => array(
                '//div[@class="priority-asset-gallery galleries standalone hasendslate"]',
            ),
            'strip' => array(
                '//div[@class="buy-photo-btn"]',
                '//div[@class="gallery-thumbs thumbs pag-thumbs")]',
            ),
        ),
        '%.*%' => array(
            'test_url' => 'http://www.jsonline.com/news/usandworld/as-many-as-a-million-expected-for-popes-last-mass-in-us-b99585180z1-329688131.html',
            'body' => array(
                '//div[@itemprop="articleBody"]',
            ),
            'strip' => array(
                '//h1',
                '//iframe',
                '//span[@class="mycapture-small-btn mycapture-btn-with-text mycapture-expandable-photo-btn-small js-mycapture-btn-small"]',
                '//div[@class="close-wrap"]',
                '//div[contains(@class,"ui-video-wrapper")]',
                '//div[contains(@class,"media-mob")]',
                '//div[contains(@class,"left-mob")]',
                '//div[contains(@class,"nerdbox")]',
                '//p/span',
                '//div[contains(@class,"oembed-asset")]',
                '//*[contains(@class,"share")]',
                '//div[contains(@class,"gallery-asset")]',
                '//div[contains(@class,"oembed-asset")]',
                '//div[@class="article-print-url"]',
            ),
        ),
    ),
);
