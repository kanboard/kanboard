<?php

namespace KanboardTests\units\Model;

use KanboardTests\units\Base;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectRoleModel;
use Kanboard\Model\ProjectRoleRestrictionModel;

class ProjectRoleRestrictionModelTest extends Base
{
    public function testCreation()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectRoleRestrictionModel = new ProjectRoleRestrictionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'my-custom-role'));
        $this->assertEquals(1, $projectRoleRestrictionModel->create(1, 1, ProjectRoleRestrictionModel::RULE_TASK_CREATION));
    }

    public function testRemove()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectRoleRestrictionModel = new ProjectRoleRestrictionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'my-custom-role'));
        $this->assertEquals(1, $projectRoleRestrictionModel->create(1, 1, ProjectRoleRestrictionModel::RULE_TASK_CREATION));
        $this->assertTrue($projectRoleRestrictionModel->remove(1, 1));
        $this->assertFalse($projectRoleRestrictionModel->remove(1, 1));
    }

    public function testRemoveFromAnotherProject()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectRoleRestrictionModel = new ProjectRoleRestrictionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project A')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project B')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'my-custom-role'));
        $this->assertEquals(1, $projectRoleRestrictionModel->create(1, 1, ProjectRoleRestrictionModel::RULE_TASK_CREATION));

        // A manager of project 2 must not be able to remove a restriction owned by project 1
        $this->assertFalse($projectRoleRestrictionModel->remove(2, 1));
        $this->assertNotEmpty($projectRoleRestrictionModel->getById(1, 1));

        // The legitimate project can still remove it
        $this->assertTrue($projectRoleRestrictionModel->remove(1, 1));
    }

    public function testGetById()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectRoleRestrictionModel = new ProjectRoleRestrictionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'my-custom-role'));
        $this->assertEquals(1, $projectRoleRestrictionModel->create(1, 1, ProjectRoleRestrictionModel::RULE_TASK_CREATION));

        $restriction = $projectRoleRestrictionModel->getById(1, 1);
        $this->assertEquals(ProjectRoleRestrictionModel::RULE_TASK_CREATION, $restriction['rule']);
        $this->assertEquals(1, $restriction['project_id']);
        $this->assertEquals(1, $restriction['restriction_id']);
    }

    public function testGetAll()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectRoleRestrictionModel = new ProjectRoleRestrictionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'my-custom-role'));
        $this->assertEquals(1, $projectRoleRestrictionModel->create(1, 1, ProjectRoleRestrictionModel::RULE_TASK_CREATION));

        $restrictions = $projectRoleRestrictionModel->getAll(1);
        $this->assertCount(1, $restrictions);
        $this->assertEquals(ProjectRoleRestrictionModel::RULE_TASK_CREATION, $restrictions[0]['rule']);
        $this->assertEquals(1, $restrictions[0]['project_id']);
        $this->assertEquals(1, $restrictions[0]['restriction_id']);
        $this->assertEquals(1, $restrictions[0]['role_id']);
    }

    public function testGetByRole()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectRoleRestrictionModel = new ProjectRoleRestrictionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'my-custom-role'));
        $this->assertEquals(1, $projectRoleRestrictionModel->create(1, 1, ProjectRoleRestrictionModel::RULE_TASK_CREATION));

        $restrictions = $projectRoleRestrictionModel->getAllByRole(1, 'my-custom-role');
        $this->assertCount(1, $restrictions);
        $this->assertEquals(ProjectRoleRestrictionModel::RULE_TASK_CREATION, $restrictions[0]['rule']);
        $this->assertEquals(1, $restrictions[0]['project_id']);
        $this->assertEquals(1, $restrictions[0]['restriction_id']);
        $this->assertEquals(1, $restrictions[0]['role_id']);
        $this->assertEquals('my-custom-role', $restrictions[0]['role']);
    }

    public function testGetRules()
    {
        $projectRoleRestrictionModel = new ProjectRoleRestrictionModel($this->container);
        $rules = $projectRoleRestrictionModel->getRules();

        $this->assertCount(6, $rules);
        $this->assertArrayHasKey(ProjectRoleRestrictionModel::RULE_TASK_OPEN_CLOSE, $rules);
    }
}
