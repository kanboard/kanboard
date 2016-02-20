<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Project;
use Kanboard\Model\Column;

class ColumnTest extends Base
{
    public function testChangePosition()
    {
        $projectModel = new Project($this->container);
        $columnModel = new Column($this->container);

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
