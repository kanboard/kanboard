<?php

require_once __DIR__.'/Base.php';

use Model\User;
use Model\Project;
use Model\ProjectPermission;
use Model\Notification;

class NotificationTest extends Base
{
    public function testFilterNone()
    {
        $u = new User($this->container);
        $n = new Notification($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user1', 'notifications_filter' => Notification::FILTER_NONE)));
        $this->assertTrue($n->filterNone($u->getById(2), array()));

        $this->assertEquals(3, $u->create(array('username' => 'user2', 'notifications_filter' => Notification::FILTER_BOTH)));
        $this->assertFalse($n->filterNone($u->getById(3), array()));
    }

    public function testFilterCreator()
    {
        $u = new User($this->container);
        $n = new Notification($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user1', 'notifications_filter' => Notification::FILTER_CREATOR)));
        $this->assertTrue($n->filterCreator($u->getById(2), array('task' => array('creator_id' => 2))));

        $this->assertEquals(3, $u->create(array('username' => 'user2', 'notifications_filter' => Notification::FILTER_CREATOR)));
        $this->assertFalse($n->filterCreator($u->getById(3), array('task' => array('creator_id' => 1))));

        $this->assertEquals(4, $u->create(array('username' => 'user3', 'notifications_filter' => Notification::FILTER_NONE)));
        $this->assertFalse($n->filterCreator($u->getById(4), array('task' => array('creator_id' => 2))));
    }

    public function testFilterAssignee()
    {
        $u = new User($this->container);
        $n = new Notification($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user1', 'notifications_filter' => Notification::FILTER_ASSIGNEE)));
        $this->assertTrue($n->filterAssignee($u->getById(2), array('task' => array('owner_id' => 2))));

        $this->assertEquals(3, $u->create(array('username' => 'user2', 'notifications_filter' => Notification::FILTER_ASSIGNEE)));
        $this->assertFalse($n->filterAssignee($u->getById(3), array('task' => array('owner_id' => 1))));

        $this->assertEquals(4, $u->create(array('username' => 'user3', 'notifications_filter' => Notification::FILTER_NONE)));
        $this->assertFalse($n->filterAssignee($u->getById(4), array('task' => array('owner_id' => 2))));
    }

    public function testFilterBoth()
    {
        $u = new User($this->container);
        $n = new Notification($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user1', 'notifications_filter' => Notification::FILTER_BOTH)));
        $this->assertTrue($n->filterBoth($u->getById(2), array('task' => array('owner_id' => 2, 'creator_id' => 1))));
        $this->assertTrue($n->filterBoth($u->getById(2), array('task' => array('owner_id' => 0, 'creator_id' => 2))));

        $this->assertEquals(3, $u->create(array('username' => 'user2', 'notifications_filter' => Notification::FILTER_BOTH)));
        $this->assertFalse($n->filterBoth($u->getById(3), array('task' => array('owner_id' => 1, 'creator_id' => 1))));
        $this->assertFalse($n->filterBoth($u->getById(3), array('task' => array('owner_id' => 2, 'creator_id' => 1))));

        $this->assertEquals(4, $u->create(array('username' => 'user3', 'notifications_filter' => Notification::FILTER_NONE)));
        $this->assertFalse($n->filterBoth($u->getById(4), array('task' => array('owner_id' => 2, 'creator_id' => 1))));
    }

    public function testFilterProject()
    {
        $u = new User($this->container);
        $n = new Notification($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));

        // No project selected
        $this->assertTrue($n->filterProject($u->getById(1), array()));

        // User that select only some projects
        $this->assertEquals(2, $u->create(array('username' => 'user2', 'notifications_filter' => Notification::FILTER_NONE)));
        $n->saveSettings(2, array('notifications_enabled' => 1, 'projects' => array(2 => true)));

        $this->assertFalse($n->filterProject($u->getById(2), array('task' => array('project_id' => 1))));
        $this->assertTrue($n->filterProject($u->getById(2), array('task' => array('project_id' => 2))));
    }

    public function testFilterUserWithNoFilter()
    {
        $u = new User($this->container);
        $n = new Notification($this->container);
        $p = new Project($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user2', 'notifications_filter' => Notification::FILTER_NONE)));

        $this->assertTrue($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1))));
    }

    public function testFilterUserWithAssigneeFilter()
    {
        $u = new User($this->container);
        $n = new Notification($this->container);
        $p = new Project($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user2', 'notifications_filter' => Notification::FILTER_ASSIGNEE)));

        $this->assertTrue($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'owner_id' => 2))));
        $this->assertFalse($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'owner_id' => 1))));
    }

    public function testFilterUserWithCreatorFilter()
    {
        $u = new User($this->container);
        $n = new Notification($this->container);
        $p = new Project($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user2', 'notifications_filter' => Notification::FILTER_CREATOR)));

        $this->assertTrue($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 2))));
        $this->assertFalse($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 1))));
    }

    public function testFilterUserWithBothFilter()
    {
        $u = new User($this->container);
        $n = new Notification($this->container);
        $p = new Project($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user2', 'notifications_filter' => Notification::FILTER_BOTH)));

        $this->assertTrue($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 2, 'owner_id' => 3))));
        $this->assertTrue($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 0, 'owner_id' => 2))));
        $this->assertFalse($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 4, 'owner_id' => 1))));
        $this->assertFalse($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 5, 'owner_id' => 0))));
    }

    public function testFilterUserWithBothFilterAndProjectSelected()
    {
        $u = new User($this->container);
        $n = new Notification($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));

        $this->assertEquals(2, $u->create(array('username' => 'user2', 'notifications_filter' => Notification::FILTER_BOTH)));

        $n->saveSettings(2, array('notifications_enabled' => 1, 'projects' => array(2 => true)));

        $this->assertFalse($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 2, 'owner_id' => 3))));
        $this->assertFalse($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 0, 'owner_id' => 2))));

        $this->assertTrue($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 2, 'creator_id' => 2, 'owner_id' => 3))));
        $this->assertTrue($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 2, 'creator_id' => 0, 'owner_id' => 2))));
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
        $this->assertEquals(2, count($users));
        $this->assertEquals('user1@here', $users[0]['email']);
        $this->assertEquals('user3@here', $users[1]['email']);
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
        $this->assertEquals(2, count($users));
        $this->assertEquals('user1@here', $users[0]['email']);
        $this->assertEquals('user3@here', $users[1]['email']);
    }

    public function testGetMailContent()
    {
        $n = new Notification($this->container);
        $this->assertNotEmpty($n->getMailContent('task.open', array('task' => array('id' => 2, 'title' => 'blah'))));
    }

    public function testGetEmailSubject()
    {
        $n = new Notification($this->container);

        $this->assertEquals(
            '[test][Task opened] blah (#2)',
            $n->getMailSubject('task.open', array('task' => array('id' => 2, 'title' => 'blah', 'project_name' => 'test')))
        );
    }

    public function testSendNotificationsToCreator()
    {
        $u = new User($this->container);
        $p = new Project($this->container);
        $n = new Notification($this->container);
        $pp = new ProjectPermission($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertEquals(2, $u->create(array('username' => 'user1', 'email' => 'user1@here', 'notifications_enabled' => 1)));
        $this->assertTrue($pp->addMember(1, 2));

        $n->sendNotifications('task.open', array('task' => array(
            'id' => 2, 'title' => 'blah', 'project_name' => 'test', 'project_id' => 1, 'owner_id' => 0, 'creator_id' => 2
        )));

        $this->assertEquals('user1@here', $this->container['emailClient']->email);
        $this->assertEquals('user1', $this->container['emailClient']->name);
        $this->assertEquals('[test][Task opened] blah (#2)', $this->container['emailClient']->subject);
        $this->assertNotEmpty($this->container['emailClient']->html);
    }

    public function testSendNotificationsToAnotherAssignee()
    {
        $u = new User($this->container);
        $p = new Project($this->container);
        $n = new Notification($this->container);
        $pp = new ProjectPermission($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertEquals(2, $u->create(array('username' => 'user1', 'email' => 'user1@here', 'notifications_enabled' => 1)));
        $this->assertTrue($pp->addMember(1, 2));

        $n->sendNotifications('task.open', array('task' => array(
            'id' => 2, 'title' => 'blah', 'project_name' => 'test', 'project_id' => 1, 'owner_id' => 1, 'creator_id' => 1
        )));

        $this->assertEmpty($this->container['emailClient']->email);
    }

    public function testSendNotificationsToNotMember()
    {
        $u = new User($this->container);
        $p = new Project($this->container);
        $n = new Notification($this->container);
        $pp = new ProjectPermission($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertEquals(2, $u->create(array('username' => 'user1', 'email' => 'user1@here', 'notifications_enabled' => 1)));

        $n->sendNotifications('task.open', array('task' => array(
            'id' => 2, 'title' => 'blah', 'project_name' => 'test', 'project_id' => 1, 'owner_id' => 0, 'creator_id' => 2
        )));

        $this->assertEmpty($this->container['emailClient']->email);
    }
}
