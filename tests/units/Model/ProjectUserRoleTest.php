<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\ProjectModel;
use Kanboard\Model\UserModel;
use Kanboard\Model\GroupModel;
use Kanboard\Model\GroupMemberModel;
use Kanboard\Model\ProjectGroupRoleModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\ProjectPermissionModel;
use Kanboard\Core\Security\Role;

class ProjectUserRoleTest extends Base
{
    public function testAddUser()
    {
        $projectModel = new ProjectModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));

        $this->assertTrue($userRoleModel->addUser(1, 1, Role::PROJECT_VIEWER));
        $this->assertFalse($userRoleModel->addUser(1, 1, Role::PROJECT_VIEWER));

        $users = $userRoleModel->getUsers(1);
        $this->assertCount(1, $users);
        $this->assertEquals(1, $users[0]['id']);
        $this->assertEquals('admin', $users[0]['username']);
        $this->assertEquals('', $users[0]['name']);
        $this->assertEquals(Role::PROJECT_VIEWER, $users[0]['role']);
    }

    public function testRemoveUser()
    {
        $projectModel = new ProjectModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));

        $this->assertTrue($userRoleModel->addUser(1, 1, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->removeUser(1, 1));
        $this->assertFalse($userRoleModel->removeUser(1, 1));

        $this->assertEmpty($userRoleModel->getUsers(1));
    }

    public function testChangeRole()
    {
        $projectModel = new ProjectModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));

        $this->assertTrue($userRoleModel->addUser(1, 1, Role::PROJECT_VIEWER));
        $this->assertTrue($userRoleModel->changeUserRole(1, 1, Role::PROJECT_MANAGER));

        $users = $userRoleModel->getUsers(1);
        $this->assertCount(1, $users);
        $this->assertEquals(1, $users[0]['id']);
        $this->assertEquals('admin', $users[0]['username']);
        $this->assertEquals('', $users[0]['name']);
        $this->assertEquals(Role::PROJECT_MANAGER, $users[0]['role']);
    }

    public function testGetRole()
    {
        $projectModel = new ProjectModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEmpty($userRoleModel->getUserRole(1, 1));

        $this->assertTrue($userRoleModel->addUser(1, 1, Role::PROJECT_VIEWER));
        $this->assertEquals(Role::PROJECT_VIEWER, $userRoleModel->getUserRole(1, 1));

        $this->assertTrue($userRoleModel->changeUserRole(1, 1, Role::PROJECT_MEMBER));
        $this->assertEquals(Role::PROJECT_MEMBER, $userRoleModel->getUserRole(1, 1));

        $this->assertTrue($userRoleModel->changeUserRole(1, 1, Role::PROJECT_MANAGER));
        $this->assertEquals(Role::PROJECT_MANAGER, $userRoleModel->getUserRole(1, 1));

        $this->assertEquals('', $userRoleModel->getUserRole(1, 2));
    }

    public function testGetRoleWithGroups()
    {
        $projectModel = new ProjectModel($this->container);
        $groupModel = new GroupModel($this->container);
        $groupRoleModel = new ProjectGroupRoleModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $groupModel->create('Group A'));

        $this->assertTrue($groupMemberModel->addUser(1, 1));
        $this->assertTrue($groupRoleModel->addGroup(1, 1, Role::PROJECT_VIEWER));

        $this->assertEquals(Role::PROJECT_VIEWER, $userRoleModel->getUserRole(1, 1));
        $this->assertEquals('', $userRoleModel->getUserRole(1, 2));
    }

    public function testGetAssignableUsersWithDisabledUsers()
    {
        $projectModel = new ProjectModel($this->container);
        $userModel = new UserModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(2, $userModel->create(array('username' => 'user1', 'name' => 'User1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2', 'name' => 'User2')));

        $this->assertTrue($userRoleModel->addUser(1, 1, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(1, 3, Role::PROJECT_MEMBER));

        $users = $userRoleModel->getAssignableUsers(1);
        $this->assertCount(3, $users);

        $this->assertEquals('admin', $users[1]);
        $this->assertEquals('User1', $users[2]);
        $this->assertEquals('User2', $users[3]);

        $this->assertTrue($userModel->disable(2));

        $users = $userRoleModel->getAssignableUsers(1);
        $this->assertCount(2, $users);

        $this->assertEquals('admin', $users[1]);
        $this->assertEquals('User2', $users[3]);
    }

    public function testGetAssignableUsersWithoutGroups()
    {
        $projectModel = new ProjectModel($this->container);
        $userModel = new UserModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(2, $userModel->create(array('username' => 'user1', 'name' => 'User1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2', 'name' => 'User2')));

        $this->assertTrue($userRoleModel->addUser(1, 1, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(1, 2, Role::PROJECT_MANAGER));
        $this->assertTrue($userRoleModel->addUser(1, 3, Role::PROJECT_VIEWER));

        $users = $userRoleModel->getAssignableUsers(1);
        $this->assertCount(2, $users);

        $this->assertEquals('admin', $users[1]);
        $this->assertEquals('User1', $users[2]);
    }

    public function testGetAssignableUsersWithGroups()
    {
        $projectModel = new ProjectModel($this->container);
        $userModel = new UserModel($this->container);
        $groupModel = new GroupModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);
        $groupRoleModel = new ProjectGroupRoleModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));

        $this->assertEquals(2, $userModel->create(array('username' => 'user1', 'name' => 'User1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2', 'name' => 'User2')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user3', 'name' => 'User3')));
        $this->assertEquals(5, $userModel->create(array('username' => 'user4', 'name' => 'User4')));

        $this->assertTrue($userRoleModel->addUser(1, 1, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(1, 2, Role::PROJECT_MANAGER));
        $this->assertTrue($userRoleModel->addUser(1, 3, Role::PROJECT_VIEWER));

        $this->assertEquals(1, $groupModel->create('Group A'));
        $this->assertEquals(2, $groupModel->create('Group B'));

        $this->assertTrue($groupMemberModel->addUser(1, 4));
        $this->assertTrue($groupMemberModel->addUser(2, 5));

        $this->assertTrue($groupRoleModel->addGroup(1, 1, Role::PROJECT_VIEWER));
        $this->assertTrue($groupRoleModel->addGroup(1, 2, Role::PROJECT_MEMBER));

        $users = $userRoleModel->getAssignableUsers(1);
        $this->assertCount(3, $users);

        $this->assertEquals('admin', $users[1]);
        $this->assertEquals('User1', $users[2]);
        $this->assertEquals('User4', $users[5]);
    }

    public function testGetAssignableUsersList()
    {
        $projectModel = new ProjectModel($this->container);
        $userModel = new UserModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Test2')));

        $this->assertEquals(2, $userModel->create(array('username' => 'user1', 'name' => 'User1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2', 'name' => 'User2')));

        $this->assertTrue($userRoleModel->addUser(2, 1, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(1, 1, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(1, 2, Role::PROJECT_MANAGER));
        $this->assertTrue($userRoleModel->addUser(1, 3, Role::PROJECT_VIEWER));

        $users = $userRoleModel->getAssignableUsersList(1);
        $this->assertCount(3, $users);

        $this->assertEquals('Unassigned', $users[0]);
        $this->assertEquals('admin', $users[1]);
        $this->assertEquals('User1', $users[2]);

        $users = $userRoleModel->getAssignableUsersList(1, true, true, true);
        $this->assertCount(4, $users);

        $this->assertEquals('Unassigned', $users[0]);
        $this->assertEquals('Everybody', $users[-1]);
        $this->assertEquals('admin', $users[1]);
        $this->assertEquals('User1', $users[2]);

        $users = $userRoleModel->getAssignableUsersList(2, true, true, true);
        $this->assertCount(1, $users);

        $this->assertEquals('admin', $users[1]);
    }

    public function testGetProjectsByUser()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $groupModel = new GroupModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);
        $groupRoleModel = new ProjectGroupRoleModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project 2')));

        $this->assertEquals(2, $userModel->create(array('username' => 'user 1', 'name' => 'User #1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user 2')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user 3')));
        $this->assertEquals(5, $userModel->create(array('username' => 'user 4')));
        $this->assertEquals(6, $userModel->create(array('username' => 'user 5', 'name' => 'User #5')));
        $this->assertEquals(7, $userModel->create(array('username' => 'user 6')));

        $this->assertEquals(1, $groupModel->create('Group C'));
        $this->assertEquals(2, $groupModel->create('Group B'));
        $this->assertEquals(3, $groupModel->create('Group A'));

        $this->assertTrue($groupMemberModel->addUser(1, 4));
        $this->assertTrue($groupMemberModel->addUser(2, 5));
        $this->assertTrue($groupMemberModel->addUser(3, 3));
        $this->assertTrue($groupMemberModel->addUser(3, 2));

        $this->assertTrue($groupRoleModel->addGroup(1, 1, Role::PROJECT_VIEWER));
        $this->assertTrue($groupRoleModel->addGroup(2, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($groupRoleModel->addGroup(1, 3, Role::PROJECT_MANAGER));

        $this->assertTrue($userRoleModel->addUser(1, 6, Role::PROJECT_MANAGER));
        $this->assertTrue($userRoleModel->addUser(2, 6, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(2, 7, Role::PROJECT_MEMBER));

        $projects = $userRoleModel->getProjectsByUser(2);
        $this->assertCount(1, $projects);
        $this->assertEquals('Project 1', $projects[1]);

        $projects = $userRoleModel->getProjectsByUser(3);
        $this->assertCount(1, $projects);
        $this->assertEquals('Project 1', $projects[1]);

        $projects = $userRoleModel->getProjectsByUser(4);
        $this->assertCount(1, $projects);
        $this->assertEquals('Project 1', $projects[1]);

        $projects = $userRoleModel->getProjectsByUser(5);
        $this->assertCount(1, $projects);
        $this->assertEquals('Project 2', $projects[2]);

        $projects = $userRoleModel->getProjectsByUser(6);
        $this->assertCount(2, $projects);
        $this->assertEquals('Project 1', $projects[1]);
        $this->assertEquals('Project 2', $projects[2]);

        $projects = $userRoleModel->getProjectsByUser(7);
        $this->assertCount(1, $projects);
        $this->assertEquals('Project 2', $projects[2]);
    }

    public function testGetActiveProjectsByUser()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $groupModel = new GroupModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);
        $groupRoleModel = new ProjectGroupRoleModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1', 'is_active' => 0)));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project 2')));

        $this->assertEquals(2, $userModel->create(array('username' => 'user 1', 'name' => 'User #1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user 2')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user 3')));
        $this->assertEquals(5, $userModel->create(array('username' => 'user 4')));
        $this->assertEquals(6, $userModel->create(array('username' => 'user 5', 'name' => 'User #5')));
        $this->assertEquals(7, $userModel->create(array('username' => 'user 6')));

        $this->assertEquals(1, $groupModel->create('Group C'));
        $this->assertEquals(2, $groupModel->create('Group B'));
        $this->assertEquals(3, $groupModel->create('Group A'));

        $this->assertTrue($groupMemberModel->addUser(1, 4));
        $this->assertTrue($groupMemberModel->addUser(2, 5));
        $this->assertTrue($groupMemberModel->addUser(3, 3));
        $this->assertTrue($groupMemberModel->addUser(3, 2));

        $this->assertTrue($groupRoleModel->addGroup(1, 1, Role::PROJECT_VIEWER));
        $this->assertTrue($groupRoleModel->addGroup(2, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($groupRoleModel->addGroup(1, 3, Role::PROJECT_MANAGER));

        $this->assertTrue($userRoleModel->addUser(1, 6, Role::PROJECT_MANAGER));
        $this->assertTrue($userRoleModel->addUser(2, 6, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(2, 7, Role::PROJECT_MEMBER));

        $projects = $userRoleModel->getProjectsByUser(2, array(ProjectModel::ACTIVE));
        $this->assertCount(0, $projects);

        $projects = $userRoleModel->getProjectsByUser(3, array(ProjectModel::ACTIVE));
        $this->assertCount(0, $projects);

        $projects = $userRoleModel->getProjectsByUser(4, array(ProjectModel::ACTIVE));
        $this->assertCount(0, $projects);

        $projects = $userRoleModel->getProjectsByUser(5, array(ProjectModel::ACTIVE));
        $this->assertCount(1, $projects);
        $this->assertEquals('Project 2', $projects[2]);

        $projects = $userRoleModel->getProjectsByUser(6, array(ProjectModel::ACTIVE));
        $this->assertCount(1, $projects);
        $this->assertEquals('Project 2', $projects[2]);

        $projects = $userRoleModel->getProjectsByUser(7, array(ProjectModel::ACTIVE));
        $this->assertCount(1, $projects);
        $this->assertEquals('Project 2', $projects[2]);
    }

    public function testGetInactiveProjectsByUser()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $groupModel = new GroupModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);
        $groupRoleModel = new ProjectGroupRoleModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1', 'is_active' => 0)));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project 2')));

        $this->assertEquals(2, $userModel->create(array('username' => 'user 1', 'name' => 'User #1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user 2')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user 3')));
        $this->assertEquals(5, $userModel->create(array('username' => 'user 4')));
        $this->assertEquals(6, $userModel->create(array('username' => 'user 5', 'name' => 'User #5')));
        $this->assertEquals(7, $userModel->create(array('username' => 'user 6')));

        $this->assertEquals(1, $groupModel->create('Group C'));
        $this->assertEquals(2, $groupModel->create('Group B'));
        $this->assertEquals(3, $groupModel->create('Group A'));

        $this->assertTrue($groupMemberModel->addUser(1, 4));
        $this->assertTrue($groupMemberModel->addUser(2, 5));
        $this->assertTrue($groupMemberModel->addUser(3, 3));
        $this->assertTrue($groupMemberModel->addUser(3, 2));

        $this->assertTrue($groupRoleModel->addGroup(1, 1, Role::PROJECT_VIEWER));
        $this->assertTrue($groupRoleModel->addGroup(2, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($groupRoleModel->addGroup(1, 3, Role::PROJECT_MANAGER));

        $this->assertTrue($userRoleModel->addUser(1, 6, Role::PROJECT_MANAGER));
        $this->assertTrue($userRoleModel->addUser(2, 6, Role::PROJECT_MEMBER));
        $this->assertTrue($userRoleModel->addUser(2, 7, Role::PROJECT_MEMBER));

        $projects = $userRoleModel->getProjectsByUser(2, array(ProjectModel::INACTIVE));
        $this->assertCount(1, $projects);
        $this->assertEquals('Project 1', $projects[1]);

        $projects = $userRoleModel->getProjectsByUser(3, array(ProjectModel::INACTIVE));
        $this->assertCount(1, $projects);
        $this->assertEquals('Project 1', $projects[1]);

        $projects = $userRoleModel->getProjectsByUser(4, array(ProjectModel::INACTIVE));
        $this->assertCount(1, $projects);
        $this->assertEquals('Project 1', $projects[1]);

        $projects = $userRoleModel->getProjectsByUser(5, array(ProjectModel::INACTIVE));
        $this->assertCount(0, $projects);

        $projects = $userRoleModel->getProjectsByUser(6, array(ProjectModel::INACTIVE));
        $this->assertCount(1, $projects);
        $this->assertEquals('Project 1', $projects[1]);

        $projects = $userRoleModel->getProjectsByUser(7, array(ProjectModel::INACTIVE));
        $this->assertCount(0, $projects);
    }
}
