<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Validator\CommentValidator;

class CommentValidatorTest extends Base
{
    public function testValidateCreation()
    {
        $validator = new CommentValidator($this->container);

        $result = $validator->validateCreation(array('user_id' => 1, 'task_id' => 1, 'comment' => 'bla'));
        $this->assertTrue($result[0]);

        $result = $validator->validateCreation(array('user_id' => 1, 'task_id' => 1, 'comment' => ''));
        $this->assertFalse($result[0]);

        $result = $validator->validateCreation(array('user_id' => 1, 'task_id' => 'a', 'comment' => 'bla'));
        $this->assertFalse($result[0]);

        $result = $validator->validateCreation(array('user_id' => 'b', 'task_id' => 1, 'comment' => 'bla'));
        $this->assertFalse($result[0]);

        $result = $validator->validateCreation(array('user_id' => 1, 'comment' => 'bla'));
        $this->assertFalse($result[0]);

        $result = $validator->validateCreation(array('task_id' => 1, 'comment' => 'bla'));
        $this->assertTrue($result[0]);

        $result = $validator->validateCreation(array('comment' => 'bla'));
        $this->assertFalse($result[0]);

        $result = $validator->validateCreation(array());
        $this->assertFalse($result[0]);
    }

    public function testValidateModification()
    {
        $validator = new CommentValidator($this->container);

        $result = $validator->validateModification(array('id' => 1, 'comment' => 'bla'));
        $this->assertTrue($result[0]);

        $result = $validator->validateModification(array('id' => 1, 'comment' => ''));
        $this->assertFalse($result[0]);

        $result = $validator->validateModification(array('comment' => 'bla'));
        $this->assertFalse($result[0]);

        $result = $validator->validateModification(array('id' => 'b', 'comment' => 'bla'));
        $this->assertFalse($result[0]);

        $result = $validator->validateModification(array());
        $this->assertFalse($result[0]);
    }
}
