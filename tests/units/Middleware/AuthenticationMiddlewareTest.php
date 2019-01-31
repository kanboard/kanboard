<?php

use Kanboard\Middleware\AuthenticationMiddleware;

require_once __DIR__.'/../Base.php';

class AuthenticationMiddlewareTest extends Base
{
    /**
     * @var AuthenticationMiddleware
     */
    private $middleware;
    private $nextMiddleware;

    public function setUp()
    {
        parent::setUp();

        $this->container['authenticationManager'] = $this
            ->getMockBuilder('Kanboard\Core\Security\AuthenticationManager')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('checkCurrentSession'))
            ->getMock();

        $this->container['applicationAuthorization'] = $this
            ->getMockBuilder('Kanboard\Core\Security\AccessMap')
            ->setMethods(array('isAllowed'))
            ->getMock();

        $this->container['response'] = $this
            ->getMockBuilder('Kanboard\Core\Http\Response')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('redirect'))
            ->getMock();

        $this->container['userSession'] = $this
            ->getMockBuilder('Kanboard\Core\User\UserSession')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('isLogged'))
            ->getMock();

        $this->nextMiddleware = $this
            ->getMockBuilder('Kanboard\Middleware\AuthenticationMiddleware')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('execute'))
            ->getMock();

        $this->middleware = new AuthenticationMiddleware($this->container);
        $this->middleware->setNextMiddleware($this->nextMiddleware);
    }

    public function testWithBadSession()
    {
        $this->container['authenticationManager']
            ->expects($this->once())
            ->method('checkCurrentSession')
            ->will($this->returnValue(false));

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
            ->will($this->returnValue(true));

        $this->container['applicationAuthorization']
            ->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(true));

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
            ->will($this->returnValue(true));

        $this->container['applicationAuthorization']
            ->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(false));

        $this->container['userSession']
            ->expects($this->once())
            ->method('isLogged')
            ->will($this->returnValue(false));

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
            ->will($this->returnValue(true));

        $this->container['applicationAuthorization']
            ->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(false));

        $this->container['userSession']
            ->expects($this->once())
            ->method('isLogged')
            ->will($this->returnValue(true));

        $this->container['response']
            ->expects($this->never())
            ->method('redirect');

        $this->nextMiddleware
            ->expects($this->once())
            ->method('execute');

        $this->middleware->execute();
    }
}
