<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\Project;

class ActionTaskAssignSpecificUser extends Base
{
    public function testBadProject()
    {
        $action = new Action\TaskAssignSpecificUser($this->registry, 3, Task::EVENT_MOVE_COLUMN);
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
        $action = new Action\TaskAssignSpecificUser($this->registry, 3, Task::EVENT_MOVE_COLUMN);
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
        $action = new Action\TaskAssignSpecificUser($this->registry, 1, Task::EVENT_MOVE_COLUMN);
        $action->setParam('column_id', 2);
        $action->setParam('user_id', 1);

        // We create a task in the first column
        $t = new Task($this->registry);
        $p = new Project($this->registry);
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

        // Our task should be assigned to the user 1
        $task = $t->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['owner_id']);
    }
}
