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
        $this->assertNotEmpty($this->container['sessionStorage']->user);
        $this->assertEquals('admin', $this->container['sessionStorage']->user['username']);
    }

    public function testInitializeLocalUserNotFound()
    {
        $userProfile = new UserProfile($this->container);
        $user = new DatabaseUserProvider(array('id' => 2));

        $this->assertFalse($userProfile->initialize($user));
        $this->assertFalse(isset($this->container['sessionStorage']->user));
    }

    public function testInitializeRemoteUser()
    {
        $userProfile = new UserProfile($this->container);
        $user = new LdapUserProvider('ldapId', 'bob', 'Bob', '', Role::APP_MANAGER, array());

        $this->assertTrue($userProfile->initialize($user));
        $this->assertNotEmpty($this->container['sessionStorage']->user);
        $this->assertEquals(2, $this->container['sessionStorage']->user['id']);
        $this->assertEquals('bob', $this->container['sessionStorage']->user['username']);
        $this->assertEquals(Role::APP_MANAGER, $this->container['sessionStorage']->user['role']);

        $user = new LdapUserProvider('ldapId', 'bob', 'Bob', '', Role::APP_MANAGER, array());

        $this->assertTrue($userProfile->initialize($user));
        $this->assertNotEmpty($this->container['sessionStorage']->user);
        $this->assertEquals(2, $this->container['sessionStorage']->user['id']);
        $this->assertEquals('bob', $this->container['sessionStorage']->user['username']);
    }

    public function testAssignRemoteUser()
    {
        $userProfile = new UserProfile($this->container);
        $user = new LdapUserProvider('ldapId', 'bob', 'Bob', '', Role::APP_MANAGER, array());

        $this->assertTrue($userProfile->assign(1, $user));
        $this->assertNotEmpty($this->container['sessionStorage']->user);
        $this->assertEquals(1, $this->container['sessionStorage']->user['id']);
        $this->assertEquals('admin', $this->container['sessionStorage']->user['username']);
        $this->assertEquals('Bob', $this->container['sessionStorage']->user['name']);
        $this->assertEquals('', $this->container['sessionStorage']->user['email']);
        $this->assertEquals(Role::APP_ADMIN, $this->container['sessionStorage']->user['role']);
    }
}
