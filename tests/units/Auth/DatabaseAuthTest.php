<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Auth\DatabaseAuth;
use Kanboard\Model\User;

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
        $userModel = new User($this->container);
        $provider = new DatabaseAuth($this->container);

        $this->assertFalse($provider->isValidSession());

        $this->assertEquals(2, $userModel->create(array('username' => 'foobar')));

        $this->container['sessionStorage']->user = array('id' => 2);
        $this->assertTrue($provider->isValidSession());

        $this->container['sessionStorage']->user = array('id' => 3);
        $this->assertFalse($provider->isValidSession());

        $this->assertTrue($userModel->disable(2));

        $this->container['sessionStorage']->user = array('id' => 2);
        $this->assertFalse($provider->isValidSession());
    }
}
