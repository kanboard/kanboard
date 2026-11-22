<?php

namespace KanboardTests\units\Api\Procedure;

use KanboardTests\units\Base;
use Kanboard\Api\Procedure\UserProcedure;
use Kanboard\Core\Security\Role;
use Kanboard\Model\GroupMemberModel;
use Kanboard\Model\GroupModel;
use Kanboard\User\LdapUserProvider;

class UserProcedureTest extends Base
{
    public function testCreateLdapUserSynchronizesGroups()
    {
        $groupModel = new GroupModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);

        $this->assertEquals(1, $groupModel->create('Group 1', 'cn=group1,dc=example,dc=org'));
        $this->assertEquals(2, $groupModel->create('Group 2', 'cn=group2,dc=example,dc=org'));

        $procedure = $this
            ->getMockBuilder(UserProcedure::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('getLdapUser'))
            ->getMock();

        $procedure
            ->expects($this->once())
            ->method('getLdapUser')
            ->with('bob')
            ->willReturn(new LdapUserProvider(
                'uid=bob,dc=example,dc=org',
                'bob',
                'Bob',
                'bob@example.org',
                Role::APP_USER,
                array('cn=group1,dc=example,dc=org')
            ));

        $userId = $procedure->createLdapUser('bob');
        $this->assertEquals(2, $userId);

        $this->assertTrue($groupMemberModel->isMember(1, $userId));
        $this->assertFalse($groupMemberModel->isMember(2, $userId));
    }
}
