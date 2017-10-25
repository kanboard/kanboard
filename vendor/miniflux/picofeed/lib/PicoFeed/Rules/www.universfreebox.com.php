<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.universfreebox.com/article/24305/4G-Bouygues-Telecom-lance-une-vente-flash-sur-son-forfait-Sensation-3Go',
            'body' => array(
                '//div[@id="corps_corps"]',
            ),
            'strip' => array(
                '//*[@id="formulaire"]',
                '//*[@id="commentaire"]',
            ),
        ),
    ),
);
