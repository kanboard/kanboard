<?php

use Kanboard\Filter\TaskStartsWithIdFilter;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;

require_once __DIR__.'/../Base.php';

class TaskStartsWithIdFilterTest extends Base
{
    public function testManyResults()
    {
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $query = $taskFinderModel->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));

        for ($i = 1; $i <= 20; $i++) {
            $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'Task #'.$i)));
        }

        $filter = new TaskStartsWithIdFilter();
        $filter->withQuery($query);
        $filter->withValue(1);
        $filter->apply();

        $tasks = $query->findAll();
        $this->assertCount(11, $tasks);
        $this->assertEquals('Task #1', $tasks[0]['title']);
        $this->assertEquals('Task #19', $tasks[10]['title']);
    }

    public function testOneResult()
    {
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $query = $taskFinderModel->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));

        for ($i = 1; $i <= 20; $i++) {
            $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'Task #'.$i)));
        }

        $filter = new TaskStartsWithIdFilter();
        $filter->withQuery($query);
        $filter->withValue(3);
        $filter->apply();

        $tasks = $query->findAll();
        $this->assertCount(1, $tasks);
        $this->assertEquals('Task #3', $tasks[0]['title']);
    }

    public function testEmptyResult()
    {
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $query = $taskFinderModel->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));

        for ($i = 1; $i <= 20; $i++) {
            $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'Task #'.$i)));
        }

        $filter = new TaskStartsWithIdFilter();
        $filter->withQuery($query);
        $filter->withValue(30);
        $filter->apply();

        $tasks = $query->findAll();
        $this->assertCount(0, $tasks);
    }

    public function testWithTwoDigits()
    {
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $query = $taskFinderModel->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));

        for ($i = 1; $i <= 20; $i++) {
            $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'Task #'.$i)));
        }

        $filter = new TaskStartsWithIdFilter();
        $filter->withQuery($query);
        $filter->withValue(11);
        $filter->apply();

        $tasks = $query->findAll();
        $this->assertCount(1, $tasks);
        $this->assertEquals('Task #11', $tasks[0]['title']);
    }
}
