<?php
return array(
    'filter' => array(
        '%.*%' => array(
            '%http://www.twogag.com/comics-rss/([^.]+)\\.jpg%' => 'http://www.twogag.com/comics/$1.jpg',
        ),
    ),
);
