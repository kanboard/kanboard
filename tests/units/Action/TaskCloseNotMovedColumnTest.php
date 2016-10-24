<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Action\TaskCloseNotMovedColumn;
use Kanboard\Event\TaskListEvent;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;

class TaskCloseNotMovedColumnTest extends Base
{
    public function testClose()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(3, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'column_id' => 2)));

        $this->container['db']->table(TaskModel::TABLE)->in('id', array(1, 3))->update(array('date_moved' => strtotime('-10days')));

        $tasks = $taskFinderModel->getAll(1);
        $event = new TaskListEvent(array('tasks' => $tasks, 'project_id' => 1));

        $action = new TaskCloseNotMovedColumn($this->container);
        $action->setProjectId(1);
        $action->setParam('duration', 2);
        $action->setParam('column_id', 2);

        $this->assertTrue($action->execute($event, TaskModel::EVENT_DAILY_CRONJOB));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['is_active']);

        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['is_active']);

        $task = $taskFinderModel->getById(3);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['is_active']);
    }
}
