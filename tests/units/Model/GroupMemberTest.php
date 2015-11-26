<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Group;
use Kanboard\Model\User;
use Kanboard\Model\GroupMember;

class GroupMemberTest extends Base
{
    public function testAddRemove()
    {
        $groupModel = new Group($this->container);
        $groupMemberModel = new GroupMember($this->container);

        $this->assertEquals(1, $groupModel->create('Test'));

        $this->assertTrue($groupMemberModel->addUser(1, 1));
        $this->assertFalse($groupMemberModel->addUser(1, 1));

        $users = $groupMemberModel->getMembers(1);
        $this->assertCount(1, $users);
        $this->assertEquals('admin', $users[0]['username']);

        $this->assertEmpty($groupMemberModel->getNotMembers(1));

        $this->assertTrue($groupMemberModel->removeUser(1, 1));
        $this->assertFalse($groupMemberModel->removeUser(1, 1));

        $this->assertEmpty($groupMemberModel->getMembers(1));
    }

    public function testMembers()
    {
        $userModel = new User($this->container);
        $groupModel = new Group($this->container);
        $groupMemberModel = new GroupMember($this->container);

        $this->assertEquals(1, $groupModel->create('Group A'));
        $this->assertEquals(2, $groupModel->create('Group B'));

        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user3')));
        $this->assertEquals(5, $userModel->create(array('username' => 'user4')));

        $this->assertTrue($groupMemberModel->addUser(1, 1));
        $this->assertTrue($groupMemberModel->addUser(1, 2));
        $this->assertTrue($groupMemberModel->addUser(1, 5));
        $this->assertTrue($groupMemberModel->addUser(2, 3));
        $this->assertTrue($groupMemberModel->addUser(2, 4));
        $this->assertTrue($groupMemberModel->addUser(2, 5));

        $users = $groupMemberModel->getMembers(1);
        $this->assertCount(3, $users);
        $this->assertEquals('admin', $users[0]['username']);
        $this->assertEquals('user1', $users[1]['username']);
        $this->assertEquals('user4', $users[2]['username']);

        $users = $groupMemberModel->getNotMembers(1);
        $this->assertCount(2, $users);
        $this->assertEquals('user2', $users[0]['username']);
        $this->assertEquals('user3', $users[1]['username']);

        $users = $groupMemberModel->getMembers(2);
        $this->assertCount(3, $users);
        $this->assertEquals('user2', $users[0]['username']);
        $this->assertEquals('user3', $users[1]['username']);
        $this->assertEquals('user4', $users[2]['username']);

        $users = $groupMemberModel->getNotMembers(2);
        $this->assertCount(2, $users);
        $this->assertEquals('admin', $users[0]['username']);
        $this->assertEquals('user1', $users[1]['username']);
    }
}
