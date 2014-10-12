<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\TaskFinder;
use Model\Project;

class ActionTaskAssignColorUser extends Base
{
    public function testBadProject()
    {
        $action = new Action\TaskAssignColorUser($this->registry, 3, Task::EVENT_CREATE);

        $event = array(
            'project_id' => 2,
            'task_id' => 3,
            'column_id' => 5,
        );

        $this->assertFalse($action->isExecutable($event));
        $this->assertFalse($action->execute($event));
    }

    public function testExecute()
    {
        $action = new Action\TaskAssignColorUser($this->registry, 1, Task::EVENT_ASSIGNEE_CHANGE);
        $action->setParam('user_id', 1);
        $action->setParam('color_id', 'blue');

        // We create a task in the first column
        $t = new Task($this->registry);
        $tf = new TaskFinder($this->registry);
        $p = new Project($this->registry);
        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $t->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1, 'color_id' => 'green')));

        // We change the assignee
        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'owner_id' => 5,
        );

        // Our event should NOT be executed
        $this->assertFalse($action->execute($event));

        // Our task should be assigned to nobody and have the green color
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals('green', $task['color_id']);

        // We change the assignee
        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'owner_id' => 1,
        );

        // Our event should be executed
        $this->assertTrue($action->execute($event));

        // Our task should be assigned to nobody and have the blue color
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals('blue', $task['color_id']);
    }
}
