<?php

require_once __DIR__.'/../Base.php';

use Event\TaskLinkEvent;
use Model\Task;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\TaskLink;
use Model\Project;

class TaskAssignColorLinkTest extends Base
{
    public function testExecute()
    {
        $action = new Action\TaskAssignColorLink($this->container, 1, TaskLink::EVENT_CREATE_UPDATE);
        $action->setParam('link_id', 2);
        $action->setParam('color_id', 'green');

        // We create a task in the first column
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $tl = new TaskLink($this->container);
        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        // The color should be yellow
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals('yellow', $task['color_id']);

        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'link_id' => 2,
        );

        // Our event should be executed
        $this->assertTrue($action->execute(new TaskLinkEvent($event)));

        // The color should be green
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals('green', $task['color_id']);
    }
}
