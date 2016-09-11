<?php

use Kanboard\Model\ColumnMoveRestrictionModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectRoleModel;

require_once __DIR__.'/../Base.php';

class ColumnMoveRestrictionModelTest extends Base
{
    public function testCreation()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $columnMoveRestrictionModel = new ColumnMoveRestrictionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'Custom Role'));
        $this->assertEquals(1, $columnMoveRestrictionModel->create(1, 1, 2, 3));
        $this->assertFalse($columnMoveRestrictionModel->create(1, 1, 2, 3));
    }

    public function testRemove()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $columnMoveRestrictionModel = new ColumnMoveRestrictionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'Custom Role'));
        $this->assertEquals(1, $columnMoveRestrictionModel->create(1, 1, 2, 3));
        $this->assertTrue($columnMoveRestrictionModel->remove(1));
    }

    public function testGetById()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $columnMoveRestrictionModel = new ColumnMoveRestrictionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $projectRoleModel->create(1, 'Role A'));
        $this->assertEquals(1, $columnMoveRestrictionModel->create(1, 1, 2, 3));

        $restriction = $columnMoveRestrictionModel->getById(1, 1);
        $this->assertEquals(1, $restriction['restriction_id']);
        $this->assertEquals('Role A', $restriction['role']);
        $this->assertEquals(1, $restriction['role_id']);
        $this->assertEquals(1, $restriction['project_id']);
        $this->assertEquals('Ready', $restriction['src_column_title']);
        $this->assertEquals('Work in progress', $restriction['dst_column_title']);
        $this->assertEquals(2, $restriction['src_column_id']);
        $this->assertEquals(3, $restriction['dst_column_id']);
    }

    public function testGetAll()
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $columnMoveRestrictionModel = new ColumnMoveRestrictionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Test')));

        $this->assertEquals(1, $projectRoleModel->create(1, 'Role A'));
        $this->assertEquals(2, $projectRoleModel->create(1, 'Role B'));
        $this->assertEquals(3, $projectRoleModel->create(2, 'Role C'));

        $this->assertEquals(1, $columnMoveRestrictionModel->create(1, 1, 2, 3));
        $this->assertEquals(2, $columnMoveRestrictionModel->create(1, 2, 3, 4));

        $restrictions = $columnMoveRestrictionModel->getAll(1);
        $this->assertCount(2, $restrictions);

        $this->assertEquals(1, $restrictions[0]['restriction_id']);
        $this->assertEquals('Role A', $restrictions[0]['role']);
        $this->assertEquals(1, $restrictions[0]['role_id']);
        $this->assertEquals(1, $restrictions[0]['project_id']);
        $this->assertEquals('Ready', $restrictions[0]['src_column_title']);
        $this->assertEquals('Work in progress', $restrictions[0]['dst_column_title']);
        $this->assertEquals(2, $restrictions[0]['src_column_id']);
        $this->assertEquals(3, $restrictions[0]['dst_column_id']);

        $this->assertEquals(2, $restrictions[1]['restriction_id']);
        $this->assertEquals('Role B', $restrictions[1]['role']);
        $this->assertEquals('Work in progress', $restrictions[1]['src_column_title']);
        $this->assertEquals('Done', $restrictions[1]['dst_column_title']);
        $this->assertEquals(3, $restrictions[1]['src_column_id']);
        $this->assertEquals(4, $restrictions[1]['dst_column_id']);
    }
}
