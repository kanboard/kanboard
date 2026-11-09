<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\BoardAjaxController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Model\ColumnMoveRestrictionModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectRoleModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class BoardAjaxControllerTest extends Base
{
    public function testSaveMovesTask()
    {
        $this->createProjects(Role::PROJECT_MEMBER, 1);
        $this->buildRequest(array('task_id' => 1, 'src_column_id' => 1, 'dst_column_id' => 2, 'swimlane_id' => 1, 'position' => 1), 'html');

        $controller = new BoardAjaxController($this->container);
        $controller->save();

        $task = $this->container['taskFinderModel']->getById(1);
        $this->assertEquals(2, $task['column_id']);
    }

    public function testSaveWithColumnFromAnotherProject()
    {
        $this->createProjects(Role::PROJECT_MEMBER, 1);
        $this->buildRequest(array('task_id' => 1, 'src_column_id' => 1, 'dst_column_id' => 6, 'swimlane_id' => 2, 'position' => 1), 'status');

        $controller = new BoardAjaxController($this->container);
        $controller->save();

        $task = $this->container['taskFinderModel']->getById(1);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['swimlane_id']);
    }

    public function testSaveChecksMoveRestrictionsWithCurrentTaskColumn()
    {
        $projectRoleModel = new ProjectRoleModel($this->container);
        $columnMoveRestrictionModel = new ColumnMoveRestrictionModel($this->container);

        $this->createProjects('Restricted', 3);
        $this->assertEquals(1, $projectRoleModel->create(1, 'Restricted'));
        $this->assertEquals(1, $columnMoveRestrictionModel->create(1, 1, 1, 2));

        $this->buildRequest(array('task_id' => 1, 'src_column_id' => 1, 'dst_column_id' => 2, 'swimlane_id' => 1, 'position' => 1));

        $controller = new BoardAjaxController($this->container);

        try {
            $controller->save();
            $this->fail('AccessForbiddenException was not thrown');
        } catch (AccessForbiddenException $e) {
        }

        $task = $this->container['taskFinderModel']->getById(1);
        $this->assertEquals(3, $task['column_id']);
    }

    private function buildRequest(array $values, $responseMethod = null)
    {
        $this->container['request'] = $this->getMockBuilder(Request::class)
            ->setConstructorArgs(array(
                $this->container,
                array(
                    'REQUEST_METHOD'        => 'POST',
                    'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
                    'CONTENT_TYPE'          => 'application/json',
                ),
                array(
                    'project_id' => 1,
                    'csrf_token' => $this->container['token']->getReusableCSRFToken(),
                ),
            ))
            ->onlyMethods(array('getBody'))
            ->getMock();

        $this->container['request']
            ->expects($this->atLeastOnce())
            ->method('getBody')
            ->willReturn(json_encode($values));

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('html', 'status'))
            ->getMock();

        $this->container['response']
            ->expects($responseMethod === 'html' ? $this->once() : $this->never())
            ->method('html');

        $this->container['response']
            ->expects($responseMethod === 'status' ? $this->once() : $this->never())
            ->method('status')
            ->with(400);
    }

    private function createProjects($role, $columnId)
    {
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'member')));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project A')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project B')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, $role));

        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'Task A1', 'column_id' => $columnId)));

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
            'username' => 'member',
            'filter' => '',
        );
    }
}
