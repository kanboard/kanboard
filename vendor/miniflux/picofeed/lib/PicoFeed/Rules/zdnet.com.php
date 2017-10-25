<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://zdnet.com.feedsportal.com/c/35462/f/675637/s/4a33c93e/sc/11/l/0L0Szdnet0N0Carticle0Cchina0Eus0Eagree0Eon0Ecybercrime0Ecooperation0Eamid0Econtinued0Etension0C0Tftag0FRSSbaffb68/story01.htm',
            'body' => array(
                '//p[@class="summary"]',
                '//div[contains(@class,"storyBody")]',
            ),
            'strip' => array(
                '//*[contains(@class,"ad-")]',
                '//p/span',
                '//script',
                '//p[@class="summary"]',
                '//div[contains(@class,"relatedContent")]',
                '//div[contains(@class,"loader")]',
                '//p[@class="photoDetails"]',
                '//div[@class="thumbnailSlider"]',
                '//div[@class="shortcodeGalleryWrapper"]',
            ),
        ),
    ),
);
