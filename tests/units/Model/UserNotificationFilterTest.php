<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\User;
use Kanboard\Model\Project;
use Kanboard\Model\UserNotificationFilter;
use Kanboard\Model\UserNotification;

class UserNotificationFilterTest extends Base
{
    public function testGetFilters()
    {
        $nf = new UserNotificationFilter($this->container);
        $filters = $nf->getFilters();
        $this->assertArrayHasKey(UserNotificationFilter::FILTER_NONE, $filters);
        $this->assertArrayHasKey(UserNotificationFilter::FILTER_BOTH, $filters);
        $this->assertArrayHasKey(UserNotificationFilter::FILTER_CREATOR, $filters);
        $this->assertArrayHasKey(UserNotificationFilter::FILTER_ASSIGNEE, $filters);
    }

    public function testSaveProjectFilter()
    {
        $nf = new UserNotificationFilter($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));

        $this->assertEmpty($nf->getSelectedProjects(1));
        $nf->saveSelectedProjects(1, array(1, 2));
        $this->assertEquals(array(1, 2), $nf->getSelectedProjects(1));
    }

    public function testSaveUserFilter()
    {
        $nf = new UserNotificationFilter($this->container);

        $this->assertEquals(UserNotificationFilter::FILTER_BOTH, $nf->getSelectedFilter(1));
        $nf->saveFilter(1, UserNotificationFilter::FILTER_CREATOR);
        $this->assertEquals(UserNotificationFilter::FILTER_CREATOR, $nf->getSelectedFilter(1));
    }

    public function testFilterNone()
    {
        $u = new User($this->container);
        $n = new UserNotificationFilter($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user1', 'notifications_filter' => UserNotificationFilter::FILTER_NONE)));
        $this->assertTrue($n->filterNone($u->getById(2), array()));

        $this->assertEquals(3, $u->create(array('username' => 'user2', 'notifications_filter' => UserNotificationFilter::FILTER_BOTH)));
        $this->assertFalse($n->filterNone($u->getById(3), array()));
    }

    public function testFilterCreator()
    {
        $u = new User($this->container);
        $n = new UserNotificationFilter($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user1', 'notifications_filter' => UserNotificationFilter::FILTER_CREATOR)));
        $this->assertTrue($n->filterCreator($u->getById(2), array('task' => array('creator_id' => 2))));

        $this->assertEquals(3, $u->create(array('username' => 'user2', 'notifications_filter' => UserNotificationFilter::FILTER_CREATOR)));
        $this->assertFalse($n->filterCreator($u->getById(3), array('task' => array('creator_id' => 1))));

        $this->assertEquals(4, $u->create(array('username' => 'user3', 'notifications_filter' => UserNotificationFilter::FILTER_NONE)));
        $this->assertFalse($n->filterCreator($u->getById(4), array('task' => array('creator_id' => 2))));
    }

    public function testFilterAssignee()
    {
        $u = new User($this->container);
        $n = new UserNotificationFilter($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user1', 'notifications_filter' => UserNotificationFilter::FILTER_ASSIGNEE)));
        $this->assertTrue($n->filterAssignee($u->getById(2), array('task' => array('owner_id' => 2))));

        $this->assertEquals(3, $u->create(array('username' => 'user2', 'notifications_filter' => UserNotificationFilter::FILTER_ASSIGNEE)));
        $this->assertFalse($n->filterAssignee($u->getById(3), array('task' => array('owner_id' => 1))));

        $this->assertEquals(4, $u->create(array('username' => 'user3', 'notifications_filter' => UserNotificationFilter::FILTER_NONE)));
        $this->assertFalse($n->filterAssignee($u->getById(4), array('task' => array('owner_id' => 2))));
    }

    public function testFilterBoth()
    {
        $u = new User($this->container);
        $n = new UserNotificationFilter($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user1', 'notifications_filter' => UserNotificationFilter::FILTER_BOTH)));
        $this->assertTrue($n->filterBoth($u->getById(2), array('task' => array('owner_id' => 2, 'creator_id' => 1))));
        $this->assertTrue($n->filterBoth($u->getById(2), array('task' => array('owner_id' => 0, 'creator_id' => 2))));

        $this->assertEquals(3, $u->create(array('username' => 'user2', 'notifications_filter' => UserNotificationFilter::FILTER_BOTH)));
        $this->assertFalse($n->filterBoth($u->getById(3), array('task' => array('owner_id' => 1, 'creator_id' => 1))));
        $this->assertFalse($n->filterBoth($u->getById(3), array('task' => array('owner_id' => 2, 'creator_id' => 1))));

        $this->assertEquals(4, $u->create(array('username' => 'user3', 'notifications_filter' => UserNotificationFilter::FILTER_NONE)));
        $this->assertFalse($n->filterBoth($u->getById(4), array('task' => array('owner_id' => 2, 'creator_id' => 1))));
    }

    public function testFilterProject()
    {
        $u = new User($this->container);
        $n = new UserNotification($this->container);
        $nf = new UserNotificationFilter($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));

        // No project selected
        $this->assertTrue($nf->filterProject($u->getById(1), array()));

        // User that select only some projects
        $this->assertEquals(2, $u->create(array('username' => 'user2', 'notifications_filter' => UserNotificationFilter::FILTER_NONE)));
        $n->saveSettings(2, array('notifications_enabled' => 1, 'notification_projects' => array(2 => true)));

        $this->assertFalse($nf->filterProject($u->getById(2), array('task' => array('project_id' => 1))));
        $this->assertTrue($nf->filterProject($u->getById(2), array('task' => array('project_id' => 2))));
    }

    public function testFilterUserWithNoFilter()
    {
        $u = new User($this->container);
        $n = new UserNotificationFilter($this->container);
        $p = new Project($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user2', 'notifications_filter' => UserNotificationFilter::FILTER_NONE)));

        $this->assertTrue($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1))));
    }

    public function testFilterUserWithAssigneeFilter()
    {
        $u = new User($this->container);
        $n = new UserNotificationFilter($this->container);
        $p = new Project($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user2', 'notifications_filter' => UserNotificationFilter::FILTER_ASSIGNEE)));

        $this->assertTrue($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'owner_id' => 2))));
        $this->assertFalse($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'owner_id' => 1))));
    }

    public function testFilterUserWithCreatorFilter()
    {
        $u = new User($this->container);
        $n = new UserNotificationFilter($this->container);
        $p = new Project($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user2', 'notifications_filter' => UserNotificationFilter::FILTER_CREATOR)));

        $this->assertTrue($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 2))));
        $this->assertFalse($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 1))));
    }

    public function testFilterUserWithBothFilter()
    {
        $u = new User($this->container);
        $n = new UserNotificationFilter($this->container);
        $p = new Project($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user2', 'notifications_filter' => UserNotificationFilter::FILTER_BOTH)));

        $this->assertTrue($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 2, 'owner_id' => 3))));
        $this->assertTrue($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 0, 'owner_id' => 2))));
        $this->assertFalse($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 4, 'owner_id' => 1))));
        $this->assertFalse($n->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 5, 'owner_id' => 0))));
    }

    public function testFilterUserWithBothFilterAndProjectSelected()
    {
        $u = new User($this->container);
        $n = new UserNotification($this->container);
        $nf = new UserNotificationFilter($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));

        $this->assertEquals(2, $u->create(array('username' => 'user2', 'notifications_filter' => UserNotificationFilter::FILTER_BOTH)));

        $n->saveSettings(2, array('notifications_enabled' => 1, 'notification_projects' => array(2 => true)));

        $this->assertFalse($nf->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 2, 'owner_id' => 3))));
        $this->assertFalse($nf->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 1, 'creator_id' => 0, 'owner_id' => 2))));

        $this->assertTrue($nf->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 2, 'creator_id' => 2, 'owner_id' => 3))));
        $this->assertTrue($nf->shouldReceiveNotification($u->getById(2), array('task' => array('project_id' => 2, 'creator_id' => 0, 'owner_id' => 2))));
    }
}
