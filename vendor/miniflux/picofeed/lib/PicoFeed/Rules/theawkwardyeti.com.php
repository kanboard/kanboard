<?php
return array(
  'grabber' => array(
    '%/comic/.*%' => array(
      'test_url' => 'http://theawkwardyeti.com/comic/things-to-do/',
      'body' => array(
        '//div[@id="comic"]'
      ),
      'strip' => array()
    )
  )
);
