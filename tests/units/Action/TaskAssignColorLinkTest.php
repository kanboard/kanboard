<?php

require_once __DIR__.'/../Base.php';

use Kanboard\EventBuilder\TaskLinkEventBuilder;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskLinkModel;
use Kanboard\Action\TaskAssignColorLink;

class TaskAssignColorLinkTest extends Base
{
    public function testChangeColor()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskLinkModel = new TaskLinkModel($this->container);

        $action = new TaskAssignColorLink($this->container);
        $action->setProjectId(1);
        $action->setParam('link_id', 2);
        $action->setParam('color_id', 'red');

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'T1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'T2', 'project_id' => 1)));
        $this->assertEquals(1, $taskLinkModel->create(1, 2, 2));

        $event = TaskLinkEventBuilder::getInstance($this->container)
            ->withTaskLinkId(1)
            ->buildEvent();

        $this->assertTrue($action->execute($event, TaskLinkModel::EVENT_CREATE_UPDATE));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals('red', $task['color_id']);
    }

    public function testWithWrongLink()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskLinkModel = new TaskLinkModel($this->container);

        $action = new TaskAssignColorLink($this->container);
        $action->setProjectId(1);
        $action->setParam('link_id', 2);
        $action->setParam('color_id', 'red');

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'T1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'T2', 'project_id' => 1)));
        $this->assertEquals(1, $taskLinkModel->create(1, 2, 1));

        $event = TaskLinkEventBuilder::getInstance($this->container)
            ->withTaskLinkId(1)
            ->buildEvent();

        $this->assertFalse($action->execute($event, TaskLinkModel::EVENT_CREATE_UPDATE));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals('yellow', $task['color_id']);
    }
}
