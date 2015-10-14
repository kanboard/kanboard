<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\User;
use Kanboard\Model\Authentication;

class AuthenticationTest extends Base
{
    public function testHasCaptcha()
    {
        $u = new User($this->container);
        $a = new Authentication($this->container);

        $this->assertFalse($a->hasCaptcha('not_found'));
        $this->assertFalse($a->hasCaptcha('admin'));

        $this->assertTrue($u->incrementFailedLogin('admin'));
        $this->assertTrue($u->incrementFailedLogin('admin'));
        $this->assertTrue($u->incrementFailedLogin('admin'));

        $this->assertFalse($a->hasCaptcha('not_found'));
        $this->assertTrue($a->hasCaptcha('admin'));
    }

    public function testHandleFailedLogin()
    {
        $u = new User($this->container);
        $a = new Authentication($this->container);

        $this->assertFalse($u->isLocked('admin'));

        for ($i = 0; $i <= 6; $i++) {
            $a->handleFailedLogin('admin');
        }

        $this->assertTrue($u->isLocked('admin'));
    }
}
