<?php

require_once __DIR__.'/Base.php';

use Model\User;
use Model\Subtask;
use Model\Comment;
use Model\Task;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\Project;

class UserTest extends Base
{
    public function testFailedLogin()
    {
        $u = new User($this->container);

        $this->assertEquals(0, $u->getFailedLogin('admin'));
        $this->assertEquals(0, $u->getFailedLogin('not_found'));

        $this->assertTrue($u->incrementFailedLogin('admin'));
        $this->assertTrue($u->incrementFailedLogin('admin'));

        $this->assertEquals(2, $u->getFailedLogin('admin'));
        $this->assertTrue($u->resetFailedLogin('admin'));
        $this->assertEquals(0, $u->getFailedLogin('admin'));
    }

    public function testLocking()
    {
        $u = new User($this->container);

        $this->assertFalse($u->isLocked('admin'));
        $this->assertFalse($u->isLocked('not_found'));
        $this->assertTrue($u->lock('admin', 1));
        $this->assertTrue($u->isLocked('admin'));
    }

    public function testGetByEmail()
    {
        $u = new User($this->container);
        $this->assertNotFalse($u->create(array('username' => 'user1', 'password' => '123456', 'email' => 'user1@localhost')));
        $this->assertNotFalse($u->create(array('username' => 'user2', 'password' => '123456', 'email' => '')));

        $this->assertNotEmpty($u->getByEmail('user1@localhost'));
        $this->assertEmpty($u->getByEmail(''));
    }

    public function testGetByGitlabId()
    {
        $u = new User($this->container);
        $this->assertNotFalse($u->create(array('username' => 'user1', 'password' => '123456', 'gitlab_id' => '1234')));

        $this->assertNotEmpty($u->getByGitlabId('1234'));
        $this->assertEmpty($u->getByGitlabId(''));
    }

    public function testGetByGithubId()
    {
        $u = new User($this->container);
        $this->assertNotFalse($u->create(array('username' => 'user1', 'password' => '123456', 'github_id' => 'plop')));
        $this->assertNotFalse($u->create(array('username' => 'user2', 'password' => '123456', 'github_id' => '')));

        $this->assertNotEmpty($u->getByGithubId('plop'));
        $this->assertEmpty($u->getByGithubId(''));
    }

    public function testGetByGoogleId()
    {
        $u = new User($this->container);
        $this->assertNotFalse($u->create(array('username' => 'user1', 'password' => '123456', 'google_id' => '1234')));
        $this->assertNotFalse($u->create(array('username' => 'user2', 'password' => '123456', 'google_id' => '')));

        $this->assertNotEmpty($u->getByGoogleId('1234'));
        $this->assertEmpty($u->getByGoogleId(''));
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
        $u = new User($this->container);

        $input = array(
            'username' => 'user1',
            'password' => '1234',
            'confirmation' => '1234',
            'name' => 'me',
            'is_admin' => '',
        );

        $u->prepare($input);
        $this->assertArrayNotHasKey('confirmation', $input);

        $this->assertArrayHasKey('password', $input);
        $this->assertNotEquals('1234', $input['password']);
        $this->assertNotEmpty($input['password']);

        $this->assertArrayHasKey('is_admin', $input);
        $this->assertInternalType('integer', $input['is_admin']);

        $input = array(
            'username' => 'user1',
            'password' => '1234',
            'current_password' => 'bla',
            'confirmation' => '1234',
            'name' => 'me',
            'is_ldap_user' => '1',
        );

        $u->prepare($input);
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

        $u->prepare($input);
        $this->assertEquals(array('id' => 2, 'name' => 'me'), $input);
    }

    public function testCreate()
    {
        $u = new User($this->container);
        $this->assertNotFalse($u->create(array('username' => 'toto', 'password' => '123456', 'name' => 'Toto')));
        $this->assertNotFalse($u->create(array('username' => 'titi', 'is_ldap_user' => 1)));
        $this->assertNotFalse($u->create(array('username' => 'papa', 'is_project_admin' => 1)));
        $this->assertFalse($u->create(array('username' => 'toto')));

        $user = $u->getById(1);
        $this->assertNotFalse($user);
        $this->assertTrue(is_array($user));
        $this->assertEquals('admin', $user['username']);
        $this->assertEquals('', $user['name']);
        $this->assertEquals(1, $user['is_admin']);
        $this->assertEquals(0, $user['is_ldap_user']);

        $user = $u->getById(2);
        $this->assertNotFalse($user);
        $this->assertTrue(is_array($user));
        $this->assertEquals('toto', $user['username']);
        $this->assertEquals('Toto', $user['name']);
        $this->assertEquals(0, $user['is_admin']);
        $this->assertEquals(0, $user['is_ldap_user']);

        $user = $u->getById(3);
        $this->assertNotFalse($user);
        $this->assertTrue(is_array($user));
        $this->assertEquals('titi', $user['username']);
        $this->assertEquals('', $user['name']);
        $this->assertEquals(0, $user['is_admin']);
        $this->assertEquals(1, $user['is_ldap_user']);

        $user = $u->getById(4);
        $this->assertNotFalse($user);
        $this->assertTrue(is_array($user));
        $this->assertEquals('papa', $user['username']);
        $this->assertEquals(0, $user['is_admin']);
        $this->assertEquals(1, $user['is_project_admin']);
    }

    public function testUpdate()
    {
        $u = new User($this->container);
        $this->assertNotFalse($u->create(array('username' => 'toto', 'password' => '123456', 'name' => 'Toto')));
        $this->assertTrue($u->update(array('id' => 2, 'username' => 'biloute')));

        $user = $u->getById(2);
        $this->assertNotFalse($user);
        $this->assertTrue(is_array($user));
        $this->assertEquals('biloute', $user['username']);
        $this->assertEquals('Toto', $user['name']);
        $this->assertEquals(0, $user['is_admin']);
        $this->assertEquals(0, $user['is_ldap_user']);
    }

    public function testRemove()
    {
        $u = new User($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $s = new Subtask($this->container);
        $c = new Comment($this->container);

        $this->assertNotFalse($u->create(array('username' => 'toto', 'password' => '123456', 'name' => 'Toto')));
        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1, 'owner_id' => 2)));
        $this->assertEquals(1, $s->create(array('title' => 'Subtask #1', 'user_id' => 2, 'task_id' => 1)));
        $this->assertEquals(1, $c->create(array('comment' => 'foobar', 'user_id' => 2, 'task_id' => 1)));

        $task = $tf->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['owner_id']);

        $this->assertTrue($u->remove(1));
        $this->assertTrue($u->remove(2));
        $this->assertFalse($u->remove(2));
        $this->assertFalse($u->remove(55));

        // Make sure that assigned tasks are unassigned after removing the user
        $task = $tf->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(0, $task['owner_id']);

        // Make sure that assigned subtasks are unassigned after removing the user
        $subtask = $s->getById(1);
        $this->assertEquals(1, $subtask['id']);
        $this->assertEquals(0, $subtask['user_id']);

        // Make sure that comments are not related to the user anymore
        $comment = $c->getById(1);
        $this->assertEquals(1, $comment['id']);
        $this->assertEquals(0, $comment['user_id']);

        // Make sure that private projects are also removed
        $user_id1 = $u->create(array('username' => 'toto1', 'password' => '123456', 'name' => 'Toto'));
        $user_id2 = $u->create(array('username' => 'toto2', 'password' => '123456', 'name' => 'Toto'));
        $this->assertNotFalse($user_id1);
        $this->assertNotFalse($user_id2);
        $this->assertEquals(2, $p->create(array('name' => 'Private project #1', 'is_private' => 1), $user_id1, true));
        $this->assertEquals(3, $p->create(array('name' => 'Private project #2', 'is_private' => 1), $user_id2, true));

        $this->assertTrue($u->remove($user_id1));

        $this->assertNotEmpty($p->getById(1));
        $this->assertNotEmpty($p->getById(3));

        $this->assertEmpty($p->getById(2));
    }

    public function testEnableDisablePublicAccess()
    {
        $u = new User($this->container);
        $this->assertNotFalse($u->create(array('username' => 'toto', 'password' => '123456')));

        $user = $u->getById(2);
        $this->assertNotEmpty($user);
        $this->assertEquals('toto', $user['username']);
        $this->assertEmpty($user['token']);

        $this->assertTrue($u->enablePublicAccess(2));

        $user = $u->getById(2);
        $this->assertNotEmpty($user);
        $this->assertEquals('toto', $user['username']);
        $this->assertNotEmpty($user['token']);

        $this->assertTrue($u->disablePublicAccess(2));

        $user = $u->getById(2);
        $this->assertNotEmpty($user);
        $this->assertEquals('toto', $user['username']);
        $this->assertEmpty($user['token']);
    }
}
