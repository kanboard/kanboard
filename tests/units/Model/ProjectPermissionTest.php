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
