<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.crash.net/motogp/interview/247550/1/exclusive-andrea-dovizioso-interview.html',
            'body' => array(
                '//div[@id="content"]',
            ),
            'strip' => array(
                '//script',
                '//style',
                '//*[@title="Social Networking"]',
                '//*[@class="crash-ad2"]',
                '//*[@class="clearfix"]',
                '//*[@class="crash-ad2"]',
                '//*[contains(@id, "divCB"]',
                '//*[@class="pnlComment"]',
                '//*[@class="comments-tabs"]',
                '//*[contains(@class, "ad-twocol"]',
                '//*[@class="stories-list"]',
                '//*[contains(@class, "btn")]',
                '//*[@class="content"]',
                '//h3',
            ),
        ),
    ),
);

