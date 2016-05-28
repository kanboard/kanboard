<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\CommentModel;
use Kanboard\Model\UserModel;
use Kanboard\Model\TaskFileModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Notification\MailNotification;
use Kanboard\Subscriber\NotificationSubscriber;

class MailTest extends Base
{
    public function testGetMailContent()
    {
        $en = new MailNotification($this->container);
        $p = new ProjectModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $s = new SubtaskModel($this->container);
        $c = new CommentModel($this->container);
        $f = new TaskFileModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertEquals(1, $s->create(array('title' => 'test', 'task_id' => 1)));
        $this->assertEquals(1, $c->create(array('comment' => 'test', 'task_id' => 1, 'user_id' => 1)));
        $this->assertEquals(1, $f->create(1, 'test', 'blah', 123));

        $task = $tf->getDetails(1);
        $subtask = $s->getById(1, true);
        $comment = $c->getById(1);
        $file = $c->getById(1);

        $this->assertNotEmpty($task);
        $this->assertNotEmpty($subtask);
        $this->assertNotEmpty($comment);
        $this->assertNotEmpty($file);

        foreach (NotificationSubscriber::getSubscribedEvents() as $event => $values) {
            $this->assertNotEmpty($en->getMailContent($event, array(
                'task' => $task,
                'comment' => $comment,
                'subtask' => $subtask,
                'file' => $file,
                'changes' => array())
            ));

            $this->assertNotEmpty($en->getMailSubject($event, array(
                'task' => $task,
                'comment' => $comment,
                'subtask' => $subtask,
                'file' => $file,
                'changes' => array())
            ));
        }
    }

    public function testSendWithEmailAddress()
    {
        $en = new MailNotification($this->container);
        $p = new ProjectModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $u = new UserModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertTrue($u->update(array('id' => 1, 'email' => 'test@localhost')));

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
                $this->equalTo('[test][New task] test (#1)'),
                $this->stringContains('test')
            );

        $en->notifyUser($u->getById(1), TaskModel::EVENT_CREATE, array('task' => $tf->getDetails(1)));
    }

    public function testSendWithoutEmailAddress()
    {
        $en = new MailNotification($this->container);
        $p = new ProjectModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $u = new UserModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $this->container['emailClient'] = $this
            ->getMockBuilder('\Kanboard\Core\Mail\Client')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('send'))
            ->getMock();

        $this->container['emailClient']
            ->expects($this->never())
            ->method('send');

        $en->notifyUser($u->getById(1), TaskModel::EVENT_CREATE, array('task' => $tf->getDetails(1)));
    }
}
