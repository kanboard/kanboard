<?php

require_once __DIR__.'/../Base.php';

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
}
