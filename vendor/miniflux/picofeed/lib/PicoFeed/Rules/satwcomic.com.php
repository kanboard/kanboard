<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://satwcomic.com/day-at-the-beach',
            'body' => array(
                '//div[@class="container"]/center/a/img',
                '//span[@itemprop="articleBody"]',
            ),
            'strip' => array(),
        ),
    ),
);
