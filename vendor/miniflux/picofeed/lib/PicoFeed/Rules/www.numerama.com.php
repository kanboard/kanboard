<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.numerama.com/sciences/125959-recherches-ladn-recompensees-nobel-de-chimie.html',
            'body' => array(
                '//article',
            ),
            'strip' => array(
                '//footer',
                '//section[@class="related-article"]',
            ),
        ),
    ),
);
