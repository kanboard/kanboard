<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Helper\Asset;
use Kanboard\Model\Config;

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
