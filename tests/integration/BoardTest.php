<?php

require_once __DIR__.'/Base.php';

class BoardTest extends Base
{
    public function testCreateProject()
    {
        $this->assertEquals(1, $this->app->createProject('A project'));
    }

    public function testGetBoard()
    {
        $board = $this->app->getBoard(1);
        $this->assertCount(1, $board);
        $this->assertEquals('Default swimlane', $board[0]['name']);

        $this->assertCount(4, $board[0]['columns']);
        $this->assertEquals('Ready', $board[0]['columns'][1]['title']);
    }
}
