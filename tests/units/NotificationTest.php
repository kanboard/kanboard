<?php

require_once __DIR__.'/Base.php';

use Model\User;
use Model\Project;
use Model\ProjectPermission;
use Model\Notification;

class NotificationTest extends Base
{
    public function testGetUsersWithNotification()
    {
        $u = new User($this->container);
        $p = new Project($this->container);
        $n = new Notification($this->container);
        $pp = new ProjectPermission($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));

        // Email + Notifications enabled
        $this->assertNotFalse($u->create(array('username' => 'user1', 'email' => 'user1@here', 'notifications_enabled' => 1)));

        // No email + Notifications enabled
        $this->assertNotFalse($u->create(array('username' => 'user2', 'email' => '', 'notifications_enabled' => 1)));

        // Email + Notifications enabled
        $this->assertNotFalse($u->create(array('username' => 'user3', 'email' => 'user3@here', 'notifications_enabled' => 1)));

        // No email + notifications disabled
        $this->assertNotFalse($u->create(array('username' => 'user4')));

        // Nobody is member of any projects
        $this->assertEmpty($pp->getMembers(1));
        $this->assertEmpty($n->getUsersWithNotification(1));

        // We allow all users to be member of our projects
        $this->assertTrue($pp->addMember(1, 1));
        $this->assertTrue($pp->addMember(1, 2));
        $this->assertTrue($pp->addMember(1, 3));
        $this->assertTrue($pp->addMember(1, 4));

        $this->assertNotEmpty($pp->getMembers(1));
        $users = $n->getUsersWithNotification(1);

        $this->assertNotEmpty($users);
        $this->assertEquals(2, count($users));
        $this->assertEquals('user1@here', $users[0]['email']);
        $this->assertEquals('user3@here', $users[1]['email']);
    }

    public function testGetUserList()
    {
        $u = new User($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $n = new Notification($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));
        $this->assertEquals(3, $p->create(array('name' => 'UnitTest3', 'is_everybody_allowed' => 1)));

        // Email + Notifications enabled
        $this->assertNotFalse($u->create(array('username' => 'user1', 'email' => 'user1@here', 'notifications_enabled' => 1)));

        // No email + Notifications enabled
        $this->assertNotFalse($u->create(array('username' => 'user2', 'email' => '', 'notifications_enabled' => 1)));

        // Email + Notifications enabled
        $this->assertNotFalse($u->create(array('username' => 'user3', 'email' => 'user3@here', 'notifications_enabled' => 1)));

        // No email + notifications disabled
        $this->assertNotFalse($u->create(array('username' => 'user4')));

        // We allow all users to be member of our projects
        $this->assertTrue($pp->addMember(1, 1));
        $this->assertTrue($pp->addMember(1, 2));
        $this->assertTrue($pp->addMember(1, 3));
        $this->assertTrue($pp->addMember(1, 4));

        $this->assertTrue($pp->addMember(2, 1));
        $this->assertTrue($pp->addMember(2, 2));
        $this->assertTrue($pp->addMember(2, 3));
        $this->assertTrue($pp->addMember(2, 4));

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

        // Project #3 allow everybody
        $users = $n->getUsersList(3);
        $this->assertNotEmpty($users);
        $this->assertEquals(1, count($users));
        $this->assertEquals('user1@here', $users[0]['email']);

        $users = $n->getUsersWithNotification(3);
        $this->assertNotEmpty($users);
        $this->assertEquals(2, count($users));
        $this->assertEquals('user1@here', $users[0]['email']);
        $this->assertEquals('user3@here', $users[1]['email']);
    }
}
