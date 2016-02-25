<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Task;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\Project;
use Kanboard\Action\TaskMoveAnotherProject;

class TaskMoveAnotherProjectTest extends Base
{
    public function testSuccess()
    {
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'column_id' => 2));

        $action = new TaskMoveAnotherProject($this->container);
        $action->setProjectId(1);
        $action->setParam('project_id', 2);
        $action->setParam('column_id', 2);

        $this->assertTrue($action->execute($event, Task::EVENT_CLOSE));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals('test', $task['title']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals(5, $task['column_id']);
    }

    public function testWithWrongColumn()
    {
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'column_id' => 3));

        $action = new TaskMoveAnotherProject($this->container);
        $action->setProjectId(1);
        $action->setParam('project_id', 2);
        $action->setParam('column_id', 2);

        $this->assertFalse($action->execute($event, Task::EVENT_CLOSE));
    }
}
