<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\ProjectModel;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\ConfigModel;

class BoardTest extends Base
{
    public function testCreation()
    {
        $p = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);
        $c = new ConfigModel($this->container);

        // Default columns

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $columns = $columnModel->getList(1);

        $this->assertTrue(is_array($columns));
        $this->assertEquals(4, count($columns));
        $this->assertEquals('Backlog', $columns[1]);
        $this->assertEquals('Ready', $columns[2]);
        $this->assertEquals('Work in progress', $columns[3]);
        $this->assertEquals('Done', $columns[4]);

        // Custom columns: spaces should be trimed, no empty columns and no duplicates
        $input = '   column #1  , column #2,column #1 ,column #3  , ';

        $this->assertTrue($c->save(array('board_columns' => $input)));
        $this->container['memoryCache']->flush();
        $this->assertEquals($input, $c->get('board_columns'));

        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));
        $columns = $columnModel->getList(2);

        $this->assertTrue(is_array($columns));
        $this->assertEquals(3, count($columns));
        $this->assertEquals('column #1', $columns[5]);
        $this->assertEquals('column #2', $columns[6]);
        $this->assertEquals('column #3', $columns[7]);
    }
}
