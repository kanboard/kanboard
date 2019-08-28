<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\TaskEvent;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Action\TaskUpdateStartDateOnMoveColumn;

class TaskUpdateStartDateOnMoveColumnTest extends Base
{
    public function testChangeColumn()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'column_id' => 1)));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => array(
                'project_id' => 1,
            ),
            'src_column_id' => 1,
        ));

        $action = new TaskUpdateStartDateOnMoveColumn($this->container);
        $action->setProjectId(1);
        $action->setParam('column_id', 1);
        $this->assertTrue($action->execute($event, TaskModel::EVENT_MOVE_COLUMN));

        $task = $taskFinderModel->getById(1);
        $this->assertTrue($task['date_started'] > 0, 'task start date updated');
    }

    public function testWithWrongColumn()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'column_id' => 2)));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => array(
                'project_id' => 1,
            ),
            'src_column_id' => 2,
        ));

        $action = new TaskUpdateStartDateOnMoveColumn($this->container);
        $action->setProjectId(1);
        $action->setParam('column_id', 1);
        $this->assertFalse($action->execute($event, TaskModel::EVENT_MOVE_COLUMN));
    }

    public function testWithStarted()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'column_id' => 1, 'date_started' => time())));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => array(
                'project_id' => 1,
                'date_started' => time(),
            ),
            'src_column_id' => 1,
        ));

        $action = new TaskUpdateStartDateOnMoveColumn($this->container);
        $action->setProjectId(1);
        $action->setParam('column_id', 1);
        $this->assertFalse($action->execute($event, TaskModel::EVENT_MOVE_COLUMN));
    }
}
