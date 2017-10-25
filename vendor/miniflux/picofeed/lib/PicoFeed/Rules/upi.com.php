<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.upi.com/Top_News/US/2015/09/26/Tech-giants-Hollywood-stars-among-guests-at-state-dinner-for-Chinas-Xi-Jinping/4541443281006/',
            'body' => array(
                '//div[@class="img"]',
                '//div/article[@itemprop="articleBody"]',
            ),
            'strip' => array(
                '//div[@align="center"]',
            ),
        ),
    ),
);
