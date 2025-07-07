<?php

namespace KanboardTests\units\Helper;

use KanboardTests\units\Base;
use Kanboard\Helper\AssetHelper;
use Kanboard\Model\ConfigModel;

class AssetHelperTest extends Base
{
    public function testCustomCss()
    {
        $h = new AssetHelper($this->container);
        $c = new ConfigModel($this->container);

        $this->assertEmpty($h->customCss());

        $this->assertTrue($c->save(array('application_stylesheet' => 'p { color: red }')));
        $this->container['memoryCache']->flush();

        $this->assertEquals('<style>p { color: red }</style>', $h->customCss());
    }
}
