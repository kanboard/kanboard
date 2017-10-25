<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.csmonitor.com/USA/Politics/2015/0925/John-Boehner-steps-down-Self-sacrificing-but-will-it-lead-to-better-government',
            'body' => array(
                '//h2[@id="summary"]',
                '//div[@class="flex-video youtube"]',
                '//div[contains(@class,"eza-body")]',
            ),
            'strip' => array(
                '//span[@id="breadcrumb"]',
                '//div[@id="byline-wrapper"]',
                '//div[@class="injection"]',
                '//*[contains(@class,"promo_link")]',
            ),
        ),
    ),
);
