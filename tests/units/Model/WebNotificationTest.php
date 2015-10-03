<?php

require_once __DIR__.'/../Base.php';

use Model\TaskFinder;
use Model\TaskCreation;
use Model\Subtask;
use Model\Comment;
use Model\User;
use Model\File;
use Model\Task;
use Model\Project;
use Model\WebNotification;
use Subscriber\NotificationSubscriber;

class WebNotificationTest extends Base
{
    public function testGetTitle()
    {
        $wn = new WebNotification($this->container);
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

        foreach (Subscriber\NotificationSubscriber::getSubscribedEvents() as $event_name => $values) {
            $title = $wn->getTitleFromEvent($event_name, array(
                'task' => $task,
                'comment' => $comment,
                'subtask' => $subtask,
                'file' => $file,
                'changes' => array()
            ));

            $this->assertNotEmpty($title);
        }
    }

    public function testHasNotification()
    {
        $wn = new WebNotification($this->container);
        $p = new Project($this->container);
        $tf = new TaskFinder($this->container);
        $tc = new TaskCreation($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $this->assertFalse($wn->hasNotifications(1));

        $wn->send($u->getById(1), Task::EVENT_CREATE, array('task' => $tf->getDetails(1)));

        $this->assertTrue($wn->hasNotifications(1));
    }

    public function testMarkAllAsRead()
    {
        $wn = new WebNotification($this->container);
        $p = new Project($this->container);
        $tf = new TaskFinder($this->container);
        $tc = new TaskCreation($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $wn->send($u->getById(1), Task::EVENT_CREATE, array('task' => $tf->getDetails(1)));

        $this->assertTrue($wn->hasNotifications(1));
        $this->assertTrue($wn->markAllAsRead(1));

        $this->assertFalse($wn->hasNotifications(1));
        $this->assertFalse($wn->markAllAsRead(1));
    }

    public function testMarkAsRead()
    {
        $wn = new WebNotification($this->container);
        $p = new Project($this->container);
        $tf = new TaskFinder($this->container);
        $tc = new TaskCreation($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $wn->send($u->getById(1), Task::EVENT_CREATE, array('task' => $tf->getDetails(1)));

        $this->assertTrue($wn->hasNotifications(1));

        $this->assertFalse($wn->markAsRead(2, 1));
        $this->assertTrue($wn->markAsRead(1, 1));

        $this->assertFalse($wn->hasNotifications(1));
        $this->assertFalse($wn->markAsRead(1, 1));
    }

    public function testGetAll()
    {
        $wn = new WebNotification($this->container);
        $p = new Project($this->container);
        $tf = new TaskFinder($this->container);
        $tc = new TaskCreation($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $wn->send($u->getById(1), Task::EVENT_CREATE, array('task' => $tf->getDetails(1)));
        $wn->send($u->getById(1), Task::EVENT_CREATE, array('task' => $tf->getDetails(1)));

        $this->assertEmpty($wn->getAll(2));

        $notifications = $wn->getAll(1);
        $this->assertCount(2, $notifications);
        $this->assertArrayHasKey('title', $notifications[0]);
        $this->assertTrue(is_array($notifications[0]['event_data']));
    }
}
