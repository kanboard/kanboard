<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\ProjectModel;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\TaskCreationModel;

class ColumnModelTest extends Base
{
    public function testGetColumn()
    {
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));

        $column = $columnModel->getById(3);
        $this->assertNotEmpty($column);
        $this->assertEquals('Work in progress', $column['title']);

        $column = $columnModel->getById(33);
        $this->assertEmpty($column);
    }

    public function testGetFirstColumnId()
    {
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $columnModel->getFirstColumnId(1));
    }

    public function testGetLastColumnId()
    {
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertEquals(4, $columnModel->getLastColumnId(1));
    }

    public function testGetLastColumnPosition()
    {
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertEquals(4, $columnModel->getLastColumnPosition(1));
    }

    public function testGetColumnIdByTitle()
    {
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertEquals(2, $columnModel->getColumnIdByTitle(1, 'Ready'));
    }

    public function testGetTitleByColumnId()
    {
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertEquals('Work in progress', $columnModel->getColumnTitleById(3));
    }

    public function testGetAll()
    {
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));

        $columns = $columnModel->getAll(1);
        $this->assertCount(4, $columns);

        $this->assertEquals(1, $columns[0]['id']);
        $this->assertEquals(1, $columns[0]['position']);
        $this->assertEquals('Backlog', $columns[0]['title']);

        $this->assertEquals(2, $columns[1]['id']);
        $this->assertEquals(2, $columns[1]['position']);
        $this->assertEquals('Ready', $columns[1]['title']);

        $this->assertEquals(3, $columns[2]['id']);
        $this->assertEquals(3, $columns[2]['position']);
        $this->assertEquals('Work in progress', $columns[2]['title']);

        $this->assertEquals(4, $columns[3]['id']);
        $this->assertEquals(4, $columns[3]['position']);
        $this->assertEquals('Done', $columns[3]['title']);
    }

    public function testGetAllWithTasksCount()
    {
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'UnitTest', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'UnitTest', 'project_id' => 1, 'column_id' => 2, 'is_active' => 0)));

        $columns = $columnModel->getAllWithTaskCount(1);
        $this->assertCount(4, $columns);

        $this->assertEquals(1, $columns[0]['id']);
        $this->assertEquals(1, $columns[0]['position']);
        $this->assertEquals(1, $columns[0]['project_id']);
        $this->assertEquals(0, $columns[0]['task_limit']);
        $this->assertEquals(0, $columns[0]['hide_in_dashboard']);
        $this->assertEquals('', $columns[0]['description']);
        $this->assertEquals('Backlog', $columns[0]['title']);
        $this->assertEquals(1, $columns[0]['nb_open_tasks']);
        $this->assertEquals(0, $columns[0]['nb_closed_tasks']);

        $this->assertEquals(2, $columns[1]['id']);
        $this->assertEquals(2, $columns[1]['position']);
        $this->assertEquals('Ready', $columns[1]['title']);
        $this->assertEquals(0, $columns[1]['nb_open_tasks']);
        $this->assertEquals(1, $columns[1]['nb_closed_tasks']);
    }

    public function testGetList()
    {
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));

        $columns = $columnModel->getList(1);
        $this->assertCount(4, $columns);
        $this->assertEquals('Backlog', $columns[1]);
        $this->assertEquals('Ready', $columns[2]);
        $this->assertEquals('Work in progress', $columns[3]);
        $this->assertEquals('Done', $columns[4]);

        $columns = $columnModel->getList(1, true);
        $this->assertCount(5, $columns);
        $this->assertEquals('All columns', $columns[-1]);
        $this->assertEquals('Backlog', $columns[1]);
        $this->assertEquals('Ready', $columns[2]);
        $this->assertEquals('Work in progress', $columns[3]);
        $this->assertEquals('Done', $columns[4]);
    }

    public function testAddColumn()
    {
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertNotFalse($columnModel->create(1, 'another column'));
        $this->assertNotFalse($columnModel->create(1, 'one more', 3, 'one more description'));

        $columns = $columnModel->getAll(1);
        $this->assertTrue(is_array($columns));
        $this->assertEquals(6, count($columns));

        $this->assertEquals('another column', $columns[4]['title']);
        $this->assertEquals(0, $columns[4]['task_limit']);
        $this->assertEquals(5, $columns[4]['position']);

        $this->assertEquals('one more', $columns[5]['title']);
        $this->assertEquals(3, $columns[5]['task_limit']);
        $this->assertEquals(6, $columns[5]['position']);
        $this->assertEquals('one more description', $columns[5]['description']);
    }

    public function testUpdateColumn()
    {
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));

        $this->assertTrue($columnModel->update(3, 'blah', 5));
        $this->assertTrue($columnModel->update(2, 'boo'));

        $column = $columnModel->getById(3);
        $this->assertNotEmpty($column);
        $this->assertEquals('blah', $column['title']);
        $this->assertEquals(5, $column['task_limit']);

        $column = $columnModel->getById(2);
        $this->assertNotEmpty($column);
        $this->assertEquals('boo', $column['title']);
        $this->assertEquals(0, $column['task_limit']);
    }

    public function testRemoveColumn()
    {
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertTrue($columnModel->remove(3));
        $this->assertFalse($columnModel->remove(322));

        $columns = $columnModel->getAll(1);
        $this->assertTrue(is_array($columns));
        $this->assertEquals(3, count($columns));
    }

    public function testChangePosition()
    {
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));

        $columns = $columnModel->getAll(1);
        $this->assertEquals(1, $columns[0]['position']);
        $this->assertEquals(1, $columns[0]['id']);
        $this->assertEquals(2, $columns[1]['position']);
        $this->assertEquals(2, $columns[1]['id']);
        $this->assertEquals(3, $columns[2]['position']);
        $this->assertEquals(3, $columns[2]['id']);

        $this->assertTrue($columnModel->changePosition(1, 3, 2));

        $columns = $columnModel->getAll(1);
        $this->assertEquals(1, $columns[0]['position']);
        $this->assertEquals(1, $columns[0]['id']);
        $this->assertEquals(2, $columns[1]['position']);
        $this->assertEquals(3, $columns[1]['id']);
        $this->assertEquals(3, $columns[2]['position']);
        $this->assertEquals(2, $columns[2]['id']);

        $this->assertTrue($columnModel->changePosition(1, 2, 1));

        $columns = $columnModel->getAll(1);
        $this->assertEquals(1, $columns[0]['position']);
        $this->assertEquals(2, $columns[0]['id']);
        $this->assertEquals(2, $columns[1]['position']);
        $this->assertEquals(1, $columns[1]['id']);
        $this->assertEquals(3, $columns[2]['position']);
        $this->assertEquals(3, $columns[2]['id']);

        $this->assertTrue($columnModel->changePosition(1, 2, 2));

        $columns = $columnModel->getAll(1);
        $this->assertEquals(1, $columns[0]['position']);
        $this->assertEquals(1, $columns[0]['id']);
        $this->assertEquals(2, $columns[1]['position']);
        $this->assertEquals(2, $columns[1]['id']);
        $this->assertEquals(3, $columns[2]['position']);
        $this->assertEquals(3, $columns[2]['id']);

        $this->assertTrue($columnModel->changePosition(1, 4, 1));

        $columns = $columnModel->getAll(1);
        $this->assertEquals(1, $columns[0]['position']);
        $this->assertEquals(4, $columns[0]['id']);
        $this->assertEquals(2, $columns[1]['position']);
        $this->assertEquals(1, $columns[1]['id']);
        $this->assertEquals(3, $columns[2]['position']);
        $this->assertEquals(2, $columns[2]['id']);

        $this->assertFalse($columnModel->changePosition(1, 2, 0));
        $this->assertFalse($columnModel->changePosition(1, 2, 5));
    }
}
