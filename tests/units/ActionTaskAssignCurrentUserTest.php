<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\Project;
use Model\Acl;

class ActionTaskAssignCurrentUser extends Base
{
    public function testBadProject()
    {
        $action = new Action\TaskAssignCurrentUser($this->registry, 3, Task::EVENT_CREATE);
        $action->setParam('column_id', 5);

        $event = array(
            'project_id' => 2,
            'task_id' => 3,
            'column_id' => 5,
        );

        $this->assertFalse($action->isExecutable($event));
        $this->assertFalse($action->execute($event));
    }

    public function testBadColumn()
    {
        $action = new Action\TaskAssignCurrentUser($this->registry, 3, Task::EVENT_CREATE);
        $action->setParam('column_id', 5);

        $event = array(
            'project_id' => 3,
            'task_id' => 3,
            'column_id' => 3,
        );

        $this->assertFalse($action->execute($event));
    }

    public function testExecute()
    {
        $action = new Action\TaskAssignCurrentUser($this->registry, 1, Task::EVENT_MOVE_COLUMN);
        $action->setParam('column_id', 2);
        $_SESSION = array(
            'user' => array('id' => 5)
        );

        // We create a task in the first column
        $t = new Task($this->registry);
        $p = new Project($this->registry);
        $a = new Acl($this->registry);

        $this->assertEquals(5, $a->getUserId());
        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $t->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1)));

        // We create an event to move the task to the 2nd column
        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'column_id' => 2,
        );

        // Our event should be executed
        $this->assertTrue($action->execute($event));

        // Our task should be assigned to the user 5 (from the session)
        $task = $t->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(5, $task['owner_id']);
    }
}
