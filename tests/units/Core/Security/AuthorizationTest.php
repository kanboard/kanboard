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
        $acl->setRoleHierarchy(Role::APP_ADMIN, array(Role::APP_MANAGER, Role::APP_USER));
        $acl->setRoleHierarchy(Role::APP_MANAGER, array(Role::APP_USER));

        $acl->add('MyController', 'myAction1', Role::APP_MANAGER);
        $acl->add('MyController', 'myAction2', Role::APP_ADMIN);
        $acl->add('MyManagerController', '*', Role::APP_MANAGER);

        $authorization = new Authorization($acl);

        $this->assertTrue($authorization->isAllowed('myController', 'myAction1', Role::APP_ADMIN));
        $this->assertTrue($authorization->isAllowed('myController', 'myAction1', Role::APP_MANAGER));
        $this->assertFalse($authorization->isAllowed('myController', 'myAction1', Role::APP_USER));
        $this->assertFalse($authorization->isAllowed('myController', 'myAction1', 'something else'));

        $this->assertTrue($authorization->isAllowed('MyManagerController', 'myAction', Role::APP_ADMIN));
        $this->assertTrue($authorization->isAllowed('MyManagerController', 'myAction', Role::APP_MANAGER));
        $this->assertFalse($authorization->isAllowed('MyManagerController', 'myAction', Role::APP_USER));
        $this->assertFalse($authorization->isAllowed('MyManagerController', 'myAction', 'something else'));

        $this->assertTrue($authorization->isAllowed('MyUserController', 'myAction', Role::APP_ADMIN));
        $this->assertTrue($authorization->isAllowed('MyUserController', 'myAction', Role::APP_MANAGER));
        $this->assertTrue($authorization->isAllowed('MyUserController', 'myAction', Role::APP_USER));
        $this->assertFalse($authorization->isAllowed('MyUserController', 'myAction', 'something else'));
    }
}
