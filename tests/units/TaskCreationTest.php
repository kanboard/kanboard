<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\TaskStatus;
use Model\Project;
use Model\ProjectPermission;

class TaskCreationTest extends Base
{
    public function onCreate($event)
    {
        $this->assertInstanceOf('Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals('test', $event_data['title']);
    }

    public function testNoProjectId()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->container['dispatcher']->addListener(Task::EVENT_CREATE_UPDATE, function() {});
        $this->container['dispatcher']->addListener(Task::EVENT_CREATE, function() {});

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(0, $tc->create(array('title' => 'test', 'project_id' => 0)));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayNotHasKey(Task::EVENT_CREATE_UPDATE.'.closure', $called);
        $this->assertArrayNotHasKey(Task::EVENT_CREATE.'.closure', $called);
    }

    public function testNoTitle()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->container['dispatcher']->addListener(Task::EVENT_CREATE_UPDATE, function() {});
        $this->container['dispatcher']->addListener(Task::EVENT_CREATE, function() {});

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1)));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(Task::EVENT_CREATE_UPDATE.'.closure', $called);
        $this->assertArrayHasKey(Task::EVENT_CREATE.'.closure', $called);

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals('Untitled', $task['title']);
        $this->assertEquals(1, $task['project_id']);
    }

    public function testMinimum()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->container['dispatcher']->addListener(Task::EVENT_CREATE_UPDATE, function() {});
        $this->container['dispatcher']->addListener(Task::EVENT_CREATE, array($this, 'onCreate'));

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test')));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(Task::EVENT_CREATE_UPDATE.'.closure', $called);
        $this->assertArrayHasKey(Task::EVENT_CREATE.'.TaskCreationTest::onCreate', $called);

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals('yellow', $task['color_id']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(0, $task['category_id']);
        $this->assertEquals(0, $task['creator_id']);

        $this->assertEquals('test', $task['title']);
        $this->assertEquals('', $task['description']);
        $this->assertEquals('', $task['reference']);

        $this->assertEquals(time(), $task['date_creation']);
        $this->assertEquals(time(), $task['date_modification']);
        $this->assertEquals(0, $task['date_due']);
        $this->assertEquals(0, $task['date_completed']);
        $this->assertEquals(0, $task['date_started']);

        $this->assertEquals(0, $task['time_estimated']);
        $this->assertEquals(0, $task['time_spent']);

        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['is_active']);
        $this->assertEquals(0, $task['score']);
    }

    public function testColorId()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test', 'color_id' => 'blue')));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals('blue', $task['color_id']);
    }

    public function testOwnerId()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test', 'owner_id' => 1)));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['owner_id']);
    }

    public function testCategoryId()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test', 'category_id' => 1)));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['category_id']);
    }

    public function testCreatorId()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test', 'creator_id' => 1)));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['creator_id']);
    }

    public function testColumnId()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test', 'column_id' => 2)));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);
    }

    public function testPosition()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test', 'column_id' => 2)));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $this->assertEquals(2, $tc->create(array('project_id' => 1, 'title' => 'test', 'column_id' => 2)));

        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(2, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['position']);
    }

    public function testDescription()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test', 'description' => 'test')));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals('test', $task['description']);
    }

    public function testReference()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test', 'reference' => 'test')));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals('test', $task['reference']);
    }

    public function testDateDue()
    {
        $date = '2014-11-23';
        $timestamp = strtotime('+2days');
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test', 'date_due' => $date)));
        $this->assertEquals(2, $tc->create(array('project_id' => 1, 'title' => 'test', 'date_due' => $timestamp)));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals($date, date('Y-m-d', $task['date_due']));

        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(2, $task['id']);
        $this->assertEquals($timestamp, $task['date_due']);
    }

    public function testDateStarted()
    {
        $date = '2014-11-23';
        $timestamp = strtotime('+2days');
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test', 'date_started' => $date)));
        $this->assertEquals(2, $tc->create(array('project_id' => 1, 'title' => 'test', 'date_started' => $timestamp)));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals($date, date('Y-m-d', $task['date_started']));

        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(2, $task['id']);
        $this->assertEquals($timestamp, $task['date_started']);
    }

    public function testTime()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test', 'time_estimated' => 1.5, 'time_spent' => 2.3)));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1.5, $task['time_estimated']);
        $this->assertEquals(2.3, $task['time_spent']);
    }

    public function testStripColumn()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test', 'another_task' => '1')));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);
    }

    public function testScore()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test', 'score' => '3')));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);
        $this->assertEquals(3, $task['score']);
    }
}
