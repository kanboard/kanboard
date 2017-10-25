<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.sciencemag.org/news/2016/01/could-bright-foamy-wak$',
            'body' => array(
                '//div[@class="row--hero"]',
                '//article[contains(@class,"primary")]',
            ),
            'strip' => array(
                '//header[@class="article__header"]',
                '//footer[@class="article__foot"]',
            ),
        ),
    )
);
