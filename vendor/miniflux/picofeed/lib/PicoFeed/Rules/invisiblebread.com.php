<?php
return array(
    'filter' => array(
        '%.*%' => array(
            '%(<img.+(\\d{4}-\\d{2}-\\d{2}).+/>)%' => '$1<img src="http://invisiblebread.com/eps/$2-extrapanel.png"/>',
        ),
    ),
);
