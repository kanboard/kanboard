<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\SubtaskController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class SubtaskControllerTest extends Base
{
    public function testSaveWithAssigneeNotMemberOfTheProject()
    {
        $this->createProject();
        $this->buildRequest(array(
            'task_id' => 1,
            'title'   => 'inject',
            'user_id' => 3,
        ));

        $controller = new SubtaskController($this->container);
        $controller->save();

        $this->assertEmpty($this->container['subtaskModel']->getAll(1));
    }

    public function testSaveWithAssigneeMemberOfTheProject()
    {
        $this->createProject();
        $this->buildRequest(array(
            'task_id' => 1,
            'title'   => 'subtask',
            'user_id' => 2,
        ), array('task_id' => 1), 'redirect');

        $controller = new SubtaskController($this->container);
        $controller->save();

        $subtasks = $this->container['subtaskModel']->getAll(1);
        $this->assertCount(1, $subtasks);
        $this->assertEquals(2, $subtasks[0]['user_id']);
    }

    public function testUpdateWithAssigneeNotMemberOfTheProject()
    {
        $this->createProject();
        $subtaskModel = new SubtaskModel($this->container);
        $this->assertEquals(1, $subtaskModel->create(array('task_id' => 1, 'title' => 'subtask')));

        $this->buildRequest(array(
            'id'      => 1,
            'task_id' => 1,
            'title'   => 'subtask',
            'user_id' => 3,
        ), array('task_id' => 1, 'subtask_id' => 1));

        $controller = new SubtaskController($this->container);
        $controller->update();

        $subtask = $subtaskModel->getById(1);
        $this->assertEquals(0, $subtask['user_id']);
    }

    private function buildRequest(array $values, array $params = array('task_id' => 1), $responseMethod = 'html')
    {
        $values['csrf_token'] = $this->container['token']->getCSRFToken();

        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'POST'),
            $params,
            $values
        );

        // The form is rendered again with the validation errors
        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array($responseMethod))
            ->getMock();

        $this->container['response']
            ->expects($this->once())
            ->method($responseMethod);
    }

    private function createProject()
    {
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'member')));
        $this->assertEquals(3, $userModel->create(array('username' => 'outsider')));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project A')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'Task A')));

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
            'username' => 'member',
        );
    }
}
