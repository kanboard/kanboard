<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\SwimlaneModel;

class SwimlaneModelTest extends Base
{
    public function testCreation()
    {
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertEquals(2, $swimlaneModel->create(1, 'Swimlane #1', '', 1));

        $swimlanes = $swimlaneModel->getAll(1);
        $this->assertNotEmpty($swimlanes);
        $this->assertEquals(2, count($swimlanes));
        $this->assertEquals('Default swimlane', $swimlanes[0]['name']);
        $this->assertEquals('Swimlane #1', $swimlanes[1]['name']);
        $this->assertEquals(0, $swimlanes[0]['task_limit']);
        $this->assertEquals(1, $swimlanes[1]['task_limit']);

        $this->assertEquals(2, $swimlaneModel->getIdByName(1, 'Swimlane #1'));
        $this->assertEquals(0, $swimlaneModel->getIdByName(2, 'Swimlane #2'));

        $this->assertEquals('Default swimlane', $swimlaneModel->getNameById(1));
        $this->assertEquals('Swimlane #1', $swimlaneModel->getNameById(2));
        $this->assertEquals('', $swimlaneModel->getNameById(23));
    }

    public function testGetFirstActiveSwimlane()
    {
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertEquals(2, $swimlaneModel->create(1, 'Swimlane #1'));
        $this->assertEquals(3, $swimlaneModel->create(1, 'Swimlane #2'));

        $this->assertTrue($swimlaneModel->disable(1, 2));

        $swimlane = $swimlaneModel->getFirstActiveSwimlane(1);
        $this->assertEquals(1, $swimlane['id']);
        $this->assertEquals('Default swimlane', $swimlane['name']);
        $this->assertSame(1, $swimlaneModel->getFirstActiveSwimlaneId(1));

        $this->assertTrue($swimlaneModel->disable(1, 1));

        $swimlane = $swimlaneModel->getFirstActiveSwimlane(1);
        $this->assertEquals(3, $swimlane['id']);
        $this->assertEquals('Swimlane #2', $swimlane['name']);
        $this->assertSame(3, $swimlaneModel->getFirstActiveSwimlaneId(1));

        $this->assertTrue($swimlaneModel->disable(1, 3));
        $this->assertNull($swimlaneModel->getFirstActiveSwimlane(1));
        $this->assertSame(0, $swimlaneModel->getFirstActiveSwimlaneId(1));
    }

    public function testGetList()
    {
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertEquals(2, $swimlaneModel->create(1, 'Swimlane #1'));
        $this->assertEquals(3, $swimlaneModel->create(1, 'Swimlane #2'));

        $swimlanes = $swimlaneModel->getList(1);
        $expected = array(
            1 => 'Default swimlane',
            2 => 'Swimlane #1',
            3 => 'Swimlane #2',
        );

        $this->assertEquals($expected, $swimlanes);
    }

    public function testUpdate()
    {
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertEquals(2, $swimlaneModel->create(1, 'Swimlane #1'));

        $this->assertTrue($swimlaneModel->update(2, array('name' => 'foobar', 'task_limit' => 1)));

        $swimlane = $swimlaneModel->getById(2);
        $this->assertEquals('foobar', $swimlane['name']);
        $this->assertEquals(1, $swimlane['task_limit']);
    }

    public function testDisableEnable()
    {
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertEquals(2, $swimlaneModel->create(1, 'Swimlane #1'));

        $swimlane = $swimlaneModel->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $this->assertEquals(3, $swimlaneModel->getLastPosition(1));
        $this->assertTrue($swimlaneModel->disable(1, 1));

        $swimlane = $swimlaneModel->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(0, $swimlane['is_active']);
        $this->assertEquals(0, $swimlane['position']);

        $this->assertEquals(2, $swimlaneModel->getLastPosition(1));

        // Create a new swimlane
        $this->assertEquals(3, $swimlaneModel->create(1, 'Swimlane #2'));

        $swimlane = $swimlaneModel->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        // Enable our disabled swimlane
        $this->assertTrue($swimlaneModel->enable(1, 1));

        $swimlane = $swimlaneModel->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(3, $swimlane['position']);
    }

    public function testRemove()
    {
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertEquals(2, $swimlaneModel->create(1, 'Swimlane #1'));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'swimlane_id' => 2)));

        $this->assertFalse($swimlaneModel->remove(1, 2));
        $this->assertTrue($swimlaneModel->remove(1, 1));

        $this->assertEmpty($swimlaneModel->getById(1));
    }

    public function testUpdatePositions()
    {
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertEquals(2, $swimlaneModel->create(1, 'Swimlane #1'));
        $this->assertEquals(3, $swimlaneModel->create(1, 'Swimlane #2'));
        $this->assertEquals(4, $swimlaneModel->create(1, 'Swimlane #3'));

        $swimlane = $swimlaneModel->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $swimlane = $swimlaneModel->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);

        $swimlane = $swimlaneModel->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(3, $swimlane['position']);

        // Disable the 2nd swimlane
        $this->assertTrue($swimlaneModel->disable(1, 2));

        $swimlane = $swimlaneModel->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $swimlane = $swimlaneModel->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(0, $swimlane['is_active']);
        $this->assertEquals(0, $swimlane['position']);

        $swimlane = $swimlaneModel->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);

        // Remove the first swimlane
        $this->assertTrue($swimlaneModel->remove(1, 1));

        $swimlane = $swimlaneModel->getById(1);
        $this->assertEmpty($swimlane);

        $swimlane = $swimlaneModel->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(0, $swimlane['is_active']);
        $this->assertEquals(0, $swimlane['position']);

        $swimlane = $swimlaneModel->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);
    }

    public function testDuplicateSwimlane()
    {
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'P2')));
        $this->assertEquals(3, $swimlaneModel->create(1, 'S1'));
        $this->assertEquals(4, $swimlaneModel->create(1, 'S2'));
        $this->assertEquals(5, $swimlaneModel->create(1, 'S3'));

        $this->assertTrue($swimlaneModel->duplicate(1, 2));

        $swimlanes = $swimlaneModel->getAll(2);

        $this->assertCount(4, $swimlanes);
        $this->assertEquals(2, $swimlanes[0]['id']);
        $this->assertEquals('Default swimlane', $swimlanes[0]['name']);
        $this->assertEquals(6, $swimlanes[1]['id']);
        $this->assertEquals('S1', $swimlanes[1]['name']);
        $this->assertEquals(7, $swimlanes[2]['id']);
        $this->assertEquals('S2', $swimlanes[2]['name']);
        $this->assertEquals(8, $swimlanes[3]['id']);
        $this->assertEquals('S3', $swimlanes[3]['name']);
    }

    public function testChangePosition()
    {
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $swimlaneModel->create(1, 'Swimlane #1'));
        $this->assertEquals(3, $swimlaneModel->create(1, 'Swimlane #2'));
        $this->assertEquals(4, $swimlaneModel->create(1, 'Swimlane #3'));
        $this->assertEquals(5, $swimlaneModel->create(1, 'Swimlane #4'));

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
        $this->assertFalse($swimlaneModel->changePosition(1, 2, 8));
    }

    public function testChangePositionWithInactiveSwimlane()
    {
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $swimlaneModel->create(1, 'Swimlane #1'));
        $this->assertEquals(3, $swimlaneModel->create(1, 'Swimlane #2'));
        $this->assertEquals(4, $swimlaneModel->create(1, 'Swimlane #3'));
        $this->assertEquals(5, $swimlaneModel->create(1, 'Swimlane #4'));

        $this->assertTrue($swimlaneModel->disable(1, 2));
        $this->assertTrue($swimlaneModel->disable(1, 3));

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

    public function testGetAllWithTaskCount()
    {
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(2, $swimlaneModel->create(1, 'Swimlane #1'));
        $this->assertEquals(3, $swimlaneModel->create(1, 'Swimlane #2'));
        $this->assertEquals(4, $swimlaneModel->create(1, 'Swimlane #3'));
        $this->assertEquals(5, $swimlaneModel->create(1, 'Swimlane #4'));

        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'swimlane_id' => 2)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'swimlane_id' => 3, 'is_active' => 0)));

        $this->assertTrue($swimlaneModel->disable(1, 2));
        $this->assertTrue($swimlaneModel->disable(1, 4));

        $swimlanes = $swimlaneModel->getAllWithTaskCount(1);
        $this->assertCount(3, $swimlanes['active']);
        $this->assertCount(2, $swimlanes['inactive']);

        $this->assertEquals('Default swimlane', $swimlanes['active'][0]['name']);
        $this->assertEquals('Swimlane #2', $swimlanes['active'][1]['name']);
        $this->assertEquals('Swimlane #4', $swimlanes['active'][2]['name']);

        $this->assertEquals(0, $swimlanes['active'][1]['nb_open_tasks']);
        $this->assertEquals(1, $swimlanes['active'][1]['nb_closed_tasks']);

        $this->assertEquals('Swimlane #1', $swimlanes['inactive'][0]['name']);
        $this->assertEquals('Swimlane #3', $swimlanes['inactive'][1]['name']);

        $this->assertEquals(1, $swimlanes['inactive'][0]['nb_open_tasks']);
        $this->assertEquals(0, $swimlanes['inactive'][0]['nb_closed_tasks']);
    }
}
