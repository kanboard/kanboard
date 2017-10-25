<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.moya-planeta.ru/travel/view/chto_yaponcu_horosho_russkomu_ne_ponyat_20432/',
            'body' => array(
                '//div[@class="full_object"]',
            ),
            'strip' => array(
                '//div[@class="full_object_panel object_panel"]',
                '//div[@class="full_object_panel_geo object_panel"]',
                '//div[@class="full_object_title"]',
                '//div[@class="full_object_social_likes"]',
                '//div[@class="full_object_planeta_likes"]',
                '//div[@class="full_object_go2comments"]',
                '//div[@id="yandex_ad_R-163191-3"]',
                '//div[@class="full_object_shop_article_recommend"]',
            ),
        ),
    ),
);
