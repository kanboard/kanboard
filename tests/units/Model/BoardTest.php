<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Project;
use Kanboard\Model\Board;
use Kanboard\Model\Column;
use Kanboard\Model\Config;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Swimlane;

class BoardTest extends Base
{
    public function testCreation()
    {
        $p = new Project($this->container);
        $b = new Board($this->container);
        $columnModel = new Column($this->container);
        $c = new Config($this->container);

        // Default columns

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $columns = $columnModel->getList(1);

        $this->assertTrue(is_array($columns));
        $this->assertEquals(4, count($columns));
        $this->assertEquals('Backlog', $columns[1]);
        $this->assertEquals('Ready', $columns[2]);
        $this->assertEquals('Work in progress', $columns[3]);
        $this->assertEquals('Done', $columns[4]);

        // Custom columns: spaces should be trimed and no empty columns
        $input = '   column #1  , column #2, ';

        $this->assertTrue($c->save(array('board_columns' => $input)));
        $this->container['memoryCache']->flush();
        $this->assertEquals($input, $c->get('board_columns'));

        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));
        $columns = $columnModel->getList(2);

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
        $this->assertEquals(6, count($board[0]));
        $this->assertArrayHasKey('name', $board[0]);
        $this->assertArrayHasKey('nb_tasks', $board[0]);
        $this->assertArrayHasKey('columns', $board[0]);
        $this->assertArrayHasKey('tasks', $board[0]['columns'][2]);
        $this->assertArrayHasKey('nb_tasks', $board[0]['columns'][2]);
        $this->assertArrayHasKey('title', $board[0]['columns'][2]);
        $this->assertArrayHasKey('nb_column_tasks', $board[0]['columns'][0]);
        $this->assertArrayHasKey('total_score', $board[0]['columns'][0]);
    }

    public function testGetBoardWithSwimlane()
    {
        $b = new Board($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $s->create(array('project_id' => 1, 'name' => 'test 1')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 3)));
        $this->assertEquals(3, $tc->create(array('title' => 'Task #3', 'project_id' => 1, 'column_id' => 2, 'swimlane_id' => 1)));
        $this->assertEquals(4, $tc->create(array('title' => 'Task #4', 'project_id' => 1, 'column_id' => 3)));
        $this->assertEquals(5, $tc->create(array('title' => 'Task #5', 'project_id' => 1, 'column_id' => 4, 'score' => 2)));
        $this->assertEquals(6, $tc->create(array('title' => 'Task #6', 'project_id' => 1, 'column_id' => 4, 'score' => 3, 'swimlane_id' => 1)));

        $board = $b->getBoard(1);
        $this->assertNotEmpty($board);
        $this->assertEquals(2, count($board));
        $this->assertEquals(6, count($board[0]));
        $this->assertArrayHasKey('name', $board[0]);
        $this->assertArrayHasKey('nb_tasks', $board[0]);
        $this->assertArrayHasKey('columns', $board[0]);
        $this->assertArrayHasKey('tasks', $board[0]['columns'][2]);
        $this->assertArrayHasKey('nb_tasks', $board[0]['columns'][2]);
        $this->assertArrayHasKey('title', $board[0]['columns'][2]);
        $this->assertArrayHasKey('nb_column_tasks', $board[0]['columns'][0]);
        $this->assertArrayNotHasKey('nb_column_tasks', $board[1]['columns'][0]);
        $this->assertArrayNotHasKey('total_score', $board[1]['columns'][0]);
        $this->assertArrayHasKey('score', $board[0]['columns'][3]);
        $this->assertArrayHasKey('total_score', $board[0]['columns'][3]);
        $this->assertEquals(2, $board[0]['columns'][3]['score']);
        $this->assertEquals(5, $board[0]['columns'][3]['total_score']);

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
}
