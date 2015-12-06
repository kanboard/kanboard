<?php

namespace Kanboard\Core\Ldap;

require_once __DIR__.'/../../Base.php';

function ldap_search($link_identifier, $base_dn, $filter, array $attributes)
{
    return QueryTest::$functions->ldap_search($link_identifier, $base_dn, $filter, $attributes);
}

function ldap_get_entries($link_identifier, $result_identifier)
{
    return QueryTest::$functions->ldap_get_entries($link_identifier, $result_identifier);
}

class QueryTest extends \Base
{
    public static $functions;
    private $client;

    public function setUp()
    {
        parent::setup();

        self::$functions = $this
            ->getMockBuilder('stdClass')
            ->setMethods(array(
                'ldap_search',
                'ldap_get_entries',
            ))
            ->getMock();

        $this->client = $this
            ->getMockBuilder('\Kanboard\Core\Ldap\Client')
            ->setMethods(array(
                'getConnection',
            ))
            ->getMock();
    }

    public function tearDown()
    {
        parent::tearDown();
        self::$functions = null;
    }

    public function testExecuteQuerySuccessfully()
    {
        $entries = array(
            'count' => 1,
            0 => array(
                'count' => 2,
                'dn' => 'uid=my_user,ou=People,dc=kanboard,dc=local',
                'displayname' => array(
                    'count' => 1,
                    0 => 'My user',
                ),
                'mail' => array(
                    'count' => 2,
                    0 => 'user1@localhost',
                    1 => 'user2@localhost',
                ),
                0 => 'displayname',
                1 => 'mail',
            )
        );

        $this->client
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

        self::$functions
            ->expects($this->once())
            ->method('ldap_search')
            ->with(
                $this->equalTo('my_ldap_resource'),
                $this->equalTo('ou=People,dc=kanboard,dc=local'),
                $this->equalTo('uid=my_user'),
                $this->equalTo(array('displayname'))
            )
            ->will($this->returnValue('search_resource'));

        self::$functions
            ->expects($this->once())
            ->method('ldap_get_entries')
            ->with(
                $this->equalTo('my_ldap_resource'),
                $this->equalTo('search_resource')
            )
            ->will($this->returnValue($entries));

        $query = new Query($this->client);
        $query->execute('ou=People,dc=kanboard,dc=local', 'uid=my_user', array('displayname'));
        $this->assertTrue($query->hasResult());

        $this->assertEquals('My user', $query->getEntries()->getFirstEntry()->getFirstValue('displayname'));
        $this->assertEquals('user1@localhost', $query->getEntries()->getFirstEntry()->getFirstValue('mail'));
        $this->assertEquals('', $query->getEntries()->getFirstEntry()->getFirstValue('not_found'));

        $this->assertEquals('uid=my_user,ou=People,dc=kanboard,dc=local', $query->getEntries()->getFirstEntry()->getDn());
        $this->assertEquals('', $query->getEntries()->getFirstEntry()->getFirstValue('missing'));
    }

    public function testExecuteQueryNotFound()
    {
        $this->client
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

        self::$functions
            ->expects($this->once())
            ->method('ldap_search')
            ->with(
                $this->equalTo('my_ldap_resource'),
                $this->equalTo('ou=People,dc=kanboard,dc=local'),
                $this->equalTo('uid=my_user'),
                $this->equalTo(array('displayname'))
            )
            ->will($this->returnValue('search_resource'));

        self::$functions
            ->expects($this->once())
            ->method('ldap_get_entries')
            ->with(
                $this->equalTo('my_ldap_resource'),
                $this->equalTo('search_resource')
            )
            ->will($this->returnValue(array()));

        $query = new Query($this->client);
        $query->execute('ou=People,dc=kanboard,dc=local', 'uid=my_user', array('displayname'));
        $this->assertFalse($query->hasResult());
    }

    public function testExecuteQueryFailed()
    {
        $this->client
            ->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

        self::$functions
            ->expects($this->once())
            ->method('ldap_search')
            ->with(
                $this->equalTo('my_ldap_resource'),
                $this->equalTo('ou=People,dc=kanboard,dc=local'),
                $this->equalTo('uid=my_user'),
                $this->equalTo(array('displayname'))
            )
            ->will($this->returnValue(false));

        $query = new Query($this->client);
        $query->execute('ou=People,dc=kanboard,dc=local', 'uid=my_user', array('displayname'));
        $this->assertFalse($query->hasResult());
    }
}
