<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Model\TaskLink;
use Kanboard\Action\TaskAssignColorLink;

class TaskAssignColorLinkTest extends Base
{
    public function testChangeColor()
    {
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'link_id' => 1));

        $action = new TaskAssignColorLink($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');
        $action->setParam('link_id', 1);

        $this->assertTrue($action->execute($event, TaskLink::EVENT_CREATE_UPDATE));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals('red', $task['color_id']);
    }

    public function testWithWrongLink()
    {
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'link_id' => 2));

        $action = new TaskAssignColorLink($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');
        $action->setParam('link_id', 1);

        $this->assertFalse($action->execute($event, TaskLink::EVENT_CREATE_UPDATE));
    }
}
