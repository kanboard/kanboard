<?php

use Kanboard\Filter\ProjectActivityTaskStatusFilter;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectActivity;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Task;
use Kanboard\Model\TaskStatus;

require_once __DIR__.'/../Base.php';

class ProjectActivityTaskStatusFilterTest extends Base
{
    public function testFilterByTaskStatus()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $taskStatus = new TaskStatus($this->container);
        $projectModel = new Project($this->container);
        $projectActivityModel = new ProjectActivity($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));

        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));

        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(1))));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 2, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(2))));

        $this->assertTrue($taskStatus->close(1));

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityTaskStatusFilter('open');
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(1, $events);
        $this->assertEquals(2, $events[0]['task_id']);

        $query = $projectActivityModel->getQuery();
        $filter = new ProjectActivityTaskStatusFilter('closed');
        $filter->withQuery($query)->apply();

        $events = $query->findAll();
        $this->assertCount(1, $events);
        $this->assertEquals(1, $events[0]['task_id']);
    }
}
