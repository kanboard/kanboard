<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'https://ymatuhin.ru/tools/git-default-editor/',
            'body' => array(
                '//section',
            ),
            'strip' => array(
              "//script",
              "//style",
              "//h1",
              "//time",
              "//aside",
              "/html/body/section/ul",
              "//amp-iframe",
              "/html/body/section/h4"          
            ),
        )
    )
);
