<?php

namespace KanboardTests\units\Validator;

use KanboardTests\units\Base;
use Kanboard\Core\Security\Role;
use Kanboard\Model\CategoryModel;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\SwimlaneModel;
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

    public function testProjectConsistency()
    {
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);
        $categoryModel = new CategoryModel($this->container);
        $userModel = new UserModel($this->container);
        $userRoleModel = new ProjectUserRoleModel($this->container);
        $taskValidator = new TaskValidator($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project 2')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'Category 1', 'project_id' => 1)));
        $this->assertEquals(2, $categoryModel->create(array('name' => 'Category 2', 'project_id' => 2)));
        $this->assertEquals(2, $userModel->create(array('username' => 'member')));
        $this->assertEquals(3, $userModel->create(array('username' => 'outsider')));
        $this->assertTrue($userRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));

        $swimlanesProject1 = $swimlaneModel->getAll(1);
        $swimlanesProject2 = $swimlaneModel->getAll(2);

        // References belonging to the same project are accepted
        $result = $taskValidator->validateCreation(array(
            'project_id' => 1,
            'title' => 'test',
            'column_id' => $columnModel->getFirstColumnId(1),
            'swimlane_id' => $swimlanesProject1[0]['id'],
            'category_id' => 1,
            'owner_id' => 2,
        ));
        $this->assertTrue($result[0]);

        // A column from another project is rejected
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'column_id' => $columnModel->getFirstColumnId(2)));
        $this->assertFalse($result[0]);
        $this->assertArrayHasKey('column_id', $result[1]);

        // A swimlane from another project is rejected
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'swimlane_id' => $swimlanesProject2[0]['id']));
        $this->assertFalse($result[0]);
        $this->assertArrayHasKey('swimlane_id', $result[1]);

        // A category from another project is rejected
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'category_id' => 2));
        $this->assertFalse($result[0]);
        $this->assertArrayHasKey('category_id', $result[1]);

        // An owner who is not a project member is rejected
        $result = $taskValidator->validateCreation(array('project_id' => 1, 'title' => 'test', 'owner_id' => 3));
        $this->assertFalse($result[0]);
        $this->assertArrayHasKey('owner_id', $result[1]);
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
}
