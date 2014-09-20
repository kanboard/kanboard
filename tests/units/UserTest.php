<?php

require_once __DIR__.'/Base.php';

use Model\User;
use Model\Task;
use Model\Project;

class UserTest extends Base
{
    public function testPrepare()
    {
        $u = new User($this->registry);

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
        $u = new User($this->registry);
        $this->assertTrue($u->create(array('username' => 'toto', 'password' => '123456', 'name' => 'Toto')));
        $this->assertTrue($u->create(array('username' => 'titi', 'is_ldap_user' => 1)));
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
    }

    public function testUpdate()
    {
        $u = new User($this->registry);
        $this->assertTrue($u->create(array('username' => 'toto', 'password' => '123456', 'name' => 'Toto')));
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
        $u = new User($this->registry);
        $t = new Task($this->registry);
        $p = new Project($this->registry);

        $this->assertTrue($u->create(array('username' => 'toto', 'password' => '123456', 'name' => 'Toto')));
        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $t->create(array('title' => 'Task #1', 'project_id' => 1, 'owner_id' => 2)));

        $task = $t->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['owner_id']);

        $this->assertTrue($u->remove(1));
        $this->assertTrue($u->remove(2));
        $this->assertFalse($u->remove(2));
        $this->assertFalse($u->remove(55));

        // Make sure that assigned tasks are unassigned after removing the user
        $task = $t->getById(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(0, $task['owner_id']);
    }
}
