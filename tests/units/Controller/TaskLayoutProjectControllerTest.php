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
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * These forms are normally opened in a modal (AJAX). Opened as a page of their own (new tab, middle click),
 * they render the task layout, whose project header needs the project.
 */
class TaskLayoutProjectControllerTest extends Base
{
    public static function controllers(): array
    {
        return array(
            'comment' => array(CommentController::class, 'controller=CommentController&amp;action=save'),
            'comment by e-mail' => array(CommentMailController::class, 'controller=CommentMailController&amp;action=save'),
            'task by e-mail' => array(TaskMailController::class, 'controller=TaskMailController&amp;action=send'),
        );
    }

    #[DataProvider('controllers')]
    public function testCreateRendersTheProjectHeaderAsAFullPage(string $class, string $formAction)
    {
        $this->createProject();
        $html = $this->renderCreate($class, false);

        $this->assertStringContainsString('class="project-header"', $html);
        $this->assertStringContainsString($formAction, $html);
    }

    public function testTaskLayoutLoadsTheProjectWhenTheCallerDoesNotPassIt()
    {
        $this->createProject();
        $taskFinderModel = new TaskFinderModel($this->container);

        $html = $this->container['helper']->layout->task('comment/create', array(
            'values' => array('project_id' => 1, 'task_id' => 1),
            'errors' => array(),
            'task' => $taskFinderModel->getDetails(1),
        ));

        $this->assertStringContainsString('class="project-header"', $html);
    }

    public function testTaskLayoutKeepsAProjectPassedByTheCaller()
    {
        $this->createProject();
        $projectModel = new ProjectModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project B')));

        $html = $this->container['helper']->layout->task('comment/create', array(
            'values' => array('project_id' => 1, 'task_id' => 1),
            'errors' => array(),
            'task' => $taskFinderModel->getDetails(1),
            'project' => $projectModel->getById(2),
        ));

        $this->assertStringContainsString('project_id=2', $html);
    }

    public function testPredefinedEmailSubjectsAreShownInTheModal()
    {
        $this->createProject();
        $projectModel = new ProjectModel($this->container);
        $this->assertTrue($projectModel->update(array('id' => 1, 'predefined_email_subjects' => "Weekly report\r\nInvoice")));

        $html = $this->renderCreate(TaskMailController::class, true);

        $this->assertStringContainsString('Weekly report', $html);
        $this->assertStringNotContainsString('class="project-header"', $html);
    }

    private function renderCreate(string $class, bool $ajax): string
    {
        $server = array('REQUEST_METHOD' => 'GET');
        if ($ajax) {
            $server['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        }

        $this->container['request'] = new Request($this->container, $server, array('task_id' => 1, 'project_id' => 1));

        $html = null;
        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('html'))
            ->getMock();
        $this->container['response']
            ->expects($this->once())
            ->method('html')
            ->willReturnCallback(function ($data) use (&$html) {
                $html = $data;
            });

        $controller = new $class($this->container);
        $controller->create();

        return $html;
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
        $_SESSION['user']['role'] = Role::APP_USER;
    }
}
