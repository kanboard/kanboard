<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Action\TaskAssignColorOnDueDate;
use Kanboard\Event\TaskListEvent;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;

class TaskAssignColorOnDueDateTest extends Base
{
    public function testChangeColor()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'date_due' => strtotime('-1 day'))));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $tasks = $taskFinderModel->getAll(1);
        $event = new TaskListEvent(array('tasks' => $tasks, 'project_id' => 1));

        $action = new TaskAssignColorOnDueDate($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');

        $this->assertTrue($action->execute($event, TaskModel::EVENT_DAILY_CRONJOB));

        $tasks = $taskFinderModel->getAll(1);
        $this->assertEquals('red', $tasks[0]['color_id']);
        $this->assertEquals('yellow', $tasks[1]['color_id']);
    }

    public function testChangeColorOnlyWhenNecessary()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'date_due' => strtotime('-1 day'), 'color_id' => 'red')));

        $tasks = $taskFinderModel->getAll(1);
        $event = new TaskListEvent(array('tasks' => $tasks, 'project_id' => 1));

        $action = new TaskAssignColorOnDueDate($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');

        $this->assertFalse($action->execute($event, TaskModel::EVENT_DAILY_CRONJOB));

        $tasks = $taskFinderModel->getAll(1);
        $this->assertEquals('red', $tasks[0]['color_id']);
    }
}
