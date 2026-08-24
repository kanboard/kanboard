<?php

namespace KanboardTests\units\Validator;

use Kanboard\Core\Security\Role;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\UserModel;
use Kanboard\Validator\SubtaskValidator;
use KanboardTests\units\Base;

class SubtaskValidatorTest extends Base
{
    public function testValidateCreation()
    {
        $subtaskValidator = new SubtaskValidator($this->container);
        $this->createProject();

        $values = array('task_id' => 1, 'title' => 'test');
        $this->assertTrue($subtaskValidator->validateCreation($values)[0]);

        unset($values['title']);
        $this->assertFalse($subtaskValidator->validateCreation($values)[0]);
    }

    public function testValidateCreationWithAssignee()
    {
        $subtaskValidator = new SubtaskValidator($this->container);
        $this->createProject();

        // Project member and unassigned subtasks are valid choices
        $this->assertTrue($subtaskValidator->validateCreation(array('task_id' => 1, 'title' => 'test', 'user_id' => 2))[0]);
        $this->assertTrue($subtaskValidator->validateCreation(array('task_id' => 1, 'title' => 'test', 'user_id' => 0))[0]);

        // Users that cannot be assigned to the project
        list($valid, $errors) = $subtaskValidator->validateCreation(array('task_id' => 1, 'title' => 'test', 'user_id' => 3));
        $this->assertFalse($valid);
        $this->assertArrayHasKey('user_id', $errors);

        $this->assertFalse($subtaskValidator->validateCreation(array('task_id' => 1, 'title' => 'test', 'user_id' => 4))[0]);
        $this->assertFalse($subtaskValidator->validateCreation(array('task_id' => 1, 'title' => 'test', 'user_id' => 42))[0]);

        // The task must exist to know the list of allowed users
        $this->assertFalse($subtaskValidator->validateCreation(array('task_id' => 42, 'title' => 'test', 'user_id' => 2))[0]);
    }

    public function testValidateModificationWithAssignee()
    {
        $subtaskValidator = new SubtaskValidator($this->container);
        $this->createProject();

        $this->assertTrue($subtaskValidator->validateModification(array('id' => 1, 'task_id' => 1, 'title' => 'test', 'user_id' => 2))[0]);
        $this->assertFalse($subtaskValidator->validateModification(array('id' => 1, 'task_id' => 1, 'title' => 'test', 'user_id' => 3))[0]);
    }

    public function testValidateApiModificationWithAssignee()
    {
        $subtaskValidator = new SubtaskValidator($this->container);
        $this->createProject();

        $this->assertTrue($subtaskValidator->validateApiModification(array('id' => 1, 'task_id' => 1, 'user_id' => 2))[0]);
        $this->assertTrue($subtaskValidator->validateApiModification(array('id' => 1, 'task_id' => 1))[0]);
        $this->assertFalse($subtaskValidator->validateApiModification(array('id' => 1, 'task_id' => 1, 'user_id' => 3))[0]);
    }

    private function createProject()
    {
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'member')));
        $this->assertEquals(3, $userModel->create(array('username' => 'outsider')));
        $this->assertEquals(4, $userModel->create(array('username' => 'viewer')));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project A')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($projectUserRoleModel->addUser(1, 4, Role::PROJECT_VIEWER));

        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'Task A')));
    }
}
