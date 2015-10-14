<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskFinder;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\Subtask;
use Kanboard\Model\Comment;
use Kanboard\Model\User;
use Kanboard\Model\File;
use Kanboard\Model\Project;
use Kanboard\Model\Task;
use Kanboard\Model\ProjectPermission;
use Kanboard\Model\Notification;
use Kanboard\Model\NotificationFilter;
use Kanboard\Model\NotificationType;
use Kanboard\Subscriber\NotificationSubscriber;

class NotificationTest extends Base
{
    public function testEnableDisableNotification()
    {
        $u = new User($this->container);
        $p = new Project($this->container);
        $n = new Notification($this->container);
        $pp = new ProjectPermission($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertEquals(2, $u->create(array('username' => 'user1')));
        $this->assertTrue($pp->addMember(1, 2));

        $this->assertEmpty($n->getUsersWithNotificationEnabled(1));
        $n->enableNotification(2);
        $this->assertNotEmpty($n->getUsersWithNotificationEnabled(1));
        $n->disableNotification(2);
        $this->assertEmpty($n->getUsersWithNotificationEnabled(1));
    }

    public function testReadWriteSettings()
    {
        $n = new Notification($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));

        $n->saveSettings(1, array(
            'notifications_enabled' => 1,
            'notifications_filter' => NotificationFilter::FILTER_CREATOR,
            'notification_types' => array(NotificationType::TYPE_EMAIL => 1),
            'notification_projects' => array(),
        ));

        $settings = $n->readSettings(1);
        $this->assertNotEmpty($settings);
        $this->assertEquals(1, $settings['notifications_enabled']);
        $this->assertEquals(NotificationFilter::FILTER_CREATOR, $settings['notifications_filter']);
        $this->assertEquals(array(NotificationType::TYPE_EMAIL), $settings['notification_types']);
        $this->assertEmpty($settings['notification_projects']);

        $n->saveSettings(1, array(
            'notifications_enabled' => 0,
        ));

        $settings = $n->readSettings(1);
        $this->assertNotEmpty($settings);
        $this->assertEquals(0, $settings['notifications_enabled']);

        $n->saveSettings(1, array(
            'notifications_enabled' => 1,
            'notifications_filter' => NotificationFilter::FILTER_ASSIGNEE,
            'notification_types' => array(NotificationType::TYPE_WEB => 1, NotificationType::TYPE_EMAIL => 1),
            'notification_projects' => array(1 => 1),
        ));

        $settings = $n->readSettings(1);
        $this->assertNotEmpty($settings);
        $this->assertEquals(1, $settings['notifications_enabled']);
        $this->assertEquals(NotificationFilter::FILTER_ASSIGNEE, $settings['notifications_filter']);
        $this->assertEquals(array(NotificationType::TYPE_EMAIL, NotificationType::TYPE_WEB), $settings['notification_types']);
        $this->assertEquals(array(1), $settings['notification_projects']);
    }

    public function testGetProjectMembersWithNotifications()
    {
        $u = new User($this->container);
        $p = new Project($this->container);
        $n = new Notification($this->container);
        $pp = new ProjectPermission($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));

        // Email + Notifications enabled
        $this->assertNotFalse($u->create(array('username' => 'user1', 'email' => 'user1@here', 'notifications_enabled' => 1)));

        // No email + Notifications enabled
        $this->assertNotFalse($u->create(array('username' => 'user2', 'email' => '', 'notifications_enabled' => 1)));

        // Email + Notifications enabled
        $this->assertNotFalse($u->create(array('username' => 'user3', 'email' => 'user3@here', 'notifications_enabled' => 1)));

        // No email + notifications disabled
        $this->assertNotFalse($u->create(array('username' => 'user4')));

        // Nobody is member of any projects
        $this->assertEmpty($pp->getMembers(1));
        $this->assertEmpty($n->getUsersWithNotificationEnabled(1));

        // We allow all users to be member of our projects
        $this->assertTrue($pp->addMember(1, 1));
        $this->assertTrue($pp->addMember(1, 2));
        $this->assertTrue($pp->addMember(1, 3));
        $this->assertTrue($pp->addMember(1, 4));

        $this->assertNotEmpty($pp->getMembers(1));
        $users = $n->getUsersWithNotificationEnabled(1);

        $this->assertNotEmpty($users);
        $this->assertCount(3, $users);
        $this->assertEquals('user1@here', $users[0]['email']);
        $this->assertEquals('', $users[1]['email']);
        $this->assertEquals('user3@here', $users[2]['email']);
    }

    public function testGetUsersWithNotificationsWhenEverybodyAllowed()
    {
        $u = new User($this->container);
        $p = new Project($this->container);
        $n = new Notification($this->container);
        $pp = new ProjectPermission($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1', 'is_everybody_allowed' => 1)));
        $this->assertTrue($pp->isEverybodyAllowed(1));

        // Email + Notifications enabled
        $this->assertNotFalse($u->create(array('username' => 'user1', 'email' => 'user1@here', 'notifications_enabled' => 1)));

        // No email + Notifications enabled
        $this->assertNotFalse($u->create(array('username' => 'user2', 'email' => '', 'notifications_enabled' => 1)));

        // Email + Notifications enabled
        $this->assertNotFalse($u->create(array('username' => 'user3', 'email' => 'user3@here', 'notifications_enabled' => 1)));

        // No email + notifications disabled
        $this->assertNotFalse($u->create(array('username' => 'user4')));

        $users = $n->getUsersWithNotificationEnabled(1);

        $this->assertNotEmpty($users);
        $this->assertCount(3, $users);
        $this->assertEquals('user1@here', $users[0]['email']);
        $this->assertEquals('', $users[1]['email']);
        $this->assertEquals('user3@here', $users[2]['email']);
    }

    public function testSendNotifications()
    {
        $u = new User($this->container);
        $n = new Notification($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $pp = new ProjectPermission($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1', 'is_everybody_allowed' => 1)));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertTrue($u->update(array('id' => 1, 'email' => 'test@localhost')));
        $this->assertTrue($pp->isEverybodyAllowed(1));

        $n->saveSettings(1, array(
            'notifications_enabled' => 1,
            'notifications_filter' => NotificationFilter::FILTER_NONE,
            'notification_types' => array(NotificationType::TYPE_WEB => 1, NotificationType::TYPE_EMAIL => 1),
        ));

        $this->container['emailNotification'] = $this
            ->getMockBuilder('\Kanboard\Model\EmailNotification')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('send'))
            ->getMock();

        $this->container['webNotification'] = $this
            ->getMockBuilder('\Kanboard\Model\WebNotification')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('send'))
            ->getMock();

        $this->container['emailNotification']
            ->expects($this->once())
            ->method('send');

        $this->container['webNotification']
            ->expects($this->once())
            ->method('send');

        $n->sendNotifications(Task::EVENT_CREATE, array('task' => $tf->getDetails(1)));
    }
}
