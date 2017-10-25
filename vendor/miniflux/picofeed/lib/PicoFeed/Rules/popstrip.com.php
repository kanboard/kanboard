<?php
return array(
    'filter' => array(
        '%.*%' => array(
            '%(<img.+/s/[^"]+/)(.+)%' => '$1$2$1bonus.png"/>',
        ),
    ),
);
