<?php

use Kanboard\Filter\TaskStatusFilter;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskModel;

require_once __DIR__.'/../Base.php';

class TaskStatusFilterTest extends Base
{
    public function testWithOpenValue()
    {
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $query = $taskFinderModel->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test1')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test2', 'is_active' => TaskModel::STATUS_CLOSED)));

        $filter = new TaskStatusFilter();
        $filter->withQuery($query);
        $filter->withValue('open');
        $filter->apply();

        $tasks = $query->findAll();
        $this->assertCount(1, $tasks);
        $this->assertEquals('test1', $tasks[0]['title']);
    }

    public function testWithOpenNumericValue()
    {
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $query = $taskFinderModel->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test1')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test2', 'is_active' => TaskModel::STATUS_CLOSED)));

        $filter = new TaskStatusFilter();
        $filter->withQuery($query);
        $filter->withValue(1);
        $filter->apply();

        $tasks = $query->findAll();
        $this->assertCount(1, $tasks);
        $this->assertEquals('test1', $tasks[0]['title']);
    }

    public function testWithClosedValue()
    {
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $query = $taskFinderModel->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test1')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test2', 'is_active' => TaskModel::STATUS_CLOSED)));

        $filter = new TaskStatusFilter();
        $filter->withQuery($query);
        $filter->withValue('closed');
        $filter->apply();

        $tasks = $query->findAll();
        $this->assertCount(1, $tasks);
        $this->assertEquals('test2', $tasks[0]['title']);
    }

    public function testWithClosedNumericValue()
    {
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $query = $taskFinderModel->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test1')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test2', 'is_active' => TaskModel::STATUS_CLOSED)));

        $filter = new TaskStatusFilter();
        $filter->withQuery($query);
        $filter->withValue(0);
        $filter->apply();

        $tasks = $query->findAll();
        $this->assertCount(1, $tasks);
        $this->assertEquals('test2', $tasks[0]['title']);
    }

    public function testWithAllValue()
    {
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $query = $taskFinderModel->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test1')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test2', 'is_active' => TaskModel::STATUS_CLOSED)));

        $filter = new TaskStatusFilter();
        $filter->withQuery($query);
        $filter->withValue('all');
        $filter->apply();

        $tasks = $query->asc(TaskModel::TABLE.'.title')->findAll();
        $this->assertCount(2, $tasks);
        $this->assertEquals('test1', $tasks[0]['title']);
        $this->assertEquals('test2', $tasks[1]['title']);
    }
}
