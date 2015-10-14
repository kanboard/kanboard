<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Auth\ReverseProxy;
use Kanboard\Model\User;

class ReverseProxyTest extends Base
{
    public function setUp()
    {
        parent::setup();
        $_SERVER = array();
    }

    public function testFailedAuthentication()
    {
        $auth = new ReverseProxy($this->container);
        $this->assertFalse($auth->authenticate());
    }

    public function testSuccessfulAuthentication()
    {
        $_SERVER[REVERSE_PROXY_USER_HEADER] = 'my_user';

        $a = new ReverseProxy($this->container);
        $u = new User($this->container);

        $this->assertTrue($a->authenticate());

        $user = $u->getByUsername('my_user');
        $this->assertNotEmpty($user);
        $this->assertEquals(0, $user['is_admin']);
        $this->assertEquals(1, $user['is_ldap_user']);
        $this->assertEquals(1, $user['disable_login_form']);
    }
}
