<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\GroupModel;
use Kanboard\Model\UserModel;
use Kanboard\Model\GroupMemberModel;

class GroupMemberTest extends Base
{
    public function testAddRemove()
    {
        $groupModel = new GroupModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);

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
        $userModel = new UserModel($this->container);
        $groupModel = new GroupModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);

        $this->assertEquals(1, $groupModel->create('Group A'));
        $this->assertEquals(2, $groupModel->create('Group B'));

        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user3')));
        $this->assertEquals(5, $userModel->create(array('username' => 'user4')));
        $this->assertEquals(6, $userModel->create(array('username' => 'user5')));
        $this->assertTrue($userModel->disable(6));

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

        $groups = $groupMemberModel->getGroups(1);
        $this->assertCount(1, $groups);
        $this->assertEquals(1, $groups[0]['id']);
        $this->assertEquals('Group A', $groups[0]['name']);

        $groups = $groupMemberModel->getGroups(2);
        $this->assertCount(1, $groups);
        $this->assertEquals(1, $groups[0]['id']);
        $this->assertEquals('Group A', $groups[0]['name']);

        $groups = $groupMemberModel->getGroups(3);
        $this->assertCount(1, $groups);
        $this->assertEquals(2, $groups[0]['id']);
        $this->assertEquals('Group B', $groups[0]['name']);

        $groups = $groupMemberModel->getGroups(4);
        $this->assertCount(1, $groups);
        $this->assertEquals(2, $groups[0]['id']);
        $this->assertEquals('Group B', $groups[0]['name']);

        $groups = $groupMemberModel->getGroups(5);
        $this->assertCount(2, $groups);
        $this->assertEquals(1, $groups[0]['id']);
        $this->assertEquals('Group A', $groups[0]['name']);
        $this->assertEquals(2, $groups[1]['id']);
        $this->assertEquals('Group B', $groups[1]['name']);

        $groups = $groupMemberModel->getGroups(6);
        $this->assertCount(0, $groups);
    }
}
