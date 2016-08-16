<?php

require_once __DIR__.'/BaseProcedureTest.php';

class GroupProcedureTest extends BaseProcedureTest
{
    public function testAll()
    {
        $this->assertCreateGroups();
        $this->assertGetAllGroups();
        $this->assertGetGroup();
        $this->assertUpdateGroup();
        $this->assertRemove();
    }

    public function assertGetAllGroups()
    {
        $groups = $this->app->getAllGroups();
        $this->assertNotEmpty($groups);
        $this->assertArrayHasKey('name', $groups[0]);
        $this->assertArrayHasKey('external_id', $groups[0]);
    }

    public function assertGetGroup()
    {
        $group = $this->app->getGroup($this->groupId1);
        $this->assertNotEmpty($group);
        $this->assertEquals($this->groupName1, $group['name']);
        $this->assertEquals('', $group['external_id']);
    }

    public function assertUpdateGroup()
    {
        $this->assertTrue($this->app->updateGroup(array(
            'group_id' => $this->groupId2,
            'name' => 'My Group C',
            'external_id' => 'something else',
        )));

        $group = $this->app->getGroup($this->groupId2);
        $this->assertNotEmpty($group);
        $this->assertEquals('My Group C', $group['name']);
        $this->assertEquals('something else', $group['external_id']);
    }

    public function assertRemove()
    {
        $this->assertTrue($this->app->removeGroup($this->groupId1));
    }
}
