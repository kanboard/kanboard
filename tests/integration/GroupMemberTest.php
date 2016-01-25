<?php

require_once __DIR__.'/Base.php';

class GroupMemberTest extends Base
{
    public function testAddMember()
    {
        $this->assertNotFalse($this->app->createGroup('My Group A'));
        $this->assertNotFalse($this->app->createGroup('My Group B'));

        $groupId = $this->getGroupId();
        $this->assertTrue($this->app->addGroupMember($groupId, 1));
    }

    public function testGetMembers()
    {
        $groups = $this->app->getAllGroups();
        $members = $this->app->getGroupMembers($groups[0]['id']);
        $this->assertCount(1, $members);
        $this->assertEquals('admin', $members[0]['username']);

        $this->assertSame(array(), $this->app->getGroupMembers($groups[1]['id']));
    }

    public function testIsGroupMember()
    {
        $groupId = $this->getGroupId();
        $this->assertTrue($this->app->isGroupMember($groupId, 1));
        $this->assertFalse($this->app->isGroupMember($groupId, 2));
    }

    public function testRemove()
    {
        $groupId = $this->getGroupId();
        $this->assertTrue($this->app->removeGroupMember($groupId, 1));
        $this->assertFalse($this->app->isGroupMember($groupId, 1));
    }
}
