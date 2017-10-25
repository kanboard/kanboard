<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.nextinpact.com/news/101122-3d-nand-intel-lance-six-nouvelles-gammes-ssd-pour-tous-usages.htm',
            'body' => array(
                '//div[@class="container_article"]',
            ),
            'strip' => array(
                '//div[@class="infos_article"]',
                '//div[@id="actu_auteur"]',
                '//div[@id="soutenir_journaliste"]',
                '//section[@id="bandeau_abonnez_vous"]',
                '//br'
            ),
        ),
    ),
);
