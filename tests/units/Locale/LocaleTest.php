<?php

require_once __DIR__.'/../Base.php';

class LocaleTest extends Base
{
    public function testLocales()
    {
        foreach (glob('app/Locale/*') as $file) {
            $locale = require($file . '/translations.php');

            foreach ($locale as $k => $v) {
                $this->assertNotEmpty($v, 'Empty value for the key "'.$k.'" in translation '.basename($file));

                foreach (array('%s', '%d') as $placeholder) {
                    $this->assertEquals(
                        substr_count($k, $placeholder),
                        substr_count($v, $placeholder),
                        'Incorrect number of ' . $placeholder . ' in ' . basename($file) .' translation of: ' . $k
                    );
                }
            }
        }
    }
}
