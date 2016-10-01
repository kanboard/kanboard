<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectMetadataModel;

class ProjectMetadataTest extends Base
{
    public function testOperations()
    {
        $projectModel = new ProjectModel($this->container);
        $projectMetadataModel = new ProjectMetadataModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'project #1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'project #2')));

        $this->assertTrue($projectMetadataModel->save(1, array('key1' => 'value1')));
        $this->assertTrue($projectMetadataModel->save(1, array('key1' => 'value2')));
        $this->assertTrue($projectMetadataModel->save(2, array('key1' => 'value1')));
        $this->assertTrue($projectMetadataModel->save(2, array('key2' => 'value2')));

        $this->assertEquals('value2', $projectMetadataModel->get(1, 'key1'));
        $this->assertEquals('value1', $projectMetadataModel->get(2, 'key1'));
        $this->assertEquals('', $projectMetadataModel->get(2, 'key3'));
        $this->assertEquals('default', $projectMetadataModel->get(2, 'key3', 'default'));

        $this->assertTrue($projectMetadataModel->exists(2, 'key1'));
        $this->assertFalse($projectMetadataModel->exists(2, 'key3'));

        $this->assertEquals(array('key1' => 'value2'), $projectMetadataModel->getAll(1));
        $this->assertEquals(array('key1' => 'value1', 'key2' => 'value2'), $projectMetadataModel->getAll(2));

        $this->assertTrue($projectMetadataModel->remove(2, 'key1'));
        $this->assertFalse($projectMetadataModel->remove(2, 'key1'));

        $this->assertEquals(array('key2' => 'value2'), $projectMetadataModel->getAll(2));
    }

    public function testAutomaticRemove()
    {
        $projectModel = new ProjectModel($this->container);
        $projectMetadataModel = new ProjectMetadataModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'project #1')));
        $this->assertTrue($projectMetadataModel->save(1, array('key1' => 'value1')));

        $this->assertTrue($projectMetadataModel->exists(1, 'key1'));
        $this->assertTrue($projectModel->remove(1));
        $this->assertFalse($projectMetadataModel->exists(1, 'key1'));
    }

    public function testDuplicate()
    {
        $projectModel = new ProjectModel($this->container);
        $projectMetadataModel = new ProjectMetadataModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'project #1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'project #2')));

        $this->assertTrue($projectMetadataModel->save(1, array('key1' => 'value1', 'key2' => 'value2')));
        $this->assertTrue($projectMetadataModel->duplicate(1, 2));

        $this->assertEquals(array('key1' => 'value1', 'key2' => 'value2'), $projectMetadataModel->getAll(2));
    }
}
