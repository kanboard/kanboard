<?php

namespace Auth;

require_once __DIR__.'/../Base.php';

function ldap_connect($hostname, $port)
{
    return LdapTest::$functions->ldap_connect($hostname, $port);
}

function ldap_set_option()
{
}

function ldap_bind($ldap, $ldap_username, $ldap_password)
{
    return LdapTest::$functions->ldap_bind($ldap, $ldap_username, $ldap_password);
}

class LdapTest extends \Base
{
    public static $functions;

    public function setUp()
    {
        parent::setup();

        self::$functions = $this
            ->getMockBuilder('stdClass')
            ->setMethods(array(
                'ldap_connect',
                'ldap_set_option',
                'ldap_bind',
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

        $ldap = new Ldap($this->container);
        $this->assertNotFalse($ldap->connect());
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

        $ldap = new Ldap($this->container);
        $this->assertFalse($ldap->connect());
    }

    public function testBindAnonymous()
    {
        self::$functions
            ->expects($this->once())
            ->method('ldap_bind')
            ->with(
                $this->equalTo('my_ldap_connection'),
                $this->equalTo(null),
                $this->equalTo(null)
            )
            ->will($this->returnValue(true));

        $ldap = new Ldap($this->container);
        $this->assertTrue($ldap->bind('my_ldap_connection', 'my_user', 'my_password', 'anonymous'));
    }

    public function testBindUser()
    {
        self::$functions
            ->expects($this->once())
            ->method('ldap_bind')
            ->with(
                $this->equalTo('my_ldap_connection'),
                $this->equalTo('uid=my_user'),
                $this->equalTo('my_password')
            )
            ->will($this->returnValue(true));

        $ldap = new Ldap($this->container);
        $this->assertTrue($ldap->bind('my_ldap_connection', 'my_user', 'my_password', 'user', 'uid=%s', 'something'));
    }

    public function testBindProxy()
    {
        self::$functions
            ->expects($this->once())
            ->method('ldap_bind')
            ->with(
                $this->equalTo('my_ldap_connection'),
                $this->equalTo('someone'),
                $this->equalTo('something')
            )
            ->will($this->returnValue(true));

        $ldap = new Ldap($this->container);
        $this->assertTrue($ldap->bind('my_ldap_connection', 'my_user', 'my_password', 'proxy', 'someone', 'something'));
    }
}
