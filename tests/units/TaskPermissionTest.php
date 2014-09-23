<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\TaskPermission;
use Model\Project;
use Model\Category;
use Model\User;

class TaskPermissionTest extends Base
{
    public function testPrepareCreation()
    {
        $t = new Task($this->registry);
        $tp = new TaskPermission($this->registry);
        $p = new Project($this->registry);
        $u = new User($this->registry);

        $this->assertTrue($u->create(array('username' => 'toto', 'password' => '123456')));
        $this->assertTrue($u->create(array('username' => 'toto2', 'password' => '123456')));
        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $t->create(array('title' => 'Task #1', 'project_id' => 1, 'creator_id' => 1)));
        $this->assertEquals(2, $t->create(array('title' => 'Task #2', 'project_id' => 1, 'creator_id' => 2)));
        $this->assertEquals(3, $t->create(array('title' => 'Task #3', 'project_id' => 1, 'creator_id' => 3)));
        $this->assertEquals(4, $t->create(array('title' => 'Task #4', 'project_id' => 1)));

        // User #1 can remove everything
        $user = $u->getbyId(1);
        $this->assertNotEmpty($user);
        $u->updateSession($user);

        $task = $t->getbyId(1);
        $this->assertNotEmpty($task);
        $this->assertTrue($tp->canRemoveTask($task));

        // User #2 can't remove the task #1
        $user = $u->getbyId(2);
        $this->assertNotEmpty($user);
        $u->updateSession($user);

        $task = $t->getbyId(1);
        $this->assertNotEmpty($task);
        $this->assertFalse($tp->canRemoveTask($task));

        // User #1 can remove everything
        $user = $u->getbyId(1);
        $this->assertNotEmpty($user);
        $u->updateSession($user);

        $task = $t->getbyId(2);
        $this->assertNotEmpty($task);
        $this->assertTrue($tp->canRemoveTask($task));

        // User #2 can remove his own task
        $user = $u->getbyId(2);
        $this->assertNotEmpty($user);
        $u->updateSession($user);

        $task = $t->getbyId(2);
        $this->assertNotEmpty($task);
        $this->assertTrue($tp->canRemoveTask($task));

        // User #1 can remove everything
        $user = $u->getbyId(1);
        $this->assertNotEmpty($user);
        $u->updateSession($user);

        $task = $t->getbyId(3);
        $this->assertNotEmpty($task);
        $this->assertTrue($tp->canRemoveTask($task));

        // User #2 can't remove the task #3
        $user = $u->getbyId(2);
        $this->assertNotEmpty($user);
        $u->updateSession($user);

        $task = $t->getbyId(3);
        $this->assertNotEmpty($task);
        $this->assertFalse($tp->canRemoveTask($task));

        // User #1 can remove everything
        $user = $u->getbyId(1);
        $this->assertNotEmpty($user);
        $u->updateSession($user);

        $task = $t->getbyId(4);
        $this->assertNotEmpty($task);
        $this->assertTrue($tp->canRemoveTask($task));

        // User #2 can't remove the task #4
        $user = $u->getbyId(2);
        $this->assertNotEmpty($user);
        $u->updateSession($user);

        $task = $t->getbyId(4);
        $this->assertNotEmpty($task);
        $this->assertFalse($tp->canRemoveTask($task));
    }
}
