<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\UserModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\CommentModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Core\Security\Role;

class UserModelTest extends Base
{
    public function testGetByEmail()
    {
        $userModel = new UserModel($this->container);
        $this->assertNotFalse($userModel->create(array('username' => 'user1', 'password' => '123456', 'email' => 'user1@localhost')));
        $this->assertNotFalse($userModel->create(array('username' => 'user2', 'password' => '123456', 'email' => '')));

        $this->assertNotEmpty($userModel->getByEmail('user1@localhost'));
        $this->assertEmpty($userModel->getByEmail(''));
    }

    public function testGetByExternalId()
    {
        $userModel = new UserModel($this->container);
        $this->assertNotFalse($userModel->create(array('username' => 'user1', 'password' => '123456', 'gitlab_id' => '1234')));

        $this->assertNotEmpty($userModel->getByExternalId('gitlab_id', '1234'));
        $this->assertEmpty($userModel->getByExternalId('gitlab_id', ''));

        $userModel = new UserModel($this->container);
        $this->assertNotFalse($userModel->create(array('username' => 'user2', 'password' => '123456', 'github_id' => 'plop')));
        $this->assertNotFalse($userModel->create(array('username' => 'user3', 'password' => '123456', 'github_id' => '')));

        $this->assertNotEmpty($userModel->getByExternalId('github_id', 'plop'));
        $this->assertEmpty($userModel->getByExternalId('github_id', ''));

        $userModel = new UserModel($this->container);
        $this->assertNotFalse($userModel->create(array('username' => 'user4', 'password' => '123456', 'google_id' => '1234')));
        $this->assertNotFalse($userModel->create(array('username' => 'user5', 'password' => '123456', 'google_id' => '')));

        $this->assertNotEmpty($userModel->getByExternalId('google_id', '1234'));
        $this->assertEmpty($userModel->getByExternalId('google_id', ''));
    }

    public function testGetByToken()
    {
        $userModel = new UserModel($this->container);
        $this->assertNotFalse($userModel->create(array('username' => 'user1', 'token' => 'random')));
        $this->assertNotFalse($userModel->create(array('username' => 'user2', 'token' => '')));

        $this->assertNotEmpty($userModel->getByToken('random'));
        $this->assertEmpty($userModel->getByToken(''));
    }

    public function testGetByUsername()
    {
        $userModel = new UserModel($this->container);
        $this->assertNotFalse($userModel->create(array('username' => 'user1')));

        $this->assertNotEmpty($userModel->getByUsername('user1'));
        $this->assertEmpty($userModel->getByUsername('user2'));
        $this->assertEmpty($userModel->getByUsername(''));
    }

    public function testExists()
    {
        $userModel = new UserModel($this->container);
        $this->assertNotFalse($userModel->create(array('username' => 'user1')));

        $this->assertTrue($userModel->exists(1));
        $this->assertTrue($userModel->exists(2));
        $this->assertFalse($userModel->exists(3));
    }

    public function testCount()
    {
        $userModel = new UserModel($this->container);
        $this->assertNotFalse($userModel->create(array('username' => 'user1')));
        $this->assertEquals(2, $userModel->count());
    }

    public function testGetAll()
    {
        $userModel = new UserModel($this->container);
        $this->assertEquals(2, $userModel->create(array('username' => 'you')));
        $this->assertEquals(3, $userModel->create(array('username' => 'me', 'name' => 'Me')));

        $users = $userModel->getAll();
        $this->assertCount(3, $users);
        $this->assertEquals('admin', $users[0]['username']);
        $this->assertEquals('me', $users[1]['username']);
        $this->assertEquals('you', $users[2]['username']);
    }

    public function testGetActiveUsersList()
    {
        $userModel = new UserModel($this->container);
        $this->assertEquals(2, $userModel->create(array('username' => 'you')));
        $this->assertEquals(3, $userModel->create(array('username' => 'me', 'name' => 'Me too')));
        $this->assertEquals(4, $userModel->create(array('username' => 'foobar', 'is_active' => 0)));

        $users = $userModel->getActiveUsersList();

        $expected = array(
            1 => 'admin',
            3 => 'Me too',
            2 => 'you',
        );

        $this->assertEquals($expected, $users);

        $users = $userModel->getActiveUsersList(true);

        $expected = array(
            UserModel::EVERYBODY_ID => 'Everybody',
            1                       => 'admin',
            3                       => 'Me too',
            2                       => 'you',
        );

        $this->assertEquals($expected, $users);
    }

    public function testIsAdmin()
    {
        $userModel = new UserModel($this->container);
        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));

        $this->assertTrue($userModel->isAdmin(1));
        $this->assertFalse($userModel->isAdmin(2));
    }

    public function testPassword()
    {
        $password = 'test123';
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $this->assertNotEmpty($hash);
        $this->assertTrue(password_verify($password, $hash));
    }

    public function testPrepare()
    {
        $userModel = new UserModel($this->container);

        $input = array(
            'username' => 'user1',
            'password' => '1234',
            'confirmation' => '1234',
            'name' => 'me',
            'role' => Role::APP_ADMIN,
        );

        $userModel->prepare($input);
        $this->assertArrayNotHasKey('confirmation', $input);

        $this->assertArrayHasKey('password', $input);
        $this->assertNotEquals('1234', $input['password']);
        $this->assertNotEmpty($input['password']);

        $input = array(
            'username' => 'user1',
            'password' => '1234',
            'current_password' => 'bla',
            'confirmation' => '1234',
            'name' => 'me',
            'is_ldap_user' => '1',
        );

        $userModel->prepare($input);
        $this->assertArrayNotHasKey('confirmation', $input);
        $this->assertArrayNotHasKey('current_password', $input);

        $this->assertArrayHasKey('password', $input);
        $this->assertNotEquals('1234', $input['password']);
        $this->assertNotEmpty($input['password']);

        $this->assertArrayHasKey('is_ldap_user', $input);
        $this->assertEquals(1, $input['is_ldap_user']);

        $input = array(
            'id' => 2,
            'name' => 'me',
        );

        $userModel->prepare($input);
        $this->assertEquals(array('id' => 2, 'name' => 'me'), $input);

        $input = array(
            'gitlab_id' => '1234',
        );

        $userModel->prepare($input);
        $this->assertEquals(array('gitlab_id' => 1234), $input);

        $input = array(
            'gitlab_id' => '',
        );

        $userModel->prepare($input);
        $this->assertEquals(array('gitlab_id' => null), $input);

        $input = array(
            'gitlab_id' => 'something',
        );

        $userModel->prepare($input);
        $this->assertEquals(array('gitlab_id' => 0), $input);

        $input = array(
            'username' => 'something',
            'password' => ''
        );

        $userModel->prepare($input);
        $this->assertEquals(array('username' => 'something'), $input);
    }

    public function testCreate()
    {
        $userModel = new UserModel($this->container);
        $this->assertEquals(2, $userModel->create(array('username' => 'user #1', 'password' => '123456', 'name' => 'User')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user #2', 'is_ldap_user' => 1)));
        $this->assertEquals(4, $userModel->create(array('username' => 'user #3', 'role' => Role::APP_MANAGER)));
        $this->assertEquals(5, $userModel->create(array('username' => 'user #4', 'gitlab_id' => '', 'role' => Role::APP_ADMIN)));
        $this->assertEquals(6, $userModel->create(array('username' => 'user #5', 'gitlab_id' => '1234')));
        $this->assertFalse($userModel->create(array('username' => 'user #1')));

        $user = $userModel->getById(1);
        $this->assertEquals('admin', $user['username']);
        $this->assertEquals('', $user['name']);
        $this->assertEquals(Role::APP_ADMIN, $user['role']);
        $this->assertEquals(0, $user['is_ldap_user']);

        $user = $userModel->getById(2);
        $this->assertEquals('user #1', $user['username']);
        $this->assertEquals('User', $user['name']);
        $this->assertEquals(Role::APP_USER, $user['role']);
        $this->assertEquals(0, $user['is_ldap_user']);

        $user = $userModel->getById(3);
        $this->assertEquals('user #2', $user['username']);
        $this->assertEquals('', $user['name']);
        $this->assertEquals(Role::APP_USER, $user['role']);
        $this->assertEquals(1, $user['is_ldap_user']);

        $user = $userModel->getById(4);
        $this->assertEquals('user #3', $user['username']);
        $this->assertEquals(Role::APP_MANAGER, $user['role']);

        $user = $userModel->getById(5);
        $this->assertEquals('user #4', $user['username']);
        $this->assertEquals('', $user['gitlab_id']);
        $this->assertEquals(Role::APP_ADMIN, $user['role']);

        $user = $userModel->getById(6);
        $this->assertEquals('user #5', $user['username']);
        $this->assertEquals('1234', $user['gitlab_id']);
        $this->assertEquals(Role::APP_USER, $user['role']);
    }

    public function testUpdate()
    {
        $userModel = new UserModel($this->container);
        $this->assertEquals(2, $userModel->create(array('username' => 'toto', 'password' => '123456', 'name' => 'Toto')));
        $this->assertEquals(3, $userModel->create(array('username' => 'plop', 'gitlab_id' => '123')));

        $this->assertTrue($userModel->update(array('id' => 2, 'username' => 'biloute')));
        $this->assertTrue($userModel->update(array('id' => 3, 'gitlab_id' => '')));

        $user = $userModel->getById(2);
        $this->assertEquals('biloute', $user['username']);
        $this->assertEquals('Toto', $user['name']);
        $this->assertEquals(Role::APP_USER, $user['role']);
        $this->assertEquals(0, $user['is_ldap_user']);

        $user = $userModel->getById(3);
        $this->assertNotEmpty($user);
        $this->assertEquals(null, $user['gitlab_id']);
    }

    public function testRemove()
    {
        $userModel = new UserModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $commentModel = new CommentModel($this->container);

        $this->assertNotFalse($userModel->create(array('username' => 'toto', 'password' => '123456', 'name' => 'Toto')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'owner_id' => 2)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'Subtask #1', 'user_id' => 2, 'task_id' => 1)));
        $this->assertEquals(1, $commentModel->create(array('comment' => 'foobar', 'user_id' => 2, 'task_id' => 1)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['owner_id']);

        $this->assertTrue($userModel->remove(1));
        $this->assertTrue($userModel->remove(2));
        $this->assertFalse($userModel->remove(2));
        $this->assertFalse($userModel->remove(55));

        // Make sure that assigned tasks are unassigned after removing the user
        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(0, $task['owner_id']);

        // Make sure that assigned subtasks are unassigned after removing the user
        $subtask = $subtaskModel->getById(1);
        $this->assertEquals(1, $subtask['id']);
        $this->assertEquals(0, $subtask['user_id']);

        // Make sure that comments are not related to the user anymore
        $comment = $commentModel->getById(1);
        $this->assertEquals(1, $comment['id']);
        $this->assertEquals(0, $comment['user_id']);

        // Make sure that private projects are also removed
        $user_id1 = $userModel->create(array('username' => 'toto1', 'password' => '123456', 'name' => 'Toto'));
        $user_id2 = $userModel->create(array('username' => 'toto2', 'password' => '123456', 'name' => 'Toto'));
        $this->assertNotFalse($user_id1);
        $this->assertNotFalse($user_id2);
        $this->assertEquals(2, $projectModel->create(array('name' => 'Private project #1', 'is_private' => 1), $user_id1, true));
        $this->assertEquals(3, $projectModel->create(array('name' => 'Private project #2', 'is_private' => 1), $user_id2, true));

        $this->assertTrue($userModel->remove($user_id1));

        $this->assertNotEmpty($projectModel->getById(1));
        $this->assertNotEmpty($projectModel->getById(3));

        $this->assertEmpty($projectModel->getById(2));
    }

    public function testEnableDisablePublicAccess()
    {
        $userModel = new UserModel($this->container);
        $this->assertNotFalse($userModel->create(array('username' => 'toto', 'password' => '123456')));

        $user = $userModel->getById(2);
        $this->assertNotEmpty($user);
        $this->assertEquals('toto', $user['username']);
        $this->assertEmpty($user['token']);

        $this->assertTrue($userModel->enablePublicAccess(2));

        $user = $userModel->getById(2);
        $this->assertNotEmpty($user);
        $this->assertEquals('toto', $user['username']);
        $this->assertNotEmpty($user['token']);

        $this->assertTrue($userModel->disablePublicAccess(2));

        $user = $userModel->getById(2);
        $this->assertNotEmpty($user);
        $this->assertEquals('toto', $user['username']);
        $this->assertEmpty($user['token']);
    }

    public function testEnableDisable()
    {
        $userModel = new UserModel($this->container);
        $this->assertEquals(2, $userModel->create(array('username' => 'toto')));

        $this->assertTrue($userModel->isActive(2));
        $user = $userModel->getById(2);
        $this->assertEquals(1, $user['is_active']);

        $this->assertTrue($userModel->disable(2));
        $user = $userModel->getById(2);
        $this->assertEquals(0, $user['is_active']);
        $this->assertFalse($userModel->isActive(2));

        $this->assertTrue($userModel->enable(2));
        $user = $userModel->getById(2);
        $this->assertEquals(1, $user['is_active']);
        $this->assertTrue($userModel->isActive(2));
    }

    public function testDisablePrivateProjects()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'toto', 'password' => '123456', 'name' => 'Toto')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1', 'is_private' => 1), 2));
        $this->assertTrue($userModel->disable(2));

        $project = $projectModel->getById(1);
        $this->assertEquals(0, $project['is_active']);
    }
}
