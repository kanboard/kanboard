<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\TaskBulkChangePropertyController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Model\CategoryModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskLinkModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class TaskBulkChangePropertyControllerTest extends Base
{
    public function testAddInternalLinkToTaskInSameProject()
    {
        $this->createProjects();
        $this->buildRequest(array(
            'task_ids'            => '1',
            'change_internallink' => 1,
            'opposite_task_id'    => 2,
            'link_id'             => 1,
        ), 'redirect');

        $controller = new TaskBulkChangePropertyController($this->container);
        $controller->save();

        $taskLinkModel = new TaskLinkModel($this->container);
        $links = $taskLinkModel->getAll(1);
        $this->assertCount(1, $links);
        $this->assertEquals(2, $links[0]['task_id']);
    }

    public function testAddInternalLinkToTaskInInaccessibleProject()
    {
        $this->createProjects();
        $this->buildRequest(array(
            'task_ids'            => '1',
            'change_internallink' => 1,
            'opposite_task_id'    => 3,
            'link_id'             => 1,
            'change_color'        => 1,
            'color_id'            => 'red',
        ));

        $controller = new TaskBulkChangePropertyController($this->container);

        try {
            $controller->save();
            $this->fail('AccessForbiddenException was not thrown');
        } catch (AccessForbiddenException $e) {
        }

        $taskLinkModel = new TaskLinkModel($this->container);
        $this->assertEmpty($taskLinkModel->getAll(1));
        $this->assertEmpty($taskLinkModel->getAll(3));

        $task = $this->container['taskFinderModel']->getById(1);
        $this->assertNotEquals('red', $task['color_id']);
    }

    public function testAddInternalLinkToNonExistentTask()
    {
        $this->createProjects();
        $this->buildRequest(array(
            'task_ids'            => '1',
            'change_internallink' => 1,
            'opposite_task_id'    => 999,
            'link_id'             => 1,
        ));

        $controller = new TaskBulkChangePropertyController($this->container);

        $this->expectException(AccessForbiddenException::class);
        $controller->save();
    }

    public function testChangeAssigneeToUserWithoutProjectAccess()
    {
        $this->createProjects();
        $userModel = new UserModel($this->container);
        $this->assertEquals(3, $userModel->create(array('username' => 'outsider')));

        $this->buildRequest(array(
            'task_ids'        => '1',
            'change_assignee' => 1,
            'owner_id'        => 3,
            'change_color'    => 1,
            'color_id'        => 'red',
        ));

        $controller = new TaskBulkChangePropertyController($this->container);

        try {
            $controller->save();
            $this->fail('AccessForbiddenException was not thrown');
        } catch (AccessForbiddenException $e) {
        }

        $task = $this->container['taskFinderModel']->getById(1);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertNotEquals('red', $task['color_id']);
    }

    public function testChangeCategoryFromAnotherProject()
    {
        $this->createProjects();
        $categoryModel = new CategoryModel($this->container);
        $this->assertEquals(1, $categoryModel->create(array('project_id' => 2, 'name' => 'Category B')));

        $this->buildRequest(array(
            'task_ids'        => '1',
            'change_category' => 1,
            'category_id'     => 1,
        ));

        $controller = new TaskBulkChangePropertyController($this->container);

        try {
            $controller->save();
            $this->fail('AccessForbiddenException was not thrown');
        } catch (AccessForbiddenException $e) {
        }

        $task = $this->container['taskFinderModel']->getById(1);
        $this->assertEquals(0, $task['category_id']);
    }

    public function testChangeAssigneeAndCategory()
    {
        $this->createProjects();
        $categoryModel = new CategoryModel($this->container);
        $this->assertEquals(1, $categoryModel->create(array('project_id' => 1, 'name' => 'Category A')));

        $this->buildRequest(array(
            'task_ids'        => '1,2',
            'change_assignee' => 1,
            'owner_id'        => 2,
            'change_category' => 1,
            'category_id'     => 1,
        ), 'redirect');

        $controller = new TaskBulkChangePropertyController($this->container);
        $controller->save();

        foreach (array(1, 2) as $taskId) {
            $task = $this->container['taskFinderModel']->getById($taskId);
            $this->assertEquals(2, $task['owner_id']);
            $this->assertEquals(1, $task['category_id']);
        }
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
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'Task A2')));
        $this->assertEquals(3, $taskCreationModel->create(array('project_id' => 2, 'title' => 'Task B1')));

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
            'username' => 'member',
        );
    }
}
