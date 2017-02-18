<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskModel;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\TaskStatusModel;
use Kanboard\Model\TaskPositionModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\SwimlaneModel;

class TaskPositionModelTest extends Base
{
    public function testGetTaskProgression()
    {
        $taskModel = new TaskModel($this->container);
        $taskStatusModel = new TaskStatusModel($this->container);
        $taskPositionModel = new TaskPositionModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(0, $taskModel->getProgress($taskFinderModel->getById(1), $columnModel->getList(1)));

        $this->assertTrue($taskPositionModel->movePosition(1, 1, 2, 1));
        $this->assertEquals(25, $taskModel->getProgress($taskFinderModel->getById(1), $columnModel->getList(1)));

        $this->assertTrue($taskPositionModel->movePosition(1, 1, 3, 1));
        $this->assertEquals(50, $taskModel->getProgress($taskFinderModel->getById(1), $columnModel->getList(1)));

        $this->assertTrue($taskPositionModel->movePosition(1, 1, 4, 1));
        $this->assertEquals(75, $taskModel->getProgress($taskFinderModel->getById(1), $columnModel->getList(1)));

        $this->assertTrue($taskStatusModel->close(1));
        $this->assertEquals(100, $taskModel->getProgress($taskFinderModel->getById(1), $columnModel->getList(1)));
    }

    public function testMoveTaskToWrongPosition()
    {
        $taskPositionModel = new TaskPositionModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));

        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 1)));

        // We move the task 2 to the position 0
        $this->assertFalse($taskPositionModel->movePosition(1, 1, 3, 0));

        // Check tasks position
        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $task = $taskFinderModel->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);
    }

    public function testMoveTaskToGreaterPosition()
    {
        $taskPositionModel = new TaskPositionModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));

        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 1)));

        // We move the task 2 to the position 42
        $this->assertTrue($taskPositionModel->movePosition(1, 1, 1, 42));

        // Check tasks position
        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $task = $taskFinderModel->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);
    }

    public function testMoveTaskToEmptyColumn()
    {
        $taskPositionModel = new TaskPositionModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));

        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 1)));

        // We move the task 1 to the column 3
        $this->assertTrue($taskPositionModel->movePosition(1, 1, 3, 1));

        // Check tasks position
        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $task = $taskFinderModel->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);
    }

    public function testMoveTaskToAnotherColumn()
    {
        $taskPositionModel = new TaskPositionModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));

        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(3, $taskCreationModel->create(array('title' => 'Task #3', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(4, $taskCreationModel->create(array('title' => 'Task #4', 'project_id' => 1, 'column_id' => 2)));
        $this->assertEquals(5, $taskCreationModel->create(array('title' => 'Task #5', 'project_id' => 1, 'column_id' => 2)));
        $this->assertEquals(6, $taskCreationModel->create(array('title' => 'Task #6', 'project_id' => 1, 'column_id' => 2)));
        $this->assertEquals(7, $taskCreationModel->create(array('title' => 'Task #7', 'project_id' => 1, 'column_id' => 3)));
        $this->assertEquals(8, $taskCreationModel->create(array('title' => 'Task #8', 'project_id' => 1, 'column_id' => 1)));

        // We move the task 3 to the column 3
        $this->assertTrue($taskPositionModel->movePosition(1, 3, 3, 2));

        // Check tasks position
        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $task = $taskFinderModel->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $task = $taskFinderModel->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $task = $taskFinderModel->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $task = $taskFinderModel->getById(5);
        $this->assertEquals(5, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $task = $taskFinderModel->getById(6);
        $this->assertEquals(6, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(3, $task['position']);

        $task = $taskFinderModel->getById(7);
        $this->assertEquals(7, $task['id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $task = $taskFinderModel->getById(8);
        $this->assertEquals(8, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(3, $task['position']);
    }

    public function testMoveTaskTop()
    {
        $taskPositionModel = new TaskPositionModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(3, $taskCreationModel->create(array('title' => 'Task #3', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(4, $taskCreationModel->create(array('title' => 'Task #4', 'project_id' => 1, 'column_id' => 1)));

        // Move the last task to the top
        $this->assertTrue($taskPositionModel->movePosition(1, 4, 1, 1));

        // Check tasks position
        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $task = $taskFinderModel->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(3, $task['position']);

        $task = $taskFinderModel->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(4, $task['position']);

        $task = $taskFinderModel->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);
    }

    public function testMoveTaskBottom()
    {
        $taskPositionModel = new TaskPositionModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(3, $taskCreationModel->create(array('title' => 'Task #3', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(4, $taskCreationModel->create(array('title' => 'Task #4', 'project_id' => 1, 'column_id' => 1)));

        // Move the first task to the bottom
        $this->assertTrue($taskPositionModel->movePosition(1, 1, 1, 4));

        // Check tasks position
        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(4, $task['position']);

        $task = $taskFinderModel->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $task = $taskFinderModel->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $task = $taskFinderModel->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(3, $task['position']);
    }

    public function testMovePosition()
    {
        $taskPositionModel = new TaskPositionModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
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

                $this->assertEquals($counter, $taskCreationModel->create($task));

                $task = $taskFinderModel->getById($counter);
                $this->assertNotEmpty($task);
                $this->assertEquals($i, $task['position']);
            }
        }

        // We move task id #4, column 1, position 4 to the column 2, position 3
        $this->assertTrue($taskPositionModel->movePosition(1, 4, 2, 3));

        // We check the new position of the task
        $task = $taskFinderModel->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(3, $task['position']);

        // The tasks before have the correct position
        $task = $taskFinderModel->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(3, $task['position']);

        $task = $taskFinderModel->getById(7);
        $this->assertEquals(7, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        // The tasks after have the correct position
        $task = $taskFinderModel->getById(5);
        $this->assertEquals(5, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(4, $task['position']);

        $task = $taskFinderModel->getById(8);
        $this->assertEquals(8, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(4, $task['position']);

        // The number of tasks per column
        $this->assertEquals($task_per_column - 1, $taskFinderModel->countByColumnId(1, 1));
        $this->assertEquals($task_per_column + 1, $taskFinderModel->countByColumnId(1, 2));
        $this->assertEquals($task_per_column, $taskFinderModel->countByColumnId(1, 3));
        $this->assertEquals($task_per_column, $taskFinderModel->countByColumnId(1, 4));

        // We move task id #1, column 1, position 1 to the column 4, position 6 (last position)
        $this->assertTrue($taskPositionModel->movePosition(1, 1, 4, $task_per_column + 1));

        // We check the new position of the task
        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(4, $task['column_id']);
        $this->assertEquals($task_per_column + 1, $task['position']);

        // The tasks before have the correct position
        $task = $taskFinderModel->getById(20);
        $this->assertEquals(20, $task['id']);
        $this->assertEquals(4, $task['column_id']);
        $this->assertEquals($task_per_column, $task['position']);

        // The tasks after have the correct position
        $task = $taskFinderModel->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        // The number of tasks per column
        $this->assertEquals($task_per_column - 2, $taskFinderModel->countByColumnId(1, 1));
        $this->assertEquals($task_per_column + 1, $taskFinderModel->countByColumnId(1, 2));
        $this->assertEquals($task_per_column, $taskFinderModel->countByColumnId(1, 3));
        $this->assertEquals($task_per_column + 1, $taskFinderModel->countByColumnId(1, 4));

        // Our previous moved task should stay at the same place
        $task = $taskFinderModel->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(3, $task['position']);

        // Test wrong position number
        $this->assertFalse($taskPositionModel->movePosition(1, 2, 3, 0));
        $this->assertFalse($taskPositionModel->movePosition(1, 2, 3, -2));

        // Wrong column
        $this->assertFalse($taskPositionModel->movePosition(1, 2, 22, 2));

        // Test position greater than the last position
        $this->assertTrue($taskPositionModel->movePosition(1, 11, 1, 22));

        $task = $taskFinderModel->getById(11);
        $this->assertEquals(11, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals($taskFinderModel->countByColumnId(1, 1), $task['position']);

        $task = $taskFinderModel->getById(5);
        $this->assertEquals(5, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals($taskFinderModel->countByColumnId(1, 1) - 1, $task['position']);

        $task = $taskFinderModel->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(3, $task['position']);

        $this->assertEquals($task_per_column - 1, $taskFinderModel->countByColumnId(1, 1));
        $this->assertEquals($task_per_column + 1, $taskFinderModel->countByColumnId(1, 2));
        $this->assertEquals($task_per_column - 1, $taskFinderModel->countByColumnId(1, 3));
        $this->assertEquals($task_per_column + 1, $taskFinderModel->countByColumnId(1, 4));

        // Our previous moved task should stay at the same place
        $task = $taskFinderModel->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(3, $task['position']);

        // Test moving task to position 1
        $this->assertTrue($taskPositionModel->movePosition(1, 14, 1, 1));

        $task = $taskFinderModel->getById(14);
        $this->assertEquals(14, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $task = $taskFinderModel->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $this->assertEquals($task_per_column, $taskFinderModel->countByColumnId(1, 1));
        $this->assertEquals($task_per_column + 1, $taskFinderModel->countByColumnId(1, 2));
        $this->assertEquals($task_per_column - 2, $taskFinderModel->countByColumnId(1, 3));
        $this->assertEquals($task_per_column + 1, $taskFinderModel->countByColumnId(1, 4));
    }

    public function testMoveTaskSwimlane()
    {
        $taskPositionModel = new TaskPositionModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(2, $swimlaneModel->create(1, 'Swimlane #1'));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(3, $taskCreationModel->create(array('title' => 'Task #3', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(4, $taskCreationModel->create(array('title' => 'Task #4', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(5, $taskCreationModel->create(array('title' => 'Task #5', 'project_id' => 1, 'column_id' => 1)));

        // Move the task to the swimlane
        $this->assertTrue($taskPositionModel->movePosition(1, 1, 2, 1, 2));

        // Check tasks position
        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['swimlane_id']);

        $task = $taskFinderModel->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);

        $task = $taskFinderModel->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);

        $task = $taskFinderModel->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(3, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);

        // Move the task to the swimlane
        $this->assertTrue($taskPositionModel->movePosition(1, 2, 2, 1, 2));

        // Check tasks position
        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals(2, $task['swimlane_id']);

        $task = $taskFinderModel->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['swimlane_id']);

        $task = $taskFinderModel->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);

        $task = $taskFinderModel->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);

        // Move the task 5 to the last column
        $this->assertTrue($taskPositionModel->movePosition(1, 5, 4, 1, 0));

        // Check tasks position
        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals(2, $task['swimlane_id']);

        $task = $taskFinderModel->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['swimlane_id']);

        $task = $taskFinderModel->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);

        $task = $taskFinderModel->getById(4);
        $this->assertEquals(4, $task['id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);

        $task = $taskFinderModel->getById(5);
        $this->assertEquals(5, $task['id']);
        $this->assertEquals(4, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);
    }

    public function testEvents()
    {
        $taskPositionModel = new TaskPositionModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(2, $swimlaneModel->create(1, 'Swimlane #1'));

        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 2)));

        $this->container['dispatcher']->addListener(TaskModel::EVENT_MOVE_COLUMN, array($this, 'onMoveColumn'));
        $this->container['dispatcher']->addListener(TaskModel::EVENT_MOVE_POSITION, array($this, 'onMovePosition'));
        $this->container['dispatcher']->addListener(TaskModel::EVENT_MOVE_SWIMLANE, array($this, 'onMoveSwimlane'));

        // We move the task 1 to the column 2
        $this->assertTrue($taskPositionModel->movePosition(1, 1, 2, 1));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $task = $taskFinderModel->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskModel::EVENT_MOVE_COLUMN.'.TaskPositionModelTest::onMoveColumn', $called);
        $this->assertEquals(1, count($called));

        // We move the task 1 to the position 2
        $this->assertTrue($taskPositionModel->movePosition(1, 1, 2, 2));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['position']);

        $task = $taskFinderModel->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskModel::EVENT_MOVE_POSITION.'.TaskPositionModelTest::onMovePosition', $called);
        $this->assertEquals(2, count($called));

        // Move to another swimlane
        $this->assertTrue($taskPositionModel->movePosition(1, 1, 3, 1, 2));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['swimlane_id']);

        $task = $taskFinderModel->getById(2);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['swimlane_id']);

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskModel::EVENT_MOVE_SWIMLANE.'.TaskPositionModelTest::onMoveSwimlane', $called);
        $this->assertEquals(3, count($called));
    }

    public function onMoveColumn($event)
    {
        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals(1, $event_data['position']);
        $this->assertEquals(2, $event_data['column_id']);
        $this->assertEquals(1, $event_data['project_id']);
    }

    public function onMovePosition($event)
    {
        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals(2, $event_data['position']);
        $this->assertEquals(2, $event_data['column_id']);
        $this->assertEquals(1, $event_data['project_id']);
    }

    public function onMoveSwimlane($event)
    {
        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals(1, $event_data['position']);
        $this->assertEquals(3, $event_data['column_id']);
        $this->assertEquals(1, $event_data['project_id']);
        $this->assertEquals(2, $event_data['swimlane_id']);
    }
}
