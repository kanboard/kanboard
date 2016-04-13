<?php

use Kanboard\Filter\ProjectActivityCreatorFilter;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectActivity;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Task;

require_once __DIR__.'/../Base.php';

class ProjectActivityCreatorFilterTest extends Base
{
    public function testWithUsername()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $projectActivityModel = new ProjectActivity($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(1))));

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityCreatorFilter('admin');
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(1, $events);
    }

    public function testWithAnotherUsername()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $projectActivityModel = new ProjectActivity($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(1))));

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityCreatorFilter('John Doe');
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(0, $events);
    }

    public function testWithCurrentUser()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $projectActivityModel = new ProjectActivity($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(1))));

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityCreatorFilter('me');
        $filter->setCurrentUserId(1);
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(1, $events);
    }

    public function testWithAnotherCurrentUser()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $projectActivityModel = new ProjectActivity($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(1))));

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityCreatorFilter('me');
        $filter->setCurrentUserId(2);
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(0, $events);
    }
}
