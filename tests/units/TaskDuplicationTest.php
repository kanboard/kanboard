<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\TaskCreation;
use Model\TaskDuplication;
use Model\TaskFinder;
use Model\TaskStatus;
use Model\Project;
use Model\ProjectPermission;
use Model\Category;
use Model\User;

class TaskDuplicationTest extends Base
{
    public function testDuplicateSameProject()
    {
        $td = new TaskDuplication($this->container);
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

        $this->assertEquals(
            1,
            $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 3, 'owner_id' => 1, 'category_id' => 2, 'time_spent' => 4.4)
        ));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(2, $task['category_id']);
        $this->assertEquals(4.4, $task['time_spent']);

        // We duplicate our task
        $this->assertEquals(2, $td->duplicate(1));
        $this->assertTrue($this->container['event']->isEventTriggered(Task::EVENT_CREATE));
        $this->assertTrue($this->container['event']->isEventTriggered(Task::EVENT_CREATE_UPDATE));

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(Task::STATUS_OPEN, $task['is_active']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(1, $task['owner_id']);
        $this->assertEquals(2, $task['category_id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals('test', $task['title']);
        $this->assertEquals(0, $task['time_spent']);
    }

    public function testDuplicateAnotherProject()
    {
        $td = new TaskDuplication($this->container);
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

        // We duplicate our task to the 2nd project
        $this->assertEquals(2, $td->duplicateToProject(1, 2));
        $this->assertTrue($this->container['event']->isEventTriggered(Task::EVENT_CREATE));
        $this->assertTrue($this->container['event']->isEventTriggered(Task::EVENT_CREATE_UPDATE));

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

    public function testDuplicateAnotherProjectWithCategory()
    {
        $td = new TaskDuplication($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $c = new Category($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        $this->assertNotFalse($c->create(array('name' => 'Category #1', 'project_id' => 1)));
        $this->assertNotFalse($c->create(array('name' => 'Category #1', 'project_id' => 2)));
        $this->assertTrue($c->exists(1, 1));
        $this->assertTrue($c->exists(2, 2));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'category_id' => 1)));

        // We duplicate our task to the 2nd project
        $this->assertEquals(2, $td->duplicateToProject(1, 2));
        $this->assertTrue($this->container['event']->isEventTriggered(Task::EVENT_CREATE));
        $this->assertTrue($this->container['event']->isEventTriggered(Task::EVENT_CREATE_UPDATE));

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(2, $task['category_id']);
        $this->assertEquals(5, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('test', $task['title']);
    }

    public function testDuplicateAnotherProjectWithUser()
    {
        $td = new TaskDuplication($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 2)));

        // We duplicate our task to the 2nd project
        $this->assertEquals(2, $td->duplicateToProject(1, 2));
        $this->assertTrue($this->container['event']->isEventTriggered(Task::EVENT_CREATE));
        $this->assertTrue($this->container['event']->isEventTriggered(Task::EVENT_CREATE_UPDATE));

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(5, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('test', $task['title']);

        // We create a new user for our project
        $user = new User($this->container);
        $this->assertNotFalse($user->create(array('username' => 'unittest#1', 'password' => 'unittest')));
        $this->assertTrue($pp->allowUser(1, 2));
        $this->assertTrue($pp->allowUser(2, 2));
        $this->assertTrue($pp->isUserAllowed(1, 2));
        $this->assertTrue($pp->isUserAllowed(2, 2));

        // We duplicate our task to the 2nd project
        $this->assertEquals(3, $td->duplicateToProject(1, 2));

        // Check the values of the duplicated task
        $task = $tf->getById(3);
        $this->assertNotEmpty($task);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals(2, $task['owner_id']);
        $this->assertEquals(2, $task['project_id']);

        // We duplicate a task with a not allowed user
        $this->assertEquals(4, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 3)));
        $this->assertEquals(5, $td->duplicateToProject(4, 2));

        $task = $tf->getById(5);
        $this->assertNotEmpty($task);
        $this->assertEquals(3, $task['position']);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals(5, $task['column_id']);
    }

    public function testMoveAnotherProject()
    {
        $td = new TaskDuplication($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $user = new User($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'owner_id' => 1, 'category_id' => 10, 'position' => 333)));

        // We duplicate our task to the 2nd project
        $this->assertTrue($td->moveToProject(1, 2));

        // Check the values of the moved task
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['owner_id']);
        $this->assertEquals(0, $task['category_id']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals(5, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals('test', $task['title']);
    }

    public function testMoveAnotherProjectWithCategory()
    {
        $td = new TaskDuplication($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $c = new Category($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        $this->assertNotFalse($c->create(array('name' => 'Category #1', 'project_id' => 1)));
        $this->assertNotFalse($c->create(array('name' => 'Category #1', 'project_id' => 2)));
        $this->assertTrue($c->exists(1, 1));
        $this->assertTrue($c->exists(2, 2));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'category_id' => 1)));

        // We move our task to the 2nd project
        $this->assertTrue($td->moveToProject(1, 2));

        // Check the values of the duplicated task
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(2, $task['category_id']);
        $this->assertEquals(5, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('test', $task['title']);
    }

    public function testMoveAnotherProjectWithUser()
    {
        $td = new TaskDuplication($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $user = new User($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        // We create a new user for our project
        $this->assertNotFalse($user->create(array('username' => 'unittest#1', 'password' => 'unittest')));
        $this->assertTrue($pp->allowUser(1, 2));
        $this->assertTrue($pp->allowUser(2, 2));
        $this->assertTrue($pp->isUserAllowed(1, 2));
        $this->assertTrue($pp->isUserAllowed(2, 2));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 2)));

        // We move our task to the 2nd project
        $this->assertTrue($td->moveToProject(1, 2));

        // Check the values of the moved task
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['owner_id']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals(5, $task['column_id']);
    }

    public function testMoveAnotherProjectWithForbiddenUser()
    {
        $td = new TaskDuplication($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $user = new User($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        // We create a new user for our project
        $this->assertNotFalse($user->create(array('username' => 'unittest#1', 'password' => 'unittest')));
        $this->assertTrue($pp->allowUser(1, 2));
        $this->assertTrue($pp->allowUser(2, 2));
        $this->assertTrue($pp->isUserAllowed(1, 2));
        $this->assertTrue($pp->isUserAllowed(2, 2));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 3)));

        // We move our task to the 2nd project
        $this->assertTrue($td->moveToProject(1, 2));

        // Check the values of the moved task
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals(5, $task['column_id']);
    }
}
