<?php

use Kanboard\Filter\ProjectActivityTaskTitleFilter;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectActivityModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskModel;

require_once __DIR__.'/../Base.php';

class ProjectActivityTaskTitleFilterTest extends Base
{
    public function testWithFullTitle()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);
        $query = $projectActivityModel->getQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));

        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test2', 'project_id' => 1)));

        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(1))));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 2, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(2))));

        $filter = new ProjectActivityTaskTitleFilter('test2');
        $filter->withQuery($query)->apply();
        $this->assertCount(1, $query->findAll());
    }

    public function testWithPartialTitle()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);
        $query = $projectActivityModel->getQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));

        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test2', 'project_id' => 1)));

        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(1))));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 2, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(2))));

        $filter = new ProjectActivityTaskTitleFilter('test');
        $filter->withQuery($query)->apply();
        $this->assertCount(2, $query->findAll());
    }

    public function testWithId()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);
        $query = $projectActivityModel->getQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));

        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test2', 'project_id' => 1)));

        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(1))));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 2, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(2))));

        $filter = new ProjectActivityTaskTitleFilter('#2');
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(1, $events);
        $this->assertEquals(2, $events[0]['task_id']);
    }
}
