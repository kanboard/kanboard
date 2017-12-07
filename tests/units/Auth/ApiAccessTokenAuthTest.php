<?php

use Kanboard\Auth\ApiAccessTokenAuth;
use Kanboard\Model\UserModel;

require_once __DIR__.'/../Base.php';

class ApiAccessTokenAuthTest extends Base
{
    public function testGetName()
    {
        $provider = new ApiAccessTokenAuth($this->container);
        $this->assertEquals('API Access Token', $provider->getName());
    }

    public function testAuthenticateWithoutToken()
    {
        $provider = new ApiAccessTokenAuth($this->container);

        $provider->setUsername('admin');
        $provider->setPassword('admin');
        $this->assertFalse($provider->authenticate());
        $this->assertNull($provider->getUser());
    }

    public function testAuthenticateWithEmptyPassword()
    {
        $provider = new ApiAccessTokenAuth($this->container);

        $provider->setUsername('admin');
        $provider->setPassword('');
        $this->assertFalse($provider->authenticate());
    }

    public function testAuthenticateWithTokenAndNoScope()
    {
        $provider = new ApiAccessTokenAuth($this->container);
        $userModel = new UserModel($this->container);

        $userModel->update(array(
            'id' => 1,
            'api_access_token' => 'test',
        ));

        $provider->setUsername('admin');
        $provider->setPassword('test');
        $this->assertFalse($provider->authenticate());
    }

    public function testAuthenticateWithToken()
    {
        $_SESSION['scope'] = 'API';

        $provider = new ApiAccessTokenAuth($this->container);
        $userModel = new UserModel($this->container);

        $userModel->update(array(
            'id' => 1,
            'api_access_token' => 'test',
        ));

        $provider->setUsername('admin');
        $provider->setPassword('test');
        $this->assertTrue($provider->authenticate());
        $this->assertInstanceOf('Kanboard\User\DatabaseUserProvider', $provider->getUser());

        $provider->setUsername('admin');
        $provider->setPassword('something else');
        $this->assertFalse($provider->authenticate());
    }
}
