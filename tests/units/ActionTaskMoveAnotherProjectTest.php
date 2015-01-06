<?php

require_once __DIR__.'/Base.php';

use Event\GenericEvent;
use Model\Task;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\Project;

class ActionTaskMoveAnotherProject extends Base
{
    public function testBadProject()
    {
        $action = new Action\TaskMoveAnotherProject($this->container, 3, Task::EVENT_MOVE_COLUMN);
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
        $action = new Action\TaskMoveAnotherProject($this->container, 3, Task::EVENT_MOVE_COLUMN);
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
        $action = new Action\TaskMoveAnotherProject($this->container, 1, Task::EVENT_MOVE_COLUMN);

        // We create a task in the first column
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'project 1')));
        $this->assertEquals(2, $p->create(array('name' => 'project 2')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1)));

        // We create an event to move the task to the 2nd column
        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'column_id' => 2,
        );

        // Our event should NOT be executed because we define the same project
        $action->setParam('column_id', 2);
        $action->setParam('project_id', 1);
        $this->assertFalse($action->execute(new GenericEvent($event)));

        // Our task should be assigned to the project 1
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['project_id']);

        // We create an event to move the task to the 2nd column
        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'column_id' => 2,
        );

        // Our event should be executed because we define a different project
        $action->setParam('column_id', 2);
        $action->setParam('project_id', 2);
        $this->assertTrue($action->execute(new GenericEvent($event)));

        // Our task should be assigned to the project 2
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(2, $task['project_id']);
    }
}
