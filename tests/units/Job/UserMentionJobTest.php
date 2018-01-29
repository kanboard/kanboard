<?php

use Kanboard\Core\Security\Role;
use Kanboard\Event\TaskEvent;
use Kanboard\Job\UserMentionJob;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\UserModel;

require_once __DIR__.'/../Base.php';

class UserMentionJobTest extends Base
{
    public function testGetMentionedUsersWithNoMentions()
    {
        $userModel = new UserModel($this->container);
        $userMentionJob = new UserMentionJob($this->container);

        $this->assertNotFalse($userModel->create(array('username' => 'user1')));
        $this->assertEmpty($userMentionJob->getMentionedUsers('test'));
    }

    public function testGetMentionedUsersWithNotficationDisabled()
    {
        $userModel = new UserModel($this->container);
        $userMentionJob = new UserMentionJob($this->container);

        $this->assertNotFalse($userModel->create(array('username' => 'user1')));
        $this->assertEmpty($userMentionJob->getMentionedUsers('test @user1'));
    }

    public function testGetMentionedUsersWithNotficationEnabled()
    {
        $userModel = new UserModel($this->container);
        $userMentionJob = new UserMentionJob($this->container);

        $this->assertNotFalse($userModel->create(array('username' => 'user1')));
        $this->assertNotFalse($userModel->create(array('username' => 'user2', 'name' => 'Foobar', 'notifications_enabled' => 1)));

        $users = $userMentionJob->getMentionedUsers('test @user2');
        $this->assertCount(1, $users);
        $this->assertEquals('user2', $users[0]['username']);
        $this->assertEquals('Foobar', $users[0]['name']);
        $this->assertEquals('', $users[0]['email']);
        $this->assertEquals('', $users[0]['language']);
    }

    public function testGetMentionedUsersWithNotficationEnabledAndPunctuationMarks()
    {
        $userModel = new UserModel($this->container);
        $userMentionJob = new UserMentionJob($this->container);

        $this->assertNotFalse($userModel->create(array('username' => 'user1')));
        $this->assertNotFalse($userModel->create(array('username' => 'user2', 'name' => 'Foobar', 'notifications_enabled' => 1)));
        $this->assertNotFalse($userModel->create(array('username' => 'user3.with.dot', 'notifications_enabled' => 1)));

        $users = $userMentionJob->getMentionedUsers('test @user2, test, @user3.with.dot.');
        $this->assertCount(2, $users);
        $this->assertEquals('user2', $users[0]['username']);
        $this->assertEquals('Foobar', $users[0]['name']);
        $this->assertEquals('', $users[0]['email']);
        $this->assertEquals('', $users[0]['language']);

        $this->assertEquals('user3.with.dot', $users[1]['username']);
        $this->assertEquals('', $users[1]['name']);
        $this->assertEquals('', $users[1]['email']);
        $this->assertEquals('', $users[1]['language']);
    }

    public function testGetMentionedUsersWithNotficationEnabledAndUserLoggedIn()
    {
        $_SESSION['user'] = array('id' => 3);
        $userModel = new UserModel($this->container);
        $userMentionJob = new UserMentionJob($this->container);

        $this->assertNotFalse($userModel->create(array('username' => 'user1')));
        $this->assertNotFalse($userModel->create(array('username' => 'user2', 'name' => 'Foobar', 'notifications_enabled' => 1)));

        $this->assertEmpty($userMentionJob->getMentionedUsers('test @user2'));
    }

    public function testFireEventsWithMultipleMentions()
    {
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $userModel = new UserModel($this->container);
        $userMentionJob = new UserMentionJob($this->container);
        $event = new TaskEvent(array('task' => array('project_id' => 1)));

        $this->assertEquals(2, $userModel->create(array('username' => 'user1', 'name' => 'User 1', 'notifications_enabled' => 1)));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2', 'name' => 'User 2', 'notifications_enabled' => 1)));

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 3, Role::PROJECT_MEMBER));

        $this->container['dispatcher']->addListener(TaskModel::EVENT_USER_MENTION, array($this, 'onUserMention'));

        $userMentionJob->execute('test @user1 @user2', TaskModel::EVENT_USER_MENTION, $event->getAll());

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskModel::EVENT_USER_MENTION.'.UserMentionJobTest::onUserMention', $called);
    }

    public function onUserMention($event)
    {
        $this->assertInstanceOf('Kanboard\Event\GenericEvent', $event);
        $this->assertEquals(array('id' => '3', 'username' => 'user2', 'name' => 'User 2', 'email' => null, 'language' => null), $event['mention']);
    }
}
