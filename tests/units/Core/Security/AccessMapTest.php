<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Security\AccessMap;

class AccessMapTest extends Base
{
    public function testRoleHierarchy()
    {
        $acl = new AccessMap;
        $acl->setRoleHierarchy('admin', array('manager', 'user'));
        $acl->setRoleHierarchy('manager', array('user'));

        $this->assertEquals(array('admin'), $acl->getRoleHierarchy('admin'));
        $this->assertEquals(array('manager', 'admin'), $acl->getRoleHierarchy('manager'));
        $this->assertEquals(array('user', 'admin', 'manager'), $acl->getRoleHierarchy('user'));
    }

    public function testAddRulesAndGetRoles()
    {
        $acl = new AccessMap;
        $acl->setDefaultRole('role3');
        $acl->setRoleHierarchy('role2', array('role1'));

        $acl->add('MyController', 'myAction1', 'role2');
        $acl->add('MyController', 'myAction2', 'role1');
        $acl->add('MyAdminController', '*', 'role2');
        $acl->add('SomethingElse', array('actionA', 'actionB'), 'role2');

        $this->assertEquals(array('role2'), $acl->getRoles('mycontroller', 'MyAction1'));
        $this->assertEquals(array('role1', 'role2'), $acl->getRoles('mycontroller', 'MyAction2'));
        $this->assertEquals(array('role2'), $acl->getRoles('Myadmincontroller', 'MyAction'));
        $this->assertEquals(array('role3'), $acl->getRoles('AnotherController', 'ActionNotFound'));
        $this->assertEquals(array('role2'), $acl->getRoles('somethingelse', 'actiona'));
        $this->assertEquals(array('role2'), $acl->getRoles('somethingelse', 'actionb'));
        $this->assertEquals(array('role3'), $acl->getRoles('somethingelse', 'actionc'));
    }
}
