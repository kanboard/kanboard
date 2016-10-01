<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Model\GroupModel;
use Kanboard\Core\Group\GroupManager;
use Kanboard\Group\DatabaseBackendGroupProvider;

class GroupManagerTest extends Base
{
    public function testFind()
    {
        $groupModel = new GroupModel($this->container);
        $groupManager = new GroupManager;

        $this->assertEquals(1, $groupModel->create('Group 1'));
        $this->assertEquals(2, $groupModel->create('Group 2'));

        $this->assertEmpty($groupManager->find('group 1'));

        $groupManager->register(new DatabaseBackendGroupProvider($this->container));
        $groupManager->register(new DatabaseBackendGroupProvider($this->container));

        $groups = $groupManager->find('group 1');
        $this->assertCount(1, $groups);
        $this->assertInstanceOf('Kanboard\Group\DatabaseGroupProvider', $groups[0]);
        $this->assertEquals('Group 1', $groups[0]->getName());
        $this->assertEquals('', $groups[0]->getExternalId());
        $this->assertEquals(1, $groups[0]->getInternalId());

        $groups = $groupManager->find('grou');
        $this->assertCount(2, $groups);
        $this->assertInstanceOf('Kanboard\Group\DatabaseGroupProvider', $groups[0]);
        $this->assertInstanceOf('Kanboard\Group\DatabaseGroupProvider', $groups[1]);
        $this->assertEquals('Group 1', $groups[0]->getName());
        $this->assertEquals('Group 2', $groups[1]->getName());
        $this->assertEquals('', $groups[0]->getExternalId());
        $this->assertEquals('', $groups[1]->getExternalId());
        $this->assertEquals(1, $groups[0]->getInternalId());
        $this->assertEquals(2, $groups[1]->getInternalId());
    }
}
