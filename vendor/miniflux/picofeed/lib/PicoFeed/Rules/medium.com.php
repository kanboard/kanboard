<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'https://medium.com/lessons-learned/917b8b63ae3e',
            'body' => array(
                '//div[@class="section-content"]',
            ),
            'strip' => array(
                '//div[contains(@class,"metabar")]',
                '//img[contains(@class,"thumbnail")]',
                '//h1',
                '//blockquote',
                '//div[@class="aspectRatioPlaceholder-fill"]',
                '//footer'
            ),
        ),
    ),
);
