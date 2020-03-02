<?php

use JsonRPC\Validator\UserValidator;

require_once __DIR__.'/../../../../vendor/autoload.php';

class UserValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testWithEmptyHosts()
    {
        $this->assertNull(UserValidator::validate(array(), 'user', 'pass'));
    }

    public function testWithValidHosts()
    {
        $this->assertNull(UserValidator::validate(array('user' => 'pass'), 'user', 'pass'));
    }

    public function testWithNotAuthorizedHosts()
    {
        $this->expectException('\JsonRPC\Exception\AuthenticationFailureException');
        UserValidator::validate(array('user' => 'pass'), 'user', 'wrong password');
    }
}
