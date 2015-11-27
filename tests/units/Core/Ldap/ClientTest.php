<?php

namespace Kanboard\Core\Ldap;

require_once __DIR__.'/../../Base.php';

function ldap_connect($hostname, $port)
{
    return ClientTest::$functions->ldap_connect($hostname, $port);
}

function ldap_set_option()
{
}

function ldap_bind($link_identifier, $bind_rdn = null, $bind_password = null)
{
    return ClientTest::$functions->ldap_bind($link_identifier, $bind_rdn, $bind_password);
}

function ldap_start_tls($link_identifier)
{
    return ClientTest::$functions->ldap_start_tls($link_identifier);
}

class ClientTest extends \Base
{
    public static $functions;
    private $ldap;

    public function setUp()
    {
        parent::setup();

        self::$functions = $this
            ->getMockBuilder('stdClass')
            ->setMethods(array(
                'ldap_connect',
                'ldap_set_option',
                'ldap_bind',
                'ldap_start_tls',
            ))
            ->getMock();
    }

    public function tearDown()
    {
        parent::tearDown();
        self::$functions = null;
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
            ->will($this->returnValue('my_ldap_resource'));

        $ldap = new Client;
        $this->assertEquals('my_ldap_resource', $ldap->getConnection('my_ldap_server'));
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
            ->will($this->returnValue(false));

        $this->setExpectedException('\Kanboard\Core\Ldap\ClientException');

        $ldap = new Client;
        $this->assertNotEquals('my_ldap_resource', $ldap->getConnection('my_ldap_server'));
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
            ->will($this->returnValue('my_ldap_resource'));

        self::$functions
            ->expects($this->once())
            ->method('ldap_start_tls')
            ->with(
                $this->equalTo('my_ldap_resource')
            )
            ->will($this->returnValue(true));

        $ldap = new Client;
        $this->assertEquals('my_ldap_resource', $ldap->getConnection('my_ldap_server', 389, true));
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
            ->will($this->returnValue('my_ldap_resource'));

        self::$functions
            ->expects($this->once())
            ->method('ldap_start_tls')
            ->with(
                $this->equalTo('my_ldap_resource')
            )
            ->will($this->returnValue(false));

        $this->setExpectedException('\Kanboard\Core\Ldap\ClientException');

        $ldap = new Client;
        $this->assertNotEquals('my_ldap_resource', $ldap->getConnection('my_ldap_server', 389, true));
    }

    public function testAnonymousAuthenticationSuccess()
    {
        self::$functions
            ->expects($this->once())
            ->method('ldap_bind')
            ->with(
                $this->equalTo('my_ldap_resource')
            )
            ->will($this->returnValue(true));

        $ldap = new Client;
        $this->assertTrue($ldap->useAnonymousAuthentication('my_ldap_resource'));
    }

    public function testAnonymousAuthenticationFailure()
    {
        self::$functions
            ->expects($this->once())
            ->method('ldap_bind')
            ->with(
                $this->equalTo('my_ldap_resource')
            )
            ->will($this->returnValue(false));

        $this->setExpectedException('\Kanboard\Core\Ldap\ClientException');

        $ldap = new Client;
        $ldap->useAnonymousAuthentication('my_ldap_resource');
    }

    public function testUserAuthenticationSuccess()
    {
        self::$functions
            ->expects($this->once())
            ->method('ldap_bind')
            ->with(
                $this->equalTo('my_ldap_resource'),
                $this->equalTo('my_ldap_user'),
                $this->equalTo('my_ldap_password')
            )
            ->will($this->returnValue(true));

        $ldap = new Client;
        $this->assertTrue($ldap->authenticate('my_ldap_resource', 'my_ldap_user', 'my_ldap_password'));
    }

    public function testUserAuthenticationFailure()
    {
        self::$functions
            ->expects($this->once())
            ->method('ldap_bind')
            ->with(
                $this->equalTo('my_ldap_resource'),
                $this->equalTo('my_ldap_user'),
                $this->equalTo('my_ldap_password')
            )
            ->will($this->returnValue(false));

        $this->setExpectedException('\Kanboard\Core\Ldap\ClientException');

        $ldap = new Client;
        $ldap->authenticate('my_ldap_resource', 'my_ldap_user', 'my_ldap_password');
    }
}
