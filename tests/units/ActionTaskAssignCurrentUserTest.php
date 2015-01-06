<?php

require_once __DIR__.'/Base.php';

use Event\GenericEvent;
use Model\Task;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\Project;
use Model\UserSession;

class ActionTaskAssignCurrentUser extends Base
{
    public function testBadProject()
    {
        $action = new Action\TaskAssignCurrentUser($this->container, 3, Task::EVENT_CREATE);
        $action->setParam('column_id', 5);

        $event = array(
            'project_id' => 2,
            'task_id' => 3,
            'column_id' => 5,
        );

        $this->assertFalse($action->isExecutable($event));
        $this->assertFalse($action->execute(new GenericEvent($event)));
    }

    public function testBadColumn()
    {
        $action = new Action\TaskAssignCurrentUser($this->container, 3, Task::EVENT_CREATE);
        $action->setParam('column_id', 5);

        $event = array(
            'project_id' => 3,
            'task_id' => 3,
            'column_id' => 3,
        );

        $this->assertFalse($action->execute(new GenericEvent($event)));
    }

    public function testExecute()
    {
        $action = new Action\TaskAssignCurrentUser($this->container, 1, Task::EVENT_MOVE_COLUMN);
        $action->setParam('column_id', 2);
        $_SESSION = array(
            'user' => array('id' => 5)
        );

        // We create a task in the first column
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $us = new UserSession($this->container);

        $this->assertEquals(5, $us->getId());
        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1)));

        // We create an event to move the task to the 2nd column
        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'column_id' => 2,
        );

        // Our event should be executed
        $this->assertTrue($action->execute(new GenericEvent($event)));

        // Our task should be assigned to the user 5 (from the session)
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(5, $task['owner_id']);
    }
}
