<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'https://www.muckrock.com/news/archives/2016/jan/13/5-concerns-private-prisons/',
            'body' => array(
                '//div[@class="content"]',
            ),
            'strip' => array(
                '//div[@class="newsletter-widget"]',
                '//div[@class="contributors"]',
                '//time',
                '//h1',
                '//div[@class="secondary"]',
                '//aside',
                '//div[@class="articles__related"]'
            ),
        ),
    ),
);
