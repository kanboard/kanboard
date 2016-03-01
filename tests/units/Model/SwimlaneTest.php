<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Project;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Swimlane;

class SwimlaneTest extends Base
{
    public function testCreation()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $s->create(array('project_id' => 1, 'name' => 'Swimlane #1')));

        $swimlanes = $s->getSwimlanes(1);
        $this->assertNotEmpty($swimlanes);
        $this->assertEquals(2, count($swimlanes));
        $this->assertEquals('Default swimlane', $swimlanes[0]['name']);
        $this->assertEquals('Swimlane #1', $swimlanes[1]['name']);

        $this->assertEquals(1, $s->getIdByName(1, 'Swimlane #1'));
        $this->assertEquals(0, $s->getIdByName(2, 'Swimlane #2'));

        $this->assertEquals('Swimlane #1', $s->getNameById(1));
        $this->assertEquals('', $s->getNameById(23));
    }

    public function testGetList()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $s->create(array('project_id' => 1, 'name' => 'Swimlane #1')));
        $this->assertEquals(2, $s->create(array('project_id' => 1, 'name' => 'Swimlane #2')));

        $swimlanes = $s->getList(1);
        $expected = array('Default swimlane', 'Swimlane #1', 'Swimlane #2');

        $this->assertEquals($expected, $swimlanes);
    }

    public function testUpdate()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $s->create(array('project_id' => 1, 'name' => 'Swimlane #1')));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals('Swimlane #1', $swimlane['name']);

        $this->assertTrue($s->update(array('id' => 1, 'name' => 'foobar')));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals('foobar', $swimlane['name']);
    }

    public function testUpdateDefaultSwimlane()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($s->updateDefault(array('id' => 1, 'default_swimlane' => 'foo', 'show_default_swimlane' => 1)));

        $default = $s->getDefault(1);
        $this->assertNotEmpty($default);
        $this->assertEquals('foo', $default['default_swimlane']);
        $this->assertEquals(1, $default['show_default_swimlane']);

        $this->assertTrue($s->updateDefault(array('id' => 1, 'default_swimlane' => 'foo', 'show_default_swimlane' => 0)));

        $default = $s->getDefault(1);
        $this->assertNotEmpty($default);
        $this->assertEquals('foo', $default['default_swimlane']);
        $this->assertEquals(0, $default['show_default_swimlane']);
    }

    public function testDisableEnableDefaultSwimlane()
    {
        $projectModel = new Project($this->container);
        $swimlaneModel = new Swimlane($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));

        $this->assertTrue($swimlaneModel->disableDefault(1));
        $default = $swimlaneModel->getDefault(1);
        $this->assertEquals(0, $default['show_default_swimlane']);

        $this->assertTrue($swimlaneModel->enableDefault(1));
        $default = $swimlaneModel->getDefault(1);
        $this->assertEquals(1, $default['show_default_swimlane']);
    }

    public function testDisableEnable()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $s->create(array('project_id' => 1, 'name' => 'Swimlane #1')));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $this->assertEquals(2, $s->getLastPosition(1));
        $this->assertTrue($s->disable(1, 1));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(0, $swimlane['is_active']);
        $this->assertEquals(0, $swimlane['position']);

        $this->assertEquals(1, $s->getLastPosition(1));

        // Create a new swimlane
        $this->assertEquals(2, $s->create(array('project_id' => 1, 'name' => 'Swimlane #2')));

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        // Enable our disabled swimlane
        $this->assertTrue($s->enable(1, 1));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);
    }

    public function testRemove()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $s->create(array('project_id' => 1, 'name' => 'Swimlane #1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'swimlane_id' => 1)));

        $task = $tf->getbyId(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['swimlane_id']);

        $this->assertTrue($s->remove(1, 1));

        $task = $tf->getbyId(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['swimlane_id']);

        $this->assertEmpty($s->getById(1));
    }

    public function testUpdatePositions()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $s->create(array('project_id' => 1, 'name' => 'Swimlane #1')));
        $this->assertEquals(2, $s->create(array('project_id' => 1, 'name' => 'Swimlane #2')));
        $this->assertEquals(3, $s->create(array('project_id' => 1, 'name' => 'Swimlane #3')));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);

        $swimlane = $s->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(3, $swimlane['position']);

        // Disable the 2nd swimlane
        $this->assertTrue($s->disable(1, 2));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(0, $swimlane['is_active']);
        $this->assertEquals(0, $swimlane['position']);

        $swimlane = $s->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);

        // Remove the first swimlane
        $this->assertTrue($s->remove(1, 1));

        $swimlane = $s->getById(1);
        $this->assertEmpty($swimlane);

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(0, $swimlane['is_active']);
        $this->assertEquals(0, $swimlane['position']);

        $swimlane = $s->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);
    }

    public function testDuplicateSwimlane()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'P1')));
        $this->assertEquals(2, $p->create(array('name' => 'P2')));
        $this->assertEquals(1, $s->create(array('project_id' => 1, 'name' => 'S1')));
        $this->assertEquals(2, $s->create(array('project_id' => 1, 'name' => 'S2')));
        $this->assertEquals(3, $s->create(array('project_id' => 1, 'name' => 'S3')));

        $default_swimlane1 = $s->getDefault(1);
        $default_swimlane1['default_swimlane'] = 'New Default';

        $this->assertTrue($s->updateDefault($default_swimlane1));

        $this->assertTrue($s->duplicate(1, 2));

        $swimlanes = $s->getAll(2);

        $this->assertCount(3, $swimlanes);
        $this->assertEquals(4, $swimlanes[0]['id']);
        $this->assertEquals('S1', $swimlanes[0]['name']);
        $this->assertEquals(5, $swimlanes[1]['id']);
        $this->assertEquals('S2', $swimlanes[1]['name']);
        $this->assertEquals(6, $swimlanes[2]['id']);
        $this->assertEquals('S3', $swimlanes[2]['name']);
        $new_default = $s->getDefault(2);
        $this->assertEquals('New Default', $new_default['default_swimlane']);
    }

    public function testChangePosition()
    {
        $projectModel = new Project($this->container);
        $swimlaneModel = new Swimlane($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $swimlaneModel->create(array('project_id' => 1, 'name' => 'Swimlane #1')));
        $this->assertEquals(2, $swimlaneModel->create(array('project_id' => 1, 'name' => 'Swimlane #2')));
        $this->assertEquals(3, $swimlaneModel->create(array('project_id' => 1, 'name' => 'Swimlane #3')));
        $this->assertEquals(4, $swimlaneModel->create(array('project_id' => 1, 'name' => 'Swimlane #4')));

        $swimlanes = $swimlaneModel->getAllByStatus(1);
        $this->assertEquals(1, $swimlanes[0]['position']);
        $this->assertEquals(1, $swimlanes[0]['id']);
        $this->assertEquals(2, $swimlanes[1]['position']);
        $this->assertEquals(2, $swimlanes[1]['id']);
        $this->assertEquals(3, $swimlanes[2]['position']);
        $this->assertEquals(3, $swimlanes[2]['id']);

        $this->assertTrue($swimlaneModel->changePosition(1, 3, 2));

        $swimlanes = $swimlaneModel->getAllByStatus(1);
        $this->assertEquals(1, $swimlanes[0]['position']);
        $this->assertEquals(1, $swimlanes[0]['id']);
        $this->assertEquals(2, $swimlanes[1]['position']);
        $this->assertEquals(3, $swimlanes[1]['id']);
        $this->assertEquals(3, $swimlanes[2]['position']);
        $this->assertEquals(2, $swimlanes[2]['id']);

        $this->assertTrue($swimlaneModel->changePosition(1, 2, 1));

        $swimlanes = $swimlaneModel->getAllByStatus(1);
        $this->assertEquals(1, $swimlanes[0]['position']);
        $this->assertEquals(2, $swimlanes[0]['id']);
        $this->assertEquals(2, $swimlanes[1]['position']);
        $this->assertEquals(1, $swimlanes[1]['id']);
        $this->assertEquals(3, $swimlanes[2]['position']);
        $this->assertEquals(3, $swimlanes[2]['id']);

        $this->assertTrue($swimlaneModel->changePosition(1, 2, 2));

        $swimlanes = $swimlaneModel->getAllByStatus(1);
        $this->assertEquals(1, $swimlanes[0]['position']);
        $this->assertEquals(1, $swimlanes[0]['id']);
        $this->assertEquals(2, $swimlanes[1]['position']);
        $this->assertEquals(2, $swimlanes[1]['id']);
        $this->assertEquals(3, $swimlanes[2]['position']);
        $this->assertEquals(3, $swimlanes[2]['id']);

        $this->assertTrue($swimlaneModel->changePosition(1, 4, 1));

        $swimlanes = $swimlaneModel->getAllByStatus(1);
        $this->assertEquals(1, $swimlanes[0]['position']);
        $this->assertEquals(4, $swimlanes[0]['id']);
        $this->assertEquals(2, $swimlanes[1]['position']);
        $this->assertEquals(1, $swimlanes[1]['id']);
        $this->assertEquals(3, $swimlanes[2]['position']);
        $this->assertEquals(2, $swimlanes[2]['id']);

        $this->assertFalse($swimlaneModel->changePosition(1, 2, 0));
        $this->assertFalse($swimlaneModel->changePosition(1, 2, 5));
    }

    public function testChangePositionWithInactiveSwimlane()
    {
        $projectModel = new Project($this->container);
        $swimlaneModel = new Swimlane($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $swimlaneModel->create(array('project_id' => 1, 'name' => 'Swimlane #1')));
        $this->assertEquals(2, $swimlaneModel->create(array('project_id' => 1, 'name' => 'Swimlane #2', 'is_active' => 0)));
        $this->assertEquals(3, $swimlaneModel->create(array('project_id' => 1, 'name' => 'Swimlane #3', 'is_active' => 0)));
        $this->assertEquals(4, $swimlaneModel->create(array('project_id' => 1, 'name' => 'Swimlane #4')));

        $swimlanes = $swimlaneModel->getAllByStatus(1);
        $this->assertEquals(1, $swimlanes[0]['position']);
        $this->assertEquals(1, $swimlanes[0]['id']);
        $this->assertEquals(2, $swimlanes[1]['position']);
        $this->assertEquals(4, $swimlanes[1]['id']);

        $this->assertTrue($swimlaneModel->changePosition(1, 4, 1));

        $swimlanes = $swimlaneModel->getAllByStatus(1);
        $this->assertEquals(1, $swimlanes[0]['position']);
        $this->assertEquals(4, $swimlanes[0]['id']);
        $this->assertEquals(2, $swimlanes[1]['position']);
        $this->assertEquals(1, $swimlanes[1]['id']);
    }
}
