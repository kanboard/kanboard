<?php

require_once __DIR__.'/Base.php';

class LocaleTest extends Base
{
    public function testLocales()
    {
        foreach(glob('app/Locale/*') as $l)
        {
            $locale = require($l . '/translations.php');
            foreach($locale as $k => $v)
            {
                if(strpos($k,'%B %e, %Y') !== false)
                    continue;
                if(strpos($k,'%b %e, %Y') !== false)
                    continue;
                foreach(array('%s', '%d') as $placeholder)
                {
                    $this->assertEquals(
                        substr_count($k, '%s'),
                        substr_count($v, '%s'),
                        'Incorrect number of ' . $placeholder . ' in ' . basename($l) .' translation of: ' . $k
                    );
                }
            }
        }
    }
}
