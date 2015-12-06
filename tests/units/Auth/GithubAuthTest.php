<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Auth\GithubAuth;
use Kanboard\Model\User;

class GithubAuthTest extends Base
{
    public function testGetName()
    {
        $provider = new GithubAuth($this->container);
        $this->assertEquals('Github', $provider->getName());
    }

    public function testAuthenticationSuccessful()
    {
        $profile = array(
            'id' => 1234,
            'email' => 'test@localhost',
            'name' => 'Test',
        );

        $provider = $this
            ->getMockBuilder('\Kanboard\Auth\GithubAuth')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'getProfile',
            ))
            ->getMock();

        $provider->expects($this->once())
            ->method('getProfile')
            ->will($this->returnValue($profile));

        $this->assertInstanceOf('Kanboard\Auth\GithubAuth', $provider->setCode('1234'));

        $this->assertTrue($provider->authenticate());

        $user = $provider->getUser();
        $this->assertInstanceOf('Kanboard\User\GithubUserProvider', $user);
        $this->assertEquals('Test', $user->getName());
        $this->assertEquals('', $user->getInternalId());
        $this->assertEquals(1234, $user->getExternalId());
        $this->assertEquals('', $user->getRole());
        $this->assertEquals('', $user->getUsername());
        $this->assertEquals('test@localhost', $user->getEmail());
        $this->assertEquals('github_id', $user->getExternalIdColumn());
        $this->assertEquals(array(), $user->getExternalGroupIds());
        $this->assertEquals(array(), $user->getExtraAttributes());
        $this->assertFalse($user->isUserCreationAllowed());
    }

    public function testAuthenticationFailed()
    {
        $provider = $this
            ->getMockBuilder('\Kanboard\Auth\GithubAuth')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'getProfile',
            ))
            ->getMock();

        $provider->expects($this->once())
            ->method('getProfile')
            ->will($this->returnValue(array()));

        $this->assertFalse($provider->authenticate());
        $this->assertEquals(null, $provider->getUser());
    }

    public function testGetService()
    {
        $provider = new GithubAuth($this->container);
        $this->assertInstanceOf('Kanboard\Core\Http\OAuth2', $provider->getService());
    }

    public function testUnlink()
    {
        $userModel = new User($this->container);
        $provider = new GithubAuth($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'test', 'github_id' => '1234')));
        $this->assertNotEmpty($userModel->getByExternalId('github_id', 1234));

        $this->assertTrue($provider->unlink(2));
        $this->assertEmpty($userModel->getByExternalId('github_id', 1234));
    }
}
