<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\TaskDuplicationController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Core\Template;
use Kanboard\Model\ColumnRestrictionModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectRoleModel;
use Kanboard\Model\ProjectRoleRestrictionModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class TaskDuplicationControllerTest extends Base
{
    public function testCopyToProjectWhereUserIsMember()
    {
        $this->createProjects(Role::PROJECT_MEMBER);
        $this->buildRequest(5, true);

        $controller = new TaskDuplicationController($this->container);
        $controller->copy();

        $this->assertEquals(1, $this->container['taskFinderModel']->countByProjectId(2));
    }

    public function testCopyToProjectWhereUserIsViewer()
    {
        $this->createProjects(Role::PROJECT_VIEWER);
        $this->buildRequest(5, false);

        $controller = new TaskDuplicationController($this->container);

        try {
            $controller->copy();
            $this->fail('A project viewer must not be able to copy a task into the project');
        } catch (AccessForbiddenException $e) {
            $this->assertEquals(0, $this->container['taskFinderModel']->countByProjectId(2));
        }
    }

    public function testMoveToProjectWhereUserIsViewer()
    {
        $this->createProjects(Role::PROJECT_VIEWER);
        $this->buildRequest(5, false);

        $controller = new TaskDuplicationController($this->container);

        try {
            $controller->move();
            $this->fail('A project viewer must not be able to move a task into the project');
        } catch (AccessForbiddenException $e) {
            $this->assertEquals(1, $this->container['taskFinderModel']->getProjectId(1));
        }
    }

    public function testMoveToProjectWithTaskCreationRestriction()
    {
        $this->createProjects('Custom Role');
        $projectRoleRestrictionModel = new ProjectRoleRestrictionModel($this->container);
        $this->assertEquals(1, $projectRoleRestrictionModel->create(2, 1, ProjectRoleRestrictionModel::RULE_TASK_CREATION));
        $this->buildRequest(5, false);

        $controller = new TaskDuplicationController($this->container);

        try {
            $controller->move();
            $this->fail('A custom role without task creation must not be able to move a task into the project');
        } catch (AccessForbiddenException $e) {
            $this->assertEquals(1, $this->container['taskFinderModel']->getProjectId(1));
        }
    }

    public function testCopyWithColumnRestrictionOnColumnMatchedByTitle()
    {
        $this->createProjects('Custom Role');
        $columnRestrictionModel = new ColumnRestrictionModel($this->container);
        $this->assertEquals(1, $columnRestrictionModel->create(2, 1, 5, ColumnRestrictionModel::RULE_BLOCK_TASK_CREATION));

        // Column 1 is "Backlog" in the source project, the task lands in "Backlog" (column 5) of the destination
        $this->buildRequest(1, false);

        $controller = new TaskDuplicationController($this->container);

        try {
            $controller->copy();
            $this->fail('The column restriction of the destination column must be applied');
        } catch (AccessForbiddenException $e) {
            $this->assertEquals(0, $this->container['taskFinderModel']->countByProjectId(2));
        }
    }

    public function testDestinationListExcludesProjectsWhereUserIsViewer()
    {
        $this->createProjects(Role::PROJECT_MEMBER);
        $this->assertEquals(3, $this->container['projectModel']->create(array('name' => 'Read only')));
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(3, 2, Role::PROJECT_VIEWER));
        $this->buildDestinationRequest();
        $params = $this->captureTemplateParams();

        $controller = new TaskDuplicationController($this->container);
        $controller->copy();

        $this->assertSame(array(2 => 'Destination'), $params['task_duplication/copy']['projects_list']);
    }

    public function testDestinationListExcludesProjectsWithoutAllowedColumn()
    {
        $this->createProjects('Custom Role');
        $projectRoleRestrictionModel = new ProjectRoleRestrictionModel($this->container);
        $this->assertEquals(1, $projectRoleRestrictionModel->create(2, 1, ProjectRoleRestrictionModel::RULE_TASK_CREATION));
        $this->buildDestinationRequest();
        $params = $this->captureTemplateParams();

        $controller = new TaskDuplicationController($this->container);
        $controller->move();

        $this->assertSame(array(), $params['task_duplication/move']['projects_list']);
    }

    public function testDestinationColumnsExcludeColumnsWithTaskCreationBlocked()
    {
        $this->createProjects('Custom Role');
        $columnRestrictionModel = new ColumnRestrictionModel($this->container);
        $this->assertEquals(1, $columnRestrictionModel->create(2, 1, 5, ColumnRestrictionModel::RULE_BLOCK_TASK_CREATION));
        $this->buildDestinationRequest();
        $params = $this->captureTemplateParams();

        $controller = new TaskDuplicationController($this->container);
        $controller->copy();

        $this->assertSame(array(2 => 'Destination'), $params['task_duplication/copy']['projects_list']);
        $this->assertSame(array(6, 7, 8), array_keys($params['task_duplication/copy']['columns_list']));
    }

    private function buildDestinationRequest()
    {
        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'GET'),
            array('task_id' => 1, 'project_id' => 1)
        );

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect', 'html'))
            ->getMock();

        $this->container['response']->expects($this->never())->method('redirect');
        $this->container['response']->expects($this->once())->method('html');
    }

    private function captureTemplateParams()
    {
        $params = new \ArrayObject();

        $this->container['template'] = $this->createStub(Template::class);
        $this->container['template']
            ->method('render')
            ->willReturnCallback(function ($template, array $templateParams = array()) use ($params) {
                $params[$template] = $templateParams;
                return '';
            });

        return $params;
    }

    private function buildRequest($columnId, $expectRedirect)
    {
        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'POST'),
            array('task_id' => 1, 'project_id' => 1),
            array(
                'id' => 1,
                'project_id' => 2,
                'swimlane_id' => 2,
                'column_id' => $columnId,
                'category_id' => 0,
                'owner_id' => 0,
                'csrf_token' => $this->container['token']->getCSRFToken(),
            )
        );

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect', 'html'))
            ->getMock();

        $this->container['response']
            ->expects($expectRedirect ? $this->once() : $this->never())
            ->method('redirect');

        $this->container['response']
            ->expects($this->never())
            ->method('html');
    }

    private function createProjects($destinationRole)
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Source')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Destination')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));

        if ($destinationRole === 'Custom Role') {
            $this->assertEquals(1, $projectRoleModel->create(2, 'Custom Role'));
        }

        $this->assertTrue($projectUserRoleModel->addUser(2, 2, $destinationRole));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'Task')));

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
            'username' => 'user',
        );
    }
}
