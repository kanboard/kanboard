<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\TaskStatus;
use Model\Project;
use Model\ProjectPermission;
use Model\Category;
use Model\User;

class TaskTest extends Base
{
    public function testRemove()
    {
        $t = new Task($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1)));

        $this->assertTrue($t->remove(1));
        $this->assertFalse($t->remove(1234));
    }
}
