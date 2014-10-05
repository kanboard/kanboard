<?php

require_once __DIR__.'/Base.php';

use Model\Project;
use Model\Board;
use Model\Config;

class BoardTest extends Base
{
    public function testCreation()
    {
        $p = new Project($this->registry);
        $b = new Board($this->registry);
        $c = new Config($this->registry);

        // Default columns

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $columns = $b->getColumnsList(1);

        $this->assertTrue(is_array($columns));
        $this->assertEquals(4, count($columns));
        $this->assertEquals('Backlog', $columns[1]);
        $this->assertEquals('Ready', $columns[2]);
        $this->assertEquals('Work in progress', $columns[3]);
        $this->assertEquals('Done', $columns[4]);

        // Custom columns: spaces should be trimed and no empty columns
        $input = '   column #1  , column #2, ';

        $this->assertTrue($c->save(array('board_columns' => $input)));
        $this->assertEquals($input, $c->get('board_columns'));

        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));
        $columns = $b->getColumnsList(2);

        $this->assertTrue(is_array($columns));
        $this->assertEquals(2, count($columns));
        $this->assertEquals('column #1', $columns[5]);
        $this->assertEquals('column #2', $columns[6]);
    }

    public function testGetBoard()
    {
        $p = new Project($this->registry);
        $b = new Board($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));

        $board = $b->get(1);
        $this->assertNotEmpty($board);
        $this->assertEquals(4, count($board));
        $this->assertTrue(array_key_exists('tasks', $board[2]));
        $this->assertTrue(array_key_exists('title', $board[2]));
    }

    public function testGetColumn()
    {
        $p = new Project($this->registry);
        $b = new Board($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));

        $column = $b->getColumn(3);
        $this->assertNotEmpty($column);
        $this->assertEquals('Work in progress', $column['title']);

        $column = $b->getColumn(33);
        $this->assertEmpty($column);
    }

    public function testRemoveColumn()
    {
        $p = new Project($this->registry);
        $b = new Board($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertTrue($b->removeColumn(3));
        $this->assertFalse($b->removeColumn(322));

        $columns = $b->getColumns(1);
        $this->assertTrue(is_array($columns));
        $this->assertEquals(3, count($columns));
    }

    public function testUpdateColumn()
    {
        $p = new Project($this->registry);
        $b = new Board($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));

        $this->assertTrue($b->updateColumn(3, 'blah', 5));
        $this->assertTrue($b->updateColumn(2, 'boo'));

        $column = $b->getColumn(3);
        $this->assertNotEmpty($column);
        $this->assertEquals('blah', $column['title']);
        $this->assertEquals(5, $column['task_limit']);

        $column = $b->getColumn(2);
        $this->assertNotEmpty($column);
        $this->assertEquals('boo', $column['title']);
        $this->assertEquals(0, $column['task_limit']);
    }

    public function testAddColumn()
    {
        $p = new Project($this->registry);
        $b = new Board($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertTrue($b->addColumn(1, 'another column'));
        $this->assertTrue($b->addColumn(1, 'one more', 3));

        $columns = $b->getColumns(1);
        $this->assertTrue(is_array($columns));
        $this->assertEquals(6, count($columns));

        $this->assertEquals('another column', $columns[4]['title']);
        $this->assertEquals(0, $columns[4]['task_limit']);
        $this->assertEquals(5, $columns[4]['position']);

        $this->assertEquals('one more', $columns[5]['title']);
        $this->assertEquals(3, $columns[5]['task_limit']);
        $this->assertEquals(6, $columns[5]['position']);
    }

    public function testMoveColumns()
    {
        $p = new Project($this->registry);
        $b = new Board($this->registry);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));

        // We get the columns of the project 2
        $columns = $b->getColumns(2);
        $columns_id = array_keys($b->getColumnsList(2));
        $this->assertNotEmpty($columns);

        // Initial order: 5, 6, 7, 8

        // Move the column 1 down
        $this->assertEquals(1, $columns[0]['position']);
        $this->assertEquals($columns_id[0], $columns[0]['id']);

        $this->assertEquals(2, $columns[1]['position']);
        $this->assertEquals($columns_id[1], $columns[1]['id']);

        $this->assertTrue($b->moveDown(2, $columns[0]['id']));
        $columns = $b->getColumns(2); // Sorted by position

        // New order: 6, 5, 7, 8

        $this->assertEquals(1, $columns[0]['position']);
        $this->assertEquals($columns_id[1], $columns[0]['id']);

        $this->assertEquals(2, $columns[1]['position']);
        $this->assertEquals($columns_id[0], $columns[1]['id']);

        // Move the column 3 up
        $this->assertTrue($b->moveUp(2, $columns[2]['id']));
        $columns = $b->getColumns(2);

        // New order: 6, 7, 5, 8

        $this->assertEquals(1, $columns[0]['position']);
        $this->assertEquals($columns_id[1], $columns[0]['id']);

        $this->assertEquals(2, $columns[1]['position']);
        $this->assertEquals($columns_id[2], $columns[1]['id']);

        $this->assertEquals(3, $columns[2]['position']);
        $this->assertEquals($columns_id[0], $columns[2]['id']);

        // Move column 1 up (must do nothing because it's the first column)
        $this->assertFalse($b->moveUp(2, $columns[0]['id']));
        $columns = $b->getColumns(2);

        // Order: 6, 7, 5, 8

        $this->assertEquals(1, $columns[0]['position']);
        $this->assertEquals($columns_id[1], $columns[0]['id']);

        // Move column 4 down (must do nothing because it's the last column)
        $this->assertFalse($b->moveDown(2, $columns[3]['id']));
        $columns = $b->getColumns(2);

        // Order: 6, 7, 5, 8

        $this->assertEquals(4, $columns[3]['position']);
        $this->assertEquals($columns_id[3], $columns[3]['id']);
    }
}
