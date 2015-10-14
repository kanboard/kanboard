<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Action\TaskAssignColorColumn;

class TaskAssignColorColumnTest extends Base
{
    public function testColorChange()
    {
        $action = new TaskAssignColorColumn($this->container, 1, Task::EVENT_MOVE_COLUMN);
        $action->setParam('column_id', 2);
        $action->setParam('color_id', 'green');

        // We create a task in the first column
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1, 'color_id' => 'yellow')));

        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'column_id' => 2,
            'color_id' => 'green',
        );

        // Our event should be executed
        $this->assertTrue($action->execute(new GenericEvent($event)));

        // Our task should have color green
        $task = $tf->getById(1);
        $this->assertEquals('green', $task['color_id']);
    }
}
