<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Auth\DatabaseAuth;
use Kanboard\Model\UserModel;

class DatabaseAuthTest extends Base
{
    public function testGetName()
    {
        $provider = new DatabaseAuth($this->container);
        $this->assertEquals('Database', $provider->getName());
    }

    public function testAuthenticate()
    {
        $provider = new DatabaseAuth($this->container);

        $provider->setUsername('admin');
        $provider->setPassword('admin');
        $this->assertTrue($provider->authenticate());

        $provider->setUsername('admin');
        $provider->setPassword('test');
        $this->assertFalse($provider->authenticate());
    }

    public function testGetUser()
    {
        $provider = new DatabaseAuth($this->container);
        $this->assertEquals(null, $provider->getUser());

        $provider = new DatabaseAuth($this->container);
        $provider->setUsername('admin');
        $provider->setPassword('admin');

        $this->assertTrue($provider->authenticate());
        $this->assertInstanceOf('Kanboard\User\DatabaseUserProvider', $provider->getUser());
    }

    public function testIsvalidSession()
    {
        $userModel = new UserModel($this->container);
        $provider = new DatabaseAuth($this->container);

        $this->assertFalse($provider->isValidSession());

        $this->assertEquals(2, $userModel->create(array('username' => 'foobar')));

        $_SESSION['user'] = array('id' => 2);
        $this->assertTrue($provider->isValidSession());

        $_SESSION['user'] = array('id' => 3);
        $this->assertFalse($provider->isValidSession());

        $this->assertTrue($userModel->disable(2));

        $_SESSION['user'] = array('id' => 2);
        $this->assertFalse($provider->isValidSession());
    }
}
