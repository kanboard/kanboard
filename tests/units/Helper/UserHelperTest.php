<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Helper\User;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectPermission;
use Kanboard\Model\User as UserModel;
use Kanboard\Core\Session;

class UserHelperTest extends Base
{
    public function testInitials()
    {
        $h = new User($this->container);

        $this->assertEquals('CN', $h->getInitials('chuck norris'));
        $this->assertEquals('A', $h->getInitials('admin'));
    }

    public function testIsProjectAdministrationAllowedForProjectAdmin()
    {
        $h = new User($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $u = new UserModel($this->container);
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

        $this->assertTrue($h->isProjectAdministrationAllowed(1));
    }

    public function testIsProjectAdministrationAllowedForProjectMember()
    {
        $h = new User($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $u = new UserModel($this->container);
        $session = new Session;

        // We create our user
        $this->assertEquals(2, $u->create(array('username' => 'unittest', 'password' => 'unittest')));

        // We create a project and set our user as project member
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($pp->addMember(1, 2));
        $this->assertTrue($pp->isMember(1, 2));
        $this->assertFalse($pp->isManager(1, 2));

        // We fake a session for him
        $session['user'] = array(
            'id' => 2,
            'is_admin' => false,
            'is_project_admin' => false,
        );

        $this->assertFalse($h->isProjectAdministrationAllowed(1));
    }

    public function testIsProjectAdministrationAllowedForProjectManager()
    {
        $h = new User($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $u = new UserModel($this->container);
        $session = new Session;

        // We create our user
        $this->assertEquals(2, $u->create(array('username' => 'unittest', 'password' => 'unittest')));

        // We create a project and set our user as project member
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($pp->addManager(1, 2));
        $this->assertTrue($pp->isMember(1, 2));
        $this->assertTrue($pp->isManager(1, 2));

        // We fake a session for him
        $session['user'] = array(
            'id' => 2,
            'is_admin' => false,
            'is_project_admin' => false,
        );

        $this->assertFalse($h->isProjectAdministrationAllowed(1));
    }

    public function testIsProjectManagementAllowedForProjectAdmin()
    {
        $h = new User($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $u = new UserModel($this->container);
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

        $this->assertTrue($h->isProjectManagementAllowed(1));
    }

    public function testIsProjectManagementAllowedForProjectMember()
    {
        $h = new User($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $u = new UserModel($this->container);
        $session = new Session;

        // We create our user
        $this->assertEquals(2, $u->create(array('username' => 'unittest', 'password' => 'unittest')));

        // We create a project and set our user as project member
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($pp->addMember(1, 2));
        $this->assertTrue($pp->isMember(1, 2));
        $this->assertFalse($pp->isManager(1, 2));

        // We fake a session for him
        $session['user'] = array(
            'id' => 2,
            'is_admin' => false,
            'is_project_admin' => false,
        );

        $this->assertFalse($h->isProjectManagementAllowed(1));
    }

    public function testIsProjectManagementAllowedForProjectManager()
    {
        $h = new User($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $u = new UserModel($this->container);
        $session = new Session;

        // We create our user
        $this->assertEquals(2, $u->create(array('username' => 'unittest', 'password' => 'unittest')));

        // We create a project and set our user as project member
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($pp->addManager(1, 2));
        $this->assertTrue($pp->isMember(1, 2));
        $this->assertTrue($pp->isManager(1, 2));

        // We fake a session for him
        $session['user'] = array(
            'id' => 2,
            'is_admin' => false,
            'is_project_admin' => false,
        );

        $this->assertTrue($h->isProjectManagementAllowed(1));
    }
}
