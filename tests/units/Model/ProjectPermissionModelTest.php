<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\ProjectPermissionModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\UserModel;
use Kanboard\Model\GroupModel;
use Kanboard\Model\GroupMemberModel;
use Kanboard\Model\ProjectGroupRoleModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Core\Security\Role;

class ProjectPermissionModelTest extends Base
{
    public function testFindByUsernames()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $groupModel = new GroupModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);
        $groupRoleModel = new ProjectGroupRoleModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);
        $projectPermissionModel = new ProjectPermissionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1')));

        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2', 'name' => 'User 2', 'email' => 'test@here', 'avatar_path' => 'test')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user3')));

        $this->assertEquals(1, $groupModel->create('Group A'));
        $this->assertTrue($groupMemberModel->addUser(1, 2));

        $this->assertTrue($groupRoleModel->addGroup(1, 1, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(1, 3, Role::PROJECT_MANAGER));

        $expected = array(
            'user1' => array(
                'username' => 'user1',
                'name' => null,
                'email' => null,
                'avatar_path' => null,
                'id' => '2',
            ),
            'user2' => array(
                'username' => 'user2',
                'name' => 'User 2',
                'email' => 'test@here',
                'avatar_path' => 'test',
                'id' => '3',
            )
        );

        $this->assertEquals($expected, $projectPermissionModel->findUsernames(1, 'us'));
        $this->assertEmpty($projectPermissionModel->findUsernames(1, 'a'));
        $this->assertEmpty($projectPermissionModel->findUsernames(2, 'user'));
    }

    public function testGetQueryByRole()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);
        $projectPermission = new ProjectPermissionModel($this->container);

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

    public function testIsUserAllowed()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $groupModel = new GroupModel($this->container);
        $groupRoleModel = new ProjectGroupRoleModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);
        $projectPermission = new ProjectPermissionModel($this->container);

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
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $groupModel = new GroupModel($this->container);
        $groupRoleModel = new ProjectGroupRoleModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);
        $projectPermission = new ProjectPermissionModel($this->container);

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

    public function testIsAssignableWhenUserIsDisabled()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);
        $projectPermission = new ProjectPermissionModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user 1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user 2', 'is_active' => 0)));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1')));

        $this->assertTrue($userRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(1, 3, Role::PROJECT_MEMBER));

        $this->assertTrue($projectPermission->isAssignable(1, 2));
        $this->assertFalse($projectPermission->isAssignable(1, 3));
    }

    public function testIsMember()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $groupModel = new GroupModel($this->container);
        $groupRoleModel = new ProjectGroupRoleModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);
        $projectPermission = new ProjectPermissionModel($this->container);

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
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);
        $projectPermission = new ProjectPermissionModel($this->container);

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

    public function testGetProjectIds()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);
        $projectPermission = new ProjectPermissionModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user 1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user 2')));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project 2', 'is_active' => 0)));

        $this->assertTrue($userRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(2, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(1, 3, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(2, 3, Role::PROJECT_MEMBER));

        $this->assertEmpty($projectPermission->getProjectIds(1));
        $this->assertEquals(array(1, 2), $projectPermission->getProjectIds(2));
        $this->assertEquals(array(1, 2), $projectPermission->getProjectIds(3));
    }

    public function testDuplicate()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $groupModel = new GroupModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);
        $groupRoleModel = new ProjectGroupRoleModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);
        $projectPermission = new ProjectPermissionModel($this->container);

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
