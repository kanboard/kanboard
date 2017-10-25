<?php
return array(
    'grabber' => array(
        '%/articles/view/comicsandcosplay/comics/critical-miss.*%' => array(
            'body' => array('//*[@class="body"]/span/img | //div[@class="folder_nav_links"]/following::p'),
            'test_url' => 'http://www.escapistmagazine.com/articles/view/comicsandcosplay/comics/critical-miss/13776-Critical-Miss-on-Framerates?utm_source=rss&amp;utm_medium=rss&amp;utm_campaign=articles',
            'strip' => array(),
        ),
        '%/articles/view/comicsandcosplay/comics/namegame.*%' => array(
            'body' => array('//*[@class="body"]/span/p/img[@height != "120"]'),
            'test_url' => 'http://www.escapistmagazine.com/articles/view/comicsandcosplay/comics/namegame/9759-Leaving-the-Nest?utm_source=rss&amp;utm_medium=rss&amp;utm_campaign=articles',
            'strip' => array(),
        ),
        '%/articles/view/comicsandcosplay/comics/stolen-pixels.*%' => array(
            'body' => array('//*[@class="body"]/span/p[2]/img'),
            'test_url' => 'http://www.escapistmagazine.com/articles/view/comicsandcosplay/comics/stolen-pixels/8866-Stolen-Pixels-258-Where-the-Boys-Are?utm_source=rss&amp;utm_medium=rss&amp;utm_campaign=articles',
            'strip' => array(),
        ),
        '%/articles/view/comicsandcosplay/comics/bumhugparade.*%' => array(
            'body' => array('//*[@class="body"]/span/p[2]/img'),
            'test_url' => 'http://www.escapistmagazine.com/articles/view/comicsandcosplay/comics/bumhugparade/8262-Bumhug-Parade-13?utm_source=rss&amp;utm_medium=rss&amp;utm_campaign=articles',
            'strip' => array(),
        ),
        '%/articles/view/comicsandcosplay.*/comics/escapistradiotheater%' => array(
            'body' => array('//*[@class="body"]/span/p[2]/img'),
            'test_url' => 'http://www.escapistmagazine.com/articles/view/comicsandcosplay/comics/escapistradiotheater/8265-The-Escapist-Radio-Theater-13?utm_source=rss&amp;utm_medium=rss&amp;utm_campaign=articles',
            'strip' => array(),
        ),
        '%/articles/view/comicsandcosplay/comics/paused.*%' => array(
            'body' => array('//*[@class="body"]/span/p[2]/img | //*[@class="body"]/span/div/img'),
            'test_url' => 'http://www.escapistmagazine.com/articles/view/comicsandcosplay/comics/paused/8263-Paused-16?utm_source=rss&amp;utm_medium=rss&amp;utm_campaign=articles',
            'strip' => array(),
        ),
        '%/articles/view/comicsandcosplay/comics/fraughtwithperil.*%' => array(
            'body' => array('//*[@class="body"]'),
            'test_url' => 'http://www.escapistmagazine.com/articles/view/comicsandcosplay/comics/fraughtwithperil/12166-The-Escapist-Presents-Escapist-Comics-Critical-Miss-B-lyeh-Fhlop?utm_source=rss&amp;utm_medium=rss&amp;utm_campaign=articles',
            'strip' => array(),
        ),
        '%/articles/view/video-games/columns/.*%' => array(
            'body' => array('//*[@id="article_content"]'),
            'test_url' => 'http://www.escapistmagazine.com/articles/view/video-games/columns/experienced-points/13971-What-50-Shades-and-Batman-Have-in-Common.2',
            'strip' => array(),
        ),
    ),
);
