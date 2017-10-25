<?php
return array(
    'grabber' => array(
        '%^/news.*%' => array(
            'test_url' => 'http://www.adventuregamers.com/news/view/31079',
            'body' => array(
                '//div[@class="bodytext"]',
            )
        ),
        '%^/videos.*%' => array(
            'test_url' => 'http://www.adventuregamers.com/videos/view/31056',
            'body' => array(
                '//iframe',
            )
        ),
        '%^/articles.*%' => array(
            'test_url' => 'http://www.adventuregamers.com/articles/view/31049',
            'body' => array(
                '//div[@class="cleft"]',
            )
        )
    ),
);
