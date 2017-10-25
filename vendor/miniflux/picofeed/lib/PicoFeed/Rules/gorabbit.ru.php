<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://gorabbit.ru/article/10-oshchushcheniy-za-rulem-kogda-tolko-poluchil-voditelskie-prava',
            'body' => array(
                '//div[@class="detail_text"]',
            ),
            'strip' => array(
                '//script',
                '//form',
                '//style',
                '//div[@class="socials"]',
                '//div[@id="cr_1"]',
                '//div[@class="related_items"]',
            ),
        ),
    ),
);
