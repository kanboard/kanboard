<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\Session;
use Kanboard\Model\Acl;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectPermission;
use Kanboard\Model\User;

class AclTest extends Base
{
    public function testMatchAcl()
    {
        $acl_rules = array(
            'controller1' => array('action1', 'action3'),
            'controller3' => '*',
            'controller5' => '-',
            'controller6' => array(),
            'controllera' => '*',
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
        $this->assertTrue($acl->matchAcl($acl_rules, 'ControllerA', 'anything'));
        $this->assertTrue($acl->matchAcl($acl_rules, 'controllera', 'anything'));
    }

    public function testPublicActions()
    {
        $acl = new Acl($this->container);
        $this->assertTrue($acl->isPublicAction('task', 'readonly'));
        $this->assertTrue($acl->isPublicAction('board', 'readonly'));
        $this->assertFalse($acl->isPublicAction('board', 'show'));
        $this->assertTrue($acl->isPublicAction('feed', 'project'));
        $this->assertTrue($acl->isPublicAction('feed', 'user'));
        $this->assertTrue($acl->isPublicAction('ical', 'project'));
        $this->assertTrue($acl->isPublicAction('ical', 'user'));
        $this->assertTrue($acl->isPublicAction('oauth', 'github'));
        $this->assertTrue($acl->isPublicAction('oauth', 'google'));
        $this->assertTrue($acl->isPublicAction('auth', 'login'));
        $this->assertTrue($acl->isPublicAction('auth', 'check'));
        $this->assertTrue($acl->isPublicAction('auth', 'captcha'));
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

    public function testProjectAdminActions()
    {
        $acl = new Acl($this->container);
        $this->assertFalse($acl->isProjectAdminAction('config', 'save'));
        $this->assertFalse($acl->isProjectAdminAction('user', 'index'));
        $this->assertTrue($acl->isProjectAdminAction('project', 'remove'));
    }

    public function testProjectManagerActions()
    {
        $acl = new Acl($this->container);
        $this->assertFalse($acl->isProjectManagerAction('board', 'readonly'));
        $this->assertFalse($acl->isProjectManagerAction('project', 'remove'));
        $this->assertFalse($acl->isProjectManagerAction('project', 'show'));
        $this->assertTrue($acl->isProjectManagerAction('project', 'disable'));
        $this->assertTrue($acl->isProjectManagerAction('category', 'index'));
        $this->assertTrue($acl->isProjectManagerAction('project', 'users'));
        $this->assertFalse($acl->isProjectManagerAction('app', 'index'));
    }

    public function testPageAccessNoSession()
    {
        $acl = new Acl($this->container);
        $session = new Session;
        $session = array();

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
        $this->assertTrue($acl->isAllowed('config', 'application'));
        $this->assertTrue($acl->isAllowed('project', 'show'));
        $this->assertTrue($acl->isAllowed('project', 'users'));
        $this->assertTrue($acl->isAllowed('project', 'remove'));
        $this->assertTrue($acl->isAllowed('category', 'edit'));
        $this->assertTrue($acl->isAllowed('task', 'remove'));
        $this->assertTrue($acl->isAllowed('app', 'index'));
    }

    public function testPageAccessProjectAdmin()
    {
        $acl = new Acl($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $u = new User($this->container);
        $session = new Session;

        // We create our user
        $this->assertEquals(2, $u->create(array('username' => 'unittest', 'password' => 'unittest')));

        // We create a project and set our user as project manager
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($pp->addMember(1, 2));
        $this->assertTrue($pp->isMember(1, 2));
        $this->assertFalse($pp->isManager(1, 2));

        // We fake a session for him
        $session['user'] = array(
            'id' => 2,
            'is_admin' => false,
            'is_project_admin' => true,
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

        $this->assertTrue($acl->isAllowed('project', 'remove', 1));
        $this->assertFalse($acl->isAllowed('project', 'remove', 2));

        $this->assertTrue($acl->isAllowed('category', 'edit', 1));
        $this->assertTrue($acl->isAllowed('task', 'remove', 1));
        $this->assertTrue($acl->isAllowed('app', 'index', 1));
    }

    public function testPageAccessProjectManager()
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

        $this->assertFalse($acl->isAllowed('project', 'remove', 1));
        $this->assertFalse($acl->isAllowed('project', 'remove', 2));

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

    public function testExtend()
    {
        $acl = new Acl($this->container);

        $this->assertFalse($acl->isProjectManagerAction('plop', 'show'));

        $acl->extend('project_manager_acl', array('plop' => '*'));

        $this->assertTrue($acl->isProjectManagerAction('plop', 'show'));
        $this->assertTrue($acl->isProjectManagerAction('swimlane', 'index'));
    }
}
