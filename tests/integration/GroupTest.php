<?php

require_once __DIR__.'/Base.php';

class GroupTest extends Base
{
    public function testCreateGroup()
    {
        $this->assertNotFalse($this->app->createGroup('My Group A'));
        $this->assertNotFalse($this->app->createGroup('My Group B', '1234'));
    }

    public function testGetter()
    {
        $groups = $this->app->getAllGroups();
        $this->assertCount(2, $groups);
        $this->assertEquals('My Group A', $groups[0]['name']);
        $this->assertEquals('', $groups[0]['external_id']);
        $this->assertEquals('My Group B', $groups[1]['name']);
        $this->assertEquals('1234', $groups[1]['external_id']);

        $group = $this->app->getGroup($groups[0]['id']);
        $this->assertNotEmpty($group);
        $this->assertEquals('My Group A', $group['name']);
        $this->assertEquals('', $group['external_id']);
    }

    public function testUpdate()
    {
        $groups = $this->app->getAllGroups();

        $this->assertTrue($this->app->updateGroup(array('group_id' => $groups[0]['id'], 'name' => 'ABC', 'external_id' => 'something')));
        $this->assertTrue($this->app->updateGroup(array('group_id' => $groups[1]['id'], 'external_id' => '')));

        $groups = $this->app->getAllGroups();
        $this->assertEquals('ABC', $groups[0]['name']);
        $this->assertEquals('something', $groups[0]['external_id']);
        $this->assertEquals('', $groups[1]['external_id']);
    }

    public function testRemove()
    {
        $groups = $this->app->getAllGroups();
        $this->assertTrue($this->app->removeGroup($groups[0]['id']));
        $this->assertTrue($this->app->removeGroup($groups[1]['id']));
        $this->assertSame(array(), $this->app->getAllGroups());
    }
}
