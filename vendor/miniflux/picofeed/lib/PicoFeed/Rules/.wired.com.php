<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.wired.com/gamelife/2013/09/ouya-free-the-games/',
            'body' => array(
                '//div[@data-js="gallerySlides"]',
                 '//div[starts-with(@class,"post")]',
            ),
            'strip' => array(
                '//h1',
                '//nav',
                '//button',
                '//figure[starts-with(@class,"rad-slide")]',
                '//figure[starts-with(@class,"end-slate")]',
                '//div[contains(@class,"mobile-")]',
                '//div[starts-with(@class,"mob-gallery-launcher")]',
                '//div[contains(@id,"mobile-")]',
                '//span[contains(@class,"slide-count")]',
                '//div[contains(@class,"show-ipad")]',
                '//img[contains(@id,"-hero-bg")]',
                '//div[@data-js="overlayWrap"]',
                '//ul[contains(@class,"metadata")]',
                '//div[@class="opening center"]',
                '//p[contains(@class="byline-mob"]',
                '//div[@id="o-gallery"]',
                '//div[starts-with(@class,"sm-col")]',
                '//div[contains(@class,"pad-b-huge")]',
                '//a[contains(@class,"visually-hidden")]',
                '//*[@class="social"]',
                '//i',
                '//div[@data-js="mobGalleryAd"]',
                '//div[contains(@class,"footer")]',
                '//div[contains(@data-js,"fader")]',
                '//div[@id="sharing"]',
                '//div[contains(@id,"related")]',
                '//div[@id="most-pop"]',
                '//ul[@id="article-tags"]',
                '//style',
                '//section[contains(@class,"footer")]'
            ),
        )
    )
);
