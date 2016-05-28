<?php

use Kanboard\Filter\ProjectActivityTaskStatusFilter;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectActivityModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskStatusModel;

require_once __DIR__.'/../Base.php';

class ProjectActivityTaskStatusFilterTest extends Base
{
    public function testFilterByTaskStatus()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $taskStatus = new TaskStatusModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));

        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));

        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(1))));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 2, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(2))));

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
