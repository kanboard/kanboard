<?php

require_once __DIR__.'/../Base.php';

use Model\TaskFinder;
use Model\TaskCreation;
use Model\Subtask;
use Model\Comment;
use Model\User;
use Model\File;
use Model\Project;
use Model\Task;
use Model\ProjectPermission;
use Model\EmailNotification;
use Subscriber\NotificationSubscriber;

class EmailNotificationTest extends Base
{
    public function testGetMailContent()
    {
        $en = new EmailNotification($this->container);
        $p = new Project($this->container);
        $tf = new TaskFinder($this->container);
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $c = new Comment($this->container);
        $f = new File($this->container);

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

        foreach (Subscriber\NotificationSubscriber::getSubscribedEvents() as $event => $values) {
            $this->assertNotEmpty($en->getMailContent($event, array(
                'task' => $task,
                'comment' => $comment,
                'subtask' => $subtask,
                'file' => $file,
                'changes' => array())
            ));
        }
    }

    public function testGetEmailSubject()
    {
        $en = new EmailNotification($this->container);

        $this->assertEquals(
            '[test][Task opened] blah (#2)',
            $en->getMailSubject(Task::EVENT_OPEN, array('task' => array('id' => 2, 'title' => 'blah', 'project_name' => 'test')))
        );
    }

    public function testSendWithEmailAddress()
    {
        $en = new EmailNotification($this->container);
        $p = new Project($this->container);
        $tf = new TaskFinder($this->container);
        $tc = new TaskCreation($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertTrue($u->update(array('id' => 1, 'email' => 'test@localhost')));

        $this->container['emailClient'] = $this
            ->getMockBuilder('\Core\EmailClient')
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

        $en->send($u->getById(1), Task::EVENT_CREATE, array('task' => $tf->getDetails(1)));
    }

    public function testSendWithoutEmailAddress()
    {
        $en = new EmailNotification($this->container);
        $p = new Project($this->container);
        $tf = new TaskFinder($this->container);
        $tc = new TaskCreation($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $this->container['emailClient'] = $this
            ->getMockBuilder('\Core\EmailClient')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('send'))
            ->getMock();

        $this->container['emailClient']
            ->expects($this->never())
            ->method('send');

        $en->send($u->getById(1), Task::EVENT_CREATE, array('task' => $tf->getDetails(1)));
    }
}
