<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://fossbytes.com/fbi-hacked-1000-computers-to-shut-down-largest-child-pornography-site-on-the-dark-web/',
            'body' => array(
            '//div[@class="entry-inner"]',
            ),
            'strip' => array(
            '//*[@class="at-above-post addthis_default_style addthis_toolbox at-wordpress-hide"]',
            '//*[@class="at-below-post addthis_default_style addthis_toolbox at-wordpress-hide"]',
            '//*[@class="at-below-post-recommended addthis_default_style addthis_toolbox at-wordpress-hide"]',
            '//*[@class="code-block code-block-12 ai-desktop"]',
            '//*[@class="code-block code-block-13 ai-tablet-phone"]',
            ),
        ),
    ),
);
