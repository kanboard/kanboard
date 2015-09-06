<?php

require_once __DIR__.'/../Base.php';

use Helper\Asset;
use Model\Config;

class AssetHelperTest extends Base
{
    public function testCustomCss()
    {
        $h = new Asset($this->container);
        $c = new Config($this->container);

        $this->assertEmpty($h->customCss());

        $this->assertTrue($c->save(array('application_stylesheet' => 'p { color: red }')));

        $this->assertEquals('<style>p { color: red }</style>', $h->customCss());
    }
}
