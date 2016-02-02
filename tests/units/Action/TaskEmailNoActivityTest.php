<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\TaskListEvent;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Model\Task;
use Kanboard\Model\User;
use Kanboard\Action\TaskEmailNoActivity;

class TaskEmailNoActivityTest extends Base
{
    public function testSendEmail()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'test', 'email' => 'chuck@norris', 'name' => 'Chuck Norris')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $this->container['db']->table(Task::TABLE)->eq('id', 1)->update(array('date_modification' => strtotime('-10days')));

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

        $this->assertTrue($action->execute($event, Task::EVENT_DAILY_CRONJOB));
    }

    public function testUserWithNoEmail()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'test', 'name' => 'Chuck Norris')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $this->container['db']->table(Task::TABLE)->eq('id', 1)->update(array('date_modification' => strtotime('-10days')));

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

        $this->assertFalse($action->execute($event, Task::EVENT_DAILY_CRONJOB));
    }

    public function testTooRecent()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);

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

        $this->assertFalse($action->execute($event, Task::EVENT_DAILY_CRONJOB));
    }
}
