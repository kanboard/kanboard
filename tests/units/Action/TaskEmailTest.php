<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Model\User;
use Kanboard\Action\TaskEmail;

class TaskEmailTest extends Base
{
    public function testNoEmail()
    {
        $action = new TaskEmail($this->container, 1, Task::EVENT_MOVE_COLUMN);
        $action->setParam('column_id', 2);
        $action->setParam('user_id', 1);
        $action->setParam('subject', 'My email subject');

        // We create a task in the first column
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1)));

        // We create an event to move the task to the 2nd column
        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'column_id' => 2,
        );

        // Email should be not be sent
        $this->container['emailClient']->expects($this->never())->method('send');

        // Our event should be executed
        $this->assertFalse($action->execute(new GenericEvent($event)));
    }

    public function testWrongColumn()
    {
        $action = new TaskEmail($this->container, 1, Task::EVENT_MOVE_COLUMN);
        $action->setParam('column_id', 2);
        $action->setParam('user_id', 1);
        $action->setParam('subject', 'My email subject');

        // We create a task in the first column
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1)));

        // We create an event to move the task to the 2nd column
        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'column_id' => 3,
        );

        // Email should be not be sent
        $this->container['emailClient']->expects($this->never())->method('send');

        // Our event should be executed
        $this->assertFalse($action->execute(new GenericEvent($event)));
    }

    public function testMoveColumn()
    {
        $action = new TaskEmail($this->container, 1, Task::EVENT_MOVE_COLUMN);
        $action->setParam('column_id', 2);
        $action->setParam('user_id', 1);
        $action->setParam('subject', 'My email subject');

        // We create a task in the first column
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1)));
        $this->assertTrue($u->update(array('id' => 1, 'email' => 'admin@localhost')));

        // We create an event to move the task to the 2nd column
        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'column_id' => 2,
        );

        // Email should be sent
        $this->container['emailClient']->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('admin@localhost'),
                $this->equalTo('admin'),
                $this->equalTo('My email subject'),
                $this->stringContains('test')
            );

        // Our event should be executed
        $this->assertTrue($action->execute(new GenericEvent($event)));
    }

    public function testTaskClose()
    {
        $action = new TaskEmail($this->container, 1, Task::EVENT_CLOSE);
        $action->setParam('column_id', 2);
        $action->setParam('user_id', 1);
        $action->setParam('subject', 'My email subject');

        // We create a task in the first column
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1)));
        $this->assertTrue($u->update(array('id' => 1, 'email' => 'admin@localhost')));

        // We create an event
        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'column_id' => 2,
        );

        // Email should be sent
        $this->container['emailClient']->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('admin@localhost'),
                $this->equalTo('admin'),
                $this->equalTo('My email subject'),
                $this->stringContains('test')
            );

        // Our event should be executed
        $this->assertTrue($action->execute(new GenericEvent($event)));
    }
}
