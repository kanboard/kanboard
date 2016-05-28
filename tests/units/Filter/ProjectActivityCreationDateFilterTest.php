<?php

use Kanboard\Filter\ProjectActivityCreationDateFilter;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectActivityModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskModel;

require_once __DIR__.'/../Base.php';

class ProjectActivityCreationDateFilterTest extends Base
{
    public function testWithToday()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(1))));

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityCreationDateFilter('today');
        $filter->setDateParser($this->container['dateParser']);
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(1, $events);
    }

    public function testWithYesterday()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(1))));

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityCreationDateFilter('yesterday');
        $filter->setDateParser($this->container['dateParser']);
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(0, $events);
    }

    public function testWithStrtotimeString()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(1))));

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityCreationDateFilter('<=last week');
        $filter->setDateParser($this->container['dateParser']);
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(0, $events);
    }

    public function testWithIsoDate()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(1))));

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityCreationDateFilter(date('Y-m-d'));
        $filter->setDateParser($this->container['dateParser']);
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(1, $events);
    }

    public function testWithOperatorAndIsoDate()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(1))));

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityCreationDateFilter('>='.date('Y-m-d'));
        $filter->setDateParser($this->container['dateParser']);
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(1, $events);

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityCreationDateFilter('<'.date('Y-m-d'));
        $filter->setDateParser($this->container['dateParser']);
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(0, $events);

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityCreationDateFilter('>'.date('Y-m-d'));
        $filter->setDateParser($this->container['dateParser']);
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(0, $events);

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityCreationDateFilter('>='.date('Y-m-d'));
        $filter->setDateParser($this->container['dateParser']);
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(1, $events);
    }
}
