<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Validator\CommentValidator;

class CommentValidatorTest extends Base
{
    public function testValidateMailCreation()
    {
        $commentValidator = new CommentValidator($this->container);

        $result = $commentValidator->validateEmailCreation(array(
            'user_id' => 1,
            'task_id' => 1,
            'comment' => 'blah',
            'emails'  => 'test@localhost, another@localhost',
            'subject' => 'something',
        ));

        $this->assertTrue($result[0]);

        $result = $commentValidator->validateEmailCreation(array(
            'user_id' => 1,
            'task_id' => 1,
            'comment' => 'blah',
            'subject' => 'something',
        ));

        $this->assertFalse($result[0]);
    }

    public function testValidateCreation()
    {
        $commentValidator = new CommentValidator($this->container);

        $result = $commentValidator->validateCreation(array('user_id' => 1, 'task_id' => 1, 'comment' => 'bla'));
        $this->assertTrue($result[0]);

        $result = $commentValidator->validateCreation(array('user_id' => 1, 'task_id' => 1, 'comment' => ''));
        $this->assertFalse($result[0]);

        $result = $commentValidator->validateCreation(array('user_id' => 1, 'task_id' => 'a', 'comment' => 'bla'));
        $this->assertFalse($result[0]);

        $result = $commentValidator->validateCreation(array('user_id' => 'b', 'task_id' => 1, 'comment' => 'bla'));
        $this->assertFalse($result[0]);

        $result = $commentValidator->validateCreation(array('user_id' => 1, 'comment' => 'bla'));
        $this->assertFalse($result[0]);

        $result = $commentValidator->validateCreation(array('task_id' => 1, 'comment' => 'bla'));
        $this->assertTrue($result[0]);

        $result = $commentValidator->validateCreation(array('comment' => 'bla'));
        $this->assertFalse($result[0]);

        $result = $commentValidator->validateCreation(array());
        $this->assertFalse($result[0]);
    }

    public function testValidateModification()
    {
        $commentValidator = new CommentValidator($this->container);

        $result = $commentValidator->validateModification(array('id' => 1, 'comment' => 'bla'));
        $this->assertTrue($result[0]);

        $result = $commentValidator->validateModification(array('id' => 1, 'comment' => ''));
        $this->assertFalse($result[0]);

        $result = $commentValidator->validateModification(array('comment' => 'bla'));
        $this->assertFalse($result[0]);

        $result = $commentValidator->validateModification(array('id' => 'b', 'comment' => 'bla'));
        $this->assertFalse($result[0]);

        $result = $commentValidator->validateModification(array());
        $this->assertFalse($result[0]);
    }
}
