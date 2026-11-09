<?php

namespace KanboardTests\units\Validator;

use KanboardTests\units\Base;
use Kanboard\Core\Security\Role;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\UserModel;
use Kanboard\Validator\TaskValidator;

class TaskValidatorTest extends Base
{
    public function testValidationEmailCreation()
    {
        $taskValidator = new TaskValidator($this->container);

        $result = $taskValidator->validateEmailCreation(array('emails' => 'test@localhost', 'subject' => 'test'));
        $this->assertTrue($result[0]);

        $result = $taskValidator->validateEmailCreation(array('subject' => 'test'));
        $this->assertFalse($result[0]);

        $result = $taskValidator->validateEmailCreation(array('emails' => 'test@localhost, test@localhost'));
        $this->assertFalse($result[0]);
    }

    public function testRequiredFields()
    {
        $taskValidator = new TaskValidator($this->container);

        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test'));
        $this->assertTrue($result[0]);

        $result = $taskValidator->validateCreation(array('project_id' => 1));
        $this->assertFalse($result[0]);

        $result = $taskValidator->validateCreation(array('title' => 'test'));
        $this->assertFalse($result[0]);
    }

    public function testRangeFields()
    {
        $taskValidator = new TaskValidator($this->container);

        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'score' => 2147483647));
        $this->assertTrue($result[0]);

        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'score' => -2147483647));
        $this->assertTrue($result[0]);

        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'score' => 0));
        $this->assertTrue($result[0]);

        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'score' => 2147483648));
        $this->assertFalse($result[0]);

        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'score' => -2147483648));
        $this->assertFalse($result[0]);
    }

    public function testSwimlaneIdField()
    {
        $taskValidator = new TaskValidator($this->container);

        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'swimlane_id' => 1));
        $this->assertTrue($result[0]);

        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'swimlane_id' => 0));
        $this->assertFalse($result[0]);
    }

    public function testStartAndDueDateFields()
    {
        $taskValidator = new TaskValidator($this->container);

        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_due' => '09/11/2017 10:50', 'date_started' => '09/11/2017 9:50'));
        $this->assertTrue($result[0]);

        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_due' => '09/11/2017 10:50', 'date_started' => '09/11/2017 10:50'));
        $this->assertTrue($result[0]);

        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_due' => '09/11/2017 10:50', 'date_started' => '09/11/2017 11:50'));
        $this->assertFalse($result[0]);

        // date_due
        // ISO dates
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_due' => '2017-02-01'));
        $this->assertTrue($result[0]);
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_due' => '2017-02-01 13:15'));
        $this->assertTrue($result[0]);
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_due' => '2017-02-01 1:15 pm'));
        $this->assertFalse($result[0]);
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_due' => '2017_02_01'));
        $this->assertTrue($result[0]);
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_due' => '2017_02_01 13:15'));
        $this->assertTrue($result[0]);
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_due' => '2017_02_01 1:15 am'));
        $this->assertFalse($result[0]);

        // d/m/Y dates
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_due' => '02/01/2017'));
        $this->assertTrue($result[0]);
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_due' => '02/01/2017 13:15'));
        $this->assertTrue($result[0]);
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_due' => '02/01/2017 1:15 pm'));
        $this->assertFalse($result[0]);

        // date_started
        // ISO dates
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_started' => '2017-02-01'));
        $this->assertTrue($result[0]);
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_started' => '2017-02-01 13:15'));
        $this->assertTrue($result[0]);
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_started' => '2017-02-01 1:15 pm'));
        $this->assertFalse($result[0]);
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_started' => '2017_02_01'));
        $this->assertTrue($result[0]);
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_started' => '2017_02_01 13:15'));
        $this->assertTrue($result[0]);
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_started' => '2017_02_01 1:15 pm'));
        $this->assertFalse($result[0]);

        // d/m/Y dates
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_started' => '02/01/2017'));
        $this->assertTrue($result[0]);
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_started' => '02/01/2017 13:15'));
        $this->assertTrue($result[0]);
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'date_started' => '02/01/2017 1:15 pm'));
        $this->assertFalse($result[0]);
    }

    public function testValidateAssignee()
    {
        $taskValidator = new TaskValidator($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(2, $userModel->create(array('username' => 'member')));
        $this->assertEquals(3, $userModel->create(array('username' => 'viewer')));
        $this->assertEquals(4, $userModel->create(array('username' => 'outsider')));
        $this->assertEquals(5, $userModel->create(array('username' => 'inactive', 'is_active' => 0)));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($projectUserRoleModel->addUser(1, 3, Role::PROJECT_VIEWER));
        $this->assertTrue($projectUserRoleModel->addUser(1, 5, Role::PROJECT_MEMBER));

        $this->assertTrue($taskValidator->validateAssignee(array('project_id' => 1))[0]);
        $this->assertTrue($taskValidator->validateAssignee(array('project_id' => 1, 'owner_id' => 0))[0]);
        $this->assertTrue($taskValidator->validateAssignee(array('project_id' => 1, 'owner_id' => 2))[0]);

        foreach (array(3, 4, 5, 999) as $userId) {
            list($valid, $errors) = $taskValidator->validateAssignee(array('project_id' => 1, 'owner_id' => $userId));
            $this->assertFalse($valid);
            $this->assertArrayHasKey('owner_id', $errors);
        }

        $this->assertFalse($taskValidator->validateAssignee(array('owner_id' => 2))[0]);

        $this->assertTrue($taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'owner_id' => 2))[0]);
        $this->assertFalse($taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'owner_id' => 4))[0]);

        $values = array('project_id' => 1, 'tasks' => 'test', 'column_id' => 1, 'swimlane_id' => 1);
        $this->assertTrue($taskValidator->validateBulkCreation($values + array('owner_id' => 2))[0]);
        $this->assertFalse($taskValidator->validateBulkCreation($values + array('owner_id' => 4))[0]);
    }
}
