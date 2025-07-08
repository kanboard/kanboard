<?php

namespace KanboardTests\units\Middleware;

use KanboardTests\units\Base;
use Kanboard\Middleware\ApplicationAuthorizationMiddleware;
use stdClass;

class ApplicationAuthorizationMiddlewareTest extends Base
{
    /**
     * @var ApplicationAuthorizationMiddleware
     */
    private $middleware;
    private $nextMiddleware;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container['helper'] = new stdClass();

        $this->container['helper']->user = $this
            ->getMockBuilder('Kanboard\Helper\UserHelper')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('hasAccess'))
            ->getMock();

        $this->nextMiddleware = $this
            ->getMockBuilder('Kanboard\Middleware\ApplicationAuthorizationMiddleware')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('execute'))
            ->getMock();

        $this->middleware = new ApplicationAuthorizationMiddleware($this->container);
        $this->middleware->setNextMiddleware($this->nextMiddleware);
    }

    public function testWithAccessDenied()
    {
        $this->container['helper']->user
            ->expects($this->once())
            ->method('hasAccess')
            ->will($this->returnValue(false));

        $this->nextMiddleware
            ->expects($this->never())
            ->method('execute');

        $this->expectException('Kanboard\Core\Controller\AccessForbiddenException');
        $this->middleware->execute();
    }

    public function testWithAccessGranted()
    {
        $this->container['helper']->user
            ->expects($this->once())
            ->method('hasAccess')
            ->will($this->returnValue(true));

        $this->nextMiddleware
            ->expects($this->once())
            ->method('execute');

        $this->middleware->execute();
    }
}
