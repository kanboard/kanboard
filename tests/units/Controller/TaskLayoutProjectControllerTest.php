<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\CommentController;
use Kanboard\Controller\CommentMailController;
use Kanboard\Controller\TaskMailController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * These forms are normally opened in a modal (AJAX). Opened as a page of their own (new tab, middle click),
 * they render the task layout, which needs the project for the project header.
 */
class TaskLayoutProjectControllerTest extends Base
{
    public static function controllers(): array
    {
        return array(
            'comment' => array(CommentController::class),
            'comment by e-mail' => array(CommentMailController::class),
            'task by e-mail' => array(TaskMailController::class),
        );
    }

    #[DataProvider('controllers')]
    public function testCreateRendersAsAFullPage(string $class)
    {
        $this->createProject();

        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'GET'),
            array('task_id' => 1, 'project_id' => 1)
        );

        $html = null;
        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('html'))
            ->getMock();
        $this->container['response']
            ->expects($this->once())
            ->method('html')
            ->willReturnCallback(function ($data) use (&$html) { $html = $data; });

        $controller = new $class($this->container);
        $controller->create();

        $this->assertStringContainsString('Project A', $html);
        $this->assertStringContainsString('<form', $html);
    }

    private function createProject()
    {
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'member', 'email' => 'member@example.org')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Project A')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'Task A')));

        // A full page reads more of the session user (language, timezone, filter) than a form post does.
        $_SESSION['user'] = $userModel->getById(2);
    }
}
