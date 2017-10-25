<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://onmilwaukee.com/movies/articles/downerspelunking.html',
            'body' => array(
                '//article[contains(@class, "show")]',
            ),
            'strip' => array(
                '//h1',
                '//div[contains(@class,"-ad")]',
                '//div[contains(@class,"_ad")]',
                '//div[@id="pub_wrapper"]',
                '//div[contains(@class,"share_tools")]',
                '//div[@class="clearfix"]',
                '//div[contains(@class,"image_control")]',
                '//section[@class="ribboned"]',
                '//div[contains(@class,"sidebar")]',
                '//aside[@class="article_tag_list"]',
                '//section[contains(@id,"more_posts")]'
            ),
        ),
    ),
);
