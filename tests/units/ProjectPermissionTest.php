<?php

require_once __DIR__.'/Base.php';

use Model\Project;
use Model\ProjectPermission;
use Model\User;

class ProjectPermissionTest extends Base
{
    public function testAllowEverybody()
    {
        $user = new User($this->container);
        $this->assertNotFalse($user->create(array('username' => 'unittest#1', 'password' => 'unittest')));
        $this->assertNotFalse($user->create(array('username' => 'unittest#2', 'password' => 'unittest')));

        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertFalse($pp->isEverybodyAllowed(1));
        $this->assertTrue($pp->isUserAllowed(1, 1));
        $this->assertFalse($pp->isUserAllowed(1, 2));
        $this->assertFalse($pp->isUserAllowed(1, 3));
        $this->assertEquals(array(), $pp->getMembers(1));
        $this->assertEquals(array('Unassigned'), $pp->getMemberList(1));

        $this->assertTrue($p->update(array('id' => 1, 'is_everybody_allowed' => 1)));
        $this->assertTrue($pp->isEverybodyAllowed(1));
        $this->assertTrue($pp->isUserAllowed(1, 1));
        $this->assertTrue($pp->isUserAllowed(1, 2));
        $this->assertTrue($pp->isUserAllowed(1, 3));
        $this->assertEquals(array('1' => 'admin', '2' => 'unittest#1', '3' => 'unittest#2'), $pp->getMembers(1));
        $this->assertEquals(array('Unassigned', '1' => 'admin', '2' => 'unittest#1', '3' => 'unittest#2'), $pp->getMemberList(1));
    }

    public function testDisallowEverybody()
    {
        // We create a regular user
        $user = new User($this->container);
        $user->create(array('username' => 'unittest', 'password' => 'unittest'));

        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        $this->assertEmpty($pp->getMembers(1)); // Nobody is specified for the given project
        $this->assertTrue($pp->isUserAllowed(1, 1)); // Admin should be allowed
        $this->assertFalse($pp->isUserAllowed(1, 2)); // Regular user should be denied
    }

    public function testAllowUser()
    {
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $user = new User($this->container);

        $user->create(array('username' => 'unittest', 'password' => 'unittest'));

        // We create a project
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        // We allow the admin user
        $this->assertTrue($pp->addMember(1, 1));
        $this->assertTrue($pp->addMember(1, 2));

        // Non-existant project
        $this->assertFalse($pp->addMember(50, 1));

        // Non-existant user
        $this->assertFalse($pp->addMember(1, 50));

        // Both users should be allowed
        $this->assertEquals(array('1' => 'admin', '2' => 'unittest'), $pp->getMembers(1));
        $this->assertTrue($pp->isUserAllowed(1, 1));
        $this->assertTrue($pp->isUserAllowed(1, 2));
    }

    public function testRevokeUser()
    {
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $user = new User($this->container);

        $user->create(array('username' => 'unittest', 'password' => 'unittest'));

        // We create a project
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        // We revoke our admin user (not existing row)
        $this->assertFalse($pp->revokeMember(1, 1));

        // We should have nobody in the users list
        $this->assertEmpty($pp->getMembers(1));

        // Only admin is allowed
        $this->assertTrue($pp->isUserAllowed(1, 1));
        $this->assertFalse($pp->isUserAllowed(1, 2));

        // We allow only the regular user
        $this->assertTrue($pp->addMember(1, 2));

        // All users should be allowed (admin and regular)
        $this->assertTrue($pp->isUserAllowed(1, 1));
        $this->assertTrue($pp->isUserAllowed(1, 2));

        // However, we should have only our regular user in the list
        $this->assertEquals(array('2' => 'unittest'), $pp->getMembers(1));

        // We allow our admin, we should have both in the list
        $this->assertTrue($pp->addMember(1, 1));
        $this->assertEquals(array('1' => 'admin', '2' => 'unittest'), $pp->getMembers(1));
        $this->assertTrue($pp->isUserAllowed(1, 1));
        $this->assertTrue($pp->isUserAllowed(1, 2));

        // We revoke the regular user
        $this->assertTrue($pp->revokeMember(1, 2));

        // Only admin should be allowed
        $this->assertTrue($pp->isUserAllowed(1, 1));
        $this->assertFalse($pp->isUserAllowed(1, 2));

        // We should have only admin in the list
        $this->assertEquals(array('1' => 'admin'), $pp->getMembers(1));

        // We revoke the admin user
        $this->assertTrue($pp->revokeMember(1, 1));
        $this->assertEmpty($pp->getMembers(1));

        // Only admin should be allowed again
        $this->assertTrue($pp->isUserAllowed(1, 1));
        $this->assertFalse($pp->isUserAllowed(1, 2));
    }

    public function testManager()
    {
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $u = new User($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'unittest', 'password' => 'unittest')));

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertFalse($pp->isMember(1, 2));
        $this->assertFalse($pp->isManager(1, 2));

        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2'), 1, true));
        $this->assertFalse($pp->isMember(2, 2));
        $this->assertFalse($pp->isManager(2, 2));

        $this->assertEquals(3, $p->create(array('name' => 'UnitTest3'), 2, true));
        $this->assertTrue($pp->isMember(3, 2));
        $this->assertTrue($pp->isManager(3, 2));

        $this->assertEquals(4, $p->create(array('name' => 'UnitTest4')));

        $this->assertTrue($pp->addManager(4, 2));
        $this->assertTrue($pp->isMember(4, 2));
        $this->assertTrue($pp->isManager(4, 2));

        $this->assertEquals(5, $p->create(array('name' => 'UnitTest5')));
        $this->assertTrue($pp->addMember(5, 2));
        $this->assertTrue($pp->changeRole(5, 2, 1));
        $this->assertTrue($pp->isMember(5, 2));
        $this->assertTrue($pp->isManager(5, 2));
        $this->assertTrue($pp->changeRole(5, 2, 0));
        $this->assertTrue($pp->isMember(5, 2));
        $this->assertFalse($pp->isManager(5, 2));
    }

    public function testUsersList()
    {
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);

        $user = new User($this->container);
        $user->create(array('username' => 'unittest', 'password' => 'unittest'));

        // We create project
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        // No restriction, we should have no body
        $this->assertEquals(
            array('Unassigned'),
            $pp->getMemberList(1)
        );

        // We allow only the regular user
        $this->assertTrue($pp->addMember(1, 2));

        $this->assertEquals(
            array(0 => 'Unassigned', 2 => 'unittest'),
            $pp->getMemberList(1)
        );

        // We allow the admin user
        $this->assertTrue($pp->addMember(1, 1));

        $this->assertEquals(
            array(0 => 'Unassigned', 1 => 'admin', 2 => 'unittest'),
            $pp->getMemberList(1)
        );

        // We revoke only the regular user
        $this->assertTrue($pp->revokeMember(1, 2));

        $this->assertEquals(
            array(0 => 'Unassigned', 1 => 'admin'),
            $pp->getMemberList(1)
        );

        // We revoke only the admin user, we should have everybody
        $this->assertTrue($pp->revokeMember(1, 1));

        $this->assertEquals(
            array(0 => 'Unassigned'),
            $pp->getMemberList(1)
        );
    }
}
