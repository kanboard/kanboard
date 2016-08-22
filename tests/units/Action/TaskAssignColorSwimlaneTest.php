<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\TaskEvent;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Action\TaskAssignColorSwimlane;

class TaskAssignColorSwimlaneTest extends Base
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
                'swimlane_id' => 2,
            )
        ));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotEquals('red', $task['color_id']);				
				
        $action = new TaskAssignColorSwimlane($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');
          $action->setParam('swimlane_id', 2);

        $this->assertTrue($action->execute($event, TaskModel::EVENT_MOVE_SWIMLANE));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals('red', $task['color_id']);
    }

    public function testWithWrongSwimlane()
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

        $action = new TaskAssignColorSwimlane($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');
        $action->setParam('swimlane_id', 2);

        $this->assertFalse($action->execute($event, TaskModel::EVENT_MOVE_SWIMLANE));
    }
}
