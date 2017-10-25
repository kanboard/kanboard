<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://milwaukeenns.org/2016/01/08/united-way-grant-enables-sdc-to-restore-free-tax-assistance-program/',
            'body' => array(
                '//div[@class="pf-content"]',
            ),
            'strip' => array(
                '//div[@class="printfriendly"]',
            ),
        ),
    ),
);
