<?php

require_once __DIR__.'/Base.php';

use Model\User;
use Model\Project;
use Model\Notification;

class NotificationTest extends Base
{
    public function testGetUserList()
    {
        $u = new User($this->registry);
        $p = new Project($this->registry);
        $n = new Notification($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));

        // Email + Notifications enabled
        $this->assertTrue($u->create(array('username' => 'user1', 'email' => 'user1@here', 'notifications_enabled' => 1)));

        // No email + Notifications enabled
        $this->assertTrue($u->create(array('username' => 'user2', 'email' => '', 'notifications_enabled' => 1)));

        // Email + Notifications enabled
        $this->assertTrue($u->create(array('username' => 'user3', 'email' => 'user3@here', 'notifications_enabled' => 1)));

        // No email + notifications disabled
        $this->assertTrue($u->create(array('username' => 'user4')));

        $users = $n->getUsersList(1);
        $this->assertNotEmpty($users);
        $this->assertEquals(2, count($users));
        $this->assertEquals('user1@here', $users[0]['email']);
        $this->assertEquals('user3@here', $users[1]['email']);

        $users = $n->getUsersList(2);
        $this->assertNotEmpty($users);
        $this->assertEquals(2, count($users));
        $this->assertEquals('user1@here', $users[0]['email']);
        $this->assertEquals('user3@here', $users[1]['email']);

        // User 3 choose to receive notification only for project 2
        $n->saveSettings(4, array('notifications_enabled' => 1, 'projects' => array(2 => true)));

        $users = $n->getUsersList(1);
        $this->assertNotEmpty($users);
        $this->assertEquals(1, count($users));
        $this->assertEquals('user1@here', $users[0]['email']);

        $users = $n->getUsersList(2);
        $this->assertNotEmpty($users);
        $this->assertEquals(2, count($users));
        $this->assertEquals('user1@here', $users[0]['email']);
        $this->assertEquals('user3@here', $users[1]['email']);

        // User 1 excluded
        $users = $n->getUsersList(1, array(2));
        $this->assertEmpty($users);

        $users = $n->getUsersList(2, array(2));
        $this->assertNotEmpty($users);
        $this->assertEquals(1, count($users));
        $this->assertEquals('user3@here', $users[0]['email']);
    }
}
