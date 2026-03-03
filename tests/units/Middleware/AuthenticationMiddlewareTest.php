<?php

namespace KanboardTests\units\Middleware;

use KanboardTests\units\Base;
use Kanboard\Middleware\AuthenticationMiddleware;

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class AuthenticationMiddlewareTest extends Base
{
    /**
     * @var AuthenticationMiddleware
     */
    private $middleware;
    private $nextMiddleware;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container['authenticationManager'] = $this
            ->getMockBuilder('Kanboard\Core\Security\AuthenticationManager')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('checkCurrentSession'))
            ->getMock();

        $this->container['applicationAuthorization'] = $this
            ->getMockBuilder('Kanboard\Core\Security\Authorization')
            ->setConstructorArgs(array(new \Kanboard\Core\Security\AccessMap()))
            ->onlyMethods(array('isAllowed'))
            ->getMock();

        $this->container['response'] = $this
            ->getMockBuilder('Kanboard\Core\Http\Response')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect'))
            ->getMock();

        $this->container['userSession'] = $this
            ->getMockBuilder('Kanboard\Core\User\UserSession')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('isLogged'))
            ->getMock();

        $this->nextMiddleware = $this
            ->getMockBuilder('Kanboard\Middleware\AuthenticationMiddleware')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('execute'))
            ->getMock();

        $this->middleware = new AuthenticationMiddleware($this->container);
        $this->middleware->setNextMiddleware($this->nextMiddleware);
    }

    public function testWithBadSession()
    {
        $this->container['authenticationManager']
            ->expects($this->once())
            ->method('checkCurrentSession')
            ->willReturn(false);

        $this->nextMiddleware
            ->expects($this->never())
            ->method('execute');

        $this->middleware->execute();
    }

    public function testWithPublicAction()
    {
        $this->container['authenticationManager']
            ->expects($this->once())
            ->method('checkCurrentSession')
            ->willReturn(true);

        $this->container['applicationAuthorization']
            ->expects($this->once())
            ->method('isAllowed')
            ->willReturn(true);

        $this->nextMiddleware
            ->expects($this->never())
            ->method('execute');

        $this->middleware->execute();
    }

    public function testWithNotAuthenticatedUser()
    {
        $this->container['authenticationManager']
            ->expects($this->once())
            ->method('checkCurrentSession')
            ->willReturn(true);

        $this->container['applicationAuthorization']
            ->expects($this->once())
            ->method('isAllowed')
            ->willReturn(false);

        $this->container['userSession']
            ->expects($this->once())
            ->method('isLogged')
            ->willReturn(false);

        $this->container['response']
            ->expects($this->once())
            ->method('redirect');

        $this->nextMiddleware
            ->expects($this->never())
            ->method('execute');

        $this->middleware->execute();
    }

    public function testWithAuthenticatedUser()
    {
        $this->container['authenticationManager']
            ->expects($this->once())
            ->method('checkCurrentSession')
            ->willReturn(true);

        $this->container['applicationAuthorization']
            ->expects($this->once())
            ->method('isAllowed')
            ->willReturn(false);

        $this->container['userSession']
            ->expects($this->once())
            ->method('isLogged')
            ->willReturn(true);

        $this->container['response']
            ->expects($this->never())
            ->method('redirect');

        $this->nextMiddleware
            ->expects($this->once())
            ->method('execute');

        $this->middleware->execute();
    }
}
