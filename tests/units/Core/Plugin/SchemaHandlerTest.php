<?php

namespace KanboardTests\units\Core\Plugin;

use KanboardTests\units\Base;
use Kanboard\Core\Plugin\SchemaHandler;

class SchemaHandlerTest extends Base
{
    public function testGetSchemaVersion()
    {
        $p = new SchemaHandler($this->container);
        $this->assertEquals(0, $p->getSchemaVersion('not_found'));

        $this->assertTrue($p->setSchemaVersion('plugin1', 1));
        $this->assertEquals(1, $p->getSchemaVersion('plugin1'));

        $this->assertTrue($p->setSchemaVersion('plugin2', 33));
        $this->assertEquals(33, $p->getSchemaVersion('plugin2'));

        $this->assertTrue($p->setSchemaVersion('plugin1', 2));
        $this->assertEquals(2, $p->getSchemaVersion('plugin1'));
    }
}
