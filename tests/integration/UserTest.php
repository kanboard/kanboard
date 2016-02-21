<?php

require_once __DIR__.'/Base.php';

class UserTest extends Base
{
    public function testDisableUser()
    {
        $this->assertEquals(2, $this->app->createUser(array('username' => 'someone', 'password' => 'test123')));
        $this->assertTrue($this->app->isActiveUser(2));

        $this->assertTrue($this->app->disableUser(2));
        $this->assertFalse($this->app->isActiveUser(2));

        $this->assertTrue($this->app->enableUser(2));
        $this->assertTrue($this->app->isActiveUser(2));
    }
}
