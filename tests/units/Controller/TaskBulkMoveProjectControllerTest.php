<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\TaskBulkMoveProjectController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class TaskBulkMoveProjectControllerTest extends Base
{
    public function testMoveTasksToAnotherProject()
    {
        $this->createProjects();

        // allow the member in the destination project so the assignee can be kept
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $projectUserRoleModel->addUser(2, 2, Role::PROJECT_MEMBER);

        $this->buildRequest(array(
            'task_ids'   => '1,2',
            'project_id' => 2,
        ), 'redirect');

        $controller = new TaskBulkMoveProjectController($this->container);
        $controller->save();

        $taskFinderModel = new TaskFinderModel($this->container);

        // keep current value: column and swimlane are remapped by name to the destination
        $task = $taskFinderModel->getById(1);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals(5, $task['column_id']);
        $this->assertEquals(2, $task['swimlane_id']);
        $this->assertEquals(2, $task['owner_id']);

        $task = $taskFinderModel->getById(2);
        $this->assertEquals(2, $task['project_id']);
    }

    public function testMoveTasksToProjectWithNoAccess()
    {
        $this->createProjects();

        // the member has no role in project 2
        $this->buildRequest(array(
            'task_ids'   => '1',
            'project_id' => 2,
        ), 'redirect');

        $controller = new TaskBulkMoveProjectController($this->container);
        $controller->save();

        $task = (new TaskFinderModel($this->container))->getById(1);
        $this->assertEquals(1, $task['project_id']);
    }

    public function testMoveTaskBelongingToAnotherProjectIsForbidden()
    {
        $this->createProjects();

        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $projectUserRoleModel->addUser(2, 2, Role::PROJECT_MEMBER);

        // task 3 belongs to project 2, nothing should be moved at all
        $this->buildRequest(array(
            'task_ids'   => '1,3',
            'project_id' => 2,
        ));

        $controller = new TaskBulkMoveProjectController($this->container);

        try {
            $controller->save();
            $this->fail('AccessForbiddenException was not thrown');
        } catch (AccessForbiddenException $e) {
        }

        $taskFinderModel = new TaskFinderModel($this->container);
        $this->assertEquals(1, $taskFinderModel->getById(1)['project_id']);
        $this->assertEquals(2, $taskFinderModel->getById(3)['project_id']);
    }

    public function testMoveTasksToSelectedColumnAndSwimlane()
    {
        $this->createProjects();

        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $projectUserRoleModel->addUser(2, 2, Role::PROJECT_MEMBER);

        $this->buildRequest(array(
            'task_ids'    => '1',
            'project_id'  => 2,
            'swimlane_id' => '2',
            'column_id'   => '6',
            'category_id' => '0',
        ), 'redirect');

        $controller = new TaskBulkMoveProjectController($this->container);
        $controller->save();

        $task = (new TaskFinderModel($this->container))->getById(1);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals(6, $task['column_id']);
        $this->assertEquals(2, $task['swimlane_id']);
        $this->assertEquals(0, $task['category_id']);
    }

    private function buildRequest(array $values, $responseMethod = null)
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
            ->onlyMethods(array('redirect'))
            ->getMock();

        $this->container['response']
            ->expects($responseMethod === 'redirect' ? $this->once() : $this->never())
            ->method('redirect');
    }

    private function createProjects()
    {
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'member')));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project A')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project B')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));

        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'Task A1', 'owner_id' => 2)));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'Task A2')));
        $this->assertEquals(3, $taskCreationModel->create(array('project_id' => 2, 'title' => 'Task B1')));

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
            'username' => 'member',
        );
    }
}
