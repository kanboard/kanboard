<?php

use Kanboard\Core\Security\Role;
use Kanboard\Model\GroupMemberModel;
use Kanboard\Model\GroupModel;
use Kanboard\Model\ProjectGroupRoleModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectRoleModel;
use Kanboard\Model\ProjectUserRoleModel;

require_once __DIR__.'/../Base.php';

class ProjectRoleModelTest extends Base
{
    public function testCreation()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'my-custom-role'));
        $this->assertFalse($projectRoleModel->create(1, 'my-custom-role'));
    }

    public function testGetAll()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'Role A'));
        $this->assertEquals(2, $projectRoleModel->create(1, 'Role B'));

        $roles = $projectRoleModel->getAll(1);
        $this->assertCount(2, $roles);

        $this->assertEquals(1, $roles[0]['role_id']);
        $this->assertEquals('Role A', $roles[0]['role']);

        $this->assertEquals(2, $roles[1]['role_id']);
        $this->assertEquals('Role B', $roles[1]['role']);
    }

    public function testModificationWithUserRole()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $groupModel = new GroupModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);

        $this->assertEquals(1, $groupModel->create('Group A'));
        $this->assertTrue($groupMemberModel->addUser(1, 1));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'Role A'));
        $this->assertTrue($projectUserRoleModel->addUser(1, 1, 'Role A'));
        $this->assertEquals('Role A', $projectUserRoleModel->getUserRole(1, 1));

        $this->assertTrue($projectRoleModel->update(1, 1, 'Role B'));
        $this->assertEquals('Role B', $projectUserRoleModel->getUserRole(1, 1));
    }

    public function testModificationWithGroupRole()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectGroupRoleModel = new ProjectGroupRoleModel($this->container);
        $groupModel = new GroupModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);

        $this->assertEquals(1, $groupModel->create('Group A'));
        $this->assertTrue($groupMemberModel->addUser(1, 1));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'Role A'));
        $this->assertTrue($projectGroupRoleModel->addGroup(1, 1, 'Role A'));
        $this->assertEquals('Role A', $projectGroupRoleModel->getUserRole(1, 1));

        $this->assertTrue($projectRoleModel->update(1, 1, 'Role B'));
        $this->assertEquals('Role B', $projectGroupRoleModel->getUserRole(1, 1));
    }

    public function testRemoveWithUserRole()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'Role A'));
        $this->assertTrue($projectUserRoleModel->addUser(1, 1, 'Role A'));
        $this->assertEquals('Role A', $projectUserRoleModel->getUserRole(1, 1));

        $this->assertTrue($projectRoleModel->remove(1, 1));
        $this->assertEmpty($projectRoleModel->getAll(1));
        $this->assertEquals(Role::PROJECT_MEMBER, $projectUserRoleModel->getUserRole(1, 1));
    }

    public function testRemoveWithGroupRole()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectGroupRoleModel = new ProjectGroupRoleModel($this->container);
        $groupModel = new GroupModel($this->container);
        $groupMemberModel = new GroupMemberModel($this->container);

        $this->assertEquals(1, $groupModel->create('Group A'));
        $this->assertTrue($groupMemberModel->addUser(1, 1));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'Role A'));
        $this->assertTrue($projectGroupRoleModel->addGroup(1, 1, 'Role A'));
        $this->assertEquals('Role A', $projectGroupRoleModel->getUserRole(1, 1));

        $this->assertTrue($projectRoleModel->remove(1, 1));
        $this->assertEmpty($projectRoleModel->getAll(1));
        $this->assertEquals(Role::PROJECT_MEMBER, $projectGroupRoleModel->getUserRole(1, 1));
    }

    public function testGetRoleListWithoutCustomRoles()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));

        $roles = $projectRoleModel->getList(1);
        $this->assertCount(3, $roles);
        $this->assertEquals('Project Manager', $roles[Role::PROJECT_MANAGER]);
    }

    public function testGetRoleListWithCustomRoles()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'Role A'));

        $roles = $projectRoleModel->getList(1);
        $this->assertCount(4, $roles);
        $this->assertEquals('Role A', $roles['Role A']);
    }

    public function testGetById()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'Role A'));

        $role = $projectRoleModel->getById(1, 1);
        $this->assertEquals(1, $role['role_id']);
        $this->assertEquals(1, $role['project_id']);
        $this->assertEquals('Role A', $role['role']);
    }
}
