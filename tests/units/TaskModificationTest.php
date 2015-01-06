<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\TaskCreation;
use Model\TaskModification;
use Model\TaskFinder;
use Model\TaskStatus;
use Model\Project;
use Model\ProjectPermission;

class TaskModificationTest extends Base
{
    public function onCreateUpdate($event)
    {
        $this->assertInstanceOf('Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals('Task #1', $event_data['title']);
    }

    public function onUpdate($event)
    {
        $this->assertInstanceOf('Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals('Task #1', $event_data['title']);
    }

    public function onAssigneeChange($event)
    {
        $this->assertInstanceOf('Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals(1, $event_data['owner_id']);
    }

    public function testChangeTitle()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tm = new TaskModification($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $this->container['dispatcher']->addListener(Task::EVENT_CREATE_UPDATE, array($this, 'onCreateUpdate'));
        $this->container['dispatcher']->addListener(Task::EVENT_UPDATE, array($this, 'onUpdate'));

        $this->assertTrue($tm->update(array('id' => 1, 'title' => 'Task #1')));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(Task::EVENT_CREATE_UPDATE.'.TaskModificationTest::onCreateUpdate', $called);
        $this->assertArrayHasKey(Task::EVENT_UPDATE.'.TaskModificationTest::onUpdate', $called);

        $task = $tf->getById(1);
        $this->assertEquals('Task #1', $task['title']);
    }

    public function testChangeAssignee()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tm = new TaskModification($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $task = $tf->getById(1);
        $this->assertEquals(0, $task['owner_id']);

        $this->container['dispatcher']->addListener(Task::EVENT_ASSIGNEE_CHANGE, array($this, 'onAssigneeChange'));

        $this->assertTrue($tm->update(array('id' => 1, 'owner_id' => 1)));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(Task::EVENT_ASSIGNEE_CHANGE.'.TaskModificationTest::onAssigneeChange', $called);

        $task = $tf->getById(1);
        $this->assertEquals(1, $task['owner_id']);
    }

    public function testChangeDescription()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tm = new TaskModification($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $task = $tf->getById(1);
        $this->assertEquals('', $task['description']);

        $this->assertTrue($tm->update(array('id' => 1, 'description' => 'test')));

        $task = $tf->getById(1);
        $this->assertEquals('test', $task['description']);
    }

    public function testChangeCategory()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tm = new TaskModification($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $task = $tf->getById(1);
        $this->assertEquals(0, $task['category_id']);

        $this->assertTrue($tm->update(array('id' => 1, 'category_id' => 1)));

        $task = $tf->getById(1);
        $this->assertEquals(1, $task['category_id']);
    }

    public function testChangeColor()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tm = new TaskModification($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $task = $tf->getById(1);
        $this->assertEquals('yellow', $task['color_id']);

        $this->assertTrue($tm->update(array('id' => 1, 'color_id' => 'blue')));

        $task = $tf->getById(1);
        $this->assertEquals('blue', $task['color_id']);
    }

    public function testChangeScore()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tm = new TaskModification($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $task = $tf->getById(1);
        $this->assertEquals(0, $task['score']);

        $this->assertTrue($tm->update(array('id' => 1, 'score' => 13)));

        $task = $tf->getById(1);
        $this->assertEquals(13, $task['score']);
    }

    public function testChangeDueDate()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tm = new TaskModification($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $task = $tf->getById(1);
        $this->assertEquals(0, $task['date_due']);

        $this->assertTrue($tm->update(array('id' => 1, 'date_due' => '2014-11-24')));

        $task = $tf->getById(1);
        $this->assertEquals('2014-11-24', date('Y-m-d', $task['date_due']));

        $this->assertTrue($tm->update(array('id' => 1, 'date_due' => time())));

        $task = $tf->getById(1);
        $this->assertEquals(date('Y-m-d'), date('Y-m-d', $task['date_due']));
    }

    public function testChangeStartedDate()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tm = new TaskModification($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $task = $tf->getById(1);
        $this->assertEquals(0, $task['date_started']);

        $this->assertTrue($tm->update(array('id' => 1, 'date_started' => '2014-11-24')));

        $task = $tf->getById(1);
        $this->assertEquals('2014-11-24', date('Y-m-d', $task['date_started']));

        $this->assertTrue($tm->update(array('id' => 1, 'date_started' => time())));

        $task = $tf->getById(1);
        $this->assertEquals(date('Y-m-d'), date('Y-m-d', $task['date_started']));
    }

    public function testChangeTimeEstimated()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tm = new TaskModification($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $task = $tf->getById(1);
        $this->assertEquals(0, $task['time_estimated']);

        $this->assertTrue($tm->update(array('id' => 1, 'time_estimated' => 13.3)));

        $task = $tf->getById(1);
        $this->assertEquals(13.3, $task['time_estimated']);
    }

    public function testChangeTimeSpent()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tm = new TaskModification($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $task = $tf->getById(1);
        $this->assertEquals(0, $task['time_spent']);

        $this->assertTrue($tm->update(array('id' => 1, 'time_spent' => 13.3)));

        $task = $tf->getById(1);
        $this->assertEquals(13.3, $task['time_spent']);
    }
}
