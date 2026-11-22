<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\BoardPopoverController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\ColumnRestrictionModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectRoleModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class BoardPopoverControllerTest extends Base
{
    public function testCloseColumnTasksAsProjectMember()
    {
        $this->createProjectWithRole(Role::PROJECT_MEMBER);
        $this->buildCloseRequest('redirect');

        (new BoardPopoverController($this->container))->closeColumnTasks();

        $this->assertEquals(0, $this->container['taskFinderModel']->getById(1)['is_active']);
        $this->assertEquals(0, $this->container['taskFinderModel']->getById(2)['is_active']);
    }

    public function testCloseColumnTasksWithColumnRestriction()
    {
        $this->createProjectWithRole('Custom Role');
        $this->assertEquals(1, (new ColumnRestrictionModel($this->container))->create(1, 1, 1, ColumnRestrictionModel::RULE_BLOCK_TASK_OPEN_CLOSE));
        $this->buildCloseRequest();

        try {
            (new BoardPopoverController($this->container))->closeColumnTasks();
            $this->fail('The column restriction must be applied');
        } catch (AccessForbiddenException $e) {
            $this->assertEquals(1, $this->container['taskFinderModel']->getById(1)['is_active']);
            $this->assertEquals(1, $this->container['taskFinderModel']->getById(2)['is_active']);
        }
    }

    public function testConfirmCloseColumnTasksWithColumnRestriction()
    {
        $this->createProjectWithRole('Custom Role');
        $this->assertEquals(1, (new ColumnRestrictionModel($this->container))->create(1, 1, 1, ColumnRestrictionModel::RULE_BLOCK_TASK_OPEN_CLOSE));
        $this->buildRequest(array(
            'project_id' => 1,
            'column_id' => 1,
            'swimlane_id' => 1,
        ));

        $this->expectException(AccessForbiddenException::class);
        (new BoardPopoverController($this->container))->confirmCloseColumnTasks();
    }

    public function testConfirmCloseColumnTasksWithColumnFromAnotherProjectIsForbidden()
    {
        $this->createProjects();
        $columnModel = new ColumnModel($this->container);
        $foreignColumn = $columnModel->getAll(2)[0];
        $swimlaneModel = new SwimlaneModel($this->container);
        $swimlane = $swimlaneModel->getFirstActiveSwimlane(1);

        $this->buildRequest(array(
            'project_id' => 1,
            'column_id' => $foreignColumn['id'],
            'swimlane_id' => $swimlane['id'],
        ));

        $this->expectException(AccessForbiddenException::class);
        (new BoardPopoverController($this->container))->confirmCloseColumnTasks();
    }

    public function testConfirmCloseColumnTasksWithSwimlaneFromAnotherProjectIsForbidden()
    {
        $this->createProjects();
        $columnModel = new ColumnModel($this->container);
        $column = $columnModel->getAll(1)[0];
        $swimlaneModel = new SwimlaneModel($this->container);
        $foreignSwimlane = $swimlaneModel->getFirstActiveSwimlane(2);

        $this->buildRequest(array(
            'project_id' => 1,
            'column_id' => $column['id'],
            'swimlane_id' => $foreignSwimlane['id'],
        ));

        $this->expectException(AccessForbiddenException::class);
        (new BoardPopoverController($this->container))->confirmCloseColumnTasks();
    }

    private function buildRequest(array $params)
    {
        $this->container['request'] = new Request($this->container, array('REQUEST_METHOD' => 'GET'), $params);
        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('html'))
            ->getMock();

        $this->container['response']
            ->expects($this->never())
            ->method('html');
    }

    private function buildCloseRequest($responseMethod = null)
    {
        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'POST'),
            array('project_id' => 1),
            array(
                'column_id' => 1,
                'swimlane_id' => 1,
                'csrf_token' => $this->container['token']->getCSRFToken(),
            )
        );

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect'))
            ->getMock();

        $this->container['response']
            ->expects($responseMethod === 'redirect' ? $this->once() : $this->never())
            ->method('redirect');
    }

    private function createProjectWithRole($role)
    {
        $this->assertEquals(2, (new UserModel($this->container))->create(array('username' => 'user')));
        $this->assertEquals(1, (new ProjectModel($this->container))->create(array('name' => 'Project A')));

        if ($role === 'Custom Role') {
            $this->assertEquals(1, (new ProjectRoleModel($this->container))->create(1, 'Custom Role'));
        }

        $this->assertTrue((new ProjectUserRoleModel($this->container))->addUser(1, 2, $role));

        $taskCreationModel = new TaskCreationModel($this->container);
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'column_id' => 1, 'swimlane_id' => 1, 'title' => 'Task 1')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'column_id' => 1, 'swimlane_id' => 1, 'title' => 'Task 2')));

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
            'username' => 'user',
        );
    }

    private function createProjects()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project A')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project B')));
    }
}
