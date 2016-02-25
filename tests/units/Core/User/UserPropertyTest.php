<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Security\Role;
use Kanboard\Core\User\UserProperty;
use Kanboard\User\LdapUserProvider;

class UserPropertyTest extends Base
{
    public function testGetProperties()
    {
        $user = new LdapUserProvider('ldapId', 'bob', 'Bob', '', Role::APP_USER, array());

        $expected = array(
            'username' => 'bob',
            'name' => 'Bob',
            'role' => Role::APP_USER,
            'is_ldap_user' => 1,
        );

        $this->assertEquals($expected, UserProperty::getProperties($user));

        $user = new LdapUserProvider('ldapId', 'bob', '', '', '', array());

        $expected = array(
            'username' => 'bob',
            'is_ldap_user' => 1,
        );

        $this->assertEquals($expected, UserProperty::getProperties($user));
    }

    public function testFilterProperties()
    {
        $profile = array(
            'id' => 123,
            'username' => 'bob',
            'name' => null,
            'email' => '',
            'other_column' => 'myvalue',
            'role' => Role::APP_ADMIN,
        );

        $properties = array(
            'external_id' => '456',
            'username' => 'bobby',
            'name' => 'Bobby',
            'email' => 'admin@localhost',
            'role' => '',
        );

        $expected = array(
            'name' => 'Bobby',
            'email' => 'admin@localhost',
        );

        $this->assertEquals($expected, UserProperty::filterProperties($profile, $properties));
    }
}
