<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\UserModificationController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Session\FlashMessage;
use Kanboard\Model\UserMetadataModel;
use KanboardTests\units\Base;

class UserModificationControllerTest extends Base
{
    public function testSavePersistsTaskSearchPreference()
    {
        $user = $this->container['userModel']->getById(1);
        $this->container['userSession']->initialize($user);

        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'POST'),
            array('user_id' => 1),
            array(
                'csrf_token' => $this->container['token']->getCSRFToken(),
                'id' => 1,
                'username' => 'admin',
                'name' => 'Administrator',
                'email' => 'admin@example.org',
                'theme' => 'light',
                'timezone' => 'UTC',
                'language' => 'en_US',
                'filter' => 'status:open',
                'task_search_all_fields' => '1',
            ),
            array(),
            array()
        );

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect'))
            ->getMock();

        $this->container['response']
            ->expects($this->once())
            ->method('redirect');

        $this->container['flash'] = $this->getMockBuilder(FlashMessage::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('success'))
            ->getMock();

        $this->container['flash']
            ->expects($this->once())
            ->method('success');

        $controller = new UserModificationController($this->container);
        $controller->save();

        $updatedUser = $this->container['userModel']->getById(1);

        $this->assertEquals('Administrator', $updatedUser['name']);
        $this->assertEquals('admin@example.org', $updatedUser['email']);
        $this->assertEquals(1, $this->container['userMetadataModel']->get(1, UserMetadataModel::KEY_TASK_SEARCH_ALL_FIELDS, 0));
    }
}
