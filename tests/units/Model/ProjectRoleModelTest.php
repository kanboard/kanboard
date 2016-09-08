<?php

use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectRoleModel;

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

    public function testModification()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'Role A'));
        $this->assertTrue($projectRoleModel->update(1, 1, 'Role B'));
    }

    public function testRemove()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'Role A'));
        $this->assertTrue($projectRoleModel->remove(1, 1));
        $this->assertEmpty($projectRoleModel->getAll(1));
    }
}
