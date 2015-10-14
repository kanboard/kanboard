<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\TaskLinkEvent;
use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\TaskLink;
use Kanboard\Model\Project;
use Kanboard\Action\TaskAssignColorLink;

class TaskAssignColorLinkTest extends Base
{
    public function testExecute()
    {
        $action = new TaskAssignColorLink($this->container, 1, TaskLink::EVENT_CREATE_UPDATE);
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
