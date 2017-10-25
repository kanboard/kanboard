<?php
return array(
    'grabber' => array(
		 '%^/products.*%' => array(
            'test_url' => 'http://www.cnet.com/products/fibaro-flood-sensor/#ftag=CADf328eec',
            'body' => array(
                '//li[contains(@class,"slide first"] || //figure[contains(@class,(promoFigure))]',
                '//div[@class="quickInfo"]',
                '//div[@class="col-6 ratings"]',
                '//div[@id="editorReview"]',
            ),
            'strip' => array(
                '//script',
                '//a[@class="clickToEnlarge"]',
                '//div[@section="topSharebar"]',
                '//div[contains(@class,"related")]',
                '//div[contains(@class,"ad-")]',
                '//div[@section="shortcodeGallery"]',
            ),
        ),
        '%.*%' => array(
            'test_url' => 'http://cnet.com.feedsportal.com/c/34938/f/645093/s/4a340866/sc/28/l/0L0Scnet0N0Cnews0Cman0Eclaims0Eonline0Epsychic0Emade0Ehim0Ebuy0E10Emillion0Epowerball0Ewinning0Eticket0C0Tftag0FCAD590Aa51e/story01.htm',
            'body' => array(
            '//p[@itemprop="description"]',
            '//div[@itemprop="articleBody"]',
            ),
            'strip' => array(
                '//script',
                '//a[@class="clickToEnlarge"]',
                '//div[@section="topSharebar"]',
                '//div[contains(@class,"related")]',
                '//div[contains(@class,"ad-")]',
                '//div[@section="shortcodeGallery"]',
            ),
        ),
    ),
);
