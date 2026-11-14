<?php

namespace KanboardTests\units\Validator;

use KanboardTests\units\Base;
use Kanboard\Validator\AuthValidator;

class AuthValidatorTest extends Base
{
    public function testValidateFormWithValidCaptchaRemovesCaptcha()
    {
        $this->container['userLockingModel']->incrementFailedLogin('admin');
        $this->container['userLockingModel']->incrementFailedLogin('admin');
        $this->container['userLockingModel']->incrementFailedLogin('admin');
        $_SESSION['captcha'] = 'test';

        $authValidator = new AuthValidator($this->container);
        list($valid, ) = $authValidator->validateForm(array(
            'username' => 'admin',
            'password' => 'admin',
            'captcha' => 'test',
        ));

        $this->assertTrue($valid);
        $this->assertArrayNotHasKey('captcha', $_SESSION);
    }

    public function testValidateFormWithInvalidCaptchaRemovesCaptcha()
    {
        $this->container['userLockingModel']->incrementFailedLogin('admin');
        $this->container['userLockingModel']->incrementFailedLogin('admin');
        $this->container['userLockingModel']->incrementFailedLogin('admin');
        $_SESSION['captcha'] = 'test';

        $authValidator = new AuthValidator($this->container);
        list($valid, ) = $authValidator->validateForm(array(
            'username' => 'admin',
            'password' => 'admin',
            'captcha' => 'wrong',
        ));

        $this->assertFalse($valid);
        $this->assertArrayNotHasKey('captcha', $_SESSION);
    }
}
