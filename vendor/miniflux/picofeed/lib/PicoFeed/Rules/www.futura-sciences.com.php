<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.futura-sciences.com/magazines/espace/infos/actu/d/astronautique-curiosity-franchi-succes-dune-dingo-gap-52289/#xtor=RSS-8',
            'body' => array(
                '//div[contains(@class, "content fiche-")]',
            ),
            'strip' => array(
                '//h1',
                '//*[contains(@class, "content-date")]',
                '//*[contains(@class, "diaporama")]',
                '//*[contains(@class, "slider")]',
                '//*[contains(@class, "cartouche")]',
                '//*[contains(@class, "noprint")]',
            ),
        ),
    ),
);
