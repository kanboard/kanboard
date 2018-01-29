<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Validator\PasswordResetValidator;

class PasswordResetValidatorTest extends Base
{
    public function testValidateModification()
    {
        $passwordResetValidator = new PasswordResetValidator($this->container);
        list($valid, ) = $passwordResetValidator->validateModification(array('password' => 'test123', 'confirmation' => 'test123'));
        $this->assertTrue($valid);
    }

    public function testValidateModificationWithWrongPasswords()
    {
        $passwordResetValidator = new PasswordResetValidator($this->container);
        list($valid, ) = $passwordResetValidator->validateModification(array('password' => 'test123', 'confirmation' => 'test456'));
        $this->assertFalse($valid);
    }

    public function testValidateModificationWithPasswordTooShort()
    {
        $passwordResetValidator = new PasswordResetValidator($this->container);
        list($valid, ) = $passwordResetValidator->validateModification(array('password' => 'test', 'confirmation' => 'test'));
        $this->assertFalse($valid);
    }

    public function testValidateCreation()
    {
        $_SESSION['captcha'] = 'test';

        $passwordResetValidator = new PasswordResetValidator($this->container);
        list($valid,) = $passwordResetValidator->validateCreation(array('username' => 'foobar', 'captcha' => 'test'));
        $this->assertTrue($valid);
    }

    public function testValidateCreationWithNoUsername()
    {
        $_SESSION['captcha'] = 'test';

        $passwordResetValidator = new PasswordResetValidator($this->container);
        list($valid,) = $passwordResetValidator->validateCreation(array('captcha' => 'test'));
        $this->assertFalse($valid);
    }

    public function testValidateCreationWithWrongCaptcha()
    {
        $_SESSION['captcha'] = 'test123';

        $passwordResetValidator = new PasswordResetValidator($this->container);
        list($valid,) = $passwordResetValidator->validateCreation(array('username' => 'foobar', 'captcha' => 'test'));
        $this->assertFalse($valid);
    }

    public function testValidateCreationWithMissingCaptcha()
    {
        $passwordResetValidator = new PasswordResetValidator($this->container);
        list($valid,) = $passwordResetValidator->validateCreation(array('username' => 'foobar', 'captcha' => 'test'));
        $this->assertFalse($valid);
    }
}
