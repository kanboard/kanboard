<?php

require_once __DIR__.'/../Base.php';

use Kanboard\EventBuilder\TaskLinkEventBuilder;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskLinkModel;
use Kanboard\Model\CategoryModel;
use Kanboard\Action\TaskAssignCategoryLink;

class TaskAssignCategoryLinkTest extends Base
{
    public function testAssignCategory()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $categoryModel = new CategoryModel($this->container);
        $taskLinkModel = new TaskLinkModel($this->container);

        $action = new TaskAssignCategoryLink($this->container);
        $action->setProjectId(1);
        $action->setParam('category_id', 1);
        $action->setParam('link_id', 2);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'C1', 'project_id' => 1)));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'T1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'T2', 'project_id' => 1)));
        $this->assertEquals(1, $taskLinkModel->create(1, 2, 2));

        $event = TaskLinkEventBuilder::getInstance($this->container)
            ->withTaskLinkId(1)
            ->buildEvent();

        $this->assertTrue($action->execute($event, TaskLinkModel::EVENT_CREATE_UPDATE));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['category_id']);
    }

    public function testWhenLinkDontMatch()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $categoryModel = new CategoryModel($this->container);
        $taskLinkModel = new TaskLinkModel($this->container);

        $action = new TaskAssignCategoryLink($this->container);
        $action->setProjectId(1);
        $action->setParam('category_id', 1);
        $action->setParam('link_id', 2);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'C1', 'project_id' => 1)));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'T1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'T2', 'project_id' => 1)));
        $this->assertEquals(1, $taskLinkModel->create(1, 2, 1));

        $event = TaskLinkEventBuilder::getInstance($this->container)
            ->withTaskLinkId(1)
            ->buildEvent();

        $this->assertFalse($action->execute($event, TaskLinkModel::EVENT_CREATE_UPDATE));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(0, $task['category_id']);
    }

    public function testThatExistingCategoryWillNotChange()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $categoryModel = new CategoryModel($this->container);
        $taskLinkModel = new TaskLinkModel($this->container);

        $action = new TaskAssignCategoryLink($this->container);
        $action->setProjectId(1);
        $action->setParam('category_id', 1);
        $action->setParam('link_id', 2);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'C1', 'project_id' => 1)));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'T1', 'project_id' => 1, 'category_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'T2', 'project_id' => 1)));
        $this->assertEquals(1, $taskLinkModel->create(1, 2, 2));

        $event = TaskLinkEventBuilder::getInstance($this->container)
            ->withTaskLinkId(1)
            ->buildEvent();

        $this->assertFalse($action->execute($event, TaskLinkModel::EVENT_CREATE_UPDATE));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['category_id']);
    }
}
