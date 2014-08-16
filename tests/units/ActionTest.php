<?php

require_once __DIR__.'/Base.php';

use Model\Action;
use Model\Project;
use Model\Board;
use Model\Task;
use Model\Category;

class ActionTest extends Base
{
    public function testFetchActions()
    {
        $action = new Action($this->registry);
        $board = new Board($this->registry);
        $project = new Project($this->registry);

        $this->assertEquals(1, $project->create(array('name' => 'unit_test')));

        // We should have nothing
        $this->assertEmpty($action->getAll());
        $this->assertEmpty($action->getAllByProject(1));

        // We create a new action
        $this->assertTrue($action->create(array(
            'project_id' => 1,
            'event_name' => Task::EVENT_MOVE_COLUMN,
            'action_name' => 'TaskClose',
            'params' => array(
                'column_id' => 4,
            )
        )));

        // We should have our action
        $this->assertNotEmpty($action->getAll());
        $this->assertEquals($action->getAll(), $action->getAllByProject(1));

        $actions = $action->getAll();

        $this->assertEquals(1, count($actions));
        $this->assertEquals(1, $actions[0]['project_id']);
        $this->assertEquals(Task::EVENT_MOVE_COLUMN, $actions[0]['event_name']);
        $this->assertEquals('TaskClose', $actions[0]['action_name']);
        $this->assertEquals('column_id', $actions[0]['params'][0]['name']);
        $this->assertEquals(4, $actions[0]['params'][0]['value']);
    }

    public function testEventMoveColumn()
    {
        $task = new Task($this->registry);
        $board = new Board($this->registry);
        $project = new Project($this->registry);
        $action = new Action($this->registry);

        // We create a project
        $this->assertEquals(1, $project->create(array('name' => 'unit_test')));

        // We create a task
        $this->assertEquals(1, $task->create(array(
            'title' => 'unit_test',
            'project_id' => 1,
            'owner_id' => 1,
            'color_id' => 'red',
            'column_id' => 1,
        )));

        // We create a new action
        $this->assertTrue($action->create(array(
            'project_id' => 1,
            'event_name' => Task::EVENT_MOVE_COLUMN,
            'action_name' => 'TaskClose',
            'params' => array(
                'column_id' => 4,
            )
        )));

        // We bind events
        $action->attachEvents();

        // Our task should be open
        $t1 = $task->getById(1);
        $this->assertEquals(1, $t1['is_active']);
        $this->assertEquals(1, $t1['column_id']);

        // We move our task
        $task->move(1, 4, 1);

        $this->assertTrue($this->registry->event->isEventTriggered(Task::EVENT_MOVE_COLUMN));
        $this->assertTrue($this->registry->event->isEventTriggered(Task::EVENT_UPDATE));

        // Our task should be closed
        $t1 = $task->getById(1);
        $this->assertEquals(4, $t1['column_id']);
        $this->assertEquals(0, $t1['is_active']);
    }

    public function testEventMovePosition()
    {
        $task = new Task($this->registry);
        $board = new Board($this->registry);
        $project = new Project($this->registry);
        $action = new Action($this->registry);

        // We create a project
        $this->assertEquals(1, $project->create(array('name' => 'unit_test')));

        // We create a task
        $this->assertEquals(1, $task->create(array(
            'title' => 'unit_test 0',
            'project_id' => 1,
            'owner_id' => 1,
            'color_id' => 'red',
            'column_id' => 1,
            'category_id' => 1,
        )));

        $this->assertEquals(2, $task->create(array(
            'title' => 'unit_test 1',
            'project_id' => 1,
            'owner_id' => 1,
            'color_id' => 'yellow',
            'column_id' => 1,
            'category_id' => 1,
        )));

        // We create a new action, when the category_id=2 then the color_id should be green
        $this->assertTrue($action->create(array(
            'project_id' => 1,
            'event_name' => Task::EVENT_MOVE_POSITION,
            'action_name' => 'TaskAssignColorCategory',
            'params' => array(
                'category_id' => 1,
                'color_id' => 'green',
            )
        )));

        // We bind events
        $action->attachEvents();

        $this->assertTrue($this->registry->event->hasListener(Task::EVENT_MOVE_POSITION, 'Action\TaskAssignColorCategory'));

        // Our task should have the color red and position=0
        $t1 = $task->getById(1);
        $this->assertEquals(0, $t1['position']);
        $this->assertEquals(1, $t1['is_active']);
        $this->assertEquals('red', $t1['color_id']);

        $t1 = $task->getById(2);
        $this->assertEquals(1, $t1['position']);
        $this->assertEquals(1, $t1['is_active']);
        $this->assertEquals('yellow', $t1['color_id']);

        // We move our tasks
        $task->move(1, 1, 1); // task #1 to position 1
        $task->move(2, 1, 0); // task #2 to position 0

        $this->assertTrue($this->registry->event->isEventTriggered(Task::EVENT_MOVE_POSITION));

        // Both tasks should be green
        $t1 = $task->getById(1);
        $this->assertEquals(1, $t1['position']);
        $this->assertEquals(1, $t1['is_active']);
        $this->assertEquals('green', $t1['color_id']);

        $t1 = $task->getById(2);
        $this->assertEquals(0, $t1['position']);
        $this->assertEquals(1, $t1['is_active']);
        $this->assertEquals('green', $t1['color_id']);
    }

    public function testExecuteMultipleActions()
    {
        $task = new Task($this->registry);
        $board = new Board($this->registry);
        $project = new Project($this->registry);
        $action = new Action($this->registry);

        // We create 2 projects
        $this->assertEquals(1, $project->create(array('name' => 'unit_test1')));
        $this->assertEquals(2, $project->create(array('name' => 'unit_test2')));

        // We create a task
        $this->assertEquals(1, $task->create(array(
            'title' => 'unit_test',
            'project_id' => 1,
            'owner_id' => 1,
            'color_id' => 'red',
            'column_id' => 1,
        )));

        // We create 2 actions
        $this->assertTrue($action->create(array(
            'project_id' => 1,
            'event_name' => Task::EVENT_CLOSE,
            'action_name' => 'TaskDuplicateAnotherProject',
            'params' => array(
                'column_id' => 4,
                'project_id' => 2,
            )
        )));

        $this->assertTrue($action->create(array(
            'project_id' => 1,
            'event_name' => Task::EVENT_MOVE_COLUMN,
            'action_name' => 'TaskClose',
            'params' => array(
                'column_id' => 4,
            )
        )));

        // We bind events
        $action->attachEvents();

        // Events should be attached
        $this->assertTrue($this->registry->event->hasListener(Task::EVENT_CLOSE, 'Action\TaskDuplicateAnotherProject'));
        $this->assertTrue($this->registry->event->hasListener(Task::EVENT_MOVE_COLUMN, 'Action\TaskClose'));

        // Our task should be open, linked to the first project and in the first column
        $t1 = $task->getById(1);
        $this->assertEquals(1, $t1['is_active']);
        $this->assertEquals(1, $t1['column_id']);
        $this->assertEquals(1, $t1['project_id']);

        // We move our task
        $task->move(1, 4, 1);

        $this->assertTrue($this->registry->event->isEventTriggered(Task::EVENT_CLOSE));
        $this->assertTrue($this->registry->event->isEventTriggered(Task::EVENT_MOVE_COLUMN));

        // Our task should be closed
        $t1 = $task->getById(1);
        $this->assertEquals(4, $t1['column_id']);
        $this->assertEquals(0, $t1['is_active']);

        // Our task should be duplicated to the 2nd project
        $t2 = $task->getById(2);
        $this->assertNotEmpty($t2);
        $this->assertNotEquals(4, $t2['column_id']);
        $this->assertEquals(1, $t2['is_active']);
        $this->assertEquals(2, $t2['project_id']);
        $this->assertEquals('unit_test', $t2['title']);
    }
}
