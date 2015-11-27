<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Security\AccessMap;

class AccessMapTest extends Base
{
    public function testGetRoles()
    {
        $acl = new AccessMap;
        $acl->setDefaultRole('role3');
        $acl->add('MyController', 'myAction1', array('role1', 'role2'));
        $acl->add('MyController', 'myAction2', array('role1'));
        $acl->add('MyAdminController', '*', array('role2'));

        $this->assertEquals(array('role1', 'role2'), $acl->getRoles('mycontroller', 'MyAction1'));
        $this->assertEquals(array('role1'), $acl->getRoles('mycontroller', 'MyAction2'));
        $this->assertEquals(array('role2'), $acl->getRoles('Myadmincontroller', 'MyAction'));
        $this->assertEquals(array('role3'), $acl->getRoles('AnotherController', 'ActionNotFound'));
    }
}
