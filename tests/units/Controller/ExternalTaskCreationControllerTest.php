<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\ExternalTaskCreationController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class ExternalTaskCreationControllerTest extends Base
{
    public function testStep3UsesAuthorizedProject()
    {
        $this->createProjects();
        $this->buildRequest(array(
            'title'             => 'External task',
            'project_id'        => 2,
            'column_id'         => 1,
            'swimlane_id'       => 1,
            'creator_id'        => 3,
            'external_provider' => 'Provider',
            'external_uri'      => 'https://example.org/issue/1',
        ), 'redirect');

        $controller = new ExternalTaskCreationController($this->container);
        $controller->step3();

        $task = $this->container['taskFinderModel']->getById(2);
        $this->assertEquals('External task', $task['title']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(2, $task['creator_id']);
        $this->assertEquals('Provider', $task['external_provider']);
        $this->assertEquals('https://example.org/issue/1', $task['external_uri']);
        $this->assertEquals(0, $this->container['taskFinderModel']->countByProjectId(2));
    }

    public function testStep3WithColumnFromAnotherProject()
    {
        $this->createProjects();
        $this->buildRequest(array(
            'title'       => 'External task',
            'project_id'  => 2,
            'column_id'   => 5,
            'swimlane_id' => 2,
        ), 'redirect');

        $controller = new ExternalTaskCreationController($this->container);
        $controller->step3();

        $this->assertEmpty($this->container['taskFinderModel']->getById(2));
    }

    private function buildRequest(array $values, $responseMethod)
    {
        $values['csrf_token'] = $this->container['token']->getCSRFToken();

        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'POST'),
            array('project_id' => 1),
            $values
        );

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect', 'html'))
            ->getMock();

        $this->container['response']
            ->expects($responseMethod === 'redirect' ? $this->once() : $this->never())
            ->method('redirect');

        $this->container['response']
            ->expects($responseMethod === 'html' ? $this->once() : $this->never())
            ->method('html');
    }

    private function createProjects()
    {
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'member')));
        $this->assertEquals(3, $userModel->create(array('username' => 'outsider')));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project A')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project B')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));

        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'Task A1')));

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
            'username' => 'member',
        );
    }
}
