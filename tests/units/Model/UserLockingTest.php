<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\UserLockingModel;

class UserLockingTest extends Base
{
    public function testFailedLogin()
    {
        $u = new UserLockingModel($this->container);

        $this->assertEquals(0, $u->getFailedLogin('admin'));
        $this->assertEquals(0, $u->getFailedLogin('not_found'));

        $this->assertTrue($u->incrementFailedLogin('admin'));
        $this->assertTrue($u->incrementFailedLogin('admin'));

        $this->assertEquals(2, $u->getFailedLogin('admin'));
        $this->assertTrue($u->resetFailedLogin('admin'));
        $this->assertEquals(0, $u->getFailedLogin('admin'));
    }

    public function testLocking()
    {
        $u = new UserLockingModel($this->container);

        $this->assertFalse($u->isLocked('admin'));
        $this->assertFalse($u->isLocked('not_found'));
        $this->assertTrue($u->lock('admin', 1));
        $this->assertTrue($u->isLocked('admin'));
    }

    public function testCaptcha()
    {
        $u = new UserLockingModel($this->container);
        $this->assertTrue($u->incrementFailedLogin('admin'));
        $this->assertFalse($u->hasCaptcha('admin', 2));

        $this->assertTrue($u->incrementFailedLogin('admin'));
        $this->assertTrue($u->hasCaptcha('admin', 2));
    }
}
