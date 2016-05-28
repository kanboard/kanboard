<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Core\User\UserSession;
use Kanboard\Model\TaskFinderModel;

class SubtaskTest extends Base
{
    public function onSubtaskCreated($event)
    {
        $this->assertInstanceOf('Kanboard\Event\SubtaskEvent', $event);
        $data = $event->getAll();

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('time_estimated', $data);
        $this->assertArrayHasKey('time_spent', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('task_id', $data);
        $this->assertArrayHasKey('user_id', $data);
        $this->assertArrayHasKey('position', $data);
        $this->assertNotEmpty($data['task_id']);
        $this->assertNotEmpty($data['id']);
    }

    public function onSubtaskUpdated($event)
    {
        $this->assertInstanceOf('Kanboard\Event\SubtaskEvent', $event);
        $data = $event->getAll();

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('time_estimated', $data);
        $this->assertArrayHasKey('time_spent', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('task_id', $data);
        $this->assertArrayHasKey('user_id', $data);
        $this->assertArrayHasKey('position', $data);
        $this->assertArrayHasKey('changes', $data);
        $this->assertArrayHasKey('user_id', $data['changes']);
        $this->assertArrayHasKey('status', $data['changes']);

        $this->assertEquals(SubtaskModel::STATUS_INPROGRESS, $data['changes']['status']);
        $this->assertEquals(1, $data['changes']['user_id']);
    }

    public function onSubtaskDeleted($event)
    {
        $this->assertInstanceOf('Kanboard\Event\SubtaskEvent', $event);
        $data = $event->getAll();

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('time_estimated', $data);
        $this->assertArrayHasKey('time_spent', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('task_id', $data);
        $this->assertArrayHasKey('user_id', $data);
        $this->assertArrayHasKey('position', $data);
        $this->assertNotEmpty($data['task_id']);
        $this->assertNotEmpty($data['id']);
    }

    public function testCreation()
    {
        $tc = new TaskCreationModel($this->container);
        $s = new SubtaskModel($this->container);
        $p = new ProjectModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->container['dispatcher']->addListener(SubtaskModel::EVENT_CREATE, array($this, 'onSubtaskCreated'));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));

        $subtask = $s->getById(1);
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

    public function testModification()
    {
        $tc = new TaskCreationModel($this->container);
        $s = new SubtaskModel($this->container);
        $p = new ProjectModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->container['dispatcher']->addListener(SubtaskModel::EVENT_UPDATE, array($this, 'onSubtaskUpdated'));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));
        $this->assertTrue($s->update(array('id' => 1, 'user_id' => 1, 'status' => SubtaskModel::STATUS_INPROGRESS)));

        $subtask = $s->getById(1);
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

    public function testRemove()
    {
        $tc = new TaskCreationModel($this->container);
        $s = new SubtaskModel($this->container);
        $p = new ProjectModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));
        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));

        $this->container['dispatcher']->addListener(SubtaskModel::EVENT_DELETE, array($this, 'onSubtaskDeleted'));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);

        $this->assertTrue($s->remove(1));

        $subtask = $s->getById(1);
        $this->assertEmpty($subtask);
    }

    public function testToggleStatusWithoutSession()
    {
        $tc = new TaskCreationModel($this->container);
        $s = new SubtaskModel($this->container);
        $p = new ProjectModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(SubtaskModel::STATUS_TODO, $subtask['status']);
        $this->assertEquals(0, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);

        $this->assertEquals(SubtaskModel::STATUS_INPROGRESS, $s->toggleStatus(1));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(SubtaskModel::STATUS_INPROGRESS, $subtask['status']);
        $this->assertEquals(0, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);

        $this->assertEquals(SubtaskModel::STATUS_DONE, $s->toggleStatus(1));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(SubtaskModel::STATUS_DONE, $subtask['status']);
        $this->assertEquals(0, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);

        $this->assertEquals(SubtaskModel::STATUS_TODO, $s->toggleStatus(1));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(SubtaskModel::STATUS_TODO, $subtask['status']);
        $this->assertEquals(0, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);
    }

    public function testToggleStatusWithSession()
    {
        $tc = new TaskCreationModel($this->container);
        $s = new SubtaskModel($this->container);
        $p = new ProjectModel($this->container);
        $us = new UserSession($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(SubtaskModel::STATUS_TODO, $subtask['status']);
        $this->assertEquals(0, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);

        // Set the current logged user
        $this->container['sessionStorage']->user = array('id' => 1);

        $this->assertEquals(SubtaskModel::STATUS_INPROGRESS, $s->toggleStatus(1));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(SubtaskModel::STATUS_INPROGRESS, $subtask['status']);
        $this->assertEquals(1, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);

        $this->assertEquals(SubtaskModel::STATUS_DONE, $s->toggleStatus(1));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(SubtaskModel::STATUS_DONE, $subtask['status']);
        $this->assertEquals(1, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);

        $this->assertEquals(SubtaskModel::STATUS_TODO, $s->toggleStatus(1));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(SubtaskModel::STATUS_TODO, $subtask['status']);
        $this->assertEquals(1, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);
    }

    public function testCloseAll()
    {
        $tc = new TaskCreationModel($this->container);
        $s = new SubtaskModel($this->container);
        $p = new ProjectModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));
        $this->assertEquals(2, $s->create(array('title' => 'subtask #2', 'task_id' => 1)));

        $this->assertTrue($s->closeAll(1));

        $subtasks = $s->getAll(1);
        $this->assertNotEmpty($subtasks);

        foreach ($subtasks as $subtask) {
            $this->assertEquals(SubtaskModel::STATUS_DONE, $subtask['status']);
        }
    }

    public function testDuplicate()
    {
        $tc = new TaskCreationModel($this->container);
        $s = new SubtaskModel($this->container);
        $p = new ProjectModel($this->container);

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

    public function testChangePosition()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1)));
        $this->assertEquals(2, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1)));
        $this->assertEquals(3, $subtaskModel->create(array('title' => 'subtask #3', 'task_id' => 1)));

        $subtasks = $subtaskModel->getAll(1);
        $this->assertEquals(1, $subtasks[0]['position']);
        $this->assertEquals(1, $subtasks[0]['id']);
        $this->assertEquals(2, $subtasks[1]['position']);
        $this->assertEquals(2, $subtasks[1]['id']);
        $this->assertEquals(3, $subtasks[2]['position']);
        $this->assertEquals(3, $subtasks[2]['id']);

        $this->assertTrue($subtaskModel->changePosition(1, 3, 2));

        $subtasks = $subtaskModel->getAll(1);
        $this->assertEquals(1, $subtasks[0]['position']);
        $this->assertEquals(1, $subtasks[0]['id']);
        $this->assertEquals(2, $subtasks[1]['position']);
        $this->assertEquals(3, $subtasks[1]['id']);
        $this->assertEquals(3, $subtasks[2]['position']);
        $this->assertEquals(2, $subtasks[2]['id']);

        $this->assertTrue($subtaskModel->changePosition(1, 2, 1));

        $subtasks = $subtaskModel->getAll(1);
        $this->assertEquals(1, $subtasks[0]['position']);
        $this->assertEquals(2, $subtasks[0]['id']);
        $this->assertEquals(2, $subtasks[1]['position']);
        $this->assertEquals(1, $subtasks[1]['id']);
        $this->assertEquals(3, $subtasks[2]['position']);
        $this->assertEquals(3, $subtasks[2]['id']);

        $this->assertTrue($subtaskModel->changePosition(1, 2, 2));

        $subtasks = $subtaskModel->getAll(1);
        $this->assertEquals(1, $subtasks[0]['position']);
        $this->assertEquals(1, $subtasks[0]['id']);
        $this->assertEquals(2, $subtasks[1]['position']);
        $this->assertEquals(2, $subtasks[1]['id']);
        $this->assertEquals(3, $subtasks[2]['position']);
        $this->assertEquals(3, $subtasks[2]['id']);

        $this->assertTrue($subtaskModel->changePosition(1, 1, 3));

        $subtasks = $subtaskModel->getAll(1);
        $this->assertEquals(1, $subtasks[0]['position']);
        $this->assertEquals(2, $subtasks[0]['id']);
        $this->assertEquals(2, $subtasks[1]['position']);
        $this->assertEquals(3, $subtasks[1]['id']);
        $this->assertEquals(3, $subtasks[2]['position']);
        $this->assertEquals(1, $subtasks[2]['id']);

        $this->assertFalse($subtaskModel->changePosition(1, 2, 0));
        $this->assertFalse($subtaskModel->changePosition(1, 2, 4));
    }

    public function testConvertToTask()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1, 'user_id' => 1, 'time_spent' => 2, 'time_estimated' => 3)));
        $task_id = $subtaskModel->convertToTask(1, 1);

        $this->assertNotFalse($task_id);
        $this->assertEmpty($subtaskModel->getById(1));

        $task = $taskFinderModel->getById($task_id);
        $this->assertEquals('subtask #1', $task['title']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(1, $task['owner_id']);
        $this->assertEquals(2, $task['time_spent']);
        $this->assertEquals(3, $task['time_estimated']);
    }
}
