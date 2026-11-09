<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\TaskBulkMoveColumnController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class TaskBulkMoveColumnControllerTest extends Base
{
    public function testMoveTasks()
    {
        $this->createProjects();
        $this->buildRequest(array('task_ids' => '1', 'column_id' => 3, 'swimlane_id' => 1), 'redirect');

        $controller = new TaskBulkMoveColumnController($this->container);
        $controller->save();

        $task = $this->container['taskFinderModel']->getById(1);
        $this->assertEquals(3, $task['column_id']);
    }

    public function testMoveTaskFromAnotherProject()
    {
        $this->createProjects();
        $this->buildRequest(array('task_ids' => '2', 'column_id' => 3, 'swimlane_id' => 1));

        $controller = new TaskBulkMoveColumnController($this->container);

        try {
            $controller->save();
            $this->fail('AccessForbiddenException was not thrown');
        } catch (AccessForbiddenException $e) {
        }

        $task = $this->container['taskFinderModel']->getById(2);
        $this->assertEquals(5, $task['column_id']);
    }

    public function testMoveTasksToColumnFromAnotherProject()
    {
        $this->createProjects();
        $this->buildRequest(array('task_ids' => '1', 'column_id' => 6, 'swimlane_id' => 2), 'redirect');

        $controller = new TaskBulkMoveColumnController($this->container);
        $controller->save();

        $task = $this->container['taskFinderModel']->getById(1);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['swimlane_id']);
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

        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'Task A1')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 2, 'title' => 'Task B1')));

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
            'username' => 'member',
        );
    }
}
