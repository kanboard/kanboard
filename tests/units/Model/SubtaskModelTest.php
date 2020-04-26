<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskFinderModel;

class SubtaskModelTest extends Base
{
    public function testCreation()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1)));

        $subtask = $subtaskModel->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(1, $subtask['id']);
        $this->assertEquals(1, $subtask['task_id']);
        $this->assertEquals('subtask #1', $subtask['title']);
        $this->assertEquals(SubtaskModel::STATUS_TODO, $subtask['status']);
        $this->assertEquals(0, $subtask['time_estimated']);
        $this->assertEquals(0, $subtask['time_spent']);
        $this->assertEquals(0, $subtask['user_id']);
        $this->assertEquals(1, $subtask['position']);
    }

    public function testCreationUpdateTaskTimeTracking()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1, 'time_estimated' => 2, 'time_spent' => 1)));
        $this->assertEquals(2, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1, 'time_estimated' => 5, 'time_spent' => 5)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(7, $task['time_estimated']);
        $this->assertEquals(6, $task['time_spent']);
    }

    public function testModification()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1)));
        $this->assertTrue($subtaskModel->update(array('id' => 1, 'task_id' => 1, 'user_id' => 1, 'status' => SubtaskModel::STATUS_INPROGRESS)));

        $subtask = $subtaskModel->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(1, $subtask['id']);
        $this->assertEquals(1, $subtask['task_id']);
        $this->assertEquals('subtask #1', $subtask['title']);
        $this->assertEquals(SubtaskModel::STATUS_INPROGRESS, $subtask['status']);
        $this->assertEquals(0, $subtask['time_estimated']);
        $this->assertEquals(0, $subtask['time_spent']);
        $this->assertEquals(1, $subtask['user_id']);
        $this->assertEquals(1, $subtask['position']);
    }

    public function testModificationUpdateTaskTimeTracking()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1)));
        $this->assertEquals(2, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1)));
        $this->assertTrue($subtaskModel->update(array('id' => 1, 'task_id' => 1, 'time_estimated' => 2, 'time_spent' => 1)));
        $this->assertTrue($subtaskModel->update(array('id' => 2, 'task_id' => 1, 'time_estimated' => 2, 'time_spent' => 1)));
        $this->assertTrue($subtaskModel->update(array('id' => 1, 'task_id' => 1, 'time_estimated' => 5, 'time_spent' => 5)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(7, $task['time_estimated']);
        $this->assertEquals(6, $task['time_spent']);
    }

    public function testRemove()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1)));

        $subtask = $subtaskModel->getById(1);
        $this->assertNotEmpty($subtask);

        $this->assertTrue($subtaskModel->remove(1));

        $subtask = $subtaskModel->getById(1);
        $this->assertEmpty($subtask);
    }

    public function testDuplicate()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $projectModel = new ProjectModel($this->container);

        // We create a project
        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));

        // We create 2 tasks
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'test 2', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 0)));

        // We create many subtasks for the first task
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1, 'time_estimated' => 5, 'time_spent' => 3, 'status' => 1, 'another_subtask' => 'on')));
        $this->assertEquals(2, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1, 'time_estimated' => '', 'time_spent' => '', 'status' => 2, 'user_id' => 1)));

        // We duplicate our subtasks
        $this->assertTrue($subtaskModel->duplicate(1, 2));
        $subtasks = $subtaskModel->getAll(2);

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
        $this->assertEquals(1, $subtasks[1]['user_id']);
        
        $this->assertEquals(1, $subtasks[0]['position']);
        $this->assertEquals(2, $subtasks[1]['position']);
    }

    public function testGetProjectId()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1)));

        $this->assertEquals(1, $subtaskModel->getProjectId(1));
        $this->assertEquals(0, $subtaskModel->getProjectId(2));
    }

    public function testGetAllByTaskIds()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1)));

        $this->assertCount(0, $subtaskModel->getAllByTaskIds(array()));
        $this->assertCount(1, $subtaskModel->getAllByTaskIds(array(1)));
    }

    public function testGetAllByTaskIdsAndAssignee()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1, 'user_id' => 1)));

        $this->assertCount(0, $subtaskModel->getAllByTaskIdsAndAssignee(array(), 1));
        $this->assertCount(0, $subtaskModel->getAllByTaskIdsAndAssignee(array(1), 2));
        $this->assertCount(1, $subtaskModel->getAllByTaskIdsAndAssignee(array(1), 1));
    }
}
