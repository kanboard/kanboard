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

    public function testDuplicateToTheSameProject()
    {
        $t = new Task($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $c = new Category($this->container);

        // We create a task and a project
        $this->assertEquals(1, $p->create(array('name' => 'test1')));

        // Some categories
        $this->assertNotFalse($c->create(array('name' => 'Category #1', 'project_id' => 1)));
        $this->assertNotFalse($c->create(array('name' => 'Category #2', 'project_id' => 1)));
        $this->assertTrue($c->exists(1, 1));
        $this->assertTrue($c->exists(2, 1));

        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 3, 'owner_id' => 1, 'category_id' => 2)));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['position']);

        // We duplicate our task
        $this->assertEquals(2, $t->duplicateToSameProject($task));
        $this->assertTrue($this->container['event']->isEventTriggered(Task::EVENT_CREATE));

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(Task::STATUS_OPEN, $task['is_active']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(1, $task['owner_id']);
        $this->assertEquals(2, $task['category_id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(2, $task['position']);
    }

    public function testDuplicateToAnotherProject()
    {
        $t = new Task($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $c = new Category($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        $this->assertNotFalse($c->create(array('name' => 'Category #1', 'project_id' => 1)));
        $this->assertTrue($c->exists(1, 1));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 1, 'category_id' => 1)));
        $task = $tf->getById(1);

        // We duplicate our task to the 2nd project
        $this->assertEquals(2, $t->duplicateToAnotherProject(2, $task));
        $this->assertTrue($this->container['event']->isEventTriggered(Task::EVENT_CREATE));

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['owner_id']);
        $this->assertEquals(0, $task['category_id']);
        $this->assertEquals(5, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('test', $task['title']);
    }

    public function testMoveToAnotherProject()
    {
        $t = new Task($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $user = new User($this->container);

        // We create a regular user
        $user->create(array('username' => 'unittest1', 'password' => 'unittest'));
        $user->create(array('username' => 'unittest2', 'password' => 'unittest'));

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1, 'category_id' => 10, 'position' => 333)));
        $this->assertEquals(2, $tc->create(array('title' => 'test2', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 3, 'category_id' => 10, 'position' => 333)));

        // We duplicate our task to the 2nd project
        $task = $tf->getById(1);
        $this->assertEquals(1, $t->moveToAnotherProject(2, $task));
        //$this->assertTrue($this->container['event']->isEventTriggered(Task::EVENT_CREATE));

        // Check the values of the duplicated task
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['owner_id']);
        $this->assertEquals(0, $task['category_id']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals(5, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals('test', $task['title']);

        // We allow only one user on the second project
        $this->assertTrue($pp->allowUser(2, 2));

        // The owner should be reseted
        $task = $tf->getById(2);
        $this->assertEquals(2, $t->moveToAnotherProject(2, $task));

        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
    }

    public function testEvents()
    {
        $t = new Task($this->container);
        $tc = new TaskCreation($this->container);
        $ts = new TaskStatus($this->container);
        $p = new Project($this->container);

        // We create a project
        $this->assertEquals(1, $p->create(array('name' => 'test')));

        // We create task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1)));

        // We update a task
        $this->assertTrue($t->update(array('title' => 'test2', 'id' => 1)));
        $this->assertTrue($this->container['event']->isEventTriggered(Task::EVENT_UPDATE));
        $this->assertTrue($this->container['event']->isEventTriggered(Task::EVENT_CREATE_UPDATE));

        // We change the assignee
        $this->assertTrue($t->update(array('owner_id' => 1, 'id' => 1)));
        $this->assertTrue($this->container['event']->isEventTriggered(Task::EVENT_ASSIGNEE_CHANGE));
    }
}
