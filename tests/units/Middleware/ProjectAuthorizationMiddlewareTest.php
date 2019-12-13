<?php

use Kanboard\Middleware\ProjectAuthorizationMiddleware;

require_once __DIR__.'/../Base.php';

class ProjectAuthorizationMiddlewareMiddlewareTest extends Base
{
    /**
     * @var ProjectAuthorizationMiddleware
     */
    private $middleware;
    private $nextMiddleware;

    public function setUp()
    {
        parent::setUp();

        $this->container['helper'] = new stdClass();

        $this->container['helper']->user = $this
            ->getMockBuilder('Kanboard\Helper\UserHelper')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('hasProjectAccess'))
            ->getMock();

        $this->container['request'] = $this
            ->getMockBuilder('Kanboard\Core\Http\Request')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('getIntegerParam'))
            ->getMock();

        $this->nextMiddleware = $this
            ->getMockBuilder('Kanboard\Middleware\ProjectAuthorizationMiddleware')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('execute'))
            ->getMock();

        $this->middleware = new ProjectAuthorizationMiddleware($this->container);
        $this->middleware->setNextMiddleware($this->nextMiddleware);
    }

    public function testWithAccessDenied()
    {
        $this->container['request']
            ->expects($this->any())
            ->method('getIntegerParam')
            ->will($this->returnValue(123));

        $this->container['helper']->user
            ->expects($this->once())
            ->method('hasProjectAccess')
            ->will($this->returnValue(false));

        $this->nextMiddleware
            ->expects($this->never())
            ->method('execute');

        $this->expectException('Kanboard\Core\Controller\AccessForbiddenException');
        $this->middleware->execute();
    }

    public function testWithAccessGranted()
    {
        $this->container['request']
            ->expects($this->any())
            ->method('getIntegerParam')
            ->will($this->returnValue(123));

        $this->container['helper']->user
            ->expects($this->once())
            ->method('hasProjectAccess')
            ->will($this->returnValue(true));

        $this->nextMiddleware
            ->expects($this->once())
            ->method('execute');

        $this->middleware->execute();
    }
}
