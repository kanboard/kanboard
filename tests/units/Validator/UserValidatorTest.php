<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Validator\UserValidator;
use Kanboard\Core\Security\Role;

class UserValidatorTest extends Base
{
    public function testValidatePasswordModification()
    {
        $validator = new UserValidator($this->container);

        $this->container['sessionStorage']->user = array(
            'id' => 1,
            'role' => Role::APP_ADMIN,
            'username' => 'admin',
        );

        $result = $validator->validatePasswordModification(array());
        $this->assertFalse($result[0]);

        $result = $validator->validatePasswordModification(array('id' => 1));
        $this->assertFalse($result[0]);

        $result = $validator->validatePasswordModification(array('id' => 1, 'password' => '123456'));
        $this->assertFalse($result[0]);

        $result = $validator->validatePasswordModification(array('id' => 1, 'password' => '123456', 'confirmation' => 'wrong'));
        $this->assertFalse($result[0]);

        $result = $validator->validatePasswordModification(array('id' => 1, 'password' => '123456', 'confirmation' => '123456'));
        $this->assertFalse($result[0]);

        $result = $validator->validatePasswordModification(array('id' => 1, 'password' => '123456', 'confirmation' => '123456', 'current_password' => 'wrong'));
        $this->assertFalse($result[0]);

        $result = $validator->validatePasswordModification(array('id' => 1, 'password' => '123456', 'confirmation' => '123456', 'current_password' => 'admin'));
        $this->assertTrue($result[0]);
    }
}
