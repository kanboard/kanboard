<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Project;
use Kanboard\Model\ProjectMetadata;

class ProjectMetadataTest extends Base
{
    public function testOperations()
    {
        $p = new Project($this->container);
        $pm = new ProjectMetadata($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'project #1')));
        $this->assertEquals(2, $p->create(array('name' => 'project #2')));

        $this->assertTrue($pm->save(1, array('key1' => 'value1')));
        $this->assertTrue($pm->save(1, array('key1' => 'value2')));
        $this->assertTrue($pm->save(2, array('key1' => 'value1')));
        $this->assertTrue($pm->save(2, array('key2' => 'value2')));

        $this->assertEquals('value2', $pm->get(1, 'key1'));
        $this->assertEquals('value1', $pm->get(2, 'key1'));
        $this->assertEquals('', $pm->get(2, 'key3'));
        $this->assertEquals('default', $pm->get(2, 'key3', 'default'));

        $this->assertTrue($pm->exists(2, 'key1'));
        $this->assertFalse($pm->exists(2, 'key3'));

        $this->assertEquals(array('key1' => 'value2'), $pm->getAll(1));
        $this->assertEquals(array('key1' => 'value1', 'key2' => 'value2'), $pm->getAll(2));
    }

    public function testAutomaticRemove()
    {
        $p = new Project($this->container);
        $pm = new ProjectMetadata($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'project #1')));
        $this->assertTrue($pm->save(1, array('key1' => 'value1')));

        $this->assertTrue($pm->exists(1, 'key1'));
        $this->assertTrue($p->remove(1));
        $this->assertFalse($pm->exists(1, 'key1'));
    }
}
