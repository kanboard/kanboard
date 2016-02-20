<?php

require_once __DIR__.'/Base.php';

class ColumnTest extends Base
{
    public function testCreateProject()
    {
        $this->assertEquals(1, $this->app->createProject('A project'));
    }

    public function testGetColumns()
    {
        $columns = $this->app->getColumns($this->getProjectId());
        $this->assertCount(4, $columns);
        $this->assertEquals('Done', $columns[3]['title']);
    }

    public function testUpdateColumn()
    {
        $this->assertTrue($this->app->updateColumn(4, 'Boo', 2));

        $columns = $this->app->getColumns($this->getProjectId());
        $this->assertEquals('Boo', $columns[3]['title']);
        $this->assertEquals(2, $columns[3]['task_limit']);
    }

    public function testAddColumn()
    {
        $column_id = $this->app->addColumn($this->getProjectId(), 'New column');

        $this->assertNotFalse($column_id);
        $this->assertInternalType('int', $column_id);
        $this->assertTrue($column_id > 0);

        $columns = $this->app->getColumns($this->getProjectId());
        $this->assertCount(5, $columns);
        $this->assertEquals('New column', $columns[4]['title']);
    }

    public function testRemoveColumn()
    {
        $this->assertTrue($this->app->removeColumn(5));

        $columns = $this->app->getColumns($this->getProjectId());
        $this->assertCount(4, $columns);
    }

    public function testChangeColumnPosition()
    {
        $this->assertTrue($this->app->changeColumnPosition($this->getProjectId(), 1, 3));

        $columns = $this->app->getColumns($this->getProjectId());
        $this->assertCount(4, $columns);

        $this->assertEquals('Ready', $columns[0]['title']);
        $this->assertEquals(1, $columns[0]['position']);
        $this->assertEquals('Work in progress', $columns[1]['title']);
        $this->assertEquals(2, $columns[1]['position']);
        $this->assertEquals('Backlog', $columns[2]['title']);
        $this->assertEquals(3, $columns[2]['position']);
        $this->assertEquals('Boo', $columns[3]['title']);
        $this->assertEquals(4, $columns[3]['position']);
    }
}
