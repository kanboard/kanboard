<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => array(
                'http://www.legorafi.fr/2016/12/16/gorafi-magazine-bravo-vous-avez-bientot-presque-survecu-a-2016/',
                'http://www.legorafi.fr/2016/12/15/manuel-valls-promet-quune-fois-elu-il-debarrassera-la-france-de-manuel-valls/',
            ),
            'body' => array(
                '//section[@id="banner_magazine"]',
                '//figure[@class="main_picture"]',
                '//div[@class="content"]',
            ),
            'strip' => array(
                '//figcaption',
                '//div[@class="sharebox"]',
                '//div[@class="tags"]',
                '//section[@class="taboola_article"]',
            ),
        ),
    ),
);
