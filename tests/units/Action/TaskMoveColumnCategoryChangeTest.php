<?php

namespace KanboardTests\units\Action;

use KanboardTests\units\Base;
use Kanboard\Event\TaskEvent;
use Kanboard\Model\CategoryModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Action\TaskMoveColumnCategoryChange;

class TaskMoveColumnCategoryChangeTest extends Base
{
    public function testCategoryUpdateMovesOpenAndClosedTasksThroughEvents()
    {
        $this->assertEquals(1, $this->container['projectModel']->create(array('name' => 'test')));
        $this->assertEquals(1, $this->container['categoryModel']->create(array('name' => 'target', 'project_id' => 1)));
        $this->assertEquals(2, $this->container['categoryModel']->create(array('name' => 'other', 'project_id' => 1)));
        $this->assertEquals(1, $this->container['actionModel']->create(array(
            'project_id' => 1,
            'event_name' => TaskModel::EVENT_UPDATE,
            'action_name' => '\\Kanboard\\Action\\TaskMoveColumnCategoryChange',
            'params' => array('category_id' => 1, 'dest_column_id' => 2),
        )));
        $this->container['actionManager']->attachEvents();

        $this->assertEquals(1, $this->container['taskCreationModel']->create(array('project_id' => 1, 'title' => 'closed')));
        $this->assertEquals(2, $this->container['taskCreationModel']->create(array('project_id' => 1, 'title' => 'open')));
        $this->assertEquals(3, $this->container['taskCreationModel']->create(array('project_id' => 1, 'title' => 'other category')));
        $this->assertTrue($this->container['taskStatusModel']->close(1));
        $closed = $this->container['taskFinderModel']->getById(1);

        $this->assertTrue($this->container['taskModificationModel']->update(array('id' => 1, 'category_id' => 1)));
        $this->assertTrue($this->container['taskModificationModel']->update(array('id' => 2, 'category_id' => 1)));
        $this->assertTrue($this->container['taskModificationModel']->update(array('id' => 3, 'category_id' => 2)));

        $task = $this->container['taskFinderModel']->getById(1);
        $this->assertEquals(1, $task['category_id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(TaskModel::STATUS_CLOSED, $task['is_active']);
        $this->assertEquals($closed['date_completed'], $task['date_completed']);
        $task = $this->container['taskFinderModel']->getById(2);
        $this->assertEquals(1, $task['category_id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(TaskModel::STATUS_OPEN, $task['is_active']);
        $task = $this->container['taskFinderModel']->getById(3);
        $this->assertEquals(2, $task['category_id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(TaskModel::STATUS_OPEN, $task['is_active']);
    }

    public function testSuccess()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $categoryModel = new CategoryModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'c1', 'project_id' => 1)));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => array(
                'project_id' => 1,
                'column_id' => 1,
                'category_id' => 1,
                'position' => 1,
                'swimlane_id' => 0,
            )
        ));

        $action = new TaskMoveColumnCategoryChange($this->container);
        $action->setProjectId(1);
        $action->setParam('category_id', 1);
        $action->setParam('dest_column_id', 2);

        $this->assertTrue($action->execute($event, TaskModel::EVENT_UPDATE));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals('test', $task['title']);
        $this->assertEquals(2, $task['column_id']);
    }

    public function testWithWrongColumn()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $categoryModel = new CategoryModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'c1', 'project_id' => 1)));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => array(
                'project_id' => 1,
                'column_id' => 2,
                'category_id' => 1,
            )
        ));

        $action = new TaskMoveColumnCategoryChange($this->container);
        $action->setProjectId(1);
        $action->setParam('category_id', 1);
        $action->setParam('dest_column_id', 2);

        $this->assertFalse($action->execute($event, TaskModel::EVENT_UPDATE));
    }

    public function testWithWrongCategory()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $categoryModel = new CategoryModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'c1', 'project_id' => 1)));
        $this->assertEquals(2, $categoryModel->create(array('name' => 'c2', 'project_id' => 1)));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => array(
                'project_id' => 1,
                'column_id' => 1,
                'category_id' => 2,
            )
        ));

        $action = new TaskMoveColumnCategoryChange($this->container);
        $action->setProjectId(1);
        $action->setParam('category_id', 1);
        $action->setParam('dest_column_id', 2);

        $this->assertFalse($action->execute($event, TaskModel::EVENT_UPDATE));
    }
}
