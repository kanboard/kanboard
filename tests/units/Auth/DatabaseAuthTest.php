<?php

namespace KanboardTests\units\Auth;

use KanboardTests\units\Base;
use Kanboard\Auth\DatabaseAuth;
use Kanboard\Core\User\UserSession;
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

        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2')));

        $fingerprint = hash('sha256', '');

        $_SESSION['user'] = array('id' => 2, 'role' => 'app-user', 'credentials_fingerprint' => $fingerprint);
        $this->assertTrue($provider->isValidSession());

        $_SESSION['user'] = array('id' => 4, 'role' => 'app-user', 'credentials_fingerprint' => $fingerprint);
        $this->assertFalse($provider->isValidSession());

        $this->assertTrue($userModel->disable(2));

        $_SESSION['user'] = array('id' => 2, 'role' => 'app-user', 'credentials_fingerprint' => $fingerprint);
        $this->assertFalse($provider->isValidSession());

        $_SESSION['user'] = array('id' => 3, 'role' => 'app-user', 'credentials_fingerprint' => $fingerprint);
        $this->assertTrue($provider->isValidSession());

        $_SESSION['user'] = array('id' => 3, 'role' => 'app-admin', 'credentials_fingerprint' => $fingerprint);
        $this->assertFalse($provider->isValidSession());
    }

    public function testIsValidSessionAfterPasswordChange()
    {
        $userModel = new UserModel($this->container);
        $userSession = new UserSession($this->container);
        $provider = new DatabaseAuth($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user1', 'password' => 'first_password')));

        $userSession->initialize($userModel->getById(2));
        $this->assertTrue($provider->isValidSession());

        $this->assertTrue($userModel->update(array('id' => 2, 'password' => 'second_password')));

        // The session of the user who made the change is refreshed and stays valid
        $this->assertTrue($provider->isValidSession());

        // Any other session opened before the change is now invalid
        $_SESSION['user']['credentials_fingerprint'] = hash('sha256', 'stale');
        $this->assertFalse($provider->isValidSession());
    }

    public function testIsValidSessionAfterOrdinaryUpdate()
    {
        $userModel = new UserModel($this->container);
        $userSession = new UserSession($this->container);
        $provider = new DatabaseAuth($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user1', 'password' => 'first_password')));

        $userSession->initialize($userModel->getById(2));
        $staleFingerprint = $userSession->getCredentialsFingerprint();

        $this->assertTrue($userModel->update(array('id' => 2, 'password' => 'second_password')));

        // Simulate a session opened before the password was changed somewhere else
        $_SESSION['user']['credentials_fingerprint'] = $staleFingerprint;
        $this->assertFalse($provider->isValidSession());

        // An update that does not change the password must not revalidate that session
        $this->assertTrue($userModel->update(array('id' => 2, 'name' => 'User #1')));
        $this->assertFalse($provider->isValidSession());
    }

    public function testIsValidSessionWithoutFingerprint()
    {
        $userModel = new UserModel($this->container);
        $provider = new DatabaseAuth($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user1', 'password' => 'first_password')));

        // Sessions created before the fingerprint existed cannot be validated
        $_SESSION['user'] = array('id' => 2, 'role' => 'app-user');
        $this->assertFalse($provider->isValidSession());
    }
}
