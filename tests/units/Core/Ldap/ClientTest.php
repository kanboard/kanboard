<?php

namespace Kanboard\Core\Ldap;

use KanboardTests\units\Core\Ldap\ClientTest;

function ldap_connect($hostname, $port)
{
    return ClientTest::$functions->ldap_connect($hostname, $port);
}

function ldap_set_option()
{
}

function ldap_get_option($link_identifier, $option, &$error)
{
    $error = 'some extended error';
}

function ldap_error($link_identifier)
{
    return 'some error';
}

function ldap_errno($link_identifier)
{
    return -100;
}

function ldap_bind($link_identifier, $bind_rdn = null, $bind_password = null)
{
    return ClientTest::$functions->ldap_bind($link_identifier, $bind_rdn, $bind_password);
}

function ldap_start_tls($link_identifier)
{
    return ClientTest::$functions->ldap_start_tls($link_identifier);
}

namespace KanboardTests\units\Core\Ldap;

use Kanboard\Core\Ldap\Client;
use KanboardTests\units\Base;

class ClientFunctionsProxy
{
    public function ldap_connect($hostname, $port)
    {
    }

    public function ldap_set_option()
    {
    }

    public function ldap_get_option($link_identifier, $option, &$error)
    {
    }

    public function ldap_bind($link_identifier, $bind_rdn = null, $bind_password = null)
    {
    }

    public function ldap_start_tls($link_identifier)
    {
    }

    public function ldap_error($link_identifier)
    {
    }

    public function ldap_errno($link_identifier)
    {
    }
}

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class ClientTest extends Base
{
    public static $functions;

    protected function setUp(): void
    {
        parent::setup();

        if (! function_exists('ldap_connect')) {
            $this->markTestSkipped('The PHP LDAP extension is required');
        }

        self::$functions = $this
            ->getMockBuilder(ClientFunctionsProxy::class)
            ->onlyMethods(array(
                'ldap_connect',
                'ldap_set_option',
                'ldap_get_option',
                'ldap_bind',
                'ldap_start_tls',
                'ldap_error',
                'ldap_errno'
            ))
            ->getMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::$functions = null;
    }

    public function testGetLdapServerNotConfigured()
    {
        $this->expectException('\LogicException');
        $ldap = new Client;
        $ldap->getLdapServer();
    }

    public function testConnectSuccess()
    {
        self::$functions
            ->expects($this->once())
            ->method('ldap_connect')
            ->with(
                $this->equalTo('my_ldap_server'),
                $this->equalTo(389)
            )
            ->willReturn('my_ldap_resource');

        $ldap = new Client;
        $ldap->open('my_ldap_server');
        $this->assertEquals('my_ldap_resource', $ldap->getConnection());
    }

    public function testConnectFailure()
    {
        self::$functions
            ->expects($this->once())
            ->method('ldap_connect')
            ->with(
                $this->equalTo('my_ldap_server'),
                $this->equalTo(389)
            )
            ->willReturn(false);

        $this->expectException('\Kanboard\Core\Ldap\ConnectionException');

        $ldap = new Client;
        $ldap->open('my_ldap_server');
        $this->assertNotEquals('my_ldap_resource', $ldap->getConnection());
    }

    public function testConnectSuccessWithTLS()
    {
        self::$functions
            ->expects($this->once())
            ->method('ldap_connect')
            ->with(
                $this->equalTo('my_ldap_server'),
                $this->equalTo(389)
            )
            ->willReturn('my_ldap_resource');

        self::$functions
            ->expects($this->once())
            ->method('ldap_start_tls')
            ->with(
                $this->equalTo('my_ldap_resource')
            )
            ->willReturn(true);

        $ldap = new Client;
        $ldap->open('my_ldap_server', 389, true);
        $this->assertEquals('my_ldap_resource', $ldap->getConnection());
    }

    public function testConnectFailureWithTLS()
    {
        self::$functions
            ->expects($this->once())
            ->method('ldap_connect')
            ->with(
                $this->equalTo('my_ldap_server'),
                $this->equalTo(389)
            )
            ->willReturn('my_ldap_resource');

        self::$functions
            ->expects($this->once())
            ->method('ldap_start_tls')
            ->with(
                $this->equalTo('my_ldap_resource')
            )
            ->willReturn(false);

        $this->expectException('\Kanboard\Core\Ldap\ConnectionException');

        $ldap = new Client;
        $ldap->open('my_ldap_server', 389, true);
        $this->assertNotEquals('my_ldap_resource', $ldap->getConnection());
    }

    public function testAnonymousAuthenticationSuccess()
    {
        self::$functions
            ->expects($this->once())
            ->method('ldap_bind')
            ->willReturn(true);

        $ldap = new Client;
        $this->assertTrue($ldap->useAnonymousAuthentication());
    }

    public function testAnonymousAuthenticationFailure()
    {
        self::$functions
            ->expects($this->once())
            ->method('ldap_bind')
            ->willReturn(false);

        $this->expectException('\Kanboard\Core\Ldap\ClientException');

        $ldap = new Client;
        $ldap->useAnonymousAuthentication();
    }

    public function testUserAuthenticationSuccess()
    {
        self::$functions
            ->expects($this->once())
            ->method('ldap_connect')
            ->with(
                $this->equalTo('my_ldap_server'),
                $this->equalTo(389)
            )
            ->willReturn('my_ldap_resource');

        self::$functions
            ->expects($this->once())
            ->method('ldap_bind')
            ->with(
                $this->equalTo('my_ldap_resource'),
                $this->equalTo('my_ldap_user'),
                $this->equalTo('my_ldap_password')
            )
            ->willReturn(true);

        $ldap = new Client;
        $ldap->open('my_ldap_server');
        $this->assertTrue($ldap->authenticate('my_ldap_user', 'my_ldap_password'));
    }

    public function testUserAuthenticationFailure()
    {
        self::$functions
            ->expects($this->once())
            ->method('ldap_connect')
            ->with(
                $this->equalTo('my_ldap_server'),
                $this->equalTo(389)
            )
            ->willReturn('my_ldap_resource');

        self::$functions
            ->expects($this->once())
            ->method('ldap_bind')
            ->with(
                $this->equalTo('my_ldap_resource'),
                $this->equalTo('my_ldap_user'),
                $this->equalTo('my_ldap_password')
            )
            ->willReturn(false);

        $this->expectException('\Kanboard\Core\Ldap\ClientException');

        $ldap = new Client;
        $ldap->open('my_ldap_server');
        $ldap->authenticate('my_ldap_user', 'my_ldap_password');
    }
}
