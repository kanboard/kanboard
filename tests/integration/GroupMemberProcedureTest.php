<?php

require_once __DIR__.'/BaseProcedureTest.php';

class GroupMemberProcedureTest extends BaseProcedureTest
{
    protected $username = 'user-group-member';
    protected $groupName1 = 'My group member A';
    protected $groupName2 = 'My group member B';

    public function testAll()
    {
        $this->assertCreateGroups();
        $this->assertCreateUser();
        $this->assertAddMember();
        $this->assertGetMembers();
        $this->assertIsGroupMember();
        $this->assertGetGroups();
        $this->assertRemove();
    }

    public function assertAddMember()
    {
        $this->assertTrue($this->app->addGroupMember($this->groupId1, $this->userId));
    }

    public function assertGetMembers()
    {
        $members = $this->app->getGroupMembers($this->groupId1);
        $this->assertCount(1, $members);
        $this->assertEquals($this->username, $members[0]['username']);
    }

    public function assertIsGroupMember()
    {
        $this->assertTrue($this->app->isGroupMember($this->groupId1, $this->userId));
        $this->assertFalse($this->app->isGroupMember($this->groupId1, $this->adminUserId));
    }

    public function assertGetGroups()
    {
        $groups = $this->app->getMemberGroups($this->userId);
        $this->assertCount(1, $groups);
        $this->assertEquals($this->groupId1, $groups[0]['id']);
        $this->assertEquals($this->groupName1, $groups[0]['name']);
    }

    public function assertRemove()
    {
        $this->assertTrue($this->app->removeGroupMember($this->groupId1, $this->userId));
        $this->assertFalse($this->app->isGroupMember($this->groupId1, $this->userId));
    }
}
