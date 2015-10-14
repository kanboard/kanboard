<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Action\TaskUpdateStartDate;

class TaskUpdateStartDateTest extends Base
{
    public function testExecute()
    {
        $action = new TaskUpdateStartDate($this->container, 1, Task::EVENT_MOVE_COLUMN);
        $action->setParam('column_id', 2);

        // We create a task in the first column
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1)));

        // The start date must be empty
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEmpty($task['date_started']);

        // We create an event to move the task to the 2nd column
        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'column_id' => 2,
        );

        // Our event should be executed
        $this->assertTrue($action->execute(new GenericEvent($event)));

        // Our task should be updated
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(time(), $task['date_started'], '', 2);
    }
}
