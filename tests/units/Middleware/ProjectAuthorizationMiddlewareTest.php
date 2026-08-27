<?php

namespace KanboardTests\units\Middleware;

use Kanboard\Core\Http\Request;
use Kanboard\Middleware\ProjectAuthorizationMiddleware;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use KanboardTests\units\Base;

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

    public function testWithTaskFromAnotherProject()
    {
        $this->createFixtures();

        // The user has access to the project given in the URL but not to the project of the task
        $this->container['request'] = new Request($this->container, array(), array('project_id' => 1, 'task_id' => 1));

        $this->container['helper']->user
            ->expects($this->once())
            ->method('hasProjectAccess')
            ->with($this->anything(), $this->anything(), 2)
            ->willReturn(false);

        $this->nextMiddleware
            ->expects($this->never())
            ->method('execute');

        $this->expectException('Kanboard\Core\Controller\AccessForbiddenException');
        $this->middleware->execute();
    }

    public function testWithTaskFromTheSameProject()
    {
        $this->createFixtures();

        $this->container['request'] = new Request($this->container, array(), array('project_id' => 2, 'task_id' => 1));

        $this->container['helper']->user
            ->expects($this->once())
            ->method('hasProjectAccess')
            ->with($this->anything(), $this->anything(), 2)
            ->willReturn(true);

        $this->nextMiddleware
            ->expects($this->once())
            ->method('execute');

        $this->middleware->execute();
    }

    private function createFixtures()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project A')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project B')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 2, 'title' => 'Task B')));
    }
}
