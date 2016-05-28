<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\UserUnreadNotificationModel;

class UserUnreadNotificationTest extends Base
{
    public function testHasNotification()
    {
        $wn = new UserUnreadNotificationModel($this->container);
        $p = new ProjectModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $tc = new TaskCreationModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $this->assertFalse($wn->hasNotifications(1));

        $wn->create(1, TaskModel::EVENT_CREATE, array('task' => $tf->getDetails(1)));

        $this->assertTrue($wn->hasNotifications(1));
    }

    public function testMarkAllAsRead()
    {
        $wn = new UserUnreadNotificationModel($this->container);
        $p = new ProjectModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $tc = new TaskCreationModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $wn->create(1, TaskModel::EVENT_CREATE, array('task' => $tf->getDetails(1)));

        $this->assertTrue($wn->hasNotifications(1));
        $this->assertTrue($wn->markAllAsRead(1));

        $this->assertFalse($wn->hasNotifications(1));
        $this->assertFalse($wn->markAllAsRead(1));
    }

    public function testMarkAsRead()
    {
        $wn = new UserUnreadNotificationModel($this->container);
        $p = new ProjectModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $tc = new TaskCreationModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $wn->create(1, TaskModel::EVENT_CREATE, array('task' => $tf->getDetails(1)));

        $this->assertTrue($wn->hasNotifications(1));

        $this->assertFalse($wn->markAsRead(2, 1));
        $this->assertTrue($wn->markAsRead(1, 1));

        $this->assertFalse($wn->hasNotifications(1));
        $this->assertFalse($wn->markAsRead(1, 1));
    }

    public function testGetAll()
    {
        $wn = new UserUnreadNotificationModel($this->container);
        $p = new ProjectModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $tc = new TaskCreationModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $wn->create(1, TaskModel::EVENT_CREATE, array('task' => $tf->getDetails(1)));
        $wn->create(1, TaskModel::EVENT_CREATE, array('task' => $tf->getDetails(1)));

        $this->assertEmpty($wn->getAll(2));

        $notifications = $wn->getAll(1);
        $this->assertCount(2, $notifications);
        $this->assertArrayHasKey('title', $notifications[0]);
        $this->assertTrue(is_array($notifications[0]['event_data']));
    }

    public function testGetOne()
    {
        $wn = new UserUnreadNotificationModel($this->container);
        $p = new ProjectModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $tc = new TaskCreationModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $wn->create(1, TaskModel::EVENT_CREATE, array('task' => $tf->getDetails(1)));
        $wn->create(1, TaskModel::EVENT_CREATE, array('task' => $tf->getDetails(1)));

        $this->assertEmpty($wn->getAll(2));

        $notification = $wn->getById(1);
        $this->assertArrayHasKey('title', $notification);
        $this->assertTrue(is_array($notification['event_data']));
    }
}
