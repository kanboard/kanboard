<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Category;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Model\Task;
use Kanboard\Action\TaskAssignColorPriority;

class TaskAssignColorPriorityTest extends Base
{
    public function testChangeColor()
    {
        $categoryModel = new Category($this->container);
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'c1', 'project_id' => 1)));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'priority' => 1));

        $action = new TaskAssignColorPriority($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');
        $action->setParam('priority', 1);

        $this->assertTrue($action->execute($event, Task::EVENT_CREATE_UPDATE));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals('red', $task['color_id']);
    }

    public function testWithWrongPriority()
    {
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'priority' => 2));

        $action = new TaskAssignColorPriority($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');
        $action->setParam('priority', 1);

        $this->assertFalse($action->execute($event, Task::EVENT_CREATE_UPDATE));
    }
}
