<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\Project;

class ActionTaskCloseTest extends Base
{
    public function testBadProject()
    {
        $action = new Action\TaskClose(3, new Task($this->registry));
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
        $action = new Action\TaskClose(3, new Task($this->registry));
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
        $action = new Action\TaskClose(1, new Task($this->registry));
        $action->setParam('column_id', 2);

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

        // Our task should be closed
        $task = $t->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['is_active']);
    }
}
