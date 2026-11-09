<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\TaskModificationController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Model\CategoryModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class TaskModificationControllerTest extends Base
{
    public function testUpdateIgnoresFieldsOutsideTheForm()
    {
        $this->createProjects();
        $this->buildRequest(array(
            'title'          => 'Changed',
            'column_id'      => 6,
            'swimlane_id'    => 2,
            'position'       => 10,
            'is_active'      => 0,
            'creator_id'     => 3,
            'date_completed' => 123,
            'project_id'     => 2,
        ), 'redirect');

        $controller = new TaskModificationController($this->container);
        $controller->update();

        $task = $this->container['taskFinderModel']->getById(1);
        $this->assertEquals('Changed', $task['title']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['swimlane_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['is_active']);
        $this->assertEquals(0, $task['creator_id']);
        $this->assertEmpty($task['date_completed']);
    }

    public function testUpdateWithCategoryFromAnotherProject()
    {
        $this->createProjects();
        $this->buildRequest(array('title' => 'Changed', 'category_id' => 2), 'html');

        $controller = new TaskModificationController($this->container);
        $controller->update();

        $task = $this->container['taskFinderModel']->getById(1);
        $this->assertEquals('Task A1', $task['title']);
        $this->assertEquals(0, $task['category_id']);
    }

    public function testUpdateWithCategoryFromSameProject()
    {
        $this->createProjects();
        $this->buildRequest(array('title' => 'Changed', 'category_id' => 1), 'redirect');

        $controller = new TaskModificationController($this->container);
        $controller->update();

        $task = $this->container['taskFinderModel']->getById(1);
        $this->assertEquals(1, $task['category_id']);
    }

    public function testUpdateWithUnassignableOwner()
    {
        $this->createProjects();
        $this->buildRequest(array('title' => 'Changed', 'owner_id' => 3), 'html');

        $controller = new TaskModificationController($this->container);
        $controller->update();

        $task = $this->container['taskFinderModel']->getById(1);
        $this->assertEquals('Task A1', $task['title']);
        $this->assertEquals(0, $task['owner_id']);
    }

    public function testUpdateWithAssignableOwner()
    {
        $this->createProjects();
        $this->buildRequest(array('title' => 'Changed', 'owner_id' => 2), 'redirect');

        $controller = new TaskModificationController($this->container);
        $controller->update();

        $task = $this->container['taskFinderModel']->getById(1);
        $this->assertEquals(2, $task['owner_id']);
    }

    private function buildRequest(array $values, $responseMethod)
    {
        $values['csrf_token'] = $this->container['token']->getCSRFToken();

        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'POST'),
            array('task_id' => 1),
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
        $categoryModel = new CategoryModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'member')));
        $this->assertEquals(3, $userModel->create(array('username' => 'outsider')));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project A')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project B')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));

        $this->assertEquals(1, $categoryModel->create(array('project_id' => 1, 'name' => 'Category A')));
        $this->assertEquals(2, $categoryModel->create(array('project_id' => 2, 'name' => 'Category B')));

        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'Task A1')));

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
            'username' => 'member',
        );
    }
}
