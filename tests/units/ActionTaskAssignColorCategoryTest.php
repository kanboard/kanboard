<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\Project;
use Model\Category;
use Event\GenericEvent;

class ActionTaskAssignColorCategory extends Base
{
    public function testBadProject()
    {
        $action = new Action\TaskAssignColorCategory($this->container, 3, Task::EVENT_CREATE_UPDATE);

        $event = array(
            'project_id' => 2,
            'task_id' => 3,
            'column_id' => 5,
        );

        $this->assertFalse($action->isExecutable($event));
        $this->assertFalse($action->execute(new GenericEvent($event)));
    }

    public function testExecute()
    {
        $action = new Action\TaskAssignColorCategory($this->container, 1, Task::EVENT_CREATE_UPDATE);
        $action->setParam('category_id', 1);
        $action->setParam('color_id', 'blue');

        // We create a task in the first column
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $c = new Category($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $c->create(array('name' => 'c1')));
        $this->assertEquals(2, $c->create(array('name' => 'c2')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1, 'color_id' => 'green', 'category_id' => 2)));

        // We create an event but we don't do anything
        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'column_id' => 1,
            'category_id' => 2,
            'position' => 2,
        );

        // Our event should NOT be executed
        $this->assertFalse($action->execute(new GenericEvent($event)));

        // Our task should be assigned to the ategory_id=1 and have the green color
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(2, $task['category_id']);
        $this->assertEquals('green', $task['color_id']);

        // We create an event to move the task
        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'column_id' => 1,
            'position' => 5,
            'category_id' => 1,
        );

        // Our event should be executed
        $this->assertTrue($action->execute(new GenericEvent($event)));

        // Our task should have the blue color
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals('blue', $task['color_id']);
    }
}
