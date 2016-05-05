<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\User\UserSession;
use Kanboard\Helper\UserHelper;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectUserRole;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\User as UserModel;
use Kanboard\Core\Security\Role;
use Kanboard\Model\User;

class UserHelperTest extends Base
{
    public function testInitials()
    {
        $helper = new UserHelper($this->container);

        $this->assertEquals('CN', $helper->getInitials('chuck norris'));
        $this->assertEquals('CN', $helper->getInitials('chuck norris #2'));
        $this->assertEquals('A', $helper->getInitials('admin'));
    }

    public function testGetRoleName()
    {
        $helper = new UserHelper($this->container);
        $this->assertEquals('Administrator', $helper->getRoleName(Role::APP_ADMIN));
        $this->assertEquals('Manager', $helper->getRoleName(Role::APP_MANAGER));
        $this->assertEquals('Project Viewer', $helper->getRoleName(Role::PROJECT_VIEWER));
    }

    public function testHasAccessForAdmins()
    {
        $helper = new UserHelper($this->container);

        $this->container['sessionStorage']->user = array(
            'id' => 2,
            'role' => Role::APP_ADMIN,
        );

        $this->assertTrue($helper->hasAccess('user', 'create'));
        $this->assertTrue($helper->hasAccess('ProjectCreation', 'create'));
        $this->assertTrue($helper->hasAccess('ProjectCreation', 'createPrivate'));
    }

    public function testHasAccessForManagers()
    {
        $helper = new UserHelper($this->container);

        $this->container['sessionStorage']->user = array(
            'id' => 2,
            'role' => Role::APP_MANAGER,
        );

        $this->assertFalse($helper->hasAccess('user', 'create'));
        $this->assertTrue($helper->hasAccess('ProjectCreation', 'create'));
        $this->assertTrue($helper->hasAccess('ProjectCreation', 'createPrivate'));
    }

    public function testHasAccessForUsers()
    {
        $helper = new UserHelper($this->container);

        $this->container['sessionStorage']->user = array(
            'id' => 2,
            'role' => Role::APP_USER,
        );

        $this->assertFalse($helper->hasAccess('user', 'create'));
        $this->assertFalse($helper->hasAccess('ProjectCreation', 'create'));
        $this->assertTrue($helper->hasAccess('ProjectCreation', 'createPrivate'));
    }

    public function testHasProjectAccessForAdmins()
    {
        $helper = new UserHelper($this->container);
        $project = new Project($this->container);

        $this->container['sessionStorage']->user = array(
            'id' => 2,
            'role' => Role::APP_ADMIN,
        );

        $this->assertEquals(1, $project->create(array('name' => 'My project')));

        $this->assertTrue($helper->hasProjectAccess('ProjectEdit', 'edit', 1));
        $this->assertTrue($helper->hasProjectAccess('board', 'show', 1));
    }

    public function testHasProjectAccessForManagers()
    {
        $helper = new UserHelper($this->container);
        $project = new Project($this->container);

        $this->container['sessionStorage']->user = array(
            'id' => 2,
            'role' => Role::APP_MANAGER,
        );

        $this->assertEquals(1, $project->create(array('name' => 'My project')));

        $this->assertFalse($helper->hasProjectAccess('ProjectEdit', 'edit', 1));
        $this->assertFalse($helper->hasProjectAccess('board', 'show', 1));
    }

    public function testHasProjectAccessForUsers()
    {
        $helper = new UserHelper($this->container);
        $project = new Project($this->container);

        $this->container['sessionStorage']->user = array(
            'id' => 2,
            'role' => Role::APP_USER,
        );

        $this->assertEquals(1, $project->create(array('name' => 'My project')));

        $this->assertFalse($helper->hasProjectAccess('ProjectEdit', 'edit', 1));
        $this->assertFalse($helper->hasProjectAccess('board', 'show', 1));
    }

    public function testHasProjectAccessForAppManagerAndProjectManagers()
    {
        $helper = new UserHelper($this->container);
        $user = new UserModel($this->container);
        $project = new Project($this->container);
        $projectUserRole = new ProjectUserRole($this->container);

        $this->container['sessionStorage']->user = array(
            'id' => 2,
            'role' => Role::APP_MANAGER,
        );

        $this->assertEquals(1, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $user->create(array('username' => 'user')));
        $this->assertTrue($projectUserRole->addUser(1, 2, Role::PROJECT_MANAGER));

        $this->assertTrue($helper->hasProjectAccess('ProjectEdit', 'edit', 1));
        $this->assertTrue($helper->hasProjectAccess('board', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('task', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('taskcreation', 'save', 1));

        $this->assertFalse($helper->hasProjectAccess('ProjectEdit', 'edit', 2));
        $this->assertFalse($helper->hasProjectAccess('board', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('task', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('taskcreation', 'save', 2));
    }

    public function testHasProjectAccessForProjectManagers()
    {
        $helper = new UserHelper($this->container);
        $user = new UserModel($this->container);
        $project = new Project($this->container);
        $projectUserRole = new ProjectUserRole($this->container);

        $this->container['sessionStorage']->user = array(
            'id' => 2,
            'role' => Role::APP_USER,
        );

        $this->assertEquals(1, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $user->create(array('username' => 'user')));
        $this->assertTrue($projectUserRole->addUser(1, 2, Role::PROJECT_MANAGER));

        $this->assertTrue($helper->hasProjectAccess('ProjectEdit', 'edit', 1));
        $this->assertTrue($helper->hasProjectAccess('board', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('task', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('taskcreation', 'save', 1));

        $this->assertFalse($helper->hasProjectAccess('ProjectEdit', 'edit', 2));
        $this->assertFalse($helper->hasProjectAccess('board', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('task', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('taskcreation', 'save', 2));
    }

    public function testHasProjectAccessForProjectMembers()
    {
        $helper = new UserHelper($this->container);
        $user = new UserModel($this->container);
        $project = new Project($this->container);
        $projectUserRole = new ProjectUserRole($this->container);

        $this->container['sessionStorage']->user = array(
            'id' => 2,
            'role' => Role::APP_USER,
        );

        $this->assertEquals(1, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $user->create(array('username' => 'user')));
        $this->assertTrue($projectUserRole->addUser(1, 2, Role::PROJECT_MEMBER));

        $this->assertFalse($helper->hasProjectAccess('ProjectEdit', 'edit', 1));
        $this->assertTrue($helper->hasProjectAccess('board', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('task', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('taskcreation', 'save', 1));

        $this->assertFalse($helper->hasProjectAccess('ProjectEdit', 'edit', 2));
        $this->assertFalse($helper->hasProjectAccess('board', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('task', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('taskcreation', 'save', 2));
    }

    public function testHasProjectAccessForProjectViewers()
    {
        $helper = new UserHelper($this->container);
        $user = new UserModel($this->container);
        $project = new Project($this->container);
        $projectUserRole = new ProjectUserRole($this->container);

        $this->container['sessionStorage']->user = array(
            'id' => 2,
            'role' => Role::APP_USER,
        );

        $this->assertEquals(1, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $project->create(array('name' => 'My project')));
        $this->assertEquals(2, $user->create(array('username' => 'user')));
        $this->assertTrue($projectUserRole->addUser(1, 2, Role::PROJECT_VIEWER));

        $this->assertFalse($helper->hasProjectAccess('ProjectEdit', 'edit', 1));
        $this->assertTrue($helper->hasProjectAccess('board', 'show', 1));
        $this->assertTrue($helper->hasProjectAccess('task', 'show', 1));
        $this->assertFalse($helper->hasProjectAccess('taskcreation', 'save', 1));

        $this->assertFalse($helper->hasProjectAccess('ProjectEdit', 'edit', 2));
        $this->assertFalse($helper->hasProjectAccess('board', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('task', 'show', 2));
        $this->assertFalse($helper->hasProjectAccess('taskcreation', 'save', 2));
    }

    public function testCanRemoveTask()
    {
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);
        $helper = new UserHelper($this->container);
        $projectModel = new Project($this->container);
        $userModel = new User($this->container);
        $userSessionModel = new UserSession($this->container);

        $this->assertNotFalse($userModel->create(array('username' => 'toto', 'password' => '123456')));
        $this->assertNotFalse($userModel->create(array('username' => 'toto2', 'password' => '123456')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'creator_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1, 'creator_id' => 2)));
        $this->assertEquals(3, $taskCreationModel->create(array('title' => 'Task #3', 'project_id' => 1, 'creator_id' => 3)));
        $this->assertEquals(4, $taskCreationModel->create(array('title' => 'Task #4', 'project_id' => 1)));

        // User #1 can remove everything
        $user = $userModel->getById(1);
        $this->assertNotEmpty($user);
        $userSessionModel->initialize($user);

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertTrue($helper->canRemoveTask($task));

        // User #2 can't remove the task #1
        $user = $userModel->getById(2);
        $this->assertNotEmpty($user);
        $userSessionModel->initialize($user);

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertFalse($helper->canRemoveTask($task));

        // User #1 can remove everything
        $user = $userModel->getById(1);
        $this->assertNotEmpty($user);
        $userSessionModel->initialize($user);

        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertTrue($helper->canRemoveTask($task));

        // User #2 can remove his own task
        $user = $userModel->getbyId(2);
        $this->assertNotEmpty($user);
        $userSessionModel->initialize($user);

        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertTrue($helper->canRemoveTask($task));

        // User #1 can remove everything
        $user = $userModel->getById(1);
        $this->assertNotEmpty($user);
        $userSessionModel->initialize($user);

        $task = $taskFinderModel->getById(3);
        $this->assertNotEmpty($task);
        $this->assertTrue($helper->canRemoveTask($task));

        // User #2 can't remove the task #3
        $user = $userModel->getById(2);
        $this->assertNotEmpty($user);
        $userSessionModel->initialize($user);

        $task = $taskFinderModel->getById(3);
        $this->assertNotEmpty($task);
        $this->assertFalse($helper->canRemoveTask($task));

        // User #1 can remove everything
        $user = $userModel->getById(1);
        $this->assertNotEmpty($user);
        $userSessionModel->initialize($user);

        $task = $taskFinderModel->getById(4);
        $this->assertNotEmpty($task);
        $this->assertTrue($helper->canRemoveTask($task));

        // User #2 can't remove the task #4
        $user = $userModel->getById(2);
        $this->assertNotEmpty($user);
        $userSessionModel->initialize($user);

        $task = $taskFinderModel->getById(4);
        $this->assertNotEmpty($task);
        $this->assertFalse($helper->canRemoveTask($task));
    }
}
