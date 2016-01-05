<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Category;
use Kanboard\Model\Task;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\Project;
use Kanboard\Action\TaskMoveColumnCategoryChange;

class TaskMoveColumnCategoryChangeTest extends Base
{
    public function testSuccess()
    {
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);
        $categoryModel = new Category($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'c1', 'project_id' => 1)));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'column_id' => 1, 'category_id' => 1));

        $action = new TaskMoveColumnCategoryChange($this->container);
        $action->setProjectId(1);
        $action->setParam('category_id', 1);
        $action->setParam('dest_column_id', 2);

        $this->assertTrue($action->execute($event, Task::EVENT_UPDATE));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals('test', $task['title']);
        $this->assertEquals(2, $task['column_id']);
    }

    public function testWithWrongColumn()
    {
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);
        $categoryModel = new Category($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'c1', 'project_id' => 1)));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'column_id' => 2, 'category_id' => 1));

        $action = new TaskMoveColumnCategoryChange($this->container);
        $action->setProjectId(1);
        $action->setParam('category_id', 1);
        $action->setParam('dest_column_id', 2);

        $this->assertFalse($action->execute($event, Task::EVENT_UPDATE));
    }

    public function testWithWrongCategory()
    {
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);
        $categoryModel = new Category($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'c1', 'project_id' => 1)));
        $this->assertEquals(2, $categoryModel->create(array('name' => 'c2', 'project_id' => 1)));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'column_id' => 1, 'category_id' => 2));

        $action = new TaskMoveColumnCategoryChange($this->container);
        $action->setProjectId(1);
        $action->setParam('category_id', 1);
        $action->setParam('dest_column_id', 2);

        $this->assertFalse($action->execute($event, Task::EVENT_UPDATE));
    }
}
