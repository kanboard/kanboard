<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Task;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\ProjectActivity;
use Kanboard\Model\Project;

class ProjectActivityTest extends Base
{
    public function testCreation()
    {
        $projectActivity = new ProjectActivity($this->container);
        $taskCreation = new TaskCreation($this->container);
        $taskFinder = new TaskFinder($this->container);
        $projectModel = new Project($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Task #1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Task #2', 'project_id' => 1)));

        $this->assertTrue($projectActivity->createEvent(1, 1, 1, Task::EVENT_CLOSE, array('task' => $taskFinder->getbyId(1))));
        $this->assertTrue($projectActivity->createEvent(1, 2, 1, Task::EVENT_UPDATE, array('task' => $taskFinder->getById(2))));
        $this->assertFalse($projectActivity->createEvent(1, 1, 0, Task::EVENT_OPEN, array('task' => $taskFinder->getbyId(1))));

        $events = $projectActivity->getQuery()->desc('id')->findAll();

        $this->assertCount(2, $events);
        $this->assertEquals(time(), $events[0]['date_creation'], '', 1);
        $this->assertEquals(Task::EVENT_UPDATE, $events[0]['event_name']);
        $this->assertEquals(Task::EVENT_CLOSE, $events[1]['event_name']);
    }

    public function testCleanup()
    {
        $projectActivity = new ProjectActivity($this->container);
        $taskCreation = new TaskCreation($this->container);
        $taskFinder = new TaskFinder($this->container);
        $projectModel = new Project($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Task #1', 'project_id' => 1)));

        $max = 15;
        $nb_events = 100;
        $task = $taskFinder->getbyId(1);

        for ($i = 0; $i < $nb_events; $i++) {
            $this->assertTrue($projectActivity->createEvent(1, 1, 1, Task::EVENT_CLOSE, array('task' => $task)));
        }

        $this->assertEquals($nb_events, $this->container['db']->table('project_activities')->count());
        $projectActivity->cleanup($max);

        $events = $projectActivity->getQuery()->desc('id')->findAll();

        $this->assertNotEmpty($events);
        $this->assertCount($max, $events);
        $this->assertEquals(100, $events[0]['id']);
        $this->assertEquals(99, $events[1]['id']);
        $this->assertEquals(86, $events[14]['id']);
    }
}
