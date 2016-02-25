<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\User\GroupSync;
use Kanboard\Model\Group;
use Kanboard\Model\GroupMember;

class GroupSyncTest extends Base
{
    public function testSynchronize()
    {
        $group = new Group($this->container);
        $groupMember = new GroupMember($this->container);
        $groupSync = new GroupSync($this->container);

        $this->assertEquals(1, $group->create('My Group 1', 'externalId1'));
        $this->assertEquals(2, $group->create('My Group 2', 'externalId2'));

        $this->assertTrue($groupMember->addUser(1, 1));

        $this->assertTrue($groupMember->isMember(1, 1));
        $this->assertFalse($groupMember->isMember(2, 1));

        $groupSync->synchronize(1, array('externalId1', 'externalId2', 'externalId3'));

        $this->assertTrue($groupMember->isMember(1, 1));
        $this->assertTrue($groupMember->isMember(2, 1));
    }
}
