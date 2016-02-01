<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\TaskListEvent;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Model\Task;
use Kanboard\Action\TaskCloseNoActivity;

class TaskCloseNoActivityTest extends Base
{
    public function testClose()
    {
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $this->container['db']->table(Task::TABLE)->eq('id', 1)->update(array('date_modification' => strtotime('-10days')));

        $tasks = $taskFinderModel->getAll(1);
        $event = new TaskListEvent(array('tasks' => $tasks, 'project_id' => 1));

        $action = new TaskCloseNoActivity($this->container);
        $action->setProjectId(1);
        $action->setParam('duration', 2);

        $this->assertTrue($action->execute($event, Task::EVENT_DAILY_CRONJOB));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['is_active']);

        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['is_active']);
    }
}
