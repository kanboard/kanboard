<?php

use Kanboard\Action\TaskAssignDueDateOnCreation;
use Kanboard\EventBuilder\TaskEventBuilder;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskModel;

require_once __DIR__.'/../Base.php';

class TaskAssignDueDateOnCreationTest extends Base
{
    public function testAction()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = TaskEventBuilder::getInstance($this->container)
            ->withTaskId(1)
            ->buildEvent();

        $action = new TaskAssignDueDateOnCreation($this->container);
        $action->setProjectId(1);
        $action->setParam('duration', 4);

        $this->assertTrue($action->execute($event, TaskModel::EVENT_CREATE));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(date('Y-m-d H:i', strtotime('+4days')), date('Y-m-d H:i', $task['date_due']));
    }
}
