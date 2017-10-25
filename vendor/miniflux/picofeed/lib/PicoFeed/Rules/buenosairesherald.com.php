<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.buenosairesherald.com/article/199344/manzur-named-next-governor-of-tucum%C3%A1n',
            'body' => array(
            '//div[@style="float:none"]',
            ),
            'strip' => array(
                '//div[contains(@class, "bz_alias_short_desc_container"]',
                '//td[@id="bz_show_bug_column_1"]',
                '//table[@id="attachment_table"]',
                '//table[@class="bz_comment_table"]',
            ),
        ),
    ),
);
