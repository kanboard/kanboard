<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Validator\TaskValidator;

class TaskValidatorTest extends Base
{
    public function testValidationEmailCreation()
    {
        $taskValidator = new TaskValidator($this->container);

        $result = $taskValidator->validateEmailCreation(array('email' => 'test@localhost', 'subject' => 'test'));
        $this->assertTrue($result[0]);

        $result = $taskValidator->validateEmailCreation(array('email' => 'test', 'subject' => 'test'));
        $this->assertFalse($result[0]);

        $result = $taskValidator->validateEmailCreation(array('subject' => 'test'));
        $this->assertFalse($result[0]);

        $result = $taskValidator->validateEmailCreation(array('email' => 'test@localhost'));
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
}
