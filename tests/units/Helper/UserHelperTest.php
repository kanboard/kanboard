<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\User\UserSession;
use Kanboard\Helper\UserHelper;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectRoleModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Core\Security\Role;
use Kanboard\Model\UserModel;

class UserHelperTest extends Base
{
    public function testGetFullname()
    {
        $userModel = new UserModel($this->container);
        $userHelper = new UserHelper($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2', 'name' => 'User #2')));

        $user1 = $userModel->getById(2);
        $user2 = $userModel->getById(3);

        $this->assertEquals('user1', $userHelper->getFullname($user1));
        $this->assertEquals('User #2', $userHelper->getFullname($user2));
    }

    public function testInitials()
    {
        $helper = new UserHelper($this->container);

        $this->assertEquals('CN', $helper->getInitials('chuck norris'));
        $this->assertEquals('CN', $helper->getInitials('chuck norris #2'));
        $this->assertEquals('A', $helper->getInitials('admin'));
        $this->assertEquals('Ü君', $helper->getInitials('Ü 君が代'));
    }

    public function testGetRoleName()
    {
        $helper = new UserHelper($this->container);
        $this->assertEquals('Administrator', $helper->getRoleName(Role::APP_ADMIN));
        $this->assertEquals('Manager', $helper->getRoleName(Role::APP_MANAGER));
        $this->assertEquals('Project Viewer', $helper->getRoleName(Role::PROJECT_VIEWER));
    }

    public function testHasAccessWithoutSession()
    {
        $helper = new UserHelper($this->container);
        $this->assertFalse($helper->hasAccess('UserCreationController', 'create'));
    }

    public function testHasAccessForAdmins()
    {
        $helper = new UserHelper($this->container);

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_ADMIN,
        );

        $this->assertTrue($helper->hasAccess('UserCreationController', 'create'));
        $this->assertTrue($helper->hasAccess('ProjectCreationController', 'create'));
        $this->assertTrue($helper->hasAccess('ProjectCreationController', 'createPrivate'));
    }

    public function testHasAccessForManagers()
    {
        $helper = new UserHelper($this->container);

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_MANAGER,
        );

        $this->assertFalse($helper->hasAccess('UserCreationController', 'show'));
        $this->assertTrue($helper->hasAccess('ProjectCreationController', 'create'));
        $this->assertTrue($helper->hasAccess('ProjectCreationController', 'createPrivate'));
    }

    public function testHasAccessForUsers()
    {
        $helper = new UserHelper($this->container);

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
        );

        $this->assertFalse($helper->hasAccess('UserCreationController', 'show'));
        $this->assertFalse($helper->hasAccess('ProjectCreationController', 'create'));
        $this->assertTrue($helper->hasAccess('ProjectCreationController', 'createPrivate'));
    }

    public function testHasProjectAccessWithoutSession()
    {
        $helper = new UserHelper($this->container);
        $project = new ProjectModel($this->container);

        $this->assertEquals(1, $project->create(array('name' => 'My project')));
        $this->assertFalse($helper->hasProjectAccess('ProjectEditController', 'edit', 1));
    }

    public function testHasProjectAccessForAdmins()
    {
        $helper = new UserHelper($this->container);
        $project = new ProjectModel($this->container);

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_ADMIN,
        );

        $this->assertEquals(1, $project->create(array('name' => 'My project')));

        $this->assertTrue($helper->hasProjectAccess('ProjectEditController', 'edit', 1));
        $this->assertTrue($helper->hasProjectAccess('BoardViewController', 'show', 1));
    }

    public function testHasProjectAccessForManagers()
    {
        $helper = new UserHelper($this->container);
        $project = new ProjectModel($this->container);

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_MANAGER,
        );

        $this->assertEquals(1, $project->create(array('name' => 'My project')));

        $this->assertFalse($helper->hasProjectAccess('ProjectEditController', 'edit', 1));
        $this->assertFalse($helper->hasProjectAccess('BoardViewController', 'show', 1));
    }

    public function testHasProjectAccessForUsers()
    {
        $helper = new UserHelper($this->container);
        $project = new ProjectModel($this->container);

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
        );

        $this->assertEquals(1, $project->create(array('name' => 'My project')));

        $this->assertFalse($helper->hasProjectAccess('ProjectEditController', 'edit', 1));
        $this->assertFalse($helper->hasProjectAccess('BoardViewController', 'show', 1));
    }

    public function testHasProjectAccessForAppManagerAndProjectManagers()
    {
        $helper = new UserHelper($this->container);
        $user = new UserModel($this->container);
        $project = new ProjectModel($this->container);
        $projectUserRole = new ProjectUserRoleModel($this->container);

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_MANAGER,
        );

        $this->assertEquals(1, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $user->create(array('username' => 'user')));
        $this->assertTrue($projectUserRole->addUser(1, 2, Role::PROJECT_MANAGER));

        $this->assertTrue($helper->hasProjectAccess('ProjectEditController', 'edit', 1));
        $this->assertTrue($helper->hasProjectAccess('BoardViewController', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('TaskViewController', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('taskcreationcontroller', 'save', 1));

        $this->assertFalse($helper->hasProjectAccess('ProjectEditController', 'edit', 2));
        $this->assertFalse($helper->hasProjectAccess('BoardViewController', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('TaskViewController', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('TaskCreationController', 'save', 2));
    }

    public function testHasProjectAccessForProjectManagers()
    {
        $helper = new UserHelper($this->container);
        $user = new UserModel($this->container);
        $project = new ProjectModel($this->container);
        $projectUserRole = new ProjectUserRoleModel($this->container);

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
        );

        $this->assertEquals(1, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $user->create(array('username' => 'user')));
        $this->assertTrue($projectUserRole->addUser(1, 2, Role::PROJECT_MANAGER));

        $this->assertTrue($helper->hasProjectAccess('ProjectEditController', 'edit', 1));
        $this->assertTrue($helper->hasProjectAccess('BoardViewController', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('TaskViewController', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('TaskCreationController', 'save', 1));

        $this->assertFalse($helper->hasProjectAccess('ProjectEditController', 'edit', 2));
        $this->assertFalse($helper->hasProjectAccess('BoardViewController', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('TaskViewController', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('TaskCreationController', 'save', 2));
    }

    public function testHasProjectAccessForProjectMembers()
    {
        $helper = new UserHelper($this->container);
        $user = new UserModel($this->container);
        $project = new ProjectModel($this->container);
        $projectUserRole = new ProjectUserRoleModel($this->container);

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
        );

        $this->assertEquals(1, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $user->create(array('username' => 'user')));
        $this->assertTrue($projectUserRole->addUser(1, 2, Role::PROJECT_MEMBER));

        $this->assertFalse($helper->hasProjectAccess('ProjectEditController', 'edit', 1));
        $this->assertTrue($helper->hasProjectAccess('BoardViewController', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('TaskViewController', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('TaskCreationController', 'save', 1));

        $this->assertFalse($helper->hasProjectAccess('ProjectEditController', 'edit', 2));
        $this->assertFalse($helper->hasProjectAccess('BoardViewController', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('TaskViewController', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('TaskCreationController', 'save', 2));
    }

    public function testHasProjectAccessForProjectViewers()
    {
        $helper = new UserHelper($this->container);
        $user = new UserModel($this->container);
        $project = new ProjectModel($this->container);
        $projectUserRole = new ProjectUserRoleModel($this->container);

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
        );

        $this->assertEquals(1, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $user->create(array('username' => 'user')));
        $this->assertTrue($projectUserRole->addUser(1, 2, Role::PROJECT_VIEWER));

        $this->assertFalse($helper->hasProjectAccess('ProjectEditController', 'edit', 1));
        $this->assertTrue($helper->hasProjectAccess('BoardViewController', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('TaskViewController', 'show', 1));
        $this->assertFalse($helper->hasProjectAccess('TaskCreationController', 'save', 1));

        $this->assertFalse($helper->hasProjectAccess('ProjectEditController', 'edit', 2));
        $this->assertFalse($helper->hasProjectAccess('BoardViewController', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('TaskViewController', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('TaskCreationController', 'save', 2));
    }

    public function testHasProjectAccessForCustomProjectRole()
    {
        $helper = new UserHelper($this->container);
        $user = new UserModel($this->container);
        $project = new ProjectModel($this->container);
        $projectUserRole = new ProjectUserRoleModel($this->container);
        $projectRole = new ProjectRoleModel($this->container);

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
        );

        $this->assertEquals(1, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $user->create(array('username' => 'user')));
        $this->assertEquals(1, $projectRole->create(1, 'Custom Role'));

        $this->assertTrue($projectUserRole->addUser(1, 2, 'Custom Role'));

        $this->assertFalse($helper->hasProjectAccess('ProjectEditController', 'edit', 1));
        $this->assertTrue($helper->hasProjectAccess('BoardViewController', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('TaskViewController', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('TaskCreationController', 'save', 1));

        $this->assertFalse($helper->hasProjectAccess('ProjectEditController', 'edit', 2));
        $this->assertFalse($helper->hasProjectAccess('BoardViewController', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('TaskViewController', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('TaskCreationController', 'save', 2));
    }
}
