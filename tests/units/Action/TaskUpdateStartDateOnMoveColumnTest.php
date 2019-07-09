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
        $this->container['db']->table(TaskModel::TABLE)->eq('id', 1)->update(array('column_id' => 2));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);

        $event = new TaskEvent(array(
            'task_id' => $task['id'],
            'task' => array(
                'project_id' => $task['project_id'],
                'column_id' => $task['column_id'],
            )
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
        $this->container['db']->table(TaskModel::TABLE)->eq('id', 1)->update(array('column_id' => 1));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);

        $event = new TaskEvent(array(
            'task_id' => $task['id'],
            'task' => array(
                'project_id' => $task['project_id'],
                'column_id' => $task['column_id'],
            )
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
        $this->container['db']->table(TaskModel::TABLE)->eq('id', 1)->update(array('column_id' => 2));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);

        $event = new TaskEvent(array(
            'task_id' => $task['id'],
            'task' => array(
                'project_id' => $task['project_id'],
                'column_id' => $task['column_id'],
                'date_started' => $task['date_started'],
            )
        ));

        $action = new TaskUpdateStartDateOnMoveColumn($this->container);
        $action->setProjectId(1);
        $action->setParam('column_id', 1);
        $this->assertFalse($action->execute($event, TaskModel::EVENT_MOVE_COLUMN));
    }
}
