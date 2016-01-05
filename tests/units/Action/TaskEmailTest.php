<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\Project;
use Kanboard\Model\User;
use Kanboard\Action\TaskEmail;

class TaskEmailTest extends Base
{
    public function testSuccess()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertTrue($userModel->update(array('id' => 1, 'email' => 'admin@localhost')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'column_id' => 2));

        $action = new TaskEmail($this->container);
        $action->setProjectId(1);
        $action->setParam('column_id', 2);
        $action->setParam('user_id', 1);
        $action->setParam('subject', 'My email subject');

        $this->container['emailClient']->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('admin@localhost'),
                $this->equalTo('admin'),
                $this->equalTo('My email subject'),
                $this->stringContains('test')
            );

        $this->assertTrue($action->execute($event, Task::EVENT_CLOSE));
    }

    public function testWithWrongColumn()
    {
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'column_id' => 3));

        $action = new TaskEmail($this->container);
        $action->setProjectId(1);
        $action->setParam('column_id', 2);
        $action->setParam('user_id', 1);
        $action->setParam('subject', 'My email subject');

        $this->assertFalse($action->execute($event, Task::EVENT_CLOSE));
    }
}
