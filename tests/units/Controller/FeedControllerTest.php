<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\FeedController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Model\CommentModel;
use Kanboard\Model\ProjectActivityModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class FeedControllerTest extends Base
{
    public function testProjectFeedHidesRestrictedComments()
    {
        $this->createCommentEvents();
        $project = $this->container['projectModel']->getById(1);

        foreach ($this->getSessions() as $name => $session) {
            $_SESSION['user'] = $session;
            $xml = $this->renderFeed('project', $project['token']);

            $this->assertStringContainsString('Standard note', $xml, $name);
            $this->assertStringNotContainsString('Admin note', $xml, $name);
        }
    }

    public function testUserFeedHidesRestrictedComments()
    {
        $userModel = new UserModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);

        $this->createCommentEvents();
        $this->assertEquals(2, $userModel->create(array('username' => 'user')));
        $this->assertTrue($userModel->enablePublicAccess(2));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));
        $user = $userModel->getById(2);

        foreach ($this->getSessions() as $name => $session) {
            $_SESSION['user'] = $session;
            $xml = $this->renderFeed('user', $user['token']);

            $this->assertStringContainsString('Standard note', $xml, $name);
            $this->assertStringNotContainsString('Admin note', $xml, $name);
        }
    }

    private function getSessions()
    {
        return array(
            'anonymous' => array(),
            'signed-in admin' => array('id' => 1, 'role' => Role::APP_ADMIN, 'username' => 'admin'),
        );
    }

    private function renderFeed($action, $token)
    {
        $this->container['request'] = new Request($this->container, array('REQUEST_METHOD' => 'GET'), array('token' => $token));

        $xml = null;
        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('xml'))
            ->getMock();
        $this->container['response']
            ->expects($this->once())
            ->method('xml')
            ->willReturnCallback(function ($data) use (&$xml) {
                $xml = $data;
            });

        (new FeedController($this->container))->$action();

        return $xml;
    }

    private function createCommentEvents()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Public project')));
        $this->assertTrue($projectModel->enablePublicAccess(1));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'Task')));
        $task = $taskFinderModel->getDetails(1);

        foreach (array(Role::APP_USER => 'Standard note', Role::APP_ADMIN => 'Admin note') as $visibility => $text) {
            $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, CommentModel::EVENT_CREATE, array(
                'task' => $task,
                'comment' => array('id' => 1, 'task_id' => 1, 'comment' => $text, 'visibility' => $visibility),
            )));
        }
    }
}
