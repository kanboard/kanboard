<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Security\Role;
use Kanboard\Core\Security\AccessMap;
use Kanboard\Core\Security\Authorization;

class AuthorizationTest extends Base
{
    public function testIsAllowed()
    {
        $acl = new AccessMap;
        $acl->setDefaultRole(Role::APP_USER);
        $acl->add('MyController', 'myAction1', array(Role::APP_ADMIN, Role::APP_MANAGER));
        $acl->add('MyController', 'myAction2', array(Role::APP_ADMIN));
        $acl->add('MyAdminController', '*', array(Role::APP_MANAGER));

        $authorization = new Authorization($acl);
        $this->assertTrue($authorization->isAllowed('myController', 'myAction1', Role::APP_ADMIN));
        $this->assertTrue($authorization->isAllowed('myController', 'myAction1', Role::APP_MANAGER));
        $this->assertFalse($authorization->isAllowed('myController', 'myAction1', Role::APP_USER));
        $this->assertTrue($authorization->isAllowed('anotherController', 'anotherAction', Role::APP_USER));
        $this->assertTrue($authorization->isAllowed('MyAdminController', 'myAction', Role::APP_MANAGER));
        $this->assertFalse($authorization->isAllowed('MyAdminController', 'myAction', Role::APP_ADMIN));
        $this->assertFalse($authorization->isAllowed('MyAdminController', 'myAction', 'something else'));
    }
}
