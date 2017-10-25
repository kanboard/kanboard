<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.monsieur-le-chien.fr/index.php?planche=672',
            'body' => array(
                '//img[starts-with(@src, "i/planches/")]',
            ),
        )
    )
);
