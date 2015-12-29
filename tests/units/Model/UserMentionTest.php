<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\Security\Role;
use Kanboard\Event\GenericEvent;
use Kanboard\Model\User;
use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectUserRole;
use Kanboard\Model\UserMention;

class UserMentionTest extends Base
{
    public function testGetMentionedUsersWithNoMentions()
    {
        $userModel = new User($this->container);
        $userMentionModel = new UserMention($this->container);

        $this->assertNotFalse($userModel->create(array('username' => 'user1')));
        $this->assertEmpty($userMentionModel->getMentionedUsers('test'));
    }

    public function testGetMentionedUsersWithNotficationDisabled()
    {
        $userModel = new User($this->container);
        $userMentionModel = new UserMention($this->container);

        $this->assertNotFalse($userModel->create(array('username' => 'user1')));
        $this->assertEmpty($userMentionModel->getMentionedUsers('test @user1'));
    }

    public function testGetMentionedUsersWithNotficationEnabled()
    {
        $userModel = new User($this->container);
        $userMentionModel = new UserMention($this->container);

        $this->assertNotFalse($userModel->create(array('username' => 'user1')));
        $this->assertNotFalse($userModel->create(array('username' => 'user2', 'name' => 'Foobar', 'notifications_enabled' => 1)));

        $users = $userMentionModel->getMentionedUsers('test @user2');
        $this->assertCount(1, $users);
        $this->assertEquals('user2', $users[0]['username']);
        $this->assertEquals('Foobar', $users[0]['name']);
        $this->assertEquals('', $users[0]['email']);
        $this->assertEquals('', $users[0]['language']);
    }

    public function testGetMentionedUsersWithNotficationEnabledAndUserLoggedIn()
    {
        $this->container['sessionStorage']->user = array('id' => 3);
        $userModel = new User($this->container);
        $userMentionModel = new UserMention($this->container);

        $this->assertNotFalse($userModel->create(array('username' => 'user1')));
        $this->assertNotFalse($userModel->create(array('username' => 'user2', 'name' => 'Foobar', 'notifications_enabled' => 1)));

        $this->assertEmpty($userMentionModel->getMentionedUsers('test @user2'));
    }

    public function testFireEventsWithMultipleMentions()
    {
        $projectUserRoleModel = new ProjectUserRole($this->container);
        $projectModel = new Project($this->container);
        $userModel = new User($this->container);
        $userMentionModel = new UserMention($this->container);
        $event = new GenericEvent(array('project_id' => 1));

        $this->assertEquals(2, $userModel->create(array('username' => 'user1', 'name' => 'User 1', 'notifications_enabled' => 1)));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2', 'name' => 'User 2', 'notifications_enabled' => 1)));

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 3, Role::PROJECT_MEMBER));

        $this->container['dispatcher']->addListener(Task::EVENT_USER_MENTION, array($this, 'onUserMention'));

        $userMentionModel->fireEvents('test @user1 @user2', Task::EVENT_USER_MENTION, $event);

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(Task::EVENT_USER_MENTION.'.UserMentionTest::onUserMention', $called);
    }

    public function testFireEventsWithNoProjectId()
    {
        $projectUserRoleModel = new ProjectUserRole($this->container);
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $userModel = new User($this->container);
        $userMentionModel = new UserMention($this->container);
        $event = new GenericEvent(array('task_id' => 1));

        $this->assertEquals(2, $userModel->create(array('username' => 'user1', 'name' => 'User 1', 'notifications_enabled' => 1)));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2', 'name' => 'User 2', 'notifications_enabled' => 1)));

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 3, Role::PROJECT_MEMBER));

        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'Task 1')));

        $this->container['dispatcher']->addListener(Task::EVENT_USER_MENTION, array($this, 'onUserMention'));

        $userMentionModel->fireEvents('test @user1 @user2', Task::EVENT_USER_MENTION, $event);

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(Task::EVENT_USER_MENTION.'.UserMentionTest::onUserMention', $called);
    }

    public function onUserMention($event)
    {
        $this->assertInstanceOf('Kanboard\Event\GenericEvent', $event);
        $this->assertEquals(array('id' => '3', 'username' => 'user2', 'name' => 'User 2', 'email' => null, 'language' => null), $event['mention']);
    }
}
