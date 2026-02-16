<?php

namespace KanboardTests\integration;

class ColumnProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My project to test columns';
    private $columns = array();

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertGetColumns();
        $this->assertUpdateColumn();
        $this->assertAddColumn();
        $this->assertRemoveColumn();
        $this->assertChangeColumnPosition();
    }

    public function assertGetColumns()
    {
        $this->columns = $this->app->getColumns($this->projectId);
        $this->assertCount(4, $this->columns);
        $this->assertEquals('Done', $this->columns[3]['title']);
    }

    public function assertUpdateColumn()
    {
        $this->assertTrue($this->app->updateColumn($this->columns[3]['id'], 'Another column', 2));

        $this->columns = $this->app->getColumns($this->projectId);
        $this->assertEquals('Another column', $this->columns[3]['title']);
        $this->assertEquals(2, $this->columns[3]['task_limit']);
    }

    public function assertAddColumn()
    {
        $column_id = $this->app->addColumn($this->projectId, 'New column');
        $this->assertNotFalse($column_id);
        $this->assertTrue($column_id > 0);

        $this->columns = $this->app->getColumns($this->projectId);
        $this->assertCount(5, $this->columns);
        $this->assertEquals('New column', $this->columns[4]['title']);
    }

    public function assertRemoveColumn()
    {
        $this->assertTrue($this->app->removeColumn($this->columns[3]['id']));

        $this->columns = $this->app->getColumns($this->projectId);
        $this->assertCount(4, $this->columns);
    }

    public function assertChangeColumnPosition()
    {
        $this->assertTrue($this->app->changeColumnPosition($this->projectId, $this->columns[0]['id'], 3));

        $this->columns = $this->app->getColumns($this->projectId);
        $this->assertEquals('Ready', $this->columns[0]['title']);
        $this->assertEquals(1, $this->columns[0]['position']);
        $this->assertEquals('Work in progress', $this->columns[1]['title']);
        $this->assertEquals(2, $this->columns[1]['position']);
        $this->assertEquals('Backlog', $this->columns[2]['title']);
        $this->assertEquals(3, $this->columns[2]['position']);
        $this->assertEquals('New column', $this->columns[3]['title']);
        $this->assertEquals(4, $this->columns[3]['position']);
    }

    public function testChangeColumnPositionCannotModifyColumnFromAnotherProjectWithForgedProjectId()
    {
        $projectIdA = $this->manager->createProject(array(
            'name' => 'Project A',
            'owner_id' => $this->managerUserId,
        ));

        $projectIdB = $this->manager->createProject(array(
            'name' => 'Project B',
            'owner_id' => $this->managerUserId,
        ));

        $this->assertNotFalse($projectIdA);
        $this->assertNotFalse($projectIdB);
        $this->assertTrue($this->manager->addProjectUser($projectIdA, $this->userUserId, 'project-manager'));

        $columnIdB = $this->manager->addColumn($projectIdB, 'Project B column');
        $this->assertNotFalse($columnIdB);

        $this->assertFalse($this->user->changeColumnPosition($projectIdA, $columnIdB, 1));

        $columnB = $this->manager->getColumn($columnIdB);
        $this->assertEquals($projectIdB, $columnB['project_id']);
    }
}
