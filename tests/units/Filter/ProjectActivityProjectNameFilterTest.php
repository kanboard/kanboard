<?php

use Kanboard\Filter\ProjectActivityProjectNameFilter;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectActivity;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Task;

require_once __DIR__.'/../Base.php';

class ProjectActivityProjectNameFilterTest extends Base
{
    public function testFilterByProjectName()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $projectActivityModel = new ProjectActivity($this->container);
        $query = $projectActivityModel->getQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'P2')));

        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test', 'project_id' => 2)));

        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(1))));
        $this->assertNotFalse($projectActivityModel->createEvent(2, 2, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(2))));

        $filter = new ProjectActivityProjectNameFilter('P1');
        $filter->withQuery($query)->apply();
        $this->assertCount(1, $query->findAll());
    }
}
