<?php

require_once __DIR__.'/Base.php';

use Model\Project;
use Model\User;
use Model\Task;
use Model\Acl;
use Model\Board;

class ProjectTest extends Base
{
    public function testCreation()
    {
        $p = new Project($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertNotEmpty($p->getById(1));
    }

    public function testAllowEverybody()
    {
        // We create a regular user
        $user = new User($this->registry);
        $user->create(array('username' => 'unittest', 'password' => 'unittest'));

        $p = new Project($this->registry);
        $this->assertEmpty($p->getAllowedUsers(1)); // Nobody is specified for the given project
        $this->assertTrue($p->isUserAllowed(1, 1)); // Everybody should be allowed
        $this->assertTrue($p->isUserAllowed(1, 2)); // Everybody should be allowed
    }

    public function testAllowUser()
    {
        $p = new Project($this->registry);
        $user = new User($this->registry);
        $user->create(array('username' => 'unittest', 'password' => 'unittest'));

        // We create a project
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        // We allow the admin user
        $this->assertTrue($p->allowUser(1, 1));

        // Non-existant project
        $this->assertFalse($p->allowUser(50, 1));

        // Non-existant user
        $this->assertFalse($p->allowUser(1, 50));

        // Our admin user should be allowed
        $this->assertEquals(array('1' => 'admin'), $p->getAllowedUsers(1));
        $this->assertTrue($p->isUserAllowed(1, 1));

        // Our regular user should be forbidden
        $this->assertFalse($p->isUserAllowed(1, 2));
    }

    public function testRevokeUser()
    {
        $p = new Project($this->registry);

        $user = new User($this->registry);
        $user->create(array('username' => 'unittest', 'password' => 'unittest'));

        // We create a project
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        // We revoke our admin user
        $this->assertTrue($p->revokeUser(1, 1));

        // We should have nobody in the users list
        $this->assertEmpty($p->getAllowedUsers(1));

        // Our admin user and our regular user should be allowed
        $this->assertTrue($p->isUserAllowed(1, 1));
        $this->assertTrue($p->isUserAllowed(1, 2));

        // We allow only the regular user
        $this->assertTrue($p->allowUser(1, 2));

        // All users should be allowed (admin and regular)
        $this->assertTrue($p->isUserAllowed(1, 1));
        $this->assertTrue($p->isUserAllowed(1, 2));

        // However, we should have only our regular user in the list
        $this->assertEquals(array('2' => 'unittest'), $p->getAllowedUsers(1));

        // We allow our admin, we should have both in the list
        $this->assertTrue($p->allowUser(1, 1));
        $this->assertEquals(array('1' => 'admin', '2' => 'unittest'), $p->getAllowedUsers(1));
        $this->assertTrue($p->isUserAllowed(1, 1));
        $this->assertTrue($p->isUserAllowed(1, 2));

        // We revoke the regular user
        $this->assertTrue($p->revokeUser(1, 2));

        // Only admin should be allowed
        $this->assertTrue($p->isUserAllowed(1, 1));
        $this->assertFalse($p->isUserAllowed(1, 2));

        // We should have only admin in the list
        $this->assertEquals(array('1' => 'admin'), $p->getAllowedUsers(1));

        // We revoke the admin user
        $this->assertTrue($p->revokeUser(1, 1));
        $this->assertEmpty($p->getAllowedUsers(1));

        // Everybody should be allowed again
        $this->assertTrue($p->isUserAllowed(1, 1));
        $this->assertTrue($p->isUserAllowed(1, 2));
    }

    public function testUsersList()
    {
        $p = new Project($this->registry);

        $user = new User($this->registry);
        $user->create(array('username' => 'unittest', 'password' => 'unittest'));

        // We create project
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        // No restriction, we should have everybody
        $this->assertEquals(
            array('Unassigned', 'admin', 'unittest'),
            $p->getUsersList(1)
        );

        // We allow only the regular user
        $this->assertTrue($p->allowUser(1, 2));

        $this->assertEquals(
            array(0 => 'Unassigned', 2 => 'unittest'),
            $p->getUsersList(1)
        );

        // We allow the admin user
        $this->assertTrue($p->allowUser(1, 1));

        $this->assertEquals(
            array(0 => 'Unassigned', 1 => 'admin', 2 => 'unittest'),
            $p->getUsersList(1)
        );

        // We revoke only the regular user
        $this->assertTrue($p->revokeUser(1, 2));

        $this->assertEquals(
            array(0 => 'Unassigned', 1 => 'admin'),
            $p->getUsersList(1)
        );

        // We revoke only the admin user, we should have everybody
        $this->assertTrue($p->revokeUser(1, 1));

        $this->assertEquals(
            array(0 => 'Unassigned', 1 => 'admin', 2 => 'unittest'),
            $p->getUsersList(1)
        );
    }
}
