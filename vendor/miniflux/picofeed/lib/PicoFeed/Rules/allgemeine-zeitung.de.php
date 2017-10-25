<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.allgemeine-zeitung.de/lokales/polizei/mainz-gonsenheim-unbekannte-rauben-esso-tankstelle-in-kurt-schumacher-strasse-aus_14913147.htm',
            'body' => array(
                '//div[contains(@class, "article")][1]',
            ),
            'strip' => array(
                '//read/h1',
                '//*[@id="t-map"]',
                '//*[contains(@class, "modules")]',
                '//*[contains(@class, "adsense")]',
                '//*[contains(@class, "linkbox")]',
                '//*[contains(@class, "info")]',
                '//*[@class="skip"]',
                '//*[@class="funcs"]',
                '//span[@class="nd address"]',
                '//a[contains(@href, "abo-und-services")]',
            ),
        ),
    ),
);
