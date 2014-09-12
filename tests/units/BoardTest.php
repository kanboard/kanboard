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

        $this->assertTrue($c->save(array('default_columns' => '   column #1  , column #2, ')));

        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));
        $columns = $b->getColumnsList(2);

        $this->assertTrue(is_array($columns));
        $this->assertEquals(2, count($columns));
        $this->assertEquals('column #1', $columns[5]);
        $this->assertEquals('column #2', $columns[6]);
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
