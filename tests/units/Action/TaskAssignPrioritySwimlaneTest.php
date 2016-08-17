<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\TaskEvent;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Action\TaskAssignPrioritySwimlane;

class TaskAssignColorColumnTest extends Base
{
    public function testChangeSwimlane()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => array(
                'project_id' => 1,
                'priority' => 1,
            )
        ));

        $action = new TaskAssignPrioritySwimlane($this->container);
        $action->setProjectId(1);
        $action->setParam('priority', 2);
        $action->setParam('swimlane_id', 2);

        $this->assertTrue($action->execute($event, TaskModel::EVENT_MOVE_SWIMLANE));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(2, $task['priority']);
    }

    public function testWithWrongCategory()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => array(
                'project_id' => 1,
                'swimlane_id' => 3,
            )
        ));

        $action = new TaskAssignColorColumn($this->container);
        $action->setProjectId(1);
        $action->setParam('priority', 2);
        $action->setParam('swimlane_id', 2);

        $this->assertFalse($action->execute($event, TaskModel::EVENT_MOVE_SWIMLANE));
    }
}
