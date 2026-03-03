<?php

namespace KanboardTests\units\Auth;

use KanboardTests\units\Base;
use Kanboard\Auth\ReverseProxyAuth;
use Kanboard\Core\Security\Role;
use Kanboard\Model\UserModel;

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class ReverseProxyAuthTest extends Base
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container['request'] = $this
            ->getMockBuilder('\Kanboard\Core\Http\Request')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('getRemoteUser'))
            ->getMock();
    }

    public function testGetName()
    {
        $provider = new ReverseProxyAuth($this->container);
        $this->assertEquals('ReverseProxy', $provider->getName());
    }

    public function testAuthenticateSuccess()
    {
        $this->container['request']
            ->expects($this->once())
            ->method('getRemoteUser')
            ->willReturn('admin');

        $provider = new ReverseProxyAuth($this->container);
        $this->assertTrue($provider->authenticate());
    }

    public function testAuthenticateFailure()
    {
        $this->container['request']
            ->expects($this->once())
            ->method('getRemoteUser')
            ->willReturn('');

        $provider = new ReverseProxyAuth($this->container);
        $this->assertFalse($provider->authenticate());
    }

    public function testValidSession()
    {
        $this->container['request']
            ->expects($this->once())
            ->method('getRemoteUser')
            ->willReturn('admin');

        $_SESSION['user'] = array(
            'username' => 'admin'
        );

        $provider = new ReverseProxyAuth($this->container);
        $this->assertTrue($provider->isValidSession());
    }

    public function testInvalidSession()
    {
        $this->container['request']
            ->expects($this->once())
            ->method('getRemoteUser')
            ->willReturn('foobar');

        $_SESSION['user'] = array(
            'username' => 'admin'
        );

        $provider = new ReverseProxyAuth($this->container);
        $this->assertFalse($provider->isValidSession());
    }

    public function testRoleForNewUser()
    {
        $this->container['request']
            ->expects($this->once())
            ->method('getRemoteUser')
            ->willReturn('someone');

        $provider = new ReverseProxyAuth($this->container);
        $this->assertTrue($provider->authenticate());

        $user = $provider->getUser();
        $this->assertEquals(Role::APP_USER, $user->getRole());
    }

    public function testRoleIsPreservedForExistingUser()
    {
        $this->container['request']
            ->expects($this->once())
            ->method('getRemoteUser')
            ->willReturn('someone');

        $provider = new ReverseProxyAuth($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'someone', 'role' => Role::APP_MANAGER)));

        $this->assertTrue($provider->authenticate());

        $user = $provider->getUser();
        $this->assertEquals(Role::APP_MANAGER, $user->getRole());
    }
}
