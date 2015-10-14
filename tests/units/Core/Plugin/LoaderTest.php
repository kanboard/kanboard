<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Plugin\Loader;

class LoaderTest extends Base
{
    public function testGetSchemaVersion()
    {
        $p = new Loader($this->container);
        $this->assertEquals(0, $p->getSchemaVersion('not_found'));

        $this->assertTrue($p->setSchemaVersion('plugin1', 1));
        $this->assertEquals(1, $p->getSchemaVersion('plugin1'));

        $this->assertTrue($p->setSchemaVersion('plugin2', 33));
        $this->assertEquals(33, $p->getSchemaVersion('plugin2'));

        $this->assertTrue($p->setSchemaVersion('plugin1', 2));
        $this->assertEquals(2, $p->getSchemaVersion('plugin1'));
    }
}
