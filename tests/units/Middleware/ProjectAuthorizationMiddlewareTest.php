<?php

namespace KanboardTests\units\Middleware;

use KanboardTests\units\Base;
use Kanboard\Middleware\ProjectAuthorizationMiddleware;
use stdClass;

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class ProjectAuthorizationMiddlewareTest extends Base
{
    /**
     * @var ProjectAuthorizationMiddleware
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
            ->onlyMethods(array('hasProjectAccess'))
            ->getMock();

        $this->container['request'] = $this
            ->getMockBuilder('Kanboard\Core\Http\Request')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('getIntegerParam'))
            ->getMock();

        $this->nextMiddleware = $this
            ->getMockBuilder('Kanboard\Middleware\ProjectAuthorizationMiddleware')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('execute'))
            ->getMock();

        $this->middleware = new ProjectAuthorizationMiddleware($this->container);
        $this->middleware->setNextMiddleware($this->nextMiddleware);
    }

    public function testWithAccessDenied()
    {
        $this->container['request']
            ->method('getIntegerParam')
            ->willReturn(123);

        $this->container['helper']->user
            ->expects($this->once())
            ->method('hasProjectAccess')
            ->willReturn(false);

        $this->nextMiddleware
            ->expects($this->never())
            ->method('execute');

        $this->expectException('Kanboard\Core\Controller\AccessForbiddenException');
        $this->middleware->execute();
    }

    public function testWithAccessGranted()
    {
        $this->container['request']
            ->method('getIntegerParam')
            ->willReturn(123);

        $this->container['helper']->user
            ->expects($this->once())
            ->method('hasProjectAccess')
            ->willReturn(true);

        $this->nextMiddleware
            ->expects($this->once())
            ->method('execute');

        $this->middleware->execute();
    }
}
