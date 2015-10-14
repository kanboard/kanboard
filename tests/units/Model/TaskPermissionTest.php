<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\TaskPermission;
use Kanboard\Model\Project;
use Kanboard\Model\Category;
use Kanboard\Model\User;
use Kanboard\Model\UserSession;

class TaskPermissionTest extends Base
{
    public function testPrepareCreation()
    {
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $tp = new TaskPermission($this->container);
        $p = new Project($this->container);
        $u = new User($this->container);
        $us = new UserSession($this->container);

        $this->assertNotFalse($u->create(array('username' => 'toto', 'password' => '123456')));
        $this->assertNotFalse($u->create(array('username' => 'toto2', 'password' => '123456')));
        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1, 'creator_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'Task #2', 'project_id' => 1, 'creator_id' => 2)));
        $this->assertEquals(3, $tc->create(array('title' => 'Task #3', 'project_id' => 1, 'creator_id' => 3)));
        $this->assertEquals(4, $tc->create(array('title' => 'Task #4', 'project_id' => 1)));

        // User #1 can remove everything
        $user = $u->getbyId(1);
        $this->assertNotEmpty($user);
        $us->refresh($user);

        $task = $tf->getbyId(1);
        $this->assertNotEmpty($task);
        $this->assertTrue($tp->canRemoveTask($task));

        // User #2 can't remove the task #1
        $user = $u->getbyId(2);
        $this->assertNotEmpty($user);
        $us->refresh($user);

        $task = $tf->getbyId(1);
        $this->assertNotEmpty($task);
        $this->assertFalse($tp->canRemoveTask($task));

        // User #1 can remove everything
        $user = $u->getbyId(1);
        $this->assertNotEmpty($user);
        $us->refresh($user);

        $task = $tf->getbyId(2);
        $this->assertNotEmpty($task);
        $this->assertTrue($tp->canRemoveTask($task));

        // User #2 can remove his own task
        $user = $u->getbyId(2);
        $this->assertNotEmpty($user);
        $us->refresh($user);

        $task = $tf->getbyId(2);
        $this->assertNotEmpty($task);
        $this->assertTrue($tp->canRemoveTask($task));

        // User #1 can remove everything
        $user = $u->getbyId(1);
        $this->assertNotEmpty($user);
        $us->refresh($user);

        $task = $tf->getbyId(3);
        $this->assertNotEmpty($task);
        $this->assertTrue($tp->canRemoveTask($task));

        // User #2 can't remove the task #3
        $user = $u->getbyId(2);
        $this->assertNotEmpty($user);
        $us->refresh($user);

        $task = $tf->getbyId(3);
        $this->assertNotEmpty($task);
        $this->assertFalse($tp->canRemoveTask($task));

        // User #1 can remove everything
        $user = $u->getbyId(1);
        $this->assertNotEmpty($user);
        $us->refresh($user);

        $task = $tf->getbyId(4);
        $this->assertNotEmpty($task);
        $this->assertTrue($tp->canRemoveTask($task));

        // User #2 can't remove the task #4
        $user = $u->getbyId(2);
        $this->assertNotEmpty($user);
        $us->refresh($user);

        $task = $tf->getbyId(4);
        $this->assertNotEmpty($task);
        $this->assertFalse($tp->canRemoveTask($task));
    }
}
