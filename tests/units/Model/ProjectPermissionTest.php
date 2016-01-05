<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\ProjectPermission;
use Kanboard\Model\Project;
use Kanboard\Model\User;
use Kanboard\Model\Group;
use Kanboard\Model\GroupMember;
use Kanboard\Model\ProjectGroupRole;
use Kanboard\Model\ProjectUserRole;
use Kanboard\Core\Security\Role;

class ProjectPermissionTest extends Base
{
    public function testFindByUsernames()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $groupModel = new Group($this->container);
        $groupMemberModel = new GroupMember($this->container);
        $groupRoleModel = new ProjectGroupRole($this->container);
        $userRoleModel = new ProjectUserRole($this->container);
        $projectPermissionModel = new ProjectPermission($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1')));

        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user3')));

        $this->assertEquals(1, $groupModel->create('Group A'));
        $this->assertTrue($groupMemberModel->addUser(1, 2));

        $this->assertTrue($groupRoleModel->addGroup(1, 1, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(1, 3, Role::PROJECT_MANAGER));

        $this->assertEquals(array('user1', 'user2'), $projectPermissionModel->findUsernames(1, 'us'));
        $this->assertEmpty($projectPermissionModel->findUsernames(1, 'a'));
        $this->assertEmpty($projectPermissionModel->findUsernames(2, 'user'));
    }

    public function testGetQueryByRole()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $groupModel = new Group($this->container);
        $groupMemberModel = new GroupMember($this->container);
        $groupRoleModel = new ProjectGroupRole($this->container);
        $userRoleModel = new ProjectUserRole($this->container);
        $projectPermission = new ProjectPermission($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project 2')));
        $this->assertEquals(3, $projectModel->create(array('name' => 'Project 3')));

        $this->assertEquals(2, $userModel->create(array('username' => 'user 1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user 2')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user 3')));
        $this->assertEquals(5, $userModel->create(array('username' => 'user 4')));

        $this->assertTrue($userRoleModel->addUser(1, 2, Role::PROJECT_MANAGER));
        $this->assertTrue($userRoleModel->addUser(1, 3, Role::PROJECT_MANAGER));
        $this->assertTrue($userRoleModel->addUser(1, 4, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(1, 5, Role::PROJECT_MEMBER));

        $this->assertTrue($userRoleModel->addUser(2, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(2, 3, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(2, 5, Role::PROJECT_MANAGER));

        $this->assertTrue($userRoleModel->addUser(3, 4, Role::PROJECT_MANAGER));
        $this->assertTrue($userRoleModel->addUser(3, 5, Role::PROJECT_VIEWER));

        $this->assertEmpty($projectPermission->getQueryByRole(array(), Role::PROJECT_MANAGER)->findAll());

        $users = $projectPermission->getQueryByRole(array(1, 2), Role::PROJECT_MANAGER)->findAll();
        $this->assertCount(3, $users);
        $this->assertEquals('user 1', $users[0]['username']);
        $this->assertEquals('Project 1', $users[0]['project_name']);
        $this->assertEquals('user 2', $users[1]['username']);
        $this->assertEquals('Project 1', $users[1]['project_name']);
        $this->assertEquals('user 4', $users[2]['username']);
        $this->assertEquals('Project 2', $users[2]['project_name']);

        $users = $projectPermission->getQueryByRole(array(1), Role::PROJECT_MANAGER)->findAll();
        $this->assertCount(2, $users);
        $this->assertEquals('user 1', $users[0]['username']);
        $this->assertEquals('Project 1', $users[0]['project_name']);
        $this->assertEquals('user 2', $users[1]['username']);
        $this->assertEquals('Project 1', $users[1]['project_name']);

        $users = $projectPermission->getQueryByRole(array(1, 2, 3), Role::PROJECT_MEMBER)->findAll();
        $this->assertCount(4, $users);
        $this->assertEquals('user 3', $users[0]['username']);
        $this->assertEquals('Project 1', $users[0]['project_name']);
        $this->assertEquals('user 4', $users[1]['username']);
        $this->assertEquals('Project 1', $users[1]['project_name']);
        $this->assertEquals('user 1', $users[2]['username']);
        $this->assertEquals('Project 2', $users[2]['project_name']);
        $this->assertEquals('user 2', $users[3]['username']);
        $this->assertEquals('Project 2', $users[3]['project_name']);

        $users = $projectPermission->getQueryByRole(array(1, 2, 3), Role::PROJECT_VIEWER)->findAll();
        $this->assertCount(1, $users);
        $this->assertEquals('user 4', $users[0]['username']);
        $this->assertEquals('Project 3', $users[0]['project_name']);
    }

    public function testEverybodyAllowed()
    {
        $projectModel = new Project($this->container);
        $projectPermission = new ProjectPermission($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project 2', 'is_everybody_allowed' => 1)));

        $this->assertFalse($projectPermission->isEverybodyAllowed(1));
        $this->assertTrue($projectPermission->isEverybodyAllowed(2));
    }

    public function testIsUserAllowed()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $groupModel = new Group($this->container);
        $groupRoleModel = new ProjectGroupRole($this->container);
        $groupMemberModel = new GroupMember($this->container);
        $userRoleModel = new ProjectUserRole($this->container);
        $projectPermission = new ProjectPermission($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user 1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user 2')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user 3')));
        $this->assertEquals(5, $userModel->create(array('username' => 'user 4')));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project 2')));

        $this->assertEquals(1, $groupModel->create('Group A'));

        $this->assertTrue($groupMemberModel->addUser(1, 2));
        $this->assertTrue($groupRoleModel->addGroup(1, 1, Role::PROJECT_VIEWER));

        $this->assertTrue($userRoleModel->addUser(1, 3, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(1, 4, Role::PROJECT_MANAGER));

        $this->assertTrue($projectPermission->isUserAllowed(1, 2));
        $this->assertTrue($projectPermission->isUserAllowed(1, 3));
        $this->assertTrue($projectPermission->isUserAllowed(1, 4));
        $this->assertFalse($projectPermission->isUserAllowed(1, 5));

        $this->assertFalse($projectPermission->isUserAllowed(2, 2));
        $this->assertFalse($projectPermission->isUserAllowed(2, 3));
        $this->assertFalse($projectPermission->isUserAllowed(2, 4));
        $this->assertFalse($projectPermission->isUserAllowed(2, 5));
    }

    public function testIsAssignable()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $groupModel = new Group($this->container);
        $groupRoleModel = new ProjectGroupRole($this->container);
        $groupMemberModel = new GroupMember($this->container);
        $userRoleModel = new ProjectUserRole($this->container);
        $projectPermission = new ProjectPermission($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user 1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user 2')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user 3')));
        $this->assertEquals(5, $userModel->create(array('username' => 'user 4')));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project 2')));

        $this->assertEquals(1, $groupModel->create('Group A'));

        $this->assertTrue($groupMemberModel->addUser(1, 2));
        $this->assertTrue($groupRoleModel->addGroup(1, 1, Role::PROJECT_VIEWER));

        $this->assertTrue($userRoleModel->addUser(1, 3, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(1, 4, Role::PROJECT_MANAGER));

        $this->assertFalse($projectPermission->isAssignable(1, 2));
        $this->assertTrue($projectPermission->isAssignable(1, 3));
        $this->assertTrue($projectPermission->isAssignable(1, 4));
        $this->assertFalse($projectPermission->isAssignable(1, 5));

        $this->assertFalse($projectPermission->isAssignable(2, 2));
        $this->assertFalse($projectPermission->isAssignable(2, 3));
        $this->assertFalse($projectPermission->isAssignable(2, 4));
        $this->assertFalse($projectPermission->isAssignable(2, 5));
    }

    public function testIsMember()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $groupModel = new Group($this->container);
        $groupRoleModel = new ProjectGroupRole($this->container);
        $groupMemberModel = new GroupMember($this->container);
        $userRoleModel = new ProjectUserRole($this->container);
        $projectPermission = new ProjectPermission($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user 1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user 2')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user 3')));
        $this->assertEquals(5, $userModel->create(array('username' => 'user 4')));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project 2')));

        $this->assertEquals(1, $groupModel->create('Group A'));

        $this->assertTrue($groupMemberModel->addUser(1, 2));
        $this->assertTrue($groupRoleModel->addGroup(1, 1, Role::PROJECT_VIEWER));

        $this->assertTrue($userRoleModel->addUser(1, 3, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(1, 4, Role::PROJECT_MANAGER));

        $this->assertTrue($projectPermission->isMember(1, 2));
        $this->assertTrue($projectPermission->isMember(1, 3));
        $this->assertTrue($projectPermission->isMember(1, 4));
        $this->assertFalse($projectPermission->isMember(1, 5));

        $this->assertFalse($projectPermission->isMember(2, 2));
        $this->assertFalse($projectPermission->isMember(2, 3));
        $this->assertFalse($projectPermission->isMember(2, 4));
        $this->assertFalse($projectPermission->isMember(2, 5));
    }

    public function testGetActiveProjectIds()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $groupModel = new Group($this->container);
        $groupRoleModel = new ProjectGroupRole($this->container);
        $groupMemberModel = new GroupMember($this->container);
        $userRoleModel = new ProjectUserRole($this->container);
        $projectPermission = new ProjectPermission($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user 1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user 2')));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project 2', 'is_active' => 0)));

        $this->assertTrue($userRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(2, 2, Role::PROJECT_VIEWER));
        $this->assertTrue($userRoleModel->addUser(1, 3, Role::PROJECT_VIEWER));

        $this->assertEmpty($projectPermission->getActiveProjectIds(1));
        $this->assertEquals(array(1), $projectPermission->getActiveProjectIds(2));
        $this->assertEquals(array(1), $projectPermission->getActiveProjectIds(3));
    }

    public function testDuplicate()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $groupModel = new Group($this->container);
        $groupMemberModel = new GroupMember($this->container);
        $groupRoleModel = new ProjectGroupRole($this->container);
        $userRoleModel = new ProjectUserRole($this->container);
        $projectPermission = new ProjectPermission($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project 2')));

        $this->assertEquals(2, $userModel->create(array('username' => 'user 1', 'name' => 'User #1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user 2')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user 3')));
        $this->assertEquals(5, $userModel->create(array('username' => 'user 4')));
        $this->assertEquals(6, $userModel->create(array('username' => 'user 5', 'name' => 'User #5')));

        $this->assertEquals(1, $groupModel->create('Group C'));
        $this->assertEquals(2, $groupModel->create('Group B'));
        $this->assertEquals(3, $groupModel->create('Group A'));

        $this->assertTrue($groupMemberModel->addUser(1, 4));
        $this->assertTrue($groupMemberModel->addUser(2, 5));
        $this->assertTrue($groupMemberModel->addUser(3, 3));
        $this->assertTrue($groupMemberModel->addUser(3, 2));

        $this->assertTrue($groupRoleModel->addGroup(1, 1, Role::PROJECT_VIEWER));
        $this->assertTrue($groupRoleModel->addGroup(1, 3, Role::PROJECT_MANAGER));

        $this->assertTrue($userRoleModel->addUser(1, 5, Role::PROJECT_MANAGER));
        $this->assertTrue($userRoleModel->addUser(1, 6, Role::PROJECT_MEMBER));

        $this->assertTrue($projectPermission->duplicate(1, 2));

        $this->assertCount(2, $userRoleModel->getUsers(2));
        $this->assertCount(3, $groupRoleModel->getUsers(2));
    }
}
