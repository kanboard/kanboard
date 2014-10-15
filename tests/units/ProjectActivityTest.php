<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\TaskFinder;
use Model\ProjectActivity;
use Model\Project;

class ProjectActivityTest extends Base
{
    public function testCreation()
    {
        $e = new ProjectActivity($this->registry);
        $t = new Task($this->registry);
        $tf = new TaskFinder($this->registry);
        $p = new Project($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $t->create(array('title' => 'Task #1', 'project_id' => 1)));
        $this->assertEquals(2, $t->create(array('title' => 'Task #2', 'project_id' => 1)));

        $this->assertTrue($e->createEvent(1, 1, 1, Task::EVENT_CLOSE, array('task' => $tf->getbyId(1))));
        $this->assertTrue($e->createEvent(1, 2, 1, Task::EVENT_UPDATE, array('task' => $tf->getById(2))));
        $this->assertFalse($e->createEvent(1, 1, 0, Task::EVENT_OPEN, array('task' => $tf->getbyId(1))));

        $events = $e->getProject(1);

        $this->assertNotEmpty($events);
        $this->assertTrue(is_array($events));
        $this->assertEquals(2, count($events));
        $this->assertEquals(time(), $events[0]['date_creation']);
        $this->assertEquals(Task::EVENT_UPDATE, $events[0]['event_name']);
        $this->assertEquals(Task::EVENT_CLOSE, $events[1]['event_name']);
    }

    public function testFetchAllContent()
    {
        $e = new ProjectActivity($this->registry);
        $t = new Task($this->registry);
        $tf = new TaskFinder($this->registry);
        $p = new Project($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $t->create(array('title' => 'Task #1', 'project_id' => 1)));

        $nb_events = 80;

        for ($i = 0; $i < $nb_events; $i++) {
            $this->assertTrue($e->createEvent(1, 1, 1, Task::EVENT_UPDATE, array('task' => $tf->getbyId(1))));
        }

        $events = $e->getProject(1);

        $this->assertNotEmpty($events);
        $this->assertTrue(is_array($events));
        $this->assertEquals(50, count($events));
        $this->assertEquals('admin', $events[0]['author']);
        $this->assertNotEmpty($events[0]['event_title']);
        $this->assertNotEmpty($events[0]['event_content']);
    }

    public function testCleanup()
    {
        $e = new ProjectActivity($this->registry);
        $t = new Task($this->registry);
        $tf = new TaskFinder($this->registry);
        $p = new Project($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $t->create(array('title' => 'Task #1', 'project_id' => 1)));

        $max = 15;
        $nb_events = 100;

        for ($i = 0; $i < $nb_events; $i++) {
            $this->assertTrue($e->createEvent(1, 1, 1, Task::EVENT_CLOSE, array('task' => $tf->getbyId(1))));
        }

        $this->assertEquals($nb_events, $this->registry->shared('db')->table('project_activities')->count());
        $e->cleanup($max);

        $events = $e->getProject(1);

        $this->assertNotEmpty($events);
        $this->assertTrue(is_array($events));
        $this->assertEquals($max, count($events));
        $this->assertEquals(100, $events[0]['id']);
        $this->assertEquals(99, $events[1]['id']);
        $this->assertEquals(86, $events[14]['id']);

        // Cleanup during task creation

        $nb_events = ProjectActivity::MAX_EVENTS + 10;

        for ($i = 0; $i < $nb_events; $i++) {
            $this->assertTrue($e->createEvent(1, 1, 1, Task::EVENT_CLOSE, array('task' => $tf->getbyId(1))));
        }

        $this->assertEquals(ProjectActivity::MAX_EVENTS, $this->registry->shared('db')->table('project_activities')->count());
    }
}
