<?php

namespace KanboardTests\units\Core;

use KanboardTests\units\Base;
use Kanboard\Core\Helper;

class HelperTest extends Base
{
    public function testRegister()
    {
        $helper = new Helper($this->container);
        $helper->register('foobar', '\Stdclass');

        $this->assertInstanceOf('Stdclass', $helper->foobar);
        $this->assertInstanceOf('Stdclass', $helper->getHelper('foobar'));
    }
}
