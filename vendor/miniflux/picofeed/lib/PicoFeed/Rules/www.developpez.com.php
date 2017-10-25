<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.developpez.com/actu/81757/Mozilla-annonce-la-disponibilite-de-Firefox-36-qui-passe-au-HTTP-2-et-permet-la-synchronisation-de-son-ecran-d-accueil/',
            'body' => array(
                '//*[@itemprop="articleBody"]',
            ),
            'strip' => array(
                '//form',
                '//div[@class="content"]/img',
                '//a[last()]/following-sibling::*',
                '//*[contains(@class,"actuTitle")]',
                '//*[contains(@class,"date")]',
                '//*[contains(@class,"inlineimg")]',
                '//*[@id="signaler"]',
                '//*[@id="signalerFrame"]',
            ),
        ),
    ),
);
