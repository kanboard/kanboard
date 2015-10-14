<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Subtask;
use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\TaskStatus;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectPermission;

class TaskStatusTest extends Base
{
    public function testStatus()
    {
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $ts = new TaskStatus($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        // The task must be open

        $this->assertTrue($ts->isOpen(1));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(Task::STATUS_OPEN, $task['is_active']);
        $this->assertEquals(0, $task['date_completed']);
        $this->assertEquals(time(), $task['date_modification'], '', 1);

        // We close the task

        $this->container['dispatcher']->addListener(Task::EVENT_CLOSE, array($this, 'onTaskClose'));
        $this->container['dispatcher']->addListener(Task::EVENT_OPEN, array($this, 'onTaskOpen'));

        $this->assertTrue($ts->close(1));
        $this->assertTrue($ts->isClosed(1));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(Task::STATUS_CLOSED, $task['is_active']);
        $this->assertEquals(time(), $task['date_completed'], 'Bad completion timestamp', 1);
        $this->assertEquals(time(), $task['date_modification'], 'Bad modification timestamp', 1);

        // We open the task again

        $this->assertTrue($ts->open(1));
        $this->assertTrue($ts->isOpen(1));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(Task::STATUS_OPEN, $task['is_active']);
        $this->assertEquals(0, $task['date_completed']);
        $this->assertEquals(time(), $task['date_modification'], '', 1);

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey('task.close.TaskStatusTest::onTaskClose', $called);
        $this->assertArrayHasKey('task.open.TaskStatusTest::onTaskOpen', $called);
    }

    public function onTaskOpen($event)
    {
        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);
        $this->assertArrayHasKey('task_id', $event);
        $this->assertNotEmpty($event['task_id']);
    }

    public function onTaskClose($event)
    {
        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);
        $this->assertArrayHasKey('task_id', $event);
        $this->assertNotEmpty($event['task_id']);
    }

    public function testThatAllSubtasksAreClosed()
    {
        $ts = new TaskStatus($this->container);
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));
        $this->assertEquals(2, $s->create(array('title' => 'subtask #2', 'task_id' => 1)));

        $this->assertTrue($ts->close(1));

        $subtasks = $s->getAll(1);
        $this->assertNotEmpty($subtasks);

        foreach ($subtasks as $subtask) {
            $this->assertEquals(Subtask::STATUS_DONE, $subtask['status']);
        }
    }
}
