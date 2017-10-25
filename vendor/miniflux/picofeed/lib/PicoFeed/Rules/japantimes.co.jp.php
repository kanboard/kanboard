<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.japantimes.co.jp/news/2015/09/27/world/social-issues-world/pope-meets-sex-abuse-victims-philadelphia-promises-accountability/',
            'body' => array(
            '//article[@role="main"]',
            ),
            'strip' => array(
            '//script',
            '//header',
            '//div[contains(@class, "meta")]',
            '//div[@class="clearfix"]',
            '//div[@class="OUTBRAIN"]',
            '//ul[@id="content_footer_menu"]',
            '//div[@class="article_footer_ad"]',
            '//div[@id="disqus_thread"]',
            ),
        ),
    ),
);
