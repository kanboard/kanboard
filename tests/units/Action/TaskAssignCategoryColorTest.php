<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Category;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Model\Task;
use Kanboard\Action\TaskAssignCategoryColor;

class TaskAssignCategoryColorTest extends Base
{
    public function testChangeCategory()
    {
        $categoryModel = new Category($this->container);
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'c1', 'project_id' => 1)));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'color_id' => 'red'));

        $action = new TaskAssignCategoryColor($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');
        $action->setParam('category_id', 1);

        $this->assertTrue($action->execute($event, Task::EVENT_CREATE_UPDATE));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['category_id']);
    }

    public function testWithWrongColor()
    {
        $categoryModel = new Category($this->container);
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'c1', 'project_id' => 1)));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'color_id' => 'blue'));

        $action = new TaskAssignCategoryColor($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');
        $action->setParam('category_id', 1);

        $this->assertFalse($action->execute($event, Task::EVENT_CREATE_UPDATE));
    }
}
