<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\Project;
use Model\ProjectPermission;
use Model\Category;
use Model\User;

class TaskFinderTest extends Base
{
    public function testGetOverdueTasks()
    {
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1, 'date_due' => strtotime('-1 day'))));
        $this->assertEquals(2, $tc->create(array('title' => 'Task #2', 'project_id' => 1, 'date_due' => strtotime('+1 day'))));
        $this->assertEquals(3, $tc->create(array('title' => 'Task #3', 'project_id' => 1, 'date_due' => 0)));
        $this->assertEquals(4, $tc->create(array('title' => 'Task #3', 'project_id' => 1)));

        $tasks = $tf->getOverdueTasks();
        $this->assertNotEmpty($tasks);
        $this->assertTrue(is_array($tasks));
        $this->assertEquals(1, count($tasks));
        $this->assertEquals('Task #1', $tasks[0]['title']);
    }
}
