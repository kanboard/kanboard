<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskStatusModel;
use Kanboard\Model\ProjectModel;

class TaskStatusModelTest extends Base
{
    public function testCloseBySwimlaneAndColumn()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $taskStatusModel = new TaskStatusModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(2, $swimlaneModel->create(1, 'Swimlane #1'));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertEquals(3, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2)));
        $this->assertEquals(4, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'swimlane_id' => 2)));
        $this->assertEquals(5, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'is_active' => 0, 'date_completed' => strtotime('2015-01-01'))));

        $taskBefore = $taskFinderModel->getById(5);

        $this->assertEquals(2, $taskFinderModel->countByColumnAndSwimlaneId(1, 1, 1));
        $this->assertEquals(1, $taskFinderModel->countByColumnAndSwimlaneId(1, 1, 2));
        $this->assertEquals(1, $taskFinderModel->countByColumnAndSwimlaneId(1, 2, 1));

        $taskStatusModel->closeTasksBySwimlaneAndColumn(1, 1);
        $this->assertEquals(0, $taskFinderModel->countByColumnAndSwimlaneId(1, 1, 1));
        $this->assertEquals(1, $taskFinderModel->countByColumnAndSwimlaneId(1, 1, 2));
        $this->assertEquals(1, $taskFinderModel->countByColumnAndSwimlaneId(1, 2, 1));

        $taskStatusModel->closeTasksBySwimlaneAndColumn(2, 1);
        $this->assertEquals(0, $taskFinderModel->countByColumnAndSwimlaneId(1, 1, 1));
        $this->assertEquals(0, $taskFinderModel->countByColumnAndSwimlaneId(1, 1, 2));
        $this->assertEquals(1, $taskFinderModel->countByColumnAndSwimlaneId(1, 2, 1));

        $taskStatusModel->closeTasksBySwimlaneAndColumn(1, 2);
        $this->assertEquals(0, $taskFinderModel->countByColumnAndSwimlaneId(1, 1, 1));
        $this->assertEquals(0, $taskFinderModel->countByColumnAndSwimlaneId(1, 1, 2));
        $this->assertEquals(0, $taskFinderModel->countByColumnAndSwimlaneId(1, 2, 1));

        $taskAfter = $taskFinderModel->getById(5);
        $this->assertEquals(strtotime('2015-01-01'), $taskAfter['date_completed']);
        $this->assertEquals($taskBefore['date_modification'], $taskAfter['date_modification']);
    }

    public function testStatus()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $taskStatusModel = new TaskStatusModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));

        // The task must be open

        $this->assertTrue($taskStatusModel->isOpen(1));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(TaskModel::STATUS_OPEN, $task['is_active']);
        $this->assertEquals(0, $task['date_completed']);
        $this->assertEquals(time(), $task['date_modification'], '', 1);

        // We close the task

        $this->container['dispatcher']->addListener(TaskModel::EVENT_CLOSE, array($this, 'onTaskClose'));
        $this->container['dispatcher']->addListener(TaskModel::EVENT_OPEN, array($this, 'onTaskOpen'));

        $this->assertTrue($taskStatusModel->close(1));
        $this->assertTrue($taskStatusModel->isClosed(1));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(TaskModel::STATUS_CLOSED, $task['is_active']);
        $this->assertEquals(time(), $task['date_completed'], 'Bad completion timestamp', 1);
        $this->assertEquals(time(), $task['date_modification'], 'Bad modification timestamp', 1);

        // We open the task again

        $this->assertTrue($taskStatusModel->open(1));
        $this->assertTrue($taskStatusModel->isOpen(1));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(TaskModel::STATUS_OPEN, $task['is_active']);
        $this->assertEquals(0, $task['date_completed']);
        $this->assertEquals(time(), $task['date_modification'], '', 1);

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey('task.close.TaskStatusModelTest::onTaskClose', $called);
        $this->assertArrayHasKey('task.open.TaskStatusModelTest::onTaskOpen', $called);
    }

    public function onTaskOpen($event)
    {
        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);
        $this->assertArrayHasKey('task_id', $event);
        $this->assertNotEmpty($event['task_id']);
    }

    public function onTaskClose($event)
    {
        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);
        $this->assertArrayHasKey('task_id', $event);
        $this->assertNotEmpty($event['task_id']);
    }

    public function testThatAllSubtasksAreClosed()
    {
        $taskStatusModel = new TaskStatusModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1)));
        $this->assertEquals(2, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1)));

        $this->assertTrue($taskStatusModel->close(1));

        $subtasks = $subtaskModel->getAll(1);
        $this->assertNotEmpty($subtasks);

        foreach ($subtasks as $subtask) {
            $this->assertEquals(SubtaskModel::STATUS_DONE, $subtask['status']);
        }
    }
}
