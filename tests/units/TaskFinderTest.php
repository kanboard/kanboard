<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\TaskFinder;
use Model\Project;
use Model\ProjectPermission;
use Model\Category;
use Model\User;

class TaskFinderTest extends Base
{
    public function testGetOverdueTasks()
    {
        $t = new Task($this->registry);
        $tf = new TaskFinder($this->registry);
        $p = new Project($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $t->create(array('title' => 'Task #1', 'project_id' => 1, 'date_due' => strtotime('-1 day'))));
        $this->assertEquals(2, $t->create(array('title' => 'Task #2', 'project_id' => 1, 'date_due' => strtotime('+1 day'))));
        $this->assertEquals(3, $t->create(array('title' => 'Task #3', 'project_id' => 1, 'date_due' => 0)));
        $this->assertEquals(4, $t->create(array('title' => 'Task #3', 'project_id' => 1)));

        $tasks = $tf->getOverdueTasks();
        $this->assertNotEmpty($tasks);
        $this->assertTrue(is_array($tasks));
        $this->assertEquals(1, count($tasks));
        $this->assertEquals('Task #1', $tasks[0]['title']);
    }
}
