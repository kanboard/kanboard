<?php

namespace KanboardTests\units\Auth;

use KanboardTests\units\Base;
use Kanboard\Auth\LdapAuth;

class LdapAuthTest extends Base
{
    public function testGetName()
    {
        $provider = new LdapAuth($this->container);
        $this->assertEquals('LDAP', $provider->getName());
    }

    public function testAuthenticateRejectsEmptyPasswordWithoutBinding()
    {
        $provider = $this
            ->getMockBuilder(LdapAuth::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('getLdapUsername', 'getLdapPassword'))
            ->getMock();

        // Credentials are only needed to open the LDAP connection, which must not happen.
        $provider->expects($this->never())->method('getLdapUsername');
        $provider->expects($this->never())->method('getLdapPassword');

        $provider->setUsername('admin');
        $provider->setPassword('');

        $this->assertFalse($provider->authenticate());
        $this->assertNull($provider->getUser());
    }
}
