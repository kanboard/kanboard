<?php

require_once __DIR__.'/Base.php';

use Core\Session;
use Model\Acl;
use Model\Project;
use Model\ProjectPermission;
use Model\User;

class AclTest extends Base
{
    public function testMatchAcl()
    {
        $acl_rules = array(
            'controller1' => array('action1', 'action3'),
            'controller3' => '*',
            'controller5' => '-',
            'controller6' => array(),
        );

        $acl = new Acl($this->container);
        $this->assertTrue($acl->matchAcl($acl_rules, 'controller1', 'aCtiOn1'));
        $this->assertTrue($acl->matchAcl($acl_rules, 'controller1', 'action1'));
        $this->assertTrue($acl->matchAcl($acl_rules, 'controller1', 'action3'));
        $this->assertFalse($acl->matchAcl($acl_rules, 'controller1', 'action2'));
        $this->assertFalse($acl->matchAcl($acl_rules, 'controller2', 'action2'));
        $this->assertFalse($acl->matchAcl($acl_rules, 'controller2', 'action3'));
        $this->assertTrue($acl->matchAcl($acl_rules, 'controller3', 'anything'));
        $this->assertFalse($acl->matchAcl($acl_rules, 'controller4', 'anything'));
        $this->assertFalse($acl->matchAcl($acl_rules, 'controller5', 'anything'));
        $this->assertFalse($acl->matchAcl($acl_rules, 'controller6', 'anything'));
    }

    public function testPublicActions()
    {
        $acl = new Acl($this->container);
        $this->assertTrue($acl->isPublicAction('board', 'readonly'));
        $this->assertFalse($acl->isPublicAction('board', 'show'));
    }

    public function testAdminActions()
    {
        $acl = new Acl($this->container);
        $this->assertFalse($acl->isAdminAction('board', 'show'));
        $this->assertFalse($acl->isAdminAction('task', 'show'));
        $this->assertTrue($acl->isAdminAction('config', 'api'));
        $this->assertTrue($acl->isAdminAction('config', 'anything'));
        $this->assertTrue($acl->isAdminAction('config', 'anything'));
        $this->assertTrue($acl->isAdminAction('user', 'save'));
    }

    public function testManagerActions()
    {
        $acl = new Acl($this->container);
        $this->assertFalse($acl->isManagerAction('board', 'readonly'));
        $this->assertFalse($acl->isManagerAction('project', 'remove'));
        $this->assertFalse($acl->isManagerAction('project', 'show'));
        $this->assertTrue($acl->isManagerAction('project', 'disable'));
        $this->assertTrue($acl->isManagerAction('category', 'index'));
        $this->assertTrue($acl->isManagerAction('project', 'users'));
        $this->assertFalse($acl->isManagerAction('app', 'index'));
    }

    public function testPageAccessNoSession()
    {
        $acl = new Acl($this->container);
        $this->assertFalse($acl->isAllowed('board', 'readonly'));
        $this->assertFalse($acl->isAllowed('task', 'show'));
        $this->assertFalse($acl->isAllowed('config', 'application'));
        $this->assertFalse($acl->isAllowed('project', 'users'));
        $this->assertFalse($acl->isAllowed('task', 'remove'));
        $this->assertTrue($acl->isAllowed('app', 'index'));
    }

    public function testPageAccessEmptySession()
    {
        $acl = new Acl($this->container);
        $session = new Session;

        $session['user'] = array();

        $this->assertFalse($acl->isAllowed('board', 'readonly'));
        $this->assertFalse($acl->isAllowed('task', 'show'));
        $this->assertFalse($acl->isAllowed('config', 'application'));
        $this->assertFalse($acl->isAllowed('project', 'users'));
        $this->assertFalse($acl->isAllowed('task', 'remove'));
        $this->assertTrue($acl->isAllowed('app', 'index'));
    }

    public function testPageAccessAdminUser()
    {
        $acl = new Acl($this->container);
        $session = new Session;

        $session['user'] = array(
            'is_admin' => true,
        );

        $this->assertTrue($acl->isAllowed('board', 'readonly'));
        $this->assertTrue($acl->isAllowed('task', 'readonly'));
        $this->assertTrue($acl->isAllowed('webhook', 'github'));
        $this->assertTrue($acl->isAllowed('task', 'show'));
        $this->assertTrue($acl->isAllowed('task', 'update'));
        $this->assertTrue($acl->isAllowed('project', 'show'));
        $this->assertTrue($acl->isAllowed('config', 'application'));
        $this->assertTrue($acl->isAllowed('project', 'users'));
        $this->assertTrue($acl->isAllowed('category', 'edit'));
        $this->assertTrue($acl->isAllowed('task', 'remove'));
        $this->assertTrue($acl->isAllowed('app', 'index'));
    }

    public function testPageAccessManager()
    {
        $acl = new Acl($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $u = new User($this->container);
        $session = new Session;

        // We create our user
        $this->assertEquals(2, $u->create(array('username' => 'unittest', 'password' => 'unittest')));

        // We create a project and set our user as project manager
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest'), 2, true));
        $this->assertTrue($pp->isMember(1, 2));
        $this->assertTrue($pp->isManager(1, 2));

        // We fake a session for him
        $session['user'] = array(
            'id' => 2,
            'is_admin' => false,
        );

        $this->assertTrue($acl->isAllowed('board', 'readonly', 1));
        $this->assertTrue($acl->isAllowed('task', 'readonly', 1));
        $this->assertTrue($acl->isAllowed('webhook', 'github', 1));
        $this->assertTrue($acl->isAllowed('task', 'show', 1));
        $this->assertFalse($acl->isAllowed('task', 'show', 2));
        $this->assertTrue($acl->isAllowed('task', 'update', 1));
        $this->assertTrue($acl->isAllowed('project', 'show', 1));
        $this->assertFalse($acl->isAllowed('config', 'application', 1));
        $this->assertTrue($acl->isAllowed('project', 'users', 1));
        $this->assertFalse($acl->isAllowed('project', 'users', 2));
        $this->assertTrue($acl->isAllowed('category', 'edit', 1));
        $this->assertTrue($acl->isAllowed('task', 'remove', 1));
        $this->assertTrue($acl->isAllowed('app', 'index', 1));
    }

    public function testPageAccessMember()
    {
        $acl = new Acl($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $u = new User($this->container);

        // We create our user
        $this->assertEquals(2, $u->create(array('username' => 'unittest', 'password' => 'unittest')));

        // We create a project and set our user as member
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));
        $this->assertTrue($pp->addMember(1, 2));
        $this->assertTrue($pp->isMember(1, 2));
        $this->assertFalse($pp->isManager(1, 2));

        $session = new Session;

        $session['user'] = array(
            'id' => 2,
            'is_admin' => false,
        );

        $this->assertTrue($acl->isAllowed('board', 'readonly', 1));
        $this->assertTrue($acl->isAllowed('task', 'readonly', 1));
        $this->assertTrue($acl->isAllowed('webhook', 'github', 1));
        $this->assertFalse($acl->isAllowed('board', 'show', 2));
        $this->assertTrue($acl->isAllowed('board', 'show', 1));
        $this->assertFalse($acl->isAllowed('task', 'show', 2));
        $this->assertTrue($acl->isAllowed('task', 'show', 1));
        $this->assertTrue($acl->isAllowed('task', 'update', 1));
        $this->assertTrue($acl->isAllowed('project', 'show', 1));
        $this->assertFalse($acl->isAllowed('config', 'application', 1));
        $this->assertFalse($acl->isAllowed('project', 'users', 1));
        $this->assertTrue($acl->isAllowed('task', 'remove', 1));
        $this->assertFalse($acl->isAllowed('task', 'remove', 2));
        $this->assertTrue($acl->isAllowed('app', 'index', 1));
    }

    public function testPageAccessNotMember()
    {
        $acl = new Acl($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $u = new User($this->container);

        // We create our user
        $this->assertEquals(2, $u->create(array('username' => 'unittest', 'password' => 'unittest')));

        // We create a project and set our user as member
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));
        $this->assertFalse($pp->isMember(1, 2));
        $this->assertFalse($pp->isManager(1, 2));

        $session = new Session;

        $session['user'] = array(
            'id' => 2,
            'is_admin' => false,
        );

        $this->assertFalse($acl->isAllowed('board', 'show', 2));
        $this->assertFalse($acl->isAllowed('board', 'show', 1));
        $this->assertFalse($acl->isAllowed('task', 'show', 1));
        $this->assertFalse($acl->isAllowed('task', 'update', 1));
        $this->assertFalse($acl->isAllowed('project', 'show', 1));
        $this->assertFalse($acl->isAllowed('config', 'application', 1));
        $this->assertFalse($acl->isAllowed('project', 'users', 1));
        $this->assertFalse($acl->isAllowed('task', 'remove', 1));
        $this->assertTrue($acl->isAllowed('app', 'index', 1));
    }
}
