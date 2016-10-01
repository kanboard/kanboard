<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\TaskEvent;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\UserModel;
use Kanboard\Action\TaskEmail;

class TaskEmailTest extends Base
{
    public function testSuccess()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertTrue($userModel->update(array('id' => 1, 'email' => 'admin@localhost')));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => $taskFinderModel->getDetails(1)
        ));

        $action = new TaskEmail($this->container);
        $action->setProjectId(1);
        $action->setParam('column_id', 1);
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

        $this->assertTrue($action->execute($event, TaskModel::EVENT_CLOSE));
    }

    public function testWithWrongColumn()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => array(
                'project_id' => 1,
                'column_id' => 3,
            )
        ));

        $action = new TaskEmail($this->container);
        $action->setProjectId(1);
        $action->setParam('column_id', 2);
        $action->setParam('user_id', 1);
        $action->setParam('subject', 'My email subject');

        $this->assertFalse($action->execute($event, TaskModel::EVENT_CLOSE));
    }
}
