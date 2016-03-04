<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Validator\PasswordResetValidator;

class PasswordResetValidatorTest extends Base
{
    public function testValidateModification()
    {
        $validator = new PasswordResetValidator($this->container);
        list($valid, ) = $validator->validateModification(array('password' => 'test123', 'confirmation' => 'test123'));
        $this->assertTrue($valid);
    }

    public function testValidateModificationWithWrongPasswords()
    {
        $validator = new PasswordResetValidator($this->container);
        list($valid, ) = $validator->validateModification(array('password' => 'test123', 'confirmation' => 'test456'));
        $this->assertFalse($valid);
    }

    public function testValidateModificationWithPasswordTooShort()
    {
        $validator = new PasswordResetValidator($this->container);
        list($valid, ) = $validator->validateModification(array('password' => 'test', 'confirmation' => 'test'));
        $this->assertFalse($valid);
    }

    public function testValidateCreation()
    {
        $this->container['sessionStorage']->captcha = 'test';

        $validator = new PasswordResetValidator($this->container);
        list($valid,) = $validator->validateCreation(array('username' => 'foobar', 'captcha' => 'test'));
        $this->assertTrue($valid);
    }

    public function testValidateCreationWithNoUsername()
    {
        $this->container['sessionStorage']->captcha = 'test';

        $validator = new PasswordResetValidator($this->container);
        list($valid,) = $validator->validateCreation(array('captcha' => 'test'));
        $this->assertFalse($valid);
    }

    public function testValidateCreationWithWrongCaptcha()
    {
        $this->container['sessionStorage']->captcha = 'test123';

        $validator = new PasswordResetValidator($this->container);
        list($valid,) = $validator->validateCreation(array('username' => 'foobar', 'captcha' => 'test'));
        $this->assertFalse($valid);
    }

    public function testValidateCreationWithMissingCaptcha()
    {
        $validator = new PasswordResetValidator($this->container);
        list($valid,) = $validator->validateCreation(array('username' => 'foobar', 'captcha' => 'test'));
        $this->assertFalse($valid);
    }
}
