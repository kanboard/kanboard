<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\CommentModel;
use Kanboard\Model\TaskLinkModel;
use Kanboard\Model\UserModel;
use Kanboard\Model\TaskFileModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Notification\MailNotification;
use Kanboard\Subscriber\NotificationSubscriber;

class MailNotificationTest extends Base
{
    public function testGetMailContent()
    {
        $mailNotification = new MailNotification($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $commentModel = new CommentModel($this->container);
        $fileModel = new TaskFileModel($this->container);
        $taskLinkModel = new TaskLinkModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'test', 'task_id' => 1)));
        $this->assertEquals(1, $commentModel->create(array('comment' => 'test', 'task_id' => 1, 'user_id' => 1)));
        $this->assertEquals(1, $fileModel->create(1, 'test', 'blah', 123));
        $this->assertEquals(1, $taskLinkModel->create(1, 2, 1));

        $task = $taskFinderModel->getDetails(1);
        $subtask = $subtaskModel->getByIdWithDetails(1);
        $comment = $commentModel->getById(1);
        $file = $commentModel->getById(1);
        $tasklink = $taskLinkModel->getById(1);

        $this->assertNotEmpty($task);
        $this->assertNotEmpty($subtask);
        $this->assertNotEmpty($comment);
        $this->assertNotEmpty($file);

        foreach (NotificationSubscriber::getSubscribedEvents() as $eventName => $values) {
            $eventData = array(
                'task' => $task,
                'comment' => $comment,
                'subtask' => $subtask,
                'file' => $file,
                'task_link' => $tasklink,
                'changes' => array()
            );
            $this->assertNotEmpty($mailNotification->getMailContent($eventName, $eventData));
            $this->assertStringStartsWith('[test] ', $mailNotification->getMailSubject($eventName, $eventData));
        }

        $this->assertStringStartsWith('[Test1, Test2] ', $mailNotification->getMailSubject(TaskModel::EVENT_OVERDUE, array(
            'tasks' => array(array('id' => 123), array('id' => 456)),
            'project_name' => 'Test1, Test2',
        )));
    }

    public function testSendWithEmailAddress()
    {
        $mailNotification = new MailNotification($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertTrue($userModel->update(array('id' => 1, 'email' => 'test@localhost')));

        $this->container['emailClient'] = $this
            ->getMockBuilder('\Kanboard\Core\Mail\Client')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('send'))
            ->getMock();

        $this->container['emailClient']
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('test@localhost'),
                $this->equalTo('admin'),
                $this->equalTo('[test] New task #1: test'),
                $this->stringContains('test')
            );

        $mailNotification->notifyUser($userModel->getById(1), TaskModel::EVENT_CREATE, array('task' => $taskFinderModel->getDetails(1)));
    }

    public function testSendWithoutEmailAddress()
    {
        $mailNotification = new MailNotification($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));

        $this->container['emailClient'] = $this
            ->getMockBuilder('\Kanboard\Core\Mail\Client')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('send'))
            ->getMock();

        $this->container['emailClient']
            ->expects($this->never())
            ->method('send');

        $mailNotification->notifyUser($userModel->getById(1), TaskModel::EVENT_CREATE, array('task' => $taskFinderModel->getDetails(1)));
    }
}
