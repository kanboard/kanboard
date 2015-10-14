<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectPermission;
use Kanboard\Model\Category;
use Kanboard\Model\User;

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
        $this->assertCount(1, $tasks);
        $this->assertEquals('Task #1', $tasks[0]['title']);
    }

    public function testGetOverdueTasksByProject()
    {
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(2, $p->create(array('name' => 'Project #2')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1, 'date_due' => strtotime('-1 day'))));
        $this->assertEquals(2, $tc->create(array('title' => 'Task #2', 'project_id' => 2, 'date_due' => strtotime('-1 day'))));
        $this->assertEquals(3, $tc->create(array('title' => 'Task #3', 'project_id' => 1, 'date_due' => strtotime('+1 day'))));
        $this->assertEquals(4, $tc->create(array('title' => 'Task #4', 'project_id' => 1, 'date_due' => 0)));
        $this->assertEquals(5, $tc->create(array('title' => 'Task #5', 'project_id' => 1)));

        $tasks = $tf->getOverdueTasksByProject(1);
        $this->assertNotEmpty($tasks);
        $this->assertTrue(is_array($tasks));
        $this->assertcount(1, $tasks);
        $this->assertEquals('Task #1', $tasks[0]['title']);
    }

    public function testGetOverdueTasksByUser()
    {
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(2, $p->create(array('name' => 'Project #2')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1, 'owner_id' => 1, 'date_due' => strtotime('-1 day'))));
        $this->assertEquals(2, $tc->create(array('title' => 'Task #2', 'project_id' => 2, 'owner_id' => 1, 'date_due' => strtotime('-1 day'))));
        $this->assertEquals(3, $tc->create(array('title' => 'Task #3', 'project_id' => 1, 'date_due' => strtotime('+1 day'))));
        $this->assertEquals(4, $tc->create(array('title' => 'Task #4', 'project_id' => 1, 'date_due' => 0)));
        $this->assertEquals(5, $tc->create(array('title' => 'Task #5', 'project_id' => 1)));

        $tasks = $tf->getOverdueTasksByUser(1);
        $this->assertNotEmpty($tasks);
        $this->assertTrue(is_array($tasks));
        $this->assertCount(2, $tasks);

        $this->assertEquals(1, $tasks[0]['id']);
        $this->assertEquals('Task #1', $tasks[0]['title']);
        $this->assertEquals(1, $tasks[0]['owner_id']);
        $this->assertEquals(1, $tasks[0]['project_id']);
        $this->assertEquals('Project #1', $tasks[0]['project_name']);
        $this->assertEquals('admin', $tasks[0]['assignee_username']);
        $this->assertEquals('', $tasks[0]['assignee_name']);

        $this->assertEquals('Task #2', $tasks[1]['title']);
    }

    public function testCountByProject()
    {
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(2, $p->create(array('name' => 'Project #2')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'Task #2', 'project_id' => 2)));
        $this->assertEquals(3, $tc->create(array('title' => 'Task #3', 'project_id' => 2)));

        $this->assertEquals(1, $tf->countByProjectId(1));
        $this->assertEquals(2, $tf->countByProjectId(2));
    }
}
