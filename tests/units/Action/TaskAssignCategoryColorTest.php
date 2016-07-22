<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\TaskEvent;
use Kanboard\Model\CategoryModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Action\TaskAssignCategoryColor;

class TaskAssignCategoryColorTest extends Base
{
    public function testChangeCategory()
    {
        $categoryModel = new CategoryModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'c1', 'project_id' => 1)));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => array(
                'project_id' => 1,
                'color_id' => 'red',
            )
        ));

        $action = new TaskAssignCategoryColor($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');
        $action->setParam('category_id', 1);

        $this->assertTrue($action->execute($event, TaskModel::EVENT_CREATE_UPDATE));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['category_id']);
    }

    public function testWithWrongColor()
    {
        $categoryModel = new CategoryModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'c1', 'project_id' => 1)));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => array(
                'project_id' => 1,
                'color_id' => 'blue',
            )
        ));

        $action = new TaskAssignCategoryColor($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');
        $action->setParam('category_id', 1);

        $this->assertFalse($action->execute($event, TaskModel::EVENT_CREATE_UPDATE));
    }
}
