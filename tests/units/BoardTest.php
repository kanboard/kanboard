<?php

require_once __DIR__.'/Base.php';

use Model\Project;
use Model\Board;
use Model\Config;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\Swimlane;

class BoardTest extends Base
{
    public function testCreation()
    {
        $p = new Project($this->container);
        $b = new Board($this->container);
        $c = new Config($this->container);

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
        $p = new Project($this->container);
        $b = new Board($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));

        $board = $b->getBoard(1);
        $this->assertNotEmpty($board);
        $this->assertEquals(1, count($board));
        $this->assertEquals(4, count($board[0]));
        $this->assertTrue(array_key_exists('name', $board[0]));
        $this->assertTrue(array_key_exists('columns', $board[0]));
        $this->assertTrue(array_key_exists('tasks', $board[0]['columns'][2]));
        $this->assertTrue(array_key_exists('title', $board[0]['columns'][2]));
    }

    public function testGetBoardWithSwimlane()
    {
        $b = new Board($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $s->create(1, 'test 1'));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 3)));
        $this->assertEquals(3, $tc->create(array('title' => 'Task #3', 'project_id' => 1, 'column_id' => 2, 'swimlane_id' => 1)));
        $this->assertEquals(4, $tc->create(array('title' => 'Task #4', 'project_id' => 1, 'column_id' => 3)));
        $this->assertEquals(5, $tc->create(array('title' => 'Task #5', 'project_id' => 1, 'column_id' => 4)));
        $this->assertEquals(6, $tc->create(array('title' => 'Task #6', 'project_id' => 1, 'column_id' => 4, 'swimlane_id' => 1)));

        $board = $b->getBoard(1);
        $this->assertNotEmpty($board);
        $this->assertEquals(2, count($board));
        $this->assertEquals(4, count($board[0]));
        $this->assertTrue(array_key_exists('name', $board[0]));
        $this->assertTrue(array_key_exists('columns', $board[0]));
        $this->assertTrue(array_key_exists('tasks', $board[0]['columns'][2]));
        $this->assertTrue(array_key_exists('title', $board[0]['columns'][2]));

        $task = $tf->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(0, $task['swimlane_id']);
        $this->assertEquals(1, $board[0]['columns'][0]['tasks'][0]['id']);
        $this->assertEquals(1, $board[0]['columns'][0]['tasks'][0]['column_id']);
        $this->assertEquals(1, $board[0]['columns'][0]['tasks'][0]['position']);
        $this->assertEquals(0, $board[0]['columns'][0]['tasks'][0]['swimlane_id']);

        $task = $tf->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(0, $task['swimlane_id']);
        $this->assertEquals(2, $board[0]['columns'][2]['tasks'][0]['id']);
        $this->assertEquals(3, $board[0]['columns'][2]['tasks'][0]['column_id']);
        $this->assertEquals(1, $board[0]['columns'][2]['tasks'][0]['position']);
        $this->assertEquals(0, $board[0]['columns'][2]['tasks'][0]['swimlane_id']);

        $task = $tf->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);
        $this->assertEquals(3, $board[1]['columns'][1]['tasks'][0]['id']);
        $this->assertEquals(2, $board[1]['columns'][1]['tasks'][0]['column_id']);
        $this->assertEquals(1, $board[1]['columns'][1]['tasks'][0]['position']);
        $this->assertEquals(1, $board[1]['columns'][1]['tasks'][0]['swimlane_id']);

        $task = $tf->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals(0, $task['swimlane_id']);
        $this->assertEquals(4, $board[0]['columns'][2]['tasks'][1]['id']);
        $this->assertEquals(3, $board[0]['columns'][2]['tasks'][1]['column_id']);
        $this->assertEquals(2, $board[0]['columns'][2]['tasks'][1]['position']);
        $this->assertEquals(0, $board[0]['columns'][2]['tasks'][1]['swimlane_id']);

        $task = $tf->getById(5);
        $this->assertEquals(5, $task['id']);
        $this->assertEquals(4, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(0, $task['swimlane_id']);
        $this->assertEquals(5, $board[0]['columns'][3]['tasks'][0]['id']);
        $this->assertEquals(4, $board[0]['columns'][3]['tasks'][0]['column_id']);
        $this->assertEquals(1, $board[0]['columns'][3]['tasks'][0]['position']);
        $this->assertEquals(0, $board[0]['columns'][3]['tasks'][0]['swimlane_id']);

        $task = $tf->getById(6);
        $this->assertEquals(6, $task['id']);
        $this->assertEquals(4, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);
        $this->assertEquals(6, $board[1]['columns'][3]['tasks'][0]['id']);
        $this->assertEquals(4, $board[1]['columns'][3]['tasks'][0]['column_id']);
        $this->assertEquals(1, $board[1]['columns'][3]['tasks'][0]['position']);
        $this->assertEquals(1, $board[1]['columns'][3]['tasks'][0]['swimlane_id']);
    }

    public function testGetColumn()
    {
        $p = new Project($this->container);
        $b = new Board($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));

        $column = $b->getColumn(3);
        $this->assertNotEmpty($column);
        $this->assertEquals('Work in progress', $column['title']);

        $column = $b->getColumn(33);
        $this->assertEmpty($column);
    }

    public function testRemoveColumn()
    {
        $p = new Project($this->container);
        $b = new Board($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertTrue($b->removeColumn(3));
        $this->assertFalse($b->removeColumn(322));

        $columns = $b->getColumns(1);
        $this->assertTrue(is_array($columns));
        $this->assertEquals(3, count($columns));
    }

    public function testUpdateColumn()
    {
        $p = new Project($this->container);
        $b = new Board($this->container);

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
        $p = new Project($this->container);
        $b = new Board($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertNotFalse($b->addColumn(1, 'another column'));
        $this->assertNotFalse($b->addColumn(1, 'one more', 3));

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
        $p = new Project($this->container);
        $b = new Board($this->container);

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
