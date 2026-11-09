<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\TaskCreationController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class TaskCreationControllerTest extends Base
{
    public function testSaveIgnoresFieldsOutsideTheForm()
    {
        $this->createProjects();
        $this->buildRequest(array(
            'title'          => 'New task',
            'column_id'      => 1,
            'swimlane_id'    => 1,
            'project_id'     => 2,
            'creator_id'     => 3,
            'is_active'      => 0,
            'date_completed' => 123,
        ), 'redirect');

        $controller = new TaskCreationController($this->container);
        $controller->save();

        $task = $this->container['taskFinderModel']->getById(2);
        $this->assertEquals('New task', $task['title']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(2, $task['creator_id']);
        $this->assertEquals(1, $task['is_active']);
        $this->assertEmpty($task['date_completed']);
    }

    public function testSaveWithColumnFromAnotherProject()
    {
        $this->createProjects();
        $this->buildRequest(array('title' => 'New task', 'column_id' => 6, 'swimlane_id' => 2), 'redirect');

        $controller = new TaskCreationController($this->container);
        $controller->save();

        $this->assertEquals(1, $this->container['taskFinderModel']->countByProjectId(1));
        $this->assertEmpty($this->container['taskFinderModel']->getById(2));
    }

    public function testSaveWithUnassignableOwner()
    {
        $this->createProjects();
        $this->buildRequest(array('title' => 'New task', 'column_id' => 1, 'swimlane_id' => 1, 'owner_id' => 3), 'html');

        $controller = new TaskCreationController($this->container);
        $controller->save();

        $this->assertEmpty($this->container['taskFinderModel']->getById(2));
    }

    public function testSaveWithAssignableOwner()
    {
        $this->createProjects();
        $this->buildRequest(array('title' => 'New task', 'column_id' => 1, 'swimlane_id' => 1, 'owner_id' => 2), 'redirect');

        $controller = new TaskCreationController($this->container);
        $controller->save();

        $task = $this->container['taskFinderModel']->getById(2);
        $this->assertEquals(2, $task['owner_id']);
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
