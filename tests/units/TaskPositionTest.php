<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\TaskPosition;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\Project;
use Model\Swimlane;

class TaskPositionTest extends Base
{
    public function testCalculatePositionBadPosition()
    {
        $tp = new TaskPosition($this->container);
        $tc = new TaskCreation($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1)));

        $this->assertFalse($tp->calculatePositions(1, 1, 2, 0));
    }

    public function testCalculatePositionBadColumn()
    {
        $tp = new TaskPosition($this->container);
        $tc = new TaskCreation($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1)));

        $this->assertFalse($tp->calculatePositions(1, 1, 10, 1));
    }

    public function testCalculatePositions()
    {
        $tp = new TaskPosition($this->container);
        $tc = new TaskCreation($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1)));

        $positions = $tp->calculatePositions(1, 1, 2, 1);
        $this->assertNotFalse($positions);
        $this->assertNotEmpty($positions);
        $this->assertEmpty($positions[1]);
        $this->assertEmpty($positions[3]);
        $this->assertEmpty($positions[4]);
        $this->assertEquals(array(1), $positions[2]);
    }

    public function testMoveTaskWithColumnThatNotChange()
    {
        $tp = new TaskPosition($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));

        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(3, $tc->create(array('title' => 'Task #3', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(4, $tc->create(array('title' => 'Task #4', 'project_id' => 1, 'column_id' => 2)));
        $this->assertEquals(5, $tc->create(array('title' => 'Task #5', 'project_id' => 1, 'column_id' => 2)));
        $this->assertEquals(6, $tc->create(array('title' => 'Task #6', 'project_id' => 1, 'column_id' => 2)));
        $this->assertEquals(7, $tc->create(array('title' => 'Task #7', 'project_id' => 1, 'column_id' => 3)));
        $this->assertEquals(8, $tc->create(array('title' => 'Task #8', 'project_id' => 1, 'column_id' => 1)));

        // We move the task 3 to the column 3
        $this->assertTrue($tp->movePosition(1, 3, 3, 2));

        // Check tasks position
        $task = $tf->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $task = $tf->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $task = $tf->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $task = $tf->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $task = $tf->getById(5);
        $this->assertEquals(5, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $task = $tf->getById(6);
        $this->assertEquals(6, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(3, $task['position']);

        $task = $tf->getById(7);
        $this->assertEquals(7, $task['id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $task = $tf->getById(8);
        $this->assertEquals(8, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(3, $task['position']);
    }

    public function testMoveTaskWithBadPreviousPosition()
    {
        $tp = new TaskPosition($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $this->container['db']->table('tasks')->insert(array('title' => 'A', 'column_id' => 1, 'project_id' => 1, 'position' => 1)));

        // Both tasks have the same position
        $this->assertEquals(2, $this->container['db']->table('tasks')->insert(array('title' => 'B', 'column_id' => 2, 'project_id' => 1, 'position' => 1)));
        $this->assertEquals(3, $this->container['db']->table('tasks')->insert(array('title' => 'C', 'column_id' => 2, 'project_id' => 1, 'position' => 1)));

        // Move the first column to the last position of the 2nd column
        $this->assertTrue($tp->movePosition(1, 1, 2, 3));

        // Check tasks position
        $task = $tf->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $task = $tf->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $task = $tf->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(3, $task['position']);
    }

    public function testMoveTaskTop()
    {
        $tp = new TaskPosition($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(3, $tc->create(array('title' => 'Task #3', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(4, $tc->create(array('title' => 'Task #4', 'project_id' => 1, 'column_id' => 1)));

        // Move the last task to the top
        $this->assertTrue($tp->movePosition(1, 4, 1, 1));

        // Check tasks position
        $task = $tf->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $task = $tf->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(3, $task['position']);

        $task = $tf->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(4, $task['position']);

        $task = $tf->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);
    }

    public function testMoveTaskBottom()
    {
        $tp = new TaskPosition($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(3, $tc->create(array('title' => 'Task #3', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(4, $tc->create(array('title' => 'Task #4', 'project_id' => 1, 'column_id' => 1)));

        // Move the last task to the bottom
        $this->assertTrue($tp->movePosition(1, 1, 1, 4));

        // Check tasks position
        $task = $tf->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(4, $task['position']);

        $task = $tf->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $task = $tf->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $task = $tf->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(3, $task['position']);
    }

    public function testMovePosition()
    {
        $tp = new TaskPosition($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $counter = 1;
        $task_per_column = 5;

        foreach (array(1, 2, 3, 4) as $column_id) {

            for ($i = 1; $i <= $task_per_column; $i++, $counter++) {

                $task = array(
                    'title' => 'Task #'.$i.'-'.$column_id,
                    'project_id' => 1,
                    'column_id' => $column_id,
                    'owner_id' => 0,
                );

                $this->assertEquals($counter, $tc->create($task));

                $task = $tf->getById($counter);
                $this->assertNotFalse($task);
                $this->assertNotEmpty($task);
                $this->assertEquals($i, $task['position']);
            }
        }

        // We move task id #4, column 1, position 4 to the column 2, position 3
        $this->assertTrue($tp->movePosition(1, 4, 2, 3));

        // We check the new position of the task
        $task = $tf->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(3, $task['position']);

        // The tasks before have the correct position
        $task = $tf->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(3, $task['position']);

        $task = $tf->getById(7);
        $this->assertEquals(7, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        // The tasks after have the correct position
        $task = $tf->getById(5);
        $this->assertEquals(5, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(4, $task['position']);

        $task = $tf->getById(8);
        $this->assertEquals(8, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(4, $task['position']);

        // The number of tasks per column
        $this->assertEquals($task_per_column - 1, $tf->countByColumnId(1, 1));
        $this->assertEquals($task_per_column + 1, $tf->countByColumnId(1, 2));
        $this->assertEquals($task_per_column, $tf->countByColumnId(1, 3));
        $this->assertEquals($task_per_column, $tf->countByColumnId(1, 4));

        // We move task id #1, column 1, position 1 to the column 4, position 6 (last position)
        $this->assertTrue($tp->movePosition(1, 1, 4, $task_per_column + 1));

        // We check the new position of the task
        $task = $tf->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(4, $task['column_id']);
        $this->assertEquals($task_per_column + 1, $task['position']);

        // The tasks before have the correct position
        $task = $tf->getById(20);
        $this->assertEquals(20, $task['id']);
        $this->assertEquals(4, $task['column_id']);
        $this->assertEquals($task_per_column, $task['position']);

        // The tasks after have the correct position
        $task = $tf->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        // The number of tasks per column
        $this->assertEquals($task_per_column - 2, $tf->countByColumnId(1, 1));
        $this->assertEquals($task_per_column + 1, $tf->countByColumnId(1, 2));
        $this->assertEquals($task_per_column, $tf->countByColumnId(1, 3));
        $this->assertEquals($task_per_column + 1, $tf->countByColumnId(1, 4));

        // Our previous moved task should stay at the same place
        $task = $tf->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(3, $task['position']);

        // Test wrong position number
        $this->assertFalse($tp->movePosition(1, 2, 3, 0));
        $this->assertFalse($tp->movePosition(1, 2, 3, -2));

        // Wrong column
        $this->assertFalse($tp->movePosition(1, 2, 22, 2));

        // Test position greater than the last position
        $this->assertTrue($tp->movePosition(1, 11, 1, 22));

        $task = $tf->getById(11);
        $this->assertEquals(11, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals($tf->countByColumnId(1, 1), $task['position']);

        $task = $tf->getById(5);
        $this->assertEquals(5, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals($tf->countByColumnId(1, 1) - 1, $task['position']);

        $task = $tf->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(3, $task['position']);

        $this->assertEquals($task_per_column - 1, $tf->countByColumnId(1, 1));
        $this->assertEquals($task_per_column + 1, $tf->countByColumnId(1, 2));
        $this->assertEquals($task_per_column - 1, $tf->countByColumnId(1, 3));
        $this->assertEquals($task_per_column + 1, $tf->countByColumnId(1, 4));

        // Our previous moved task should stay at the same place
        $task = $tf->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(3, $task['position']);

        // Test moving task to position 1
        $this->assertTrue($tp->movePosition(1, 14, 1, 1));

        $task = $tf->getById(14);
        $this->assertEquals(14, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $task = $tf->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $this->assertEquals($task_per_column, $tf->countByColumnId(1, 1));
        $this->assertEquals($task_per_column + 1, $tf->countByColumnId(1, 2));
        $this->assertEquals($task_per_column - 2, $tf->countByColumnId(1, 3));
        $this->assertEquals($task_per_column + 1, $tf->countByColumnId(1, 4));
    }

    public function testMoveTaskSwimlane()
    {
        $tp = new TaskPosition($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $s->create(1, 'test 1'));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(3, $tc->create(array('title' => 'Task #3', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(4, $tc->create(array('title' => 'Task #4', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(5, $tc->create(array('title' => 'Task #5', 'project_id' => 1, 'column_id' => 1)));

        // Move the task to the swimlane
        $this->assertTrue($tp->movePosition(1, 1, 2, 1, 1));

        // Check tasks position
        $task = $tf->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);

        $task = $tf->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(0, $task['swimlane_id']);

        $task = $tf->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals(0, $task['swimlane_id']);

        $task = $tf->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(3, $task['position']);
        $this->assertEquals(0, $task['swimlane_id']);

        // Move the task to the swimlane
        $this->assertTrue($tp->movePosition(1, 2, 2, 1, 1));

        // Check tasks position
        $task = $tf->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);

        $task = $tf->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);

        $task = $tf->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(0, $task['swimlane_id']);

        $task = $tf->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals(0, $task['swimlane_id']);

        // Move the task 5 to the last column
        $this->assertTrue($tp->movePosition(1, 5, 4, 1, 0));

        // Check tasks position
        $task = $tf->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);

        $task = $tf->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);

        $task = $tf->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(0, $task['swimlane_id']);

        $task = $tf->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals(0, $task['swimlane_id']);

        $task = $tf->getById(5);
        $this->assertEquals(5, $task['id']);
        $this->assertEquals(4, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(0, $task['swimlane_id']);
    }

    public function testEvents()
    {
        $tp = new TaskPosition($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $s->create(1, 'test 1'));

        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 2)));

        $this->container['dispatcher']->addListener(Task::EVENT_MOVE_COLUMN, array($this, 'onMoveColumn'));
        $this->container['dispatcher']->addListener(Task::EVENT_MOVE_POSITION, array($this, 'onMovePosition'));
        $this->container['dispatcher']->addListener(Task::EVENT_MOVE_SWIMLANE, array($this, 'onMoveSwimlane'));

        // We move the task 1 to the column 2
        $this->assertTrue($tp->movePosition(1, 1, 2, 1));

        $task = $tf->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $task = $tf->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(Task::EVENT_MOVE_COLUMN.'.TaskPositionTest::onMoveColumn', $called);
        $this->assertEquals(1, count($called));

        // We move the task 1 to the position 2
        $this->assertTrue($tp->movePosition(1, 1, 2, 2));

        $task = $tf->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $task = $tf->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(Task::EVENT_MOVE_POSITION.'.TaskPositionTest::onMovePosition', $called);
        $this->assertEquals(2, count($called));

        // Move to another swimlane
        $this->assertTrue($tp->movePosition(1, 1, 3, 1, 1));

        $task = $tf->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);

        $task = $tf->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(0, $task['swimlane_id']);

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(Task::EVENT_MOVE_SWIMLANE.'.TaskPositionTest::onMoveSwimlane', $called);
        $this->assertEquals(3, count($called));
    }

    public function onMoveColumn($event)
    {
        $this->assertInstanceOf('Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals(1, $event_data['position']);
        $this->assertEquals(2, $event_data['column_id']);
        $this->assertEquals(1, $event_data['project_id']);
    }

    public function onMovePosition($event)
    {
        $this->assertInstanceOf('Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals(2, $event_data['position']);
        $this->assertEquals(2, $event_data['column_id']);
        $this->assertEquals(1, $event_data['project_id']);
    }

    public function onMoveSwimlane($event)
    {
        $this->assertInstanceOf('Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals(1, $event_data['position']);
        $this->assertEquals(3, $event_data['column_id']);
        $this->assertEquals(1, $event_data['project_id']);
        $this->assertEquals(1, $event_data['swimlane_id']);
    }
}
