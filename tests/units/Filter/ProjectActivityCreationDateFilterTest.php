<?php

use Kanboard\Filter\ProjectActivityCreationDateFilter;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectActivity;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Task;

require_once __DIR__.'/../Base.php';

class ProjectActivityCreationDateFilterTest extends Base
{
    public function testWithToday()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $projectActivityModel = new ProjectActivity($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(1))));

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityCreationDateFilter('today');
        $filter->setDateParser($this->container['dateParser']);
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(1, $events);
    }

    public function testWithYesterday()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $projectActivityModel = new ProjectActivity($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(1))));

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityCreationDateFilter('yesterday');
        $filter->setDateParser($this->container['dateParser']);
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(0, $events);
    }

    public function testWithIsoDate()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $projectActivityModel = new ProjectActivity($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(1))));

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityCreationDateFilter(date('Y-m-d'));
        $filter->setDateParser($this->container['dateParser']);
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(1, $events);
    }

    public function testWithOperatorAndIsoDate()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $projectActivityModel = new ProjectActivity($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(1))));

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
