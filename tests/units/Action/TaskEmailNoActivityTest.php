<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\TaskListEvent;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\UserModel;
use Kanboard\Action\TaskEmailNoActivity;

class TaskEmailNoActivityTest extends Base
{
    public function testSendEmail()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'test', 'email' => 'chuck@norris', 'name' => 'Chuck Norris')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $this->container['db']->table(TaskModel::TABLE)->eq('id', 1)->update(array('date_modification' => strtotime('-10days')));

        $tasks = $taskFinderModel->getAll(1);
        $event = new TaskListEvent(array('tasks' => $tasks, 'project_id' => 1));

        $action = new TaskEmailNoActivity($this->container);
        $action->setProjectId(1);
        $action->setParam('user_id', 2);
        $action->setParam('subject', 'Old tasks');
        $action->setParam('duration', 2);

        $this->container['emailClient']
            ->expects($this->once())
            ->method('send')
            ->with('chuck@norris', 'Chuck Norris', 'Old tasks', $this->anything());

        $this->assertTrue($action->execute($event, TaskModel::EVENT_DAILY_CRONJOB));
    }

    public function testUserWithNoEmail()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'test', 'name' => 'Chuck Norris')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $this->container['db']->table(TaskModel::TABLE)->eq('id', 1)->update(array('date_modification' => strtotime('-10days')));

        $tasks = $taskFinderModel->getAll(1);
        $event = new TaskListEvent(array('tasks' => $tasks, 'project_id' => 1));

        $action = new TaskEmailNoActivity($this->container);
        $action->setProjectId(1);
        $action->setParam('user_id', 2);
        $action->setParam('subject', 'Old tasks');
        $action->setParam('duration', 2);

        $this->container['emailClient']
            ->expects($this->never())
            ->method('send');

        $this->assertFalse($action->execute($event, TaskModel::EVENT_DAILY_CRONJOB));
    }

    public function testTooRecent()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'test', 'email' => 'chuck@norris', 'name' => 'Chuck Norris')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $tasks = $taskFinderModel->getAll(1);
        $event = new TaskListEvent(array('tasks' => $tasks, 'project_id' => 1));

        $action = new TaskEmailNoActivity($this->container);
        $action->setProjectId(1);
        $action->setParam('user_id', 2);
        $action->setParam('subject', 'Old tasks');
        $action->setParam('duration', 2);

        $this->container['emailClient']
            ->expects($this->never())
            ->method('send');

        $this->assertFalse($action->execute($event, TaskModel::EVENT_DAILY_CRONJOB));
    }
}
