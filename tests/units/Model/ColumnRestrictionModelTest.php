<?php

use Kanboard\Model\ColumnRestrictionModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectRoleModel;

require_once __DIR__.'/../Base.php';

class ColumnRestrictionModelTest extends Base
{
    public function testCreation()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $columnRestrictionModel = new ColumnRestrictionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'my-custom-role'));
        $this->assertEquals(1, $columnRestrictionModel->create(1, 1, 2, ColumnRestrictionModel::RULE_BLOCK_TASK_CREATION));
    }

    public function testRemove()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectRoleRestrictionModel = new ColumnRestrictionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'my-custom-role'));
        $this->assertEquals(1, $projectRoleRestrictionModel->create(1, 1, 2, ColumnRestrictionModel::RULE_ALLOW_TASK_OPEN_CLOSE));
        $this->assertTrue($projectRoleRestrictionModel->remove(1));
        $this->assertFalse($projectRoleRestrictionModel->remove(1));
    }

    public function testGetById()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectRoleRestrictionModel = new ColumnRestrictionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'my-custom-role'));
        $this->assertEquals(1, $projectRoleRestrictionModel->create(1, 1, 2, ColumnRestrictionModel::RULE_ALLOW_TASK_CREATION));

        $restriction = $projectRoleRestrictionModel->getById(1, 1);
        $this->assertEquals(ColumnRestrictionModel::RULE_ALLOW_TASK_CREATION, $restriction['rule']);
        $this->assertEquals(1, $restriction['project_id']);
        $this->assertEquals(1, $restriction['restriction_id']);
        $this->assertEquals('Ready', $restriction['column_title']);
    }

    public function testGetAll()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectRoleRestrictionModel = new ColumnRestrictionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'my-custom-role'));
        $this->assertEquals(1, $projectRoleRestrictionModel->create(1, 1, 2, ColumnRestrictionModel::RULE_ALLOW_TASK_CREATION));

        $restrictions = $projectRoleRestrictionModel->getAll(1);
        $this->assertCount(1, $restrictions);
        $this->assertEquals(ColumnRestrictionModel::RULE_ALLOW_TASK_CREATION, $restrictions[0]['rule']);
        $this->assertEquals(1, $restrictions[0]['project_id']);
        $this->assertEquals(1, $restrictions[0]['restriction_id']);
        $this->assertEquals(1, $restrictions[0]['role_id']);
        $this->assertEquals(2, $restrictions[0]['column_id']);
        $this->assertEquals('Ready', $restrictions[0]['column_title']);
    }

    public function testGetByRole()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $columnRestrictionModel = new ColumnRestrictionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'my-custom-role'));
        $this->assertEquals(1, $columnRestrictionModel->create(1, 1, 2, ColumnRestrictionModel::RULE_BLOCK_TASK_CREATION));

        $restrictions = $columnRestrictionModel->getAllByRole(1, 'my-custom-role');
        $this->assertCount(1, $restrictions);
        $this->assertEquals(ColumnRestrictionModel::RULE_BLOCK_TASK_CREATION, $restrictions[0]['rule']);
        $this->assertEquals(1, $restrictions[0]['project_id']);
        $this->assertEquals(1, $restrictions[0]['restriction_id']);
        $this->assertEquals(1, $restrictions[0]['role_id']);
        $this->assertEquals(2, $restrictions[0]['column_id']);
        $this->assertEquals('my-custom-role', $restrictions[0]['role']);
    }

    public function testGetRules()
    {
        $columnRestrictionModel = new ColumnRestrictionModel($this->container);
        $rules = $columnRestrictionModel->getRules();

        $this->assertCount(4, $rules);
        $this->assertArrayHasKey(ColumnRestrictionModel::RULE_ALLOW_TASK_CREATION, $rules);
    }
}
