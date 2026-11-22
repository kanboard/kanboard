<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\TaskStatusController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Model\ColumnRestrictionModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectRoleModel;
use Kanboard\Model\ProjectRoleRestrictionModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class TaskStatusControllerTest extends Base
{
    public function testCloseTaskAsProjectMember()
    {
        $this->createProject(Role::PROJECT_MEMBER);
        $this->buildRequest(true, 'redirect');

        (new TaskStatusController($this->container))->close();

        $this->assertEquals(0, $this->container['taskFinderModel']->getById(1)['is_active']);
    }

    public function testCloseTaskWithProjectRestriction()
    {
        $this->createProject('Custom Role');
        $this->assertEquals(1, (new ProjectRoleRestrictionModel($this->container))->create(1, 1, ProjectRoleRestrictionModel::RULE_TASK_OPEN_CLOSE));
        $this->buildRequest(true);

        try {
            (new TaskStatusController($this->container))->close();
            $this->fail('A custom role without open/close permission must not be able to close a task');
        } catch (AccessForbiddenException $e) {
            $this->assertEquals(1, $this->container['taskFinderModel']->getById(1)['is_active']);
        }
    }

    public function testOpenTaskWithProjectRestriction()
    {
        $this->createProject('Custom Role');
        $this->assertTrue($this->container['taskStatusModel']->close(1));
        $this->assertEquals(1, (new ProjectRoleRestrictionModel($this->container))->create(1, 1, ProjectRoleRestrictionModel::RULE_TASK_OPEN_CLOSE));
        $this->buildRequest(true);

        try {
            (new TaskStatusController($this->container))->open();
            $this->fail('A custom role without open/close permission must not be able to open a task');
        } catch (AccessForbiddenException $e) {
            $this->assertEquals(0, $this->container['taskFinderModel']->getById(1)['is_active']);
        }
    }

    public function testCloseConfirmationWithProjectRestriction()
    {
        $this->createProject('Custom Role');
        $this->assertEquals(1, (new ProjectRoleRestrictionModel($this->container))->create(1, 1, ProjectRoleRestrictionModel::RULE_TASK_OPEN_CLOSE));
        $this->buildRequest(false);

        $this->expectException(AccessForbiddenException::class);
        (new TaskStatusController($this->container))->close();
    }

    public function testCloseTaskWithColumnRestriction()
    {
        $this->createProject('Custom Role');
        $this->assertEquals(1, (new ColumnRestrictionModel($this->container))->create(1, 1, 1, ColumnRestrictionModel::RULE_BLOCK_TASK_OPEN_CLOSE));
        $this->buildRequest(true);

        try {
            (new TaskStatusController($this->container))->close();
            $this->fail('The column restriction must be applied');
        } catch (AccessForbiddenException $e) {
            $this->assertEquals(1, $this->container['taskFinderModel']->getById(1)['is_active']);
        }
    }

    public function testCloseTaskWhenColumnRuleOverridesProjectRestriction()
    {
        $this->createProject('Custom Role');
        $this->assertEquals(1, (new ProjectRoleRestrictionModel($this->container))->create(1, 1, ProjectRoleRestrictionModel::RULE_TASK_OPEN_CLOSE));
        $this->assertEquals(1, (new ColumnRestrictionModel($this->container))->create(1, 1, 1, ColumnRestrictionModel::RULE_ALLOW_TASK_OPEN_CLOSE));
        $this->buildRequest(true, 'redirect');

        (new TaskStatusController($this->container))->close();

        $this->assertEquals(0, $this->container['taskFinderModel']->getById(1)['is_active']);
    }

    private function buildRequest($confirmation, $responseMethod = null)
    {
        $params = array('task_id' => 1);

        if ($confirmation) {
            $params['confirmation'] = 'yes';
            $params['csrf_token'] = $this->container['token']->getCSRFToken();
        }

        $this->container['request'] = new Request($this->container, array('REQUEST_METHOD' => 'GET'), $params);

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect', 'html'))
            ->getMock();

        $this->container['response']
            ->expects($responseMethod === 'redirect' ? $this->once() : $this->never())
            ->method('redirect');

        $this->container['response']
            ->expects($this->never())
            ->method('html');
    }

    private function createProject($role)
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Project')));

        if ($role === 'Custom Role') {
            $this->assertEquals(1, $projectRoleModel->create(1, 'Custom Role'));
        }

        $this->assertTrue($projectUserRoleModel->addUser(1, 2, $role));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'column_id' => 1, 'title' => 'Task')));

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
            'username' => 'user',
        );
    }
}
