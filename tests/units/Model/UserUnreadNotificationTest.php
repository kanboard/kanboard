<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskFinder;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\Subtask;
use Kanboard\Model\Comment;
use Kanboard\Model\User;
use Kanboard\Model\File;
use Kanboard\Model\Task;
use Kanboard\Model\Project;
use Kanboard\Model\UserUnreadNotification;

class UserUnreadNotificationTest extends Base
{
    public function testHasNotification()
    {
        $wn = new UserUnreadNotification($this->container);
        $p = new Project($this->container);
        $tf = new TaskFinder($this->container);
        $tc = new TaskCreation($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $this->assertFalse($wn->hasNotifications(1));

        $wn->create(1, Task::EVENT_CREATE, array('task' => $tf->getDetails(1)));

        $this->assertTrue($wn->hasNotifications(1));
    }

    public function testMarkAllAsRead()
    {
        $wn = new UserUnreadNotification($this->container);
        $p = new Project($this->container);
        $tf = new TaskFinder($this->container);
        $tc = new TaskCreation($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $wn->create(1, Task::EVENT_CREATE, array('task' => $tf->getDetails(1)));

        $this->assertTrue($wn->hasNotifications(1));
        $this->assertTrue($wn->markAllAsRead(1));

        $this->assertFalse($wn->hasNotifications(1));
        $this->assertFalse($wn->markAllAsRead(1));
    }

    public function testMarkAsRead()
    {
        $wn = new UserUnreadNotification($this->container);
        $p = new Project($this->container);
        $tf = new TaskFinder($this->container);
        $tc = new TaskCreation($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $wn->create(1, Task::EVENT_CREATE, array('task' => $tf->getDetails(1)));

        $this->assertTrue($wn->hasNotifications(1));

        $this->assertFalse($wn->markAsRead(2, 1));
        $this->assertTrue($wn->markAsRead(1, 1));

        $this->assertFalse($wn->hasNotifications(1));
        $this->assertFalse($wn->markAsRead(1, 1));
    }

    public function testGetAll()
    {
        $wn = new UserUnreadNotification($this->container);
        $p = new Project($this->container);
        $tf = new TaskFinder($this->container);
        $tc = new TaskCreation($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $wn->create(1, Task::EVENT_CREATE, array('task' => $tf->getDetails(1)));
        $wn->create(1, Task::EVENT_CREATE, array('task' => $tf->getDetails(1)));

        $this->assertEmpty($wn->getAll(2));

        $notifications = $wn->getAll(1);
        $this->assertCount(2, $notifications);
        $this->assertArrayHasKey('title', $notifications[0]);
        $this->assertTrue(is_array($notifications[0]['event_data']));
    }
}
