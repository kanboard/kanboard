<?php

use Kanboard\Helper\ProjectActivityHelper;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectActivity;
use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;

require_once __DIR__.'/../Base.php';

class ProjectActivityHelperTest extends Base
{
    public function testGetProjectEvents()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $projectActivityModel = new ProjectActivity($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));

        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(3, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));

        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(1))));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 2, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(2))));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 3, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(3))));

        $helper = new ProjectActivityHelper($this->container);
        $events = $helper->getProjectEvents(1);

        $this->assertCount(3, $events);
        $this->assertEquals(3, $events[0]['task_id']);
        $this->assertNotEmpty($events[0]['event_content']);
        $this->assertNotEmpty($events[0]['event_title']);
        $this->assertNotEmpty($events[0]['author']);
        $this->assertInternalType('array', $events[0]['task']);
    }

    public function testGetProjectsEvents()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $projectActivityModel = new ProjectActivity($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'P2')));
        $this->assertEquals(3, $projectModel->create(array('name' => 'P3')));

        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test', 'project_id' => 2)));
        $this->assertEquals(3, $taskCreation->create(array('title' => 'Test', 'project_id' => 3)));

        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(1))));
        $this->assertNotFalse($projectActivityModel->createEvent(2, 2, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(2))));
        $this->assertNotFalse($projectActivityModel->createEvent(3, 3, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(3))));

        $helper = new ProjectActivityHelper($this->container);
        $events = $helper->getProjectsEvents(array(1, 2));

        $this->assertCount(2, $events);
        $this->assertEquals(2, $events[0]['task_id']);
        $this->assertNotEmpty($events[0]['event_content']);
        $this->assertNotEmpty($events[0]['event_title']);
        $this->assertNotEmpty($events[0]['author']);
        $this->assertInternalType('array', $events[0]['task']);
    }

    public function testGetTaskEvents()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $projectActivityModel = new ProjectActivity($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));

        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));

        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(1))));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 2, 1, Task::EVENT_CREATE, array('task' => $taskFinder->getById(2))));

        $helper = new ProjectActivityHelper($this->container);
        $events = $helper->getTaskEvents(1);

        $this->assertCount(1, $events);
        $this->assertEquals(1, $events[0]['task_id']);
        $this->assertNotEmpty($events[0]['event_content']);
        $this->assertNotEmpty($events[0]['event_title']);
        $this->assertNotEmpty($events[0]['author']);
        $this->assertInternalType('array', $events[0]['task']);
    }
}
