<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\WebNotificationController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Model\TaskModel;
use KanboardTests\units\Base;

class WebNotificationControllerTest extends Base
{
    public function testRedirectRejectsNotificationFromAnotherUser()
    {
        $this->createFixture();
        $this->buildRequest(3, 3);

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('html', 'redirect'))
            ->getMock();
        $this->container['response']
            ->expects($this->once())
            ->method('html');
        $this->container['response']
            ->expects($this->never())
            ->method('redirect');

        (new WebNotificationController($this->container))->redirect();

        $this->assertCount(1, $this->container['userUnreadNotificationModel']->getAll(2));
    }

    public function testRedirectAllowsNotificationOwner()
    {
        $this->createFixture();
        $this->buildRequest(2, 2);
        $this->expectTaskRedirect();

        (new WebNotificationController($this->container))->redirect();

        $this->assertEmpty($this->container['userUnreadNotificationModel']->getAll(2));
    }

    public function testRedirectAllowsAdministratorForRequestedUser()
    {
        $this->createFixture();
        $this->buildRequest(1, 2);
        $this->expectTaskRedirect();

        (new WebNotificationController($this->container))->redirect();

        $this->assertEmpty($this->container['userUnreadNotificationModel']->getAll(2));
    }

    private function createFixture()
    {
        $this->assertEquals(2, $this->container['userModel']->create(array('username' => 'victim')));
        $this->assertEquals(3, $this->container['userModel']->create(array('username' => 'attacker')));
        $this->assertEquals(1, $this->container['projectModel']->create(array('name' => 'Private project', 'is_private' => 1), 2, true));
        $this->assertEquals(1, $this->container['taskCreationModel']->create(array('title' => 'Private task', 'project_id' => 1)));

        $this->container['userUnreadNotificationModel']->create(
            2,
            TaskModel::EVENT_CREATE,
            array('task' => $this->container['taskFinderModel']->getDetails(1))
        );
    }

    private function buildRequest($sessionUserId, $requestedUserId)
    {
        $this->container['userSession']->initialize($this->container['userModel']->getById($sessionUserId));
        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'GET'),
            array('notification_id' => 1, 'user_id' => $requestedUserId),
            array(),
            array(),
            array()
        );
    }

    private function expectTaskRedirect()
    {
        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect'))
            ->getMock();
        $this->container['response']
            ->expects($this->once())
            ->method('redirect')
            ->with($this->callback(function ($url) {
                return strpos($url, 'task_id=1') !== false;
            }));
    }
}
