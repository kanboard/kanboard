<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\TaskCreation;
use Model\Subtask;
use Model\Project;
use Model\Category;
use Model\User;

class SubTaskTest extends Base
{
    public function testMoveUp()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));
        $this->assertEquals(2, $s->create(array('title' => 'subtask #2', 'task_id' => 1)));
        $this->assertEquals(3, $s->create(array('title' => 'subtask #3', 'task_id' => 1)));

        // Check positions
        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(1, $subtask['position']);

        $subtask = $s->getById(2);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(2, $subtask['position']);

        $subtask = $s->getById(3);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(3, $subtask['position']);

        // Move up
        $this->assertTrue($s->moveUp(1, 2));

        // Check positions
        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(2, $subtask['position']);

        $subtask = $s->getById(2);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(1, $subtask['position']);

        $subtask = $s->getById(3);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(3, $subtask['position']);

        // We can't move up #2
        $this->assertFalse($s->moveUp(1, 2));

        // Test remove
        $this->assertTrue($s->remove(1));
        $this->assertTrue($s->moveUp(1, 3));

        // Check positions
        $subtask = $s->getById(1);
        $this->assertEmpty($subtask);

        $subtask = $s->getById(2);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(2, $subtask['position']);

        $subtask = $s->getById(3);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(1, $subtask['position']);
    }

    public function testMoveDown()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));
        $this->assertEquals(2, $s->create(array('title' => 'subtask #2', 'task_id' => 1)));
        $this->assertEquals(3, $s->create(array('title' => 'subtask #3', 'task_id' => 1)));

        // Move down #1
        $this->assertTrue($s->moveDown(1, 1));

        // Check positions
        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(2, $subtask['position']);

        $subtask = $s->getById(2);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(1, $subtask['position']);

        $subtask = $s->getById(3);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(3, $subtask['position']);

        // We can't move down #3
        $this->assertFalse($s->moveDown(1, 3));

        // Test remove
        $this->assertTrue($s->remove(1));
        $this->assertTrue($s->moveDown(1, 2));

        // Check positions
        $subtask = $s->getById(1);
        $this->assertEmpty($subtask);

        $subtask = $s->getById(2);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(2, $subtask['position']);

        $subtask = $s->getById(3);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(1, $subtask['position']);
    }

    public function testDuplicate()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $p = new Project($this->container);

        // We create a project
        $this->assertEquals(1, $p->create(array('name' => 'test1')));

        // We create 2 tasks
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'test 2', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 0)));

        // We create many subtasks for the first task
        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1, 'time_estimated' => 5, 'time_spent' => 3, 'status' => 1, 'another_subtask' => 'on')));
        $this->assertEquals(2, $s->create(array('title' => 'subtask #2', 'task_id' => 1, 'time_estimated' => '', 'time_spent' => '', 'status' => 2, 'user_id' => 1)));

        // We duplicate our subtasks
        $this->assertTrue($s->duplicate(1, 2));
        $subtasks = $s->getAll(2);

        $this->assertNotFalse($subtasks);
        $this->assertNotEmpty($subtasks);
        $this->assertEquals(2, count($subtasks));

        $this->assertEquals('subtask #1', $subtasks[0]['title']);
        $this->assertEquals('subtask #2', $subtasks[1]['title']);

        $this->assertEquals(2, $subtasks[0]['task_id']);
        $this->assertEquals(2, $subtasks[1]['task_id']);

        $this->assertEquals(5, $subtasks[0]['time_estimated']);
        $this->assertEquals(0, $subtasks[1]['time_estimated']);

        $this->assertEquals(0, $subtasks[0]['time_spent']);
        $this->assertEquals(0, $subtasks[1]['time_spent']);

        $this->assertEquals(0, $subtasks[0]['status']);
        $this->assertEquals(0, $subtasks[1]['status']);

        $this->assertEquals(0, $subtasks[0]['user_id']);
        $this->assertEquals(0, $subtasks[1]['user_id']);

        $this->assertEquals(1, $subtasks[0]['position']);
        $this->assertEquals(2, $subtasks[1]['position']);
    }
}
