<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Security\Role;
use Kanboard\Core\User\UserProfile;
use Kanboard\User\LdapUserProvider;
use Kanboard\User\DatabaseUserProvider;

class UserProfileTest extends Base
{
    public function testInitializeLocalUser()
    {
        $userProfile = new UserProfile($this->container);
        $user = new DatabaseUserProvider(array('id' => 1));

        $this->assertTrue($userProfile->initialize($user));
        $this->assertNotEmpty($_SESSION['user']);
        $this->assertEquals('admin', $_SESSION['user']['username']);
    }

    public function testInitializeLocalUserNotFound()
    {
        $userProfile = new UserProfile($this->container);
        $user = new DatabaseUserProvider(array('id' => 2));

        $this->assertFalse($userProfile->initialize($user));
        $this->assertFalse(isset($_SESSION['user']));
    }

    public function testInitializeRemoteUser()
    {
        $userProfile = new UserProfile($this->container);
        $user = new LdapUserProvider('ldapId', 'bob', 'Bob', '', Role::APP_MANAGER, array());

        $this->assertTrue($userProfile->initialize($user));
        $this->assertNotEmpty($_SESSION['user']);
        $this->assertEquals(2, $_SESSION['user']['id']);
        $this->assertEquals('bob', $_SESSION['user']['username']);
        $this->assertEquals(Role::APP_MANAGER, $_SESSION['user']['role']);

        $user = new LdapUserProvider('ldapId', 'bob', 'Bob', '', Role::APP_MANAGER, array());

        $this->assertTrue($userProfile->initialize($user));
        $this->assertNotEmpty($_SESSION['user']);
        $this->assertEquals(2, $_SESSION['user']['id']);
        $this->assertEquals('bob', $_SESSION['user']['username']);
    }

    public function testAssignRemoteUser()
    {
        $userProfile = new UserProfile($this->container);
        $user = new LdapUserProvider('ldapId', 'bob', 'Bob', '', Role::APP_MANAGER, array());

        $this->assertTrue($userProfile->assign(1, $user));
        $this->assertNotEmpty($_SESSION['user']);
        $this->assertEquals(1, $_SESSION['user']['id']);
        $this->assertEquals('admin', $_SESSION['user']['username']);
        $this->assertEquals('Bob', $_SESSION['user']['name']);
        $this->assertEquals('', $_SESSION['user']['email']);
        $this->assertEquals(Role::APP_MANAGER, $_SESSION['user']['role']);
    }
}
